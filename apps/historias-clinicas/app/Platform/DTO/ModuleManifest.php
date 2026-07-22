<?php

namespace App\Platform\DTO;

final class ModuleManifest
{
    public function __construct(
        public readonly string $key,
        public readonly string $nombre,
        public readonly string $descripcion = '',
        public readonly string $icon = '',
        public readonly string $color = '',
        public readonly string $category = '',
        public readonly string $version = '1.0.0',
        public readonly string $minimumPlatformVersion = '1.0.0',
        public readonly string $author = 'Arioli',
        public readonly int $priority = 100,
        public readonly bool $beta = false,
        public readonly bool $hidden = false,
    ) {
    }
}
