<?php

namespace App\Providers;

use App\Platform\Contracts\Services\CapabilityInstallerContract;
use App\Platform\Contracts\Services\ComponenteInstallerContract;
use App\Platform\Contracts\Services\ExtensionInstallerContract;
use App\Platform\Contracts\Services\FieldVisibilityInstallerContract;
use App\Platform\PlatformRegistry;
use App\Platform\Services\CapabilityInstaller;
use App\Platform\Services\ComponenteInstaller;
use App\Platform\Services\ExtensionInstaller;
use App\Platform\Services\FieldVisibilityInstaller;
use Illuminate\Support\ServiceProvider;

class PlatformServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PlatformRegistry::class, function () {
            $registry = new PlatformRegistry();

            foreach (config('platform.modules', []) as $moduleClass) {
                $registry->register(new $moduleClass());
            }

            return $registry;
        });

        $this->app->bind(FieldVisibilityInstallerContract::class, FieldVisibilityInstaller::class);
        $this->app->bind(CapabilityInstallerContract::class, CapabilityInstaller::class);
        $this->app->bind(ComponenteInstallerContract::class, ComponenteInstaller::class);
        $this->app->bind(ExtensionInstallerContract::class, ExtensionInstaller::class);

        $this->registrarConfiguracionDePlataforma();
    }

    /**
     * Los subdirectorios bajo config/ NO se auto-cargan (Laravel solo
     * escanea config/*.php sin recursión). Se institucionaliza acá: todo
     * archivo bajo config/platform/ queda registrado automáticamente vía
     * mergeConfigFrom, sin que haya que acordarse de agregar una línea por
     * archivo nuevo.
     */
    private function registrarConfiguracionDePlataforma(): void
    {
        foreach (glob(config_path('platform/*.php')) as $archivo) {
            $this->mergeConfigFrom($archivo, basename($archivo, '.php'));
        }
    }
}
