<?php

namespace App\Console\Commands;

use App\Models\AppVersion;
use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProvisionSubastasDirectly extends Command
{
    protected $signature = 'provision:subastas-direct {tenant_id} {admin_name} {admin_email} {admin_password}';
    protected $description = 'Provision Subastas tenant — clones schema from subastas_default, creates admin user';

    // Base instance con schema completo — mismo criterio que Turnos: clonar
    // de una base viva en vez de mantener SQL hardcodeado en paralelo.
    private const SOURCE_DB = 'subastas_default';

    public function handle(): int
    {
        $tenantId      = $this->argument('tenant_id');
        $adminName     = $this->argument('admin_name');
        $adminEmail    = $this->argument('admin_email');
        $adminPassword = $this->argument('admin_password');
        $dbName        = 'subastas_' . $tenantId;
        $sourceDb      = self::SOURCE_DB;

        $this->info("Provisionando Subastas tenant: {$tenantId}");

        $latestVersion = AppVersion::latestFor('subastas')?->version ?? '1.0.0';

        try {
            $sourceExists = DB::selectOne(
                "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?",
                [$sourceDb]
            );
            if (!$sourceExists) {
                $this->error("La base de datos de referencia '{$sourceDb}' no existe. Verificá que el contenedor saas_subastas esté corriendo.");
                return 1;
            }

            $this->info("Creando base de datos: {$dbName}");
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            $this->info("Clonando estructura desde {$sourceDb}...");
            DB::statement("SET FOREIGN_KEY_CHECKS=0");
            $tables = DB::select(
                "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME != 'app_meta'",
                [$sourceDb]
            );
            foreach ($tables as $row) {
                DB::statement("CREATE TABLE IF NOT EXISTS `{$dbName}`.`{$row->TABLE_NAME}` LIKE `{$sourceDb}`.`{$row->TABLE_NAME}`");
            }
            DB::statement("SET FOREIGN_KEY_CHECKS=1");
            $this->info(count($tables) . " tablas clonadas.");

            $this->info("Creando usuario administrador...");
            $userExists = DB::selectOne("SELECT COUNT(*) as n FROM `{$dbName}`.`users` WHERE email = ?", [$adminEmail])->n > 0;
            if (!$userExists) {
                DB::statement(
                    "INSERT INTO `{$dbName}`.`users` (name, email, password, role, acceso_permitido, created_at, updated_at)
                     VALUES (?, ?, ?, 'admin', 1, NOW(), NOW())",
                    [$adminName, $adminEmail, Hash::make($adminPassword)]
                );
            }

            // Fila única de configuración (id=1) — necesaria para que
            // EnsureSetupComplete no fuerce el wizard en un tenant recién
            // provisionado por compra (ver apps/subastas SetupWizardController).
            $configExists = DB::selectOne("SELECT COUNT(*) as n FROM `{$dbName}`.`configuraciones` WHERE id = 1")->n > 0;
            if (!$configExists) {
                DB::statement(
                    "INSERT INTO `{$dbName}`.`configuraciones` (id, nombre_aplicacion, setup_completed, created_at, updated_at)
                     VALUES (1, 'Subastas', 0, NOW(), NOW())"
                );
            }

            DB::statement("
                CREATE TABLE IF NOT EXISTS `{$dbName}`.`app_meta` (
                    `key` VARCHAR(100) NOT NULL PRIMARY KEY,
                    value TEXT NOT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            DB::statement(
                "INSERT INTO `{$dbName}`.`app_meta` (`key`, value, updated_at) VALUES ('version', ?, NOW())
                 ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()",
                [$latestVersion]
            );

            $plan = \App\Models\Plan::sellable()
                ->whereHas('product', fn($q) => $q->where('slug', 'subastas'))
                ->orderBy('price')->first();

            if ($plan) {
                $existing = License::where('tenant_id', $tenantId)
                    ->whereHas('plan.product', fn($q) => $q->where('slug', 'subastas'))
                    ->first();

                if ($existing) {
                    DB::table('licenses')->where('id', $existing->id)->update(['installed_version' => $latestVersion]);
                } else {
                    License::create([
                        'tenant_id'         => $tenantId,
                        'plan_id'           => $plan->id,
                        'active'            => true,
                        'installed_version' => $latestVersion,
                        'starts_at'         => now()->toDateString(),
                        'expires_at'        => now()->addYear()->toDateString(),
                    ]);
                }
                $this->info("Licencia registrada/actualizada.");
            }

            $this->info("✅ Subastas tenant '{$tenantId}' provisionado exitosamente!");
            $this->info("📋 Base de datos: {$dbName}");
            $this->info("👤 Admin: {$adminEmail}");

            return 0;

        } catch (\Throwable $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
