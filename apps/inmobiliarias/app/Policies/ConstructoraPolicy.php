<?php

namespace App\Policies;

use App\Models\Constructora;
use App\Models\User;

// Catálogo estructural: cualquier rol autenticado lo ve, solo admin lo
// gestiona (Gate::before ya deja pasar a admin — acá solo negamos al resto).
class ConstructoraPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Constructora $constructora): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Constructora $constructora): bool
    {
        return false;
    }

    public function delete(User $user, Constructora $constructora): bool
    {
        return false;
    }
}
