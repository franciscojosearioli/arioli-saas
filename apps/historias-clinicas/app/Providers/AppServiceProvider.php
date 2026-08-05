<?php

namespace App\Providers;

use App\Services\License\LicenseClient;
use App\Services\License\LicenseClientInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\ConfiguracionSistema;
use MatanYadaev\EloquentSpatial\Objects\Geometry;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LicenseClientInterface::class, fn() => LicenseClient::fromConfig());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Geometry::macro('getName', function (): string {
            /** @var Geometry $this */
            return class_basename($this);
        });

        // Compartir la configuración del sistema con todas las vistas (cacheada 10 min).
        //
        // La clave incluye el tenant explícitamente — no alcanza con confiar
        // en config('cache.prefix') (que IdentifyTenant reasigna por
        // request): si algún middleware anterior en el pipeline (ej.
        // PreventRequestsDuringMaintenance) ya resolvió el store de Redis
        // con el prefix genérico de la app, ese store queda cacheado para
        // el resto del request y el cambio de config() posterior no lo
        // afecta. Confirmado en vivo: sin esto, dos tenants distintos
        // terminaban viendo el nombre/config de un tercero durante la
        // ventana de 10 minutos — no era cosmético, era una fuga real de
        // datos entre tenants.
        View::composer('*', function ($view) {
            $tenantKey = request()?->attributes->get('tenant_id') ?? 'central';

            $sistemaConfig = Cache::remember('sistema_config_' . $tenantKey, 600, function () {
                return ConfiguracionSistema::instancia();
            });
            $view->with('sistemaConfig', $sistemaConfig);
        });
    }
}