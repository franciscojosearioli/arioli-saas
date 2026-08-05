<?php

namespace App\Services\Dashboard;

use App\Contracts\DashboardProviderInterface;
use App\Models\Hosting;

class HostingDashboardProvider implements DashboardProviderInterface
{
    public function widgets(): array
    {
        return [
            [
                'group' => 'Dominios y hosting',
                'label' => 'Hostings por vencer (30 días)',
                'value' => Hosting::whereBetween('expires_at', [now(), now()->addDays(30)])->count(),
                'color' => 'amber',
            ],
            [
                'group' => 'Dominios y hosting',
                'label' => 'Hostings expirados',
                'value' => Hosting::where('expires_at', '<', now())->count(),
                'color' => 'red',
            ],
        ];
    }
}
