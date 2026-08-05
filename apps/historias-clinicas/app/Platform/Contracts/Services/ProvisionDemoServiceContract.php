<?php

namespace App\Platform\Contracts\Services;

use App\Models\DemoInstance;

/**
 * Etapa 6.4 (ver docs/ARQUITECTURA_MODULAR.md): el caso de uso completo
 * "provisionar una demo" — antes vivía solo dentro del comando artisan
 * demo:crear. Se extrae a un servicio recién ahora, porque apareció la
 * segunda repetición real (CLI + HTTP) que justifica la extracción, no
 * antes.
 */
interface ProvisionDemoServiceContract
{
    /**
     * Crea, provisiona y activa una DemoInstance. Nunca lanza una
     * excepción por una falla de provisioning — deja el registro en
     * 'error' (con error_message) y lo devuelve así, para que el
     * llamador (comando o controller) decida cómo mostrarlo. Solo lanza
     * InvalidArgumentException si el perfil no existe, porque eso es un
     * error de uso del propio llamador, no una falla de provisioning.
     */
    public function provisionar(
        string $perfilKey,
        ?string $solicitanteNombre = null,
        ?string $solicitanteEmail = null,
        int $horasVigencia = 24
    ): DemoInstance;
}
