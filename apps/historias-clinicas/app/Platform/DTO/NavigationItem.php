<?php

namespace App\Platform\DTO;

final class NavigationItem
{
    /**
     * @param NavigationItem[] $children
     */
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly ?string $route = null,
        public readonly ?string $icon = null,
        public readonly ?string $capabilityRequerida = null,
        public readonly ?string $permisoRequerido = null,
        public readonly int $orden = 100,
        public readonly ?string $seccion = null,
        public readonly ?\Closure $badge = null,
        public readonly array $children = [],
    ) {
    }
}
