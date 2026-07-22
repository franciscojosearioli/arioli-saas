<?php

namespace App\Platform\Services;

use App\Models\User;
use App\Platform\Contracts\Services\NavigationInstallerContract;
use App\Platform\DTO\Componente;
use App\Platform\PlatformRegistry;
use Illuminate\Support\Facades\DB;

/**
 * A diferencia de CapabilityInstaller/FieldVisibilityInstaller, acá no hay
 * nada que persistir — la navegación de un Componente se resuelve en el
 * momento de pedirla (mismo criterio que ya usa PlatformRegistry para la
 * navegación de los ModuleDefinition, que tampoco se guarda en ninguna
 * tabla). "Installer" en el nombre es por consistencia con el resto del
 * pipeline, no porque escriba estado — no se llama desde
 * ComponenteInstaller::instalar() por esta misma razón: no hay nada que
 * instalar, solo resolver.
 *
 * Primer caso real que obligó a esto: Odontología necesitaba aparecer en
 * el menú, y ningún Componente podía aportar navegación todavía (solo
 * ModuleDefinition podía). Ver docs/ARQUITECTURA_MODULAR.md, Etapa 4.1.
 */
class NavigationInstaller implements NavigationInstallerContract
{
    public function __construct(private readonly PlatformRegistry $registry)
    {
    }

    public function resolverPara(User $user): array
    {
        $catalogo = config('componentes', []);

        $instalados = DB::table('componentes_instalados')
            ->pluck('componente_key')
            ->map(fn (string $key) => $catalogo[$key] ?? null)
            ->filter();

        $items = [];

        /** @var Componente $componente */
        foreach ($instalados as $componente) {
            foreach ($componente->navegacionSeed as $item) {
                if ($item->capabilityRequerida && ! $this->registry->isCapabilityEnabled($item->capabilityRequerida)) {
                    continue;
                }

                if ($item->permisoRequerido && $user->cannot($item->permisoRequerido)) {
                    continue;
                }

                $items[] = $item;
            }
        }

        usort($items, fn ($a, $b) => $a->orden <=> $b->orden);

        return $items;
    }
}
