<?php

namespace App\Services\Contracts\Placeholders;

use App\Contracts\PlaceholderProviderInterface;

class DatePlaceholderProvider implements PlaceholderProviderInterface
{
    public function supports(array $context): bool
    {
        return true;
    }

    public function resolve(array $context): array
    {
        return [
            'fecha_hoy' => now()->format('d/m/Y'),
        ];
    }
}
