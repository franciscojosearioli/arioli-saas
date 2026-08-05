<?php

namespace App\Policies;

use App\Models\Comision;
use App\Models\User;

// §04: la Comisión se genera sola al cerrar una Operación (Operacion::
// cerrar()) — no hay creación manual vía API, solo consulta y liquidación.
class ComisionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Comision $comision): bool
    {
        return $user->hasRole('administrativo')
            || $user->hasRole('solo-lectura')
            || ($user->hasRole('agente') && $comision->agente_id === $user->id);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Comision $comision): bool
    {
        return $user->hasRole('administrativo');
    }

    public function delete(User $user, Comision $comision): bool
    {
        return false;
    }
}
