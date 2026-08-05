<?php

namespace App\Policies;

use App\Models\Propiedad;
use App\Models\User;

// §07: el portafolio de propiedades es del equipo, no de un agente
// individual — todos los roles ven todo. Cargar/editar es trabajo de
// agente o admin; borrar (con historial detrás) queda solo para admin.
class PropiedadPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Propiedad $propiedad): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agente');
    }

    public function update(User $user, Propiedad $propiedad): bool
    {
        return $user->hasRole('agente');
    }

    public function delete(User $user, Propiedad $propiedad): bool
    {
        return false;
    }
}
