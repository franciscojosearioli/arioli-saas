<?php

namespace App\Jobs;

use App\Enums\BillingCycle;
use App\Enums\ClientEventType;
use App\Enums\ClientServiceStatus;
use App\Mail\MaintenanceConfirmationRequestMail;
use App\Models\ClientEvent;
use App\Models\ClientService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * Corre el día 1 de cada mes, antes que GenerateMonthlyServiceCharges — a
 * cada ClientService de Mantenimiento con auto_maintenance_hestia activo le
 * manda el mail preguntando si quiere el mantenimiento de este mes, con un
 * link firmado para confirmar. El cobro real se dispara recién cuando el
 * backup (RunMaintenanceBackup) termina bien, no acá.
 */
class SendMaintenanceConfirmationRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $services = ClientService::with('client.contacts', 'project.hosting.account')
            ->where('billing_cycle', BillingCycle::Mensual)
            ->where('status', ClientServiceStatus::Active)
            ->where('auto_maintenance_hestia', true)
            ->get();

        foreach ($services as $service) {
            if ($service->maintenanceRequestedThisMonth()) {
                continue;
            }

            $contact = $service->client->contacts->firstWhere('is_primary', true)
                ?? $service->client->contacts->first();

            if (! $contact?->email) {
                Log::warning('SendMaintenanceConfirmationRequests: sin contacto con email', ['service_id' => $service->id]);

                continue;
            }

            $confirmUrl = URL::temporarySignedRoute(
                'maintenance.confirm',
                now()->addDays(20),
                ['service' => $service->id],
            );

            try {
                Mail::to($contact->email)->send(new MaintenanceConfirmationRequestMail($service, $contact, $confirmUrl));

                $service->update(['maintenance_requested_at' => now()]);

                ClientEvent::log(
                    $service->client,
                    'Se envió la solicitud de confirmación del mantenimiento mensual',
                    ClientEventType::Service,
                    $service,
                );
            } catch (\Throwable $e) {
                Log::error('SendMaintenanceConfirmationRequests: error al enviar', [
                    'service_id' => $service->id,
                    'message'    => $e->getMessage(),
                ]);
            }
        }
    }
}
