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

        // Compartir la configuración del sistema con todas las vistas (cacheada 10 min)
        View::composer('*', function ($view) {
            $sistemaConfig = Cache::remember('sistema_config', 600, function () {
                return ConfiguracionSistema::instancia();
            });
            $view->with('sistemaConfig', $sistemaConfig);
        });
    }
}