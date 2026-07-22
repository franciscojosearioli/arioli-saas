<?php

namespace App\Platform\DTO;

final class FieldVisibilitySeed
{
    public function __construct(
        public readonly string $entidad,
        public readonly string $campo,
        public readonly string $tipo,       // 'campo' | 'seccion' | 'tab'
        public readonly bool $visible,
        public readonly ?bool $requerido = null,
    ) {
    }
}
