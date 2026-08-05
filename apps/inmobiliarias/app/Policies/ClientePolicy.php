<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

// Mismo criterio que Propiedad: la cartera de clientes/propietarios es
// del equipo, no de un agente individual.
class ClientePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Cliente $cliente): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agente');
    }

    public function update(User $user, Cliente $cliente): bool
    {
        return $user->hasRole('agente');
    }

    public function delete(User $user, Cliente $cliente): bool
    {
        return false;
    }
}
