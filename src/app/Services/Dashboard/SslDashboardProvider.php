<?php

namespace App\Services\Dashboard;

use App\Contracts\DashboardProviderInterface;
use App\Models\SslCertificate;

class SslDashboardProvider implements DashboardProviderInterface
{
    public function widgets(): array
    {
        return [
            [
                'group' => 'Dominios y hosting',
                'label' => 'SSL por vencer (30 días)',
                'value' => SslCertificate::whereBetween('expires_at', [now(), now()->addDays(30)])->count(),
                'color' => 'amber',
            ],
            [
                'group' => 'Dominios y hosting',
                'label' => 'SSL expirados',
                'value' => SslCertificate::where('expires_at', '<', now())->count(),
                'color' => 'red',
            ],
        ];
    }
}
