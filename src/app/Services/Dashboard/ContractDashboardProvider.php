<?php

namespace App\Services\Dashboard;

use App\Contracts\DashboardProviderInterface;
use App\Models\Contract;

class ContractDashboardProvider implements DashboardProviderInterface
{
    public function widgets(): array
    {
        return [
            [
                'group' => 'Contratos y propuestas',
                'label' => 'Contratos con firma pendiente',
                'value' => Contract::where('status', 'pending_signature')->count(),
                'color' => 'amber',
            ],
        ];
    }
}
