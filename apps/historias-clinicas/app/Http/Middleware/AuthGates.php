<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Platform\PlatformRegistry;
use Closure;
use Illuminate\Support\Facades\Gate;

class AuthGates
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (! $user) {
            return $next($request);
        }

        $roles              = Role::with('permissions')->get();
        $permissionsArray   = [];
        $capabilityPorTitulo = [];

        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissionsArray[$permission->title][] = $role->id;
                $capabilityPorTitulo[$permission->title] = $permission->capability_key;
            }
        }

        $registry = app(PlatformRegistry::class);

        foreach ($permissionsArray as $title => $roles) {
            $capabilityKey = $capabilityPorTitulo[$title];
            $habilitado = $capabilityKey === null || $registry->isCapabilityEnabled($capabilityKey);

            Gate::define($title, function ($user) use ($roles, $habilitado) {
                return $habilitado && count(array_intersect($user->roles->pluck('id')->toArray(), $roles)) > 0;
            });
        }

        return $next($request);
    }
}