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
 * Etapa 6.1 + Gate G-01 (ver docs/ARQUITECTURA_MODULAR.md): provisiona un
 * tenant nuevo por código, usando exclusivamente la conexión
 * `mysql_tenant_admin` — un usuario acotado al patrón `historias_%`, sin
 * privilegios administrativos de servidor. Nunca usa la conexión `mysql`
 * (la de `saas_user`, compartida por toda la infraestructura del servidor).
 *
 * Ni el modelo Tenant ni los installers de Platform (ComponenteInstaller,
 * CapabilityInstaller, FieldVisibilityInstaller...) fijan una conexión
 * explícita: todos usan la conexión default de Eloquent. Por eso este
 * comando reapunta temporalmente `database.default` a `mysql_tenant_admin`
 * durante toda su ejecución — el mismo mecanismo que la versión anterior
 * aplicaba sobre la conexión `mysql`, solo que ahora sobre un usuario
 * acotado. Se restaura al finalizar (defensivo: el proceso termina de
 * todos modos al volver de handle()).
 *
 * Separación conceptual deliberada (incluso usando hoy el mismo usuario
 * para ambas): "Database Provisioning" (crear la base física) es una
 * responsabilidad distinta de "Tenant Provisioning" (migraciones, seeds,
 * Perfil) — la primera es una operación de servidor; la segunda es
 * contenido de aplicación. Separadas en dos métodos, no dos clases — no
 * hay todavía un segundo consumidor que justifique más que eso.
 *
 * Etapa 6.4: `key` (interno, sufijo de la DB, nunca visible) y `slug`
 * (público, DNS-safe, usado por IdentifyTenant para resolver el
 * subdominio) son campos separados a propósito — ver
 * docs/ARQUITECTURA_MODULAR.md.
 */
class TenantsCrear extends Command
{
    private const CONEXION = 'mysql_tenant_admin';

    protected $signature = 'tenants:crear
        {key : Clave del tenant, minúsculas/números/guión bajo/guión medio}
        {--slug= : Slug público para el subdominio (por defecto, key con _ reemplazado por -)}
        {--perfil= : Clave de un Perfil en config/platform/perfiles.php}
        {--con-datos-demo : Sembrar el Escenario Demo del perfil elegido (Etapa 6.2)}';

    protected $description = 'Crea un tenant nuevo: DB + migraciones + seeders base + Perfil opcional + datos demo opcionales';

    public function handle(): int
    {
        $key = $this->argument('key');

        if (! preg_match('/^[a-z0-9_-]+$/', $key)) {
            $this->error('La clave solo puede tener minúsculas, números, guión bajo y guión medio.');
            return self::FAILURE;
        }

        $slug = $this->option('slug') ?: str_replace('_', '-', $key);

        if (! preg_match('/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', $slug)) {
            $this->error("El slug '{$slug}' no es válido — solo minúsculas, números y guión medio (no al inicio/final).");
            return self::FAILURE;
        }

        $reservados = config('reserved_slugs', []);
        $valorReservado = collect([$key, $slug])->first(fn ($v) => in_array(strtolower($v), $reservados, true));
        if ($valorReservado) {
            $this->error("'{$valorReservado}' es un subdominio reservado de la plataforma (ver config/platform/reserved_slugs.php) — no puede usarse como tenant_key ni slug.");
            return self::FAILURE;
        }

        $database = 'historias_' . $key;
        $originalDefault = Config::get('database.default');
        $originalDatabase = Config::get('database.connections.' . self::CONEXION . '.database');

        Config::set('database.default', self::CONEXION);
        $this->conectarComo($originalDatabase);

        if (Tenant::where('tenant_key', $key)->exists()) {
            $this->error("Ya existe un tenant con la clave '{$key}'.");
            $this->restaurarDefault($originalDefault);
            return self::FAILURE;
        }

        if (Tenant::where('slug', $slug)->exists()) {
            $this->error("Ya existe un tenant con el slug '{$slug}'.");
            $this->restaurarDefault($originalDefault);
            return self::FAILURE;
        }

        try {
            $this->crearBaseDeDatos($database);
        } catch (Throwable $e) {
            $this->error("Falló la creación de la base '{$database}': {$e->getMessage()}");
            $this->restaurarDefault($originalDefault);
            return self::FAILURE;
        }

        $tenant = Tenant::create([
            'tenant_key' => $key,
            'slug' => $slug,
            'database' => $database,
            'status' => 'en_migracion',
        ]);

        try {
            $this->provisionarTenant($perfilKey = $this->option('perfil'), (bool) $this->option('con-datos-demo'), $database);
        } catch (Throwable $e) {
            $this->conectarComo($originalDatabase);
            $tenant->update(['status' => 'error', 'last_migration_status' => 'error']);
            $this->error("Falló el provisioning de '{$key}': {$e->getMessage()}");
            $this->restaurarDefault($originalDefault);

            return self::FAILURE;
        }

        $this->conectarComo($originalDatabase);
        $tenant->update([
            'status' => 'activo',
            'last_migration_at' => now(),
            'last_migration_status' => 'ok',
            'version' => config('version.current'),
        ]);

        $this->info("Tenant '{$key}' creado correctamente ({$database}).");
        $this->restaurarDefault($originalDefault);

        return self::SUCCESS;
    }

    /**
     * Database Provisioning: crear la base física. Nada de contenido
     * todavía — es responsabilidad de provisionarTenant().
     */
    private function crearBaseDeDatos(string $database): void
    {
        DB::connection(self::CONEXION)->statement(
            "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
    }

    /**
     * Tenant Provisioning: contenido de aplicación dentro de una base que
     * ya existe — migraciones, seeders base, Perfil, datos demo.
     */
    private function provisionarTenant(?string $perfilKey, bool $conDatosDemo, string $database): void
    {
        $this->conectarComo($database);

        Artisan::call('migrate', ['--force' => true]);
        $this->line(Artisan::output());

        Artisan::call('db:seed', ['--force' => true]);
        $this->line(Artisan::output());

        Artisan::call('db:seed', ['--class' => 'CapabilityStatesSeeder', '--force' => true]);

        if (! $perfilKey) {
            return;
        }

        $perfil = config('perfiles.' . $perfilKey);

        if (! $perfil) {
            $this->warn("Perfil '{$perfilKey}' no existe en config/platform/perfiles.php — tenant creado sin componentes opcionales.");
            return;
        }

        app(ComponenteInstallerContract::class)->instalar($perfil->componentes);
        $this->info("Perfil '{$perfilKey}' aplicado: " . implode(', ', $perfil->componentes ?: ['(sin componentes opcionales)']));

        // El nombre del sistema (login, título de pestaña) se deriva del
        // Perfil, no de config('componentes') — clinica_general no instala
        // ningún Componente pero igual necesita su propio nombre.
        \App\Models\ConfiguracionSistema::instancia()->update(['nombre_sistema' => $perfil->nombreSistema]);

        if ($conDatosDemo && $perfil->demoSeeder) {
            Artisan::call('db:seed', ['--class' => $perfil->demoSeeder, '--force' => true]);
            $this->info("Escenario demo de '{$perfilKey}' sembrado.");
        }
    }

    private function conectarComo(string $database): void
    {
        Config::set('database.connections.' . self::CONEXION . '.database', $database);
        DB::purge(self::CONEXION);
        DB::reconnect(self::CONEXION);
    }

    private function restaurarDefault(?string $originalDefault): void
    {
        Config::set('database.default', $originalDefault);
    }
}
