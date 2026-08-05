<?php

namespace App\Providers;

use App\Models\Configuracion;
use App\Models\FotoPropiedad;
use App\Models\Propiedad;
use App\Observers\FotoPropiedadObserver;
use App\Observers\PropiedadObserver;
use App\Services\License\LicenseClient;
use App\Services\License\LicenseClientInterface;
use App\Services\Publicaciones\ChannelAdapterRegistry;
use App\Services\Publicaciones\FacebookAdapter;
use App\Services\Publicaciones\InstagramAdapter;
use App\Services\Publicaciones\MetaGraphClient;
use App\Services\Publicaciones\SitioWebAdapter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LicenseClientInterface::class, fn () => LicenseClient::fromConfig());

        // §09 Fase 4: constructor pide appId/appSecret (primitivos) — sin
        // este bind, el container no puede auto-resolverlo en ningún lado
        // donde se pide por type-hint (controller, comando).
        $this->app->bind(MetaGraphClient::class, fn () => MetaGraphClient::fromConfig());

        // §09: único lugar que arma la lista de canales soportados. El
        // storefront propio (§08) no es un canal — se muestra en cuanto
        // existe la Publicación, sin adapter (Rev. 1.3).
        $this->app->singleton(ChannelAdapterRegistry::class, fn () => new ChannelAdapterRegistry([
            'sitio_web' => new SitioWebAdapter,
            'facebook' => new FacebookAdapter,
            'instagram' => new InstagramAdapter,
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

        // §08: branding del storefront — evita repetir Configuracion::
        // actual() en cada método de StorefrontController.
        View::composer('layouts.storefront', function ($view): void {
            $view->with('configuracion', Configuracion::actual());
        });
    }
}
