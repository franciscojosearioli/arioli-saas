<?php

namespace App\Policies;

use App\Models\Cuota;
use App\Models\User;

// §07: "Administrativo — finanzas: cuotas, pagos, caja". El agente ve las
// cuotas de sus propias operaciones (necesita saber si el cliente está al
// día) pero no las gestiona — eso es trabajo administrativo, no comercial.
class CuotaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Cuota $cuota): bool
    {
        return $user->hasRole('administrativo')
            || $user->hasRole('solo-lectura')
            || ($user->hasRole('agente') && $cuota->operacion->agente_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrativo');
    }

    public function update(User $user, Cuota $cuota): bool
    {
        return $user->hasRole('administrativo');
    }

    public function delete(User $user, Cuota $cuota): bool
    {
        return false;
    }
}
