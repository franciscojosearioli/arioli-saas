<?php

namespace App\Contracts;

interface PlaceholderProviderInterface
{
    /**
     * @param array $context contexto de renderizado (tenant_id, customer_*, contractable, etc.)
     */
    public function supports(array $context): bool;

    /**
     * @param array $context
     * @return array<string, string> placeholder => valor (sin llaves, ej. 'cliente_nombre' => 'Juan Pérez')
     */
    public function resolve(array $context): array;
}
