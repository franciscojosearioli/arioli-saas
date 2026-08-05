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
        \Illuminate\Support\Facades\View::share([
            'appTenantDomain'  => config('app.tenant_domain'),
            'appAdminDomain'   => config('app.admin_domain'),
            'appClienteDomain' => config('app.cliente_domain'),
            'appLandingDomain' => config('app.landing_domain'),
        ]);
    }
}
