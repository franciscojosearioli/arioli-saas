<?php

namespace App\Policies;

use App\Models\Documento;
use App\Models\User;

// Adjunto polimórfico transversal (§04) — cualquier rol con acceso al
// panel puede ver y subir documentación de su trabajo diario; borrar
// queda para admin (con historial detrás, igual que el resto del catálogo).
class DocumentoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Documento $documento): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return ! $user->hasRole('solo-lectura');
    }

    public function update(User $user, Documento $documento): bool
    {
        return $documento->subido_por_id === $user->id;
    }

    public function delete(User $user, Documento $documento): bool
    {
        return false;
    }
}
