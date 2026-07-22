<?php

namespace App\Platform\DTO;

use App\Platform\Contracts\ComponenteExtension;

/**
 * Puramente descriptivo — no sabe cómo se instala, no toca la base, no
 * ejecuta SQL. Toda la inteligencia vive en ComponenteInstaller y sus
 * sub-instaladores. Ver docs/ARQUITECTURA_MODULAR.md, Etapa 3.
 */
final class Componente
{
    /**
     * @param string[] $capabilities
     * @param string[] $fieldVisibilitySeed claves de preset en config/platform/field_visibility_presets.php
     * @param array $tiposDocumentoSeed
     * @param array $configuracionInicial
     */
    public function __construct(
        public readonly string $key,
        public readonly string $nombre,
        public readonly string $descripcion = '',
        public readonly array $capabilities = [],
        public readonly array $fieldVisibilitySeed = [],
        public readonly array $tiposDocumentoSeed = [],
        public readonly array $configuracionInicial = [],
        public readonly ?ComponenteExtension $extension = null,
    ) {
    }
}
