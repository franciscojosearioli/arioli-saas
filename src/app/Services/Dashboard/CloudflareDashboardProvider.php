<?php

namespace App\Services\Dashboard;

use App\Contracts\DashboardProviderInterface;
use App\Enums\CloudflareServiceStatus;
use App\Models\CloudflareService;

class CloudflareDashboardProvider implements DashboardProviderInterface
{
    public function widgets(): array
    {
        return [
            [
                'group' => 'Dominios y hosting',
                'label' => 'Cloudflare activos',
                'value' => CloudflareService::where('status', CloudflareServiceStatus::Activo)->count(),
                'color' => 'green',
            ],
            [
                'group' => 'Dominios y hosting',
                'label' => 'Cloudflare por vencer (30 días)',
                'value' => CloudflareService::whereBetween('expires_at', [now(), now()->addDays(30)])->count(),
                'color' => 'amber',
            ],
        ];
    }
}
