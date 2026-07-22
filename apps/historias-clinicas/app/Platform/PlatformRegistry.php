<?php

namespace App\Platform;

use App\Models\User;
use App\Platform\Contracts\ModuleDefinition;
use App\Platform\DTO\NavigationContribution;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Único punto que el resto del sistema consulta para saber qué está
 * habilitado y qué aporta cada módulo — nada pregunta directamente a un
 * módulo. Ver docs/ARQUITECTURA_MODULAR.md secciones 2 y 5.
 *
 * Fase 0: solo implementa lo que efectivamente se usa hoy (registro de
 * módulos, resolución de capabilities, navegación). El resto del método
 * surface documentado (publishers, widgets, eventCatalog, extensiones) se
 * agrega cuando exista el primer módulo que realmente los necesite.
 */
class PlatformRegistry
{
    /** @var array<string, ModuleDefinition> */
    private array $modules = [];

    /** @var array<string, bool>|null */
    private ?array $capabilityCache = null;

    public function register(ModuleDefinition $module): void
    {
        $key = $module->manifest()->key;

        foreach ($module->conflicts() as $conflictKey) {
            if (isset($this->modules[$conflictKey])) {
                throw new RuntimeException(
                    "El módulo '{$key}' declara conflicto con '{$conflictKey}', que ya está registrado."
                );
            }
        }

        $this->modules[$key] = $module;
    }

    /** @return ModuleDefinition[] */
    public function all(): array
    {
        return $this->modules;
    }

    public function get(string $key): ?ModuleDefinition
    {
        return $this->modules[$key] ?? null;
    }

    public function isCapabilityEnabled(string $capabilityKey): bool
    {
        $states = $this->loadCapabilityStates();

        // resolución jerárquica: self y todos los ancestros deben estar enabled
        $partes = explode('.', $capabilityKey);
        $prefijo = '';

        foreach ($partes as $parte) {
            $prefijo = $prefijo === '' ? $parte : "{$prefijo}.{$parte}";

            if (! ($states[$prefijo] ?? false)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return \App\Platform\DTO\NavigationItem[]
     */
    public function navigationParaUsuario(User $user): array
    {
        $items = [];

        foreach ($this->modules as $module) {
            foreach ($module->contributions() as $contribution) {
                if (! $contribution instanceof NavigationContribution) {
                    continue;
                }

                $item = $contribution->item;

                if ($item->capabilityRequerida && ! $this->isCapabilityEnabled($item->capabilityRequerida)) {
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

    /** @return array<string, bool> capability_key => enabled */
    private function loadCapabilityStates(): array
    {
        if ($this->capabilityCache !== null) {
            return $this->capabilityCache;
        }

        $this->capabilityCache = DB::table('capability_states')
            ->pluck('enabled', 'capability_key')
            ->map(fn ($enabled) => (bool) $enabled)
            ->all();

        return $this->capabilityCache;
    }
}
