<?php

namespace App\Policies;

use App\Models\Operacion;
use App\Models\User;

// §07: "Agente/Vendedor — sus propios leads y operaciones (salvo que sea
// admin)". Mismo criterio de dueño que LeadPolicy. Administrativo y
// solo-lectura ven todas las operaciones (necesitan contexto para
// finanzas/reportes) pero no las gestionan.
class OperacionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Operacion $operacion): bool
    {
        return $user->hasRole('administrativo')
            || $user->hasRole('solo-lectura')
            || $this->esDueno($user, $operacion);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agente');
    }

    public function update(User $user, Operacion $operacion): bool
    {
        return $this->esDueno($user, $operacion);
    }

    public function delete(User $user, Operacion $operacion): bool
    {
        return false;
    }

    private function esDueno(User $user, Operacion $operacion): bool
    {
        return $user->hasRole('agente') && $operacion->agente_id === $user->id;
    }
}
