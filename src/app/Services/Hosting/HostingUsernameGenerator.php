<?php

namespace App\Services\Hosting;

use App\Models\Hosting;

/**
 * Único lugar que decide el username técnico de una cuenta de hosting.
 * Nunca se deriva del nombre del cliente ni del dominio (HestiaCP tiene
 * límites de longitud/caracteres, y esos datos pueden cambiar) — se basa
 * en el id de `Hosting`, que es estable y único por diseño.
 */
class HostingUsernameGenerator
{
    public function forHosting(Hosting $hosting): string
    {
        return 'ah' . str_pad((string) $hosting->id, 5, '0', STR_PAD_LEFT);
    }
}
