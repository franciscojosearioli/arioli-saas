<?php

namespace App\Services\Contracts\Placeholders;

use App\Contracts\PlaceholderProviderInterface;
use App\Models\Order;

class OrderPlaceholderProvider implements PlaceholderProviderInterface
{
    public function supports(array $context): bool
    {
        return ($context['contractable'] ?? null) instanceof Order;
    }

    public function resolve(array $context): array
    {
        /** @var Order $order */
        $order = $context['contractable'];
        $order->loadMissing('plan.product');

        return [
            'sistema_nombre' => $order->plan?->product?->name ?? '',
            'plan_nombre'    => $order->plan?->period_label ?? '',
            'monto'          => number_format((float) $order->amount, 2, ',', '.'),
        ];
    }
}
