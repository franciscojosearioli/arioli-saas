<?php

namespace App\Console\Commands;

use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateTallerProDirectly extends Command
{
    protected $signature   = 'update:tallerpro-direct {tenant_id} {version}';
    protected $description = 'Apply additive schema changes and update version for a TallerPro tenant';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant_id');
        $version  = $this->argument('version');
        $dbName   = 'tallerpro_' . $tenantId;

        $this->info("Actualizando TallerPro tenant '{$tenantId}' a versión {$version}");

        // Configure a temporary named connection pointing to the tenant DB
        config(['database.connections.tallerpro_update' => array_merge(
            config('database.connections.mysql'),
            ['database' => $dbName],
        )]);

        try {
            DB::connection('tallerpro_update')->statement("
                CREATE TABLE IF NOT EXISTS app_meta (
                    `key` VARCHAR(100) NOT NULL PRIMARY KEY,
                    value TEXT NOT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Apply version-specific additive migrations here as new versions are released.
            // Each block must be idempotent and only add — never remove — columns or tables.
            // Example for a future version 1.1.0:
            // if (version_compare($version, '1.1.0', '>=')) {
            //     DB::connection('tallerpro_update')->statement("ALTER TABLE clients ADD COLUMN IF NOT EXISTS tax_id VARCHAR(30) NULL");
            // }

            DB::connection('tallerpro_update')->statement("
                INSERT INTO app_meta (`key`, value, updated_at) VALUES ('version', ?, NOW())
                ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()
            ", [$version]);

            $this->info("app_meta actualizado a versión {$version}");

            // Purge the temporary connection so it does not leak
            DB::purge('tallerpro_update');

            // Sync to central licenses table
            $updated = License::whereHas('plan.product', fn($q) => $q->where('slug', 'tallerpro'))
                ->where('tenant_id', $tenantId)
                ->where('active', true)
                ->update(['installed_version' => $version]);

            $this->info("Licencias actualizadas: {$updated}");
            $this->info("TallerPro tenant '{$tenantId}' actualizado exitosamente a {$version}");

            return 0;

        } catch (\Throwable $e) {
            DB::purge('tallerpro_update');
            $this->error('Error durante la actualización: ' . $e->getMessage());
            return 1;
        }
    }
}
