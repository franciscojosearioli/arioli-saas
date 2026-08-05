<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Database\Models\Domain;

class PurgeDemoEnvironment extends Command
{
    protected $signature = 'saas:purge-non-demo
                            {--dry-run : Solo listar qué se eliminaría, sin hacer cambios}
                            {--force  : Saltar la confirmación interactiva}';

    protected $description = 'Elimina todos los tenants y licencias que NO sean demo. Deja el sistema en estado limpio de entorno demo.';

    // Prefijo de base de datos por producto slug
    private const DB_PREFIXES = [
        'loteos'             => 'loteos_',
        'tallerpro'          => 'tallerpro_',
        'historias-clinicas' => 'historias_',
        'turnos'             => 'turnos_',
        'subastas'           => 'subastas_',
    ];

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info($isDryRun
            ? '=== MODO DRY-RUN: no se realizarán cambios ==='
            : '=== LIMPIEZA ENTORNO DEMO — Arioli SaaS ==='
        );
        $this->newLine();

        // ── 1. Auditoría: licencias no-demo ──────────────────────────────────────
        $nonDemoLicenses = License::with(['plan.product'])
            ->where(fn($q) => $q->where('license_type', '!=', 'demo')->orWhereNull('license_type'))
            ->get();

        if ($nonDemoLicenses->isEmpty()) {
            $this->info('No hay licencias no-demo. El sistema ya está en estado limpio.');
            return 0;
        }

        $this->warn("Se encontraron {$nonDemoLicenses->count()} licencias NO demo:");
        $this->newLine();

        $rows = [];
        foreach ($nonDemoLicenses as $license) {
            $productSlug = $license->plan?->product?->slug ?? '(sin producto)';
            $dbPrefix    = self::DB_PREFIXES[$productSlug] ?? null;
            $dbName      = $dbPrefix ? $dbPrefix . $license->tenant_id : '(no aplica)';
            $rows[]      = [
                $license->id,
                $license->tenant_id,
                $productSlug,
                $license->license_type ?? 'NULL',
                $dbName,
            ];
        }

        $this->table(
            ['License ID', 'Tenant ID', 'Producto', 'Tipo', 'Base de datos'],
            $rows
        );
        $this->newLine();

        // ── 2. Tenants sin ninguna licencia demo ─────────────────────────────────
        $demoTenantIds  = License::where('license_type', 'demo')->pluck('tenant_id')->unique();
        $orphanTenants  = Tenant::whereNotIn('id', $demoTenantIds)->get();

        if ($orphanTenants->isNotEmpty()) {
            $this->warn("Además hay {$orphanTenants->count()} tenant(s) sin licencia demo asociada:");
            $this->table(
                ['Tenant ID', 'is_demo', 'Creado'],
                $orphanTenants->map(fn($t) => [
                    $t->id,
                    $t->is_demo ? 'sí' : 'no',
                    $t->created_at?->format('Y-m-d H:i') ?? '-',
                ])->toArray()
            );
            $this->newLine();
        }

        if ($isDryRun) {
            $this->comment('Dry-run completado. Ejecutá sin --dry-run para aplicar los cambios.');
            return 0;
        }

        // ── 3. Confirmación ──────────────────────────────────────────────────────
        if (!$this->option('force')) {
            $this->error('⚠️  ESTA OPERACIÓN ES IRREVERSIBLE.');
            $this->error('   Se eliminarán bases de datos completas de tenants.');
            $this->newLine();
            if (!$this->confirm('¿Confirmás la limpieza completa de todos los elementos no-demo?')) {
                $this->comment('Operación cancelada.');
                return 0;
            }
        }

        $this->newLine();
        $errors = [];

        // ── 4. Eliminar licencias no-demo y sus bases de datos ───────────────────
        foreach ($nonDemoLicenses as $license) {
            $tenantId    = $license->tenant_id;
            $productSlug = $license->plan?->product?->slug;
            $dbPrefix    = $productSlug ? (self::DB_PREFIXES[$productSlug] ?? null) : null;
            $dbName      = $dbPrefix ? $dbPrefix . $tenantId : null;

            $this->line("Procesando tenant <info>{$tenantId}</info> ({$productSlug})...");

            // Eliminar base de datos
            if ($dbName) {
                try {
                    $cfg = config('database.connections.mysql');
                    $pdo = new \PDO(
                        "mysql:host={$cfg['host']};port={$cfg['port']};charset=utf8mb4",
                        $cfg['username'],
                        $cfg['password'],
                        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                    );

                    $dbExists = $pdo->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($dbName))->fetch();

                    if ($dbExists) {
                        $pdo->exec("DROP DATABASE `{$dbName}`");
                        $this->line("  ✓ BD eliminada: {$dbName}");
                        Log::info('saas:purge-non-demo dropped database', ['db' => $dbName, 'tenant_id' => $tenantId]);
                    } else {
                        $this->line("  · BD no existe (ya estaba limpia): {$dbName}");
                    }
                } catch (\Throwable $e) {
                    $msg = "  ✗ Error eliminando BD {$dbName}: " . $e->getMessage();
                    $this->error($msg);
                    $errors[] = $msg;
                    Log::error('saas:purge-non-demo failed to drop db', ['db' => $dbName, 'error' => $e->getMessage()]);
                }
            }

            // Eliminar dominios asociados
            try {
                $deleted = Domain::where('tenant_id', $tenantId)->delete();
                if ($deleted > 0) {
                    $this->line("  ✓ {$deleted} dominio(s) eliminado(s)");
                }
            } catch (\Throwable $e) {
                $errors[] = "Error eliminando dominios de {$tenantId}: " . $e->getMessage();
            }

            // Eliminar licencia
            try {
                $license->delete();
                $this->line("  ✓ Licencia #{$license->id} eliminada");
            } catch (\Throwable $e) {
                $errors[] = "Error eliminando licencia #{$license->id}: " . $e->getMessage();
            }
        }

        // ── 5. Eliminar tenants huérfanos ────────────────────────────────────────
        $demoTenantIds = License::where('license_type', 'demo')->pluck('tenant_id')->unique();

        $orphanTenants = Tenant::whereNotIn('id', $demoTenantIds)->get();
        foreach ($orphanTenants as $tenant) {
            try {
                $tenant->delete();
                $this->line("  ✓ Tenant huérfano eliminado: {$tenant->id}");
                Log::info('saas:purge-non-demo deleted orphan tenant', ['tenant_id' => $tenant->id]);
            } catch (\Throwable $e) {
                $errors[] = "Error eliminando tenant {$tenant->id}: " . $e->getMessage();
            }
        }

        // ── 6. Eliminar órdenes no-demo ──────────────────────────────────────────
        try {
            $purgedOrders = Order::whereNotIn('tenant_id', $demoTenantIds)
                ->orWhereNull('tenant_id')
                ->delete();
            if ($purgedOrders > 0) {
                $this->line("  ✓ {$purgedOrders} orden(es) no-demo eliminada(s)");
                Log::info('saas:purge-non-demo deleted orders', ['count' => $purgedOrders]);
            }
        } catch (\Throwable $e) {
            $errors[] = "Error eliminando órdenes: " . $e->getMessage();
        }

        $this->newLine();

        if (!empty($errors)) {
            $this->error('Limpieza completada con errores:');
            foreach ($errors as $err) {
                $this->line("  - {$err}");
            }
            return 1;
        }

        $this->info('✅ Limpieza completada. El sistema queda en estado demo limpio.');
        Log::info('saas:purge-non-demo completed successfully');
        return 0;
    }
}
