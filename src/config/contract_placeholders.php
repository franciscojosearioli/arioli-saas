<?php

// Proveedores de placeholders para plantillas de contratos. Agregar uno
// nuevo (ej. datos de un futuro módulo de Servicios) es una clase que
// implemente App\Contracts\PlaceholderProviderInterface + una línea acá —
// PlaceholderResolver y ContractTemplateRenderer no se tocan.

return [
    \App\Services\Contracts\Placeholders\EmpresaPlaceholderProvider::class,
    \App\Services\Contracts\Placeholders\TenantPlaceholderProvider::class,
    \App\Services\Contracts\Placeholders\OrderPlaceholderProvider::class,
    \App\Services\Contracts\Placeholders\LicensePlaceholderProvider::class,
    \App\Services\Contracts\Placeholders\DatePlaceholderProvider::class,
];
