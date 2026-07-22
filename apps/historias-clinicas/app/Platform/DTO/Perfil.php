<?php

namespace App\Platform\DTO;

/**
 * Puramente descriptivo, igual que Componente — no sabe cómo se aplica.
 * "Aplicar" un Perfil es literalmente ComponenteInstaller::instalar($perfil->componentes)
 * — no hace falta una clase instaladora nueva (ver docs/ARQUITECTURA_MODULAR.md, Etapa 5.1).
 *
 * Aditivo, igual que Componente/ComponenteInstaller: perfecto para un tenant
 * nuevo (aditivo = exclusivo cuando no hay nada previo instalado). NO limpia
 * un tenant que ya tiene otros Componentes activos — para eso hace falta el
 * paso explícito de desactivación (capability_states, Etapa 4.5), no algo
 * que aplicar un Perfil resuelva solo.
 */
final class Perfil
{
    /** @param string[] $componentes claves de Componente en config/platform/componentes.php */
    public function __construct(
        public readonly string $key,
        public readonly string $nombre,
        public readonly string $descripcion = '',
        public readonly array $componentes = [],
    ) {
    }
}
