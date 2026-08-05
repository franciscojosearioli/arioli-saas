<?php

namespace App\Console\Commands;

use App\Models\AppVersion;
use App\Models\License;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProvisionDemoInstance extends Command
{
    protected $signature   = 'demo:provision {product : Slug del producto (loteos|tallerpro|historias-clinicas|turnos|subastas)}';
    protected $description = 'Provisiona una instancia demo oficial para el producto indicado (tenant_id=demo)';

    private const DEMO_TENANT = 'demo';
    private const DEMO_NAME   = 'Demo Admin';
    private const DEMO_PASS   = 'demo1234';

    private static array $config = [
        'loteos'             => ['provision' => 'provision:loteos-direct',    'db_prefix' => 'loteos_',    'email' => 'admin@demo.com', 'app_name' => 'Loteos Demo'],
        'tallerpro'          => ['provision' => 'provision:tallerpro-direct', 'db_prefix' => 'tallerpro_', 'email' => 'admin@demo.com', 'app_name' => 'TallerPro Demo'],
        'historias-clinicas' => ['provision' => null,                         'db_prefix' => 'historias_', 'email' => 'admin@demo.com',            'app_name' => 'Historias Clínicas Demo'],
        'turnos'             => ['provision' => 'provision:turnos-direct',    'db_prefix' => 'turnos_',    'email' => 'admin@demo.com', 'app_name' => 'Turnos Demo'],
        'subastas'           => ['provision' => 'provision:subastas-direct', 'db_prefix' => 'subastas_',  'email' => 'admin@demo.com', 'app_name' => 'Subastas Demo'],
    ];

    public function handle(): int
    {
        $slug = $this->argument('product');

        if (!isset(self::$config[$slug])) {
            $this->error("Producto inválido: {$slug}. Use: loteos, tallerpro, historias-clinicas, turnos, subastas");
            return 1;
        }

        $product = Product::where('slug', $slug)->first();
        if (!$product) {
            $this->error("Producto '{$slug}' no encontrado en la base de datos.");
            return 1;
        }

        $existing = License::where('tenant_id', self::DEMO_TENANT)
            ->whereHas('plan.product', fn($q) => $q->where('slug', $slug))
            ->first();

        if ($existing) {
            $this->warn("Ya existe una instancia demo para {$slug} (license_id={$existing->id}).");
            $this->warn("Use 'demo:reset {$slug}' para reiniciarla.");
            return 1;
        }

        // Find or create Demo plan
        $demoPlan = Plan::firstOrCreate(
            ['product_id' => $product->id, 'name' => 'Demo'],
            ['period' => 'monthly', 'period_months' => 0, 'is_demo' => true, 'base_price' => 0, 'price' => 0, 'discount_percent' => 0, 'active' => true]
        );

        $cfg     = self::$config[$slug];
        $dbName  = $cfg['db_prefix'] . self::DEMO_TENANT;
        $email   = $cfg['email'];
        $appName = $cfg['app_name'];

        $this->info("Provisionando demo {$slug} (db={$dbName})...");

        try {
            if ($slug === 'historias-clinicas') {
                $result = $this->provisionHistorias($dbName, $email, $appName, $demoPlan);
            } else {
                $exitCode = Artisan::call($cfg['provision'], [
                    'tenant_id' => self::DEMO_TENANT, 'admin_name' => self::DEMO_NAME,
                    'admin_email' => $email, 'admin_password' => self::DEMO_PASS,
                ]);
                if ($exitCode !== 0) {
                    $this->error("Provision failed (exit={$exitCode}): " . Artisan::output());
                    return 1;
                }
                $result = ['success' => true];
            }

            if (!($result['success'] ?? false)) {
                $this->error("Error: " . ($result['error'] ?? 'desconocido'));
                return 1;
            }
        } catch (\Throwable $e) {
            $this->error("Excepción: " . $e->getMessage());
            Log::error('demo:provision failed', ['product' => $slug, 'error' => $e->getMessage()]);
            return 1;
        }

        // Update license to demo type
        $license = License::where('tenant_id', self::DEMO_TENANT)
            ->whereHas('plan.product', fn($q) => $q->where('slug', $slug))
            ->first();

        if ($license) {
            $license->update(['plan_id' => $demoPlan->id, 'license_type' => 'demo', 'expires_at' => null]);
            $this->info("Licencia actualizada a tipo DEMO.");
        }

        // Seed demo data via internal HTTP endpoint
        $this->info("Sembrando datos demo...");
        try {
            $this->callInternalEndpoint($product->public_domain, 'seed');
        } catch (\Throwable $e) {
            $this->warn("Error sembrando datos (no crítico): " . $e->getMessage());
        }

        $tenantDomain = env('SAAS_TENANT_DOMAIN', '127.0.0.1.nip.io');
        $this->info("Demo {$slug} provisionada exitosamente!");
        $this->info("  URL:      demo.{$product->public_domain}.{$tenantDomain}");
        $this->info("  Email:    {$email}");
        $this->info("  Password: " . self::DEMO_PASS);

        return 0;
    }

    // ─────────────────────────────────────────────────────────────
    // Internal HTTP call to app's /internal/demo/{action} endpoint
    // ─────────────────────────────────────────────────────────────
    private function callInternalEndpoint(string $publicDomain, string $action): void
    {
        $domain = env('SAAS_TENANT_DOMAIN', '127.0.0.1.nip.io');
        $host   = "demo.{$publicDomain}.{$domain}";
        $secret = env('API_LICENSE_SECRET');

        $response = Http::timeout(30)
            ->withToken($secret)
            ->withHeaders(['Host' => $host])
            ->post("http://nginx/internal/demo/{$action}");

        if (!$response->successful()) {
            throw new \RuntimeException("Internal {$action} call failed [{$response->status()}]: " . $response->body());
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Historias: copy structure from test DB + insert reference data
    // ─────────────────────────────────────────────────────────────
    private function provisionHistorias(string $dbName, string $email, string $appName, Plan $demoPlan): array
    {
        $sourceDb      = 'historias_test';
        $latestVersion = AppVersion::latestFor('historias-clinicas')?->version ?? '1.0.0';

        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            $tables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=?", [$sourceDb]);
            foreach ($tables as $row) {
                if ($row->TABLE_NAME === 'app_meta') continue;
                DB::statement("CREATE TABLE IF NOT EXISTS `{$dbName}`.`{$row->TABLE_NAME}` LIKE `{$sourceDb}`.`{$row->TABLE_NAME}`");
            }

            foreach (['roles', 'permissions', 'permission_role', 'user_alerts'] as $t) {
                $n = DB::select("SELECT COUNT(*) as n FROM `{$sourceDb}`.`{$t}`")[0]->n ?? 0;
                if ($n > 0) {
                    DB::statement("INSERT IGNORE INTO `{$dbName}`.`{$t}` SELECT * FROM `{$sourceDb}`.`{$t}`");
                }
            }

            DB::statement("
                CREATE TABLE IF NOT EXISTS `{$dbName}`.`app_meta` (
                    `key` VARCHAR(100) NOT NULL PRIMARY KEY, value TEXT NOT NULL, updated_at TIMESTAMP NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            DB::statement("INSERT INTO `{$dbName}`.`app_meta` (`key`, value, updated_at) VALUES ('version',?,NOW())
                ON DUPLICATE KEY UPDATE value=VALUES(value),updated_at=NOW()", [$latestVersion]);

            $exists = DB::select("SELECT COUNT(*) as n FROM `{$dbName}`.`users` WHERE email=?", [$email])[0]->n > 0;
            if (!$exists) {
                DB::statement("INSERT INTO `{$dbName}`.`users` (name,email,password,created_at,updated_at) VALUES (?,?,?,NOW(),NOW())",
                    [self::DEMO_NAME, $email, Hash::make(self::DEMO_PASS)]);
                $userId = DB::select("SELECT LAST_INSERT_ID() as id")[0]->id;
                DB::statement("INSERT IGNORE INTO `{$dbName}`.`role_user` (user_id,role_id) VALUES (?,1)", [$userId]);
            }

            DB::statement("USE " . config('database.connections.mysql.database'));

            // Create license in saas_central — usa el $demoPlan ya resuelto (por id, no por
            // precio: con licencias indefinidas sin costo puede haber más de un plan price=0
            // para el mismo producto, y "el más barato" ya no identifica al Demo de forma única).
            if ($demoPlan) {
                License::create([
                    'tenant_id' => self::DEMO_TENANT, 'plan_id' => $demoPlan->id,
                    'active' => true, 'installed_version' => $latestVersion,
                    'starts_at' => now()->toDateString(), 'expires_at' => now()->addYear()->toDateString(),
                ]);
            }

            return ['success' => true];
        } catch (\Throwable $e) {
            DB::statement("USE " . config('database.connections.mysql.database'));
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
