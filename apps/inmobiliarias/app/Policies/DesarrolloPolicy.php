<?php

namespace App\Policies;

use App\Models\Desarrollo;
use App\Models\User;

// Mismo criterio que Constructora: estructura del catálogo, solo admin
// la gestiona (Gate::before ya deja pasar a admin).
class DesarrolloPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Desarrollo $desarrollo): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Desarrollo $desarrollo): bool
    {
        return false;
    }

    public function delete(User $user, Desarrollo $desarrollo): bool
    {
        return false;
    }
}
