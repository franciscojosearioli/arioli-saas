<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // users/roles/permisos viven en database/migrations/tenant (§02/§07
        // del Artifact de arquitectura) — el landlord no las corre por
        // defecto. Bajo phpunit no hay contexto de tenant, así que las
        // sumamos acá para poder testear auth contra un único connection.
        if ($this->app->runningUnitTests()) {
            $this->loadMigrationsFrom(database_path('migrations/tenant'));
        }
    }
}
