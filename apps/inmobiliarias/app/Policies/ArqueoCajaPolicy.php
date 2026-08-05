<?php

namespace App\Policies;

use App\Models\ArqueoCaja;
use App\Models\User;

// §07/§17 Rev. 1.2: "cerrar caja del día" es administrativo. Un agente no
// tiene ninguna razón para ver la caja — no forma parte de "sus leads y
// operaciones" (§07).
class ArqueoCajaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrativo') || $user->hasRole('solo-lectura');
    }

    public function view(User $user, ArqueoCaja $arqueoCaja): bool
    {
        return $user->hasRole('administrativo') || $user->hasRole('solo-lectura');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrativo');
    }

    public function update(User $user, ArqueoCaja $arqueoCaja): bool
    {
        return false;
    }

    public function delete(User $user, ArqueoCaja $arqueoCaja): bool
    {
        return false;
    }
}
