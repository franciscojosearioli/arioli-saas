<?php

namespace App\Providers;

use App\Models\Constructora;
use App\Models\FotoPropiedad;
use App\Models\Propiedad;
use App\Observers\ConstructoraObserver;
use App\Observers\FotoPropiedadObserver;
use App\Observers\PropiedadObserver;
use App\Services\License\LicenseClient;
use App\Services\License\LicenseClientInterface;
use App\Services\Publicaciones\ChannelAdapterRegistry;
use App\Services\Publicaciones\MarketplacePropioAdapter;
use App\Services\Publicaciones\SitioWebAdapter;
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

        // §09: único lugar que arma la lista de canales soportados.
        $this->app->singleton(ChannelAdapterRegistry::class, fn () => new ChannelAdapterRegistry([
            'marketplace' => new MarketplacePropioAdapter,
            'sitio_web' => new SitioWebAdapter,
        ]));
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

        // §09: outbox pattern — se dispara desde el propio modelo, no
        // desde cada controller/Livewire que toca una Propiedad, así
        // ningún camino de escritura se olvida de encolar el evento.
        Propiedad::observe(PropiedadObserver::class);
        FotoPropiedad::observe(FotoPropiedadObserver::class);
        Constructora::observe(ConstructoraObserver::class);
    }
}
