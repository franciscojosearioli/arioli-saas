<?php

namespace App\Services\Clients;

use App\Enums\ChargeStatus;
use App\Enums\ClientEventType;
use App\Mail\ChargeMail;
use App\Models\Charge;
use App\Models\ClientEvent;
use App\Models\ClientService;
use App\Services\Payments\ChargePaymentLinkService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Genera el cobro + link de pago + mail de un ClientService mensual — antes
 * vivía duplicado dentro de GenerateMonthlyServiceCharges::handle(). Ahora
 * también lo usa RunMaintenanceBackup (el mantenimiento con backup automático
 * cobra recién después de que el backup termina bien, no el día 1 como el
 * resto de los servicios mensuales).
 */
class MaintenanceChargeService
{
    public function __construct(private ChargePaymentLinkService $paymentLinks) {}

    public function chargeFor(ClientService $service): ?Charge
    {
        $yaCobradoEsteMes = Charge::where('chargeable_type', ClientService::class)
            ->where('chargeable_id', $service->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->exists();

        if ($yaCobradoEsteMes) {
            return null;
        }

        try {
            $charge = Charge::create([
                'client_id'       => $service->client_id,
                'chargeable_type' => ClientService::class,
                'chargeable_id'   => $service->id,
                'concept'         => "Mantenimiento mensual — {$service->service_type->label()} ({$this->monthLabel()})",
                'amount'          => $service->amount,
                'currency'        => 'ARS',
                'status'          => ChargeStatus::Pending,
                'due_date'        => now()->addDays(10),
            ]);

            $this->paymentLinks->generate($charge);

            $contact = $service->client->contacts->firstWhere('is_primary', true)
                ?? $service->client->contacts->first();

            if ($contact?->email) {
                Mail::to($contact->email)->send(new ChargeMail($charge, $contact));
            }

            ClientEvent::log(
                $service->client,
                "Se generó el cobro mensual \"{$charge->concept}\"",
                ClientEventType::Charge,
                $charge,
            );

            return $charge;
        } catch (\Throwable $e) {
            Log::error('MaintenanceChargeService: error al generar el cobro', [
                'service_id' => $service->id,
                'message'    => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function monthLabel(): string
    {
        return ucfirst(now()->translatedFormat('F Y'));
    }
}
