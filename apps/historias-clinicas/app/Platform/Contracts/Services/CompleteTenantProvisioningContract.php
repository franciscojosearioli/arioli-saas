<?php

namespace App\Platform\Contracts\Services;

use App\Models\User;

/**
 * Etapa 6.5 (ver docs/ARQUITECTURA_MODULAR.md): "reclamar credenciales"
 * no es simplemente cambiar una contraseña — es completar el
 * onboarding (validar el link, resolver el tenant, actualizar la
 * contraseña, invalidar el link). Encapsulado acá, no en el controller.
 */
interface CompleteTenantProvisioningContract
{
    /**
     * Devuelve el usuario administrador (para loguearlo) si el reclamo
     * fue exitoso, o null si el link ya había sido usado antes (estado
     * normal, no un error — el controller decide qué mostrar). Lanza
     * una excepción solo si el tenant no existe, que es un estado
     * realmente inesperado.
     */
    public function completar(string $slug, string $password): ?User;
}
