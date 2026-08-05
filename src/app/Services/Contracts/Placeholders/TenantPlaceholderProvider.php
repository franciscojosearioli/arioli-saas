<?php

namespace App\Services\Contracts\Placeholders;

use App\Contracts\PlaceholderProviderInterface;

class TenantPlaceholderProvider implements PlaceholderProviderInterface
{
    public function supports(array $context): bool
    {
        return isset($context['tenant_id']);
    }

    public function resolve(array $context): array
    {
        return [
            'tenant_id'      => $context['tenant_id'] ?? '',
            'cliente_nombre' => $context['customer_name'] ?? '',
            'cliente_email'  => $context['customer_email'] ?? '',
            'cliente_cuit'   => $context['customer_cuit'] ?? '',
        ];
    }
}
