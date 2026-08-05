<?php

namespace App\Services\Dashboard;

use App\Contracts\DashboardProviderInterface;
use App\Models\Quote;

class QuoteDashboardProvider implements DashboardProviderInterface
{
    public function widgets(): array
    {
        return [
            [
                'group' => 'Contratos y propuestas',
                'label' => 'Propuestas esperando respuesta',
                'value' => Quote::where('status', 'enviada')->count(),
                'color' => 'amber',
            ],
        ];
    }
}
