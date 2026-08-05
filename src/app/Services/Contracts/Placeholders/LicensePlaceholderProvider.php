<?php

namespace App\Services\Contracts\Placeholders;

use App\Contracts\PlaceholderProviderInterface;
use App\Models\License;

class LicensePlaceholderProvider implements PlaceholderProviderInterface
{
    public function supports(array $context): bool
    {
        return ($context['contractable'] ?? null) instanceof License;
    }

    public function resolve(array $context): array
    {
        /** @var License $license */
        $license = $context['contractable'];
        $license->loadMissing('plan.product');

        return [
            'sistema_nombre' => $license->plan?->product?->name ?? '',
            'plan_nombre'    => $license->plan?->period_label ?? '',
            'vencimiento'    => $license->expires_at?->format('d/m/Y') ?? '',
        ];
    }
}
