<?php

namespace App\Policies;

use App\Models\Configuracion;
use App\Models\User;

// Parámetros del tenant (§03) — solo Owner/Admin, ni siquiera
// administrativo. El % de comisión afecta directamente lo que se le paga
// a cada agente.
class ConfiguracionPolicy
{
    public function view(User $user, Configuracion $configuracion): bool
    {
        return false;
    }

    public function update(User $user, Configuracion $configuracion): bool
    {
        return false;
    }
}
