<?php

namespace App\Console\Commands;

use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateHistoriasDirectly extends Command
{
    protected $signature   = 'update:historias-clinicas-direct {tenant_id} {version}';
    protected $description = 'Apply additive schema changes and update version for a Historias Clínicas tenant';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant_id');
        $version  = $this->argument('version');
        $dbName   = 'historias_' . $tenantId;

        $this->info("Actualizando Historias Clínicas tenant '{$tenantId}' a versión {$version}");

        config(['database.connections.historias_update' => array_merge(
            config('database.connections.mysql'),
            ['database' => $dbName],
        )]);

        try {
            DB::connection('historias_update')->statement("
                CREATE TABLE IF NOT EXISTS app_meta (
                    `key` VARCHAR(100) NOT NULL PRIMARY KEY,
                    value TEXT NOT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Apply version-specific additive migrations here as new versions are released.
            // Each block must be idempotent and only add — never remove — columns or tables.

            DB::connection('historias_update')->statement("
                INSERT INTO app_meta (`key`, value, updated_at) VALUES ('version', ?, NOW())
                ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()
            ", [$version]);

            $this->info("app_meta actualizado a versión {$version}");

            DB::purge('historias_update');

            $updated = License::whereHas('plan.product', fn($q) => $q->where('slug', 'historias-clinicas'))
                ->where('tenant_id', $tenantId)
                ->where('active', true)
                ->update(['installed_version' => $version]);

            $this->info("Licencias actualizadas: {$updated}");
            $this->info("Historias Clínicas tenant '{$tenantId}' actualizado exitosamente a {$version}");

            return 0;

        } catch (\Throwable $e) {
            DB::purge('historias_update');
            $this->error('Error durante la actualización: ' . $e->getMessage());
            return 1;
        }
    }
}
