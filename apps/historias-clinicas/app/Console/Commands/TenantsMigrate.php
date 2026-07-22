<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Corre las migraciones de database/migrations (NO database/migrations/platform,
 * esas son exclusivas de la DB maestra) contra cada tenant registrado en la
 * tabla `tenants`. Reemplaza a information_schema como mecanismo de
 * iteración multi-tenant — ver docs/ARQUITECTURA_MODULAR.md sección 6.
 */
class TenantsMigrate extends Command
{
    protected $signature = 'tenants:migrate';

    protected $description = 'Corre las migraciones de plataforma/dominio contra todos los tenants activos';

    public function handle(): int
    {
        $originalDatabase = Config::get('database.connections.mysql.database');

        $tenants = Tenant::where('status', '!=', 'suspendido')->get();

        if ($tenants->isEmpty()) {
            $this->warn('No hay tenants registrados en la tabla tenants.');

            return self::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            $this->info("Migrando tenant '{$tenant->tenant_key}' ({$tenant->database})...");

            Config::set('database.connections.mysql.database', $tenant->database);
            DB::purge('mysql');
            DB::reconnect('mysql');

            $status = 'ok';
            $errorMessage = null;

            try {
                Artisan::call('migrate', ['--force' => true]);
                $this->line(Artisan::output());
            } catch (Throwable $e) {
                $status = 'error';
                $errorMessage = $e->getMessage();
                $this->error("Falló la migración de '{$tenant->tenant_key}': {$errorMessage}");
            }

            // Volver a la conexión maestra ANTES de actualizar el registro del
            // tenant — `tenants` solo existe ahí, no en la DB del tenant que
            // recién migramos.
            Config::set('database.connections.mysql.database', $originalDatabase);
            DB::purge('mysql');
            DB::reconnect('mysql');

            $tenant->update([
                'last_migration_at' => now(),
                'last_migration_status' => $status,
                'version' => $status === 'ok' ? config('version.current') : $tenant->version,
            ]);
        }

        return self::SUCCESS;
    }
}
