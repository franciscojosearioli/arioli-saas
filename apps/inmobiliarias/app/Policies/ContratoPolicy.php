<?php

namespace App\Policies;

use App\Models\Contrato;
use App\Models\User;

// Un contrato hereda el criterio de dueño de su Operación (§04: "1
// contrato principal por Operación") — no tiene reglas de acceso propias.
class ContratoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contrato $contrato): bool
    {
        return $user->hasRole('administrativo')
            || $user->hasRole('solo-lectura')
            || $this->esDueno($user, $contrato);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agente');
    }

    public function update(User $user, Contrato $contrato): bool
    {
        return $this->esDueno($user, $contrato);
    }

    public function delete(User $user, Contrato $contrato): bool
    {
        return false;
    }

    private function esDueno(User $user, Contrato $contrato): bool
    {
        return $user->hasRole('agente') && $contrato->operacion->agente_id === $user->id;
    }
}
