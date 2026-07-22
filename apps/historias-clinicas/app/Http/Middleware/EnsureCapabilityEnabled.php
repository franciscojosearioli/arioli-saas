<?php

namespace App\Http\Middleware;

use App\Platform\PlatformRegistry;
use Closure;

/**
 * Gatea una ruta por capability, independiente de permisos. Necesario para
 * módulos como Consentimientos, que no tienen permiso propio (usan
 * paciente_edit/paciente_show prestados) — ahí el AND capability+permiso de
 * AuthGates no alcanza, porque no hay un permiso dedicado del que colgar el
 * capability_key. Ver docs/ARQUITECTURA_MODULAR.md, módulo Consentimientos.
 */
class EnsureCapabilityEnabled
{
    public function handle($request, Closure $next, string $capabilityKey)
    {
        if (! app(PlatformRegistry::class)->isCapabilityEnabled($capabilityKey)) {
            abort(404);
        }

        return $next($request);
    }
}
