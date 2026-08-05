<?php

namespace App\Services\Publicaciones;

use InvalidArgumentException;

/**
 * §09: "agregar un canal nuevo es escribir una clase que lo implementa y
 * registrarla — nada en Propiedades, Operaciones o el resto del dominio
 * cambia." Este registry es el único lugar que conoce la lista completa
 * de canales soportados.
 */
class ChannelAdapterRegistry
{
    /** @param array<string, ChannelAdapter> $adapters */
    public function __construct(private readonly array $adapters) {}

    public function para(string $canal): ChannelAdapter
    {
        return $this->adapters[$canal]
            ?? throw new InvalidArgumentException("No hay ChannelAdapter registrado para el canal [{$canal}].");
    }
}
