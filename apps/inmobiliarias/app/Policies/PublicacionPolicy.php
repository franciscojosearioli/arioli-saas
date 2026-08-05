<?php

namespace App\Policies;

use App\Models\Publicacion;
use App\Models\User;

// Mismo criterio que PropiedadPolicy (§07): publicar es parte de
// gestionar el catálogo, no un permiso aparte — el portafolio es del
// equipo, cualquier agente puede publicar cualquier propiedad.
class PublicacionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Publicacion $publicacion): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agente');
    }

    public function update(User $user, Publicacion $publicacion): bool
    {
        return $user->hasRole('agente');
    }

    public function delete(User $user, Publicacion $publicacion): bool
    {
        return false;
    }
}
