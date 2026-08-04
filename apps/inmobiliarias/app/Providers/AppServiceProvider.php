<?php

namespace App\Providers;

use App\Services\License\LicenseClient;
use App\Services\License\LicenseClientInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LicenseClientInterface::class, fn () => LicenseClient::fromConfig());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Owner/Admin (§07 del Artifact) pasa cualquier Policy sin que cada
        // una tenga que repetir el chequeo — las Policies de cada módulo
        // (Fase 1 en adelante) solo necesitan cubrir los demás roles.
        Gate::before(fn ($user) => $user->hasRole('admin') ? true : null);
    }
}
