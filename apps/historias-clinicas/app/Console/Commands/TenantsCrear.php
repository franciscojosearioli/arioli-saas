<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Platform\Contracts\Services\ComponenteInstallerContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Etapa 6.1: provisiona un tenant nuevo por código — la base técnica que
 * el resto de "Perfiles de Implementación y Demos Especializadas" necesita.
 * Sin UI, sin expiración automática todavía (ver docs/ARQUITECTURA_MODULAR.md).
 *
 * No borra la base automáticamente si algo falla a mitad de camino — el
 * tenant queda con status='error' para que un humano decida, mismo
 * criterio de no auto-destruir que el resto de la sesión.
 */
class TenantsCrear extends Command
{
    protected $signature = 'tenants:crear {key : Clave del tenant, minúsculas/números/guión bajo} {--perfil= : Clave de un Perfil en config/platform/perfiles.php}';

    protected $description = 'Crea un tenant nuevo: DB + migraciones + seeders base + Perfil opcional';

    public function handle(): int
    {
        $key = $this->argument('key');

        if (! preg_match('/^[a-z0-9_]+$/', $key)) {
            $this->error('La clave solo puede tener minúsculas, números y guión bajo.');
            return self::FAILURE;
        }

        if (Tenant::where('tenant_key', $key)->exists()) {
            $this->error("Ya existe un tenant con la clave '{$key}'.");
            return self::FAILURE;
        }

        $database = 'historias_' . $key;
        $originalDatabase = Config::get('database.connections.mysql.database');

        DB::statement("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $tenant = Tenant::create([
            'tenant_key' => $key,
            'database' => $database,
            'status' => 'en_migracion',
        ]);

        Config::set('database.connections.mysql.database', $database);
        DB::purge('mysql');
        DB::reconnect('mysql');

        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->line(Artisan::output());

            Artisan::call('db:seed', ['--force' => true]);
            $this->line(Artisan::output());

            Artisan::call('db:seed', ['--class' => 'CapabilityStatesSeeder', '--force' => true]);

            $perfilKey = $this->option('perfil');
            if ($perfilKey) {
                $perfil = config('perfiles.' . $perfilKey);

                if ($perfil) {
                    app(ComponenteInstallerContract::class)->instalar($perfil->componentes);
                    $this->info("Perfil '{$perfilKey}' aplicado: " . implode(', ', $perfil->componentes ?: ['(sin componentes opcionales)']));
                } else {
                    $this->warn("Perfil '{$perfilKey}' no existe en config/platform/perfiles.php — tenant creado sin componentes opcionales.");
                }
            }
        } catch (Throwable $e) {
            Config::set('database.connections.mysql.database', $originalDatabase);
            DB::purge('mysql');
            DB::reconnect('mysql');

            $tenant->update(['status' => 'error', 'last_migration_status' => 'error']);
            $this->error("Falló la creación de '{$key}': {$e->getMessage()}");

            return self::FAILURE;
        }

        Config::set('database.connections.mysql.database', $originalDatabase);
        DB::purge('mysql');
        DB::reconnect('mysql');

        $tenant->update([
            'status' => 'activo',
            'last_migration_at' => now(),
            'last_migration_status' => 'ok',
            'version' => config('version.current'),
        ]);

        $this->info("Tenant '{$key}' creado correctamente ({$database}).");

        return self::SUCCESS;
    }
}
