<?php

namespace App\Jobs;

use App\Enums\BillingCycle;
use App\Enums\ClientServiceStatus;
use App\Models\ClientService;
use App\Services\Clients\MaintenanceChargeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Corre el día 1 de cada mes: genera el cobro de cada ClientService mensual
 * activo (si todavía no se generó uno este mes para ese servicio) y le manda
 * el link de pago al cliente por email — vía MaintenanceChargeService.
 *
 * Los servicios con auto_maintenance_hestia=true quedan afuera de este loop
 * a propósito: esos cobran recién cuando termina bien el backup automático
 * (ver SendMaintenanceConfirmationRequests → confirmación → RunMaintenanceBackup),
 * no de una el día 1 como el resto.
 */
class GenerateMonthlyServiceCharges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(MaintenanceChargeService $charges): void
    {
        $services = ClientService::with('client.contacts')
            ->where('billing_cycle', BillingCycle::Mensual)
            ->where('status', ClientServiceStatus::Active)
            ->where('auto_maintenance_hestia', false)
            ->get();

        foreach ($services as $service) {
            $charges->chargeFor($service);
        }
    }
}
