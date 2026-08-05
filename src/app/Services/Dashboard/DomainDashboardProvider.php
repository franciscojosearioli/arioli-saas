<?php

namespace App\Services\Dashboard;

use App\Contracts\DashboardProviderInterface;
use App\Models\ClientDomain;

class DomainDashboardProvider implements DashboardProviderInterface
{
    public function widgets(): array
    {
        return [
            [
                'group' => 'Dominios y hosting',
                'label' => 'Dominios por vencer (30 días)',
                'value' => ClientDomain::whereBetween('expires_at', [now(), now()->addDays(30)])->count(),
                'color' => 'amber',
            ],
            [
                'group' => 'Dominios y hosting',
                'label' => 'Dominios expirados',
                'value' => ClientDomain::where('expires_at', '<', now())->count(),
                'color' => 'red',
            ],
        ];
    }
}
