<?php

namespace App\Services\Dashboard;

use App\Contracts\DashboardProviderInterface;
use App\Enums\CommercialStatus;
use App\Models\Client;

class ClientDashboardProvider implements DashboardProviderInterface
{
    public function widgets(): array
    {
        return [
            [
                'group' => 'Clientes',
                'label' => 'Clientes activos',
                'value' => Client::where('commercial_status', CommercialStatus::Activo)->count(),
                'color' => 'green',
            ],
            [
                'group' => 'Clientes',
                'label' => 'Prospectos',
                'value' => Client::where('commercial_status', CommercialStatus::Prospecto)->count(),
                'color' => 'gray',
            ],
            [
                'group' => 'Clientes',
                'label' => 'Presupuestos enviados',
                'value' => Client::where('commercial_status', CommercialStatus::PresupuestoEnviado)->count(),
                'color' => 'amber',
            ],
        ];
    }
}
