<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

// §07: "Agente/Vendedor — sus propios leads y operaciones (salvo que sea
// admin)". A diferencia de Propiedad/Cliente, acá sí hay dueño: un agente
// solo ve/edita los leads que tiene asignados. El índice además filtra por
// esto en el controller (la Policy autoriza un registro puntual, no lista).
class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Lead $lead): bool
    {
        return $this->esDueno($user, $lead);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agente');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $this->esDueno($user, $lead);
    }

    public function delete(User $user, Lead $lead): bool
    {
        return false;
    }

    private function esDueno(User $user, Lead $lead): bool
    {
        return $user->hasRole('agente') && $lead->agente_id === $user->id;
    }
}
