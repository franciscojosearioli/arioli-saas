<?php

namespace App\Policies;

use App\Models\Pago;
use App\Models\User;

// §07: registrar cobros es trabajo administrativo. Un pago ya registrado
// no se edita libremente (integridad de caja) — ni siquiera administrativo
// puede tocarlo una vez cargado, solo admin ante una corrección real.
class PagoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Pago $pago): bool
    {
        return $user->hasRole('administrativo')
            || $user->hasRole('solo-lectura')
            || ($user->hasRole('agente') && $pago->cuota->operacion->agente_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrativo');
    }

    public function update(User $user, Pago $pago): bool
    {
        return false;
    }

    public function delete(User $user, Pago $pago): bool
    {
        return false;
    }
}
