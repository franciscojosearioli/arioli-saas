<?php

namespace App\Console\Commands;

use App\Models\AppVersion;
use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProvisionHistoriasDirectly extends Command
{
    protected $signature = 'provision:historias-clinicas-direct {tenant_id} {admin_name} {admin_email} {admin_password}';
    protected $description = 'Provision Historias Clínicas tenant — clones schema from historias_default, creates admin user';

    // Reference DB: base instance with complete schema + roles/permissions seed data
    private const SOURCE_DB = 'historias_default';

    // Tables whose rows are copied into every new tenant (structural reference data — no patient data)
    private const REFERENCE_TABLES = ['roles', 'permissions', 'permission_role', 'user_alerts'];

    public function handle(): int
    {
        $tenantId      = $this->argument('tenant_id');
        $adminName     = $this->argument('admin_name');
        $adminEmail    = $this->argument('admin_email');
        $adminPassword = $this->argument('admin_password');
        $dbName        = 'historias_' . $tenantId;
        $sourceDb      = self::SOURCE_DB;

        $this->info("Provisionando Historias Clínicas tenant: {$tenantId}");

        $latestVersion = AppVersion::latestFor('historias-clinicas')?->version ?? '1.0.0';

        try {
            // 0. Verify reference database exists
            $sourceExists = DB::selectOne(
                "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?",
                [$sourceDb]
            );
            if (!$sourceExists) {
                $this->error("Base de datos de referencia '{$sourceDb}' no existe.");
                $this->error("La base de datos de referencia '{$sourceDb}' no existe. Verificá que el contenedor saas_historias esté corriendo.");
                return 1;
            }

            // 1. Create tenant database
            $this->info("Creando base de datos: {$dbName}");
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 2. Clone schema from reference database (no docker exec — pure SQL)
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

            // 3. Copy reference data (roles, permissions — no patient data)
            foreach (self::REFERENCE_TABLES as $table) {
                $n = DB::selectOne("SELECT COUNT(*) as n FROM `{$sourceDb}`.`{$table}`")->n ?? 0;
                if ($n > 0) {
                    DB::statement("INSERT IGNORE INTO `{$dbName}`.`{$table}` SELECT * FROM `{$sourceDb}`.`{$table}`");
                }
            }

            // 4. Create admin user
            $this->info("Creando usuario administrador...");
            $userExists = DB::selectOne("SELECT COUNT(*) as n FROM `{$dbName}`.`users` WHERE email = ?", [$adminEmail])->n > 0;
            if (!$userExists) {
                DB::statement(
                    "INSERT INTO `{$dbName}`.`users` (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())",
                    [$adminName, $adminEmail, Hash::make($adminPassword)]
                );
            }

            // 5. Assign Admin role (id=1)
            $userId    = DB::selectOne("SELECT id FROM `{$dbName}`.`users` WHERE email = ?", [$adminEmail])?->id;
            $roleExists = DB::selectOne("SELECT COUNT(*) as n FROM `{$dbName}`.`roles` WHERE id = 1")->n > 0;
            if ($userId && $roleExists) {
                $alreadyAssigned = DB::selectOne(
                    "SELECT COUNT(*) as n FROM `{$dbName}`.`role_user` WHERE user_id = ? AND role_id = 1",
                    [$userId]
                )->n > 0;
                if (!$alreadyAssigned) {
                    DB::statement("INSERT INTO `{$dbName}`.`role_user` (user_id, role_id) VALUES (?, 1)", [$userId]);
                }
                $this->info("Rol Admin asignado a {$adminEmail}");
            }

            // 6. Create app_meta with current version
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

            // 7. Create or update License record in saas_central
            // sellable(): nunca el plan "Demo" (price=0 siempre gana un orderBy('price') a secas)
            $plan = \App\Models\Plan::sellable()
                ->whereHas('product', fn($q) => $q->where('slug', 'historias-clinicas'))
                ->orderBy('price')->first();

            if ($plan) {
                $existing = License::where('tenant_id', $tenantId)
                    ->whereHas('plan.product', fn($q) => $q->where('slug', 'historias-clinicas'))
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

            $this->info("✅ Historias Clínicas tenant '{$tenantId}' provisionado exitosamente!");
            $this->info("📋 Base de datos: {$dbName}");
            $this->info("👤 Admin: {$adminEmail}");

            return 0;

        } catch (\Throwable $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
