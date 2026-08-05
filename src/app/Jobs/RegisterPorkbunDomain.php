<?php

namespace App\Jobs;

use App\Enums\ClientEventType;
use App\Enums\DomainStatus;
use App\Models\ClientDomain;
use App\Models\ClientEvent;
use App\Services\Dns\DnsProviderManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Se dispara cuando el Charge de un registro de dominio se marca pagado
 * (ChargeWebhookController → ChargeFulfillmentService). Recién acá se llama
 * a Porkbun de verdad — nunca antes de que el cliente haya pagado. Idempotente
 * (si el dominio ya no está Pendiente, no hace nada) por si el webhook llega
 * duplicado.
 */
class RegisterPorkbunDomain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public readonly ClientDomain $domain)
    {
    }

    public function handle(): void
    {
        $domain = $this->domain;

        if ($domain->status !== DomainStatus::Pendiente) {
            Log::info('RegisterPorkbunDomain: el dominio ya no está pendiente, no se reintenta', ['domain_id' => $domain->id]);

            return;
        }

        $result = DnsProviderManager::driver()->register(['domain' => $domain->domain_name]);

        if ($result['status'] !== 'registered') {
            ClientEvent::log(
                $domain->client,
                "El cliente pagó el registro de {$domain->domain_name} pero Porkbun lo rechazó — requiere resolución manual (reembolso o dominio alternativo)",
                ClientEventType::Domain,
                $domain
            );
            Log::error('RegisterPorkbunDomain: Porkbun rechazó el registro tras el pago', [
                'domain_id' => $domain->id,
                'message'   => $result['message'],
            ]);

            return;
        }

        $domain->update([
            'status'        => DomainStatus::Activo,
            'registered_at' => now(),
            'expires_at'    => now()->addYear(),
        ]);

        ClientEvent::log($domain->client, "Dominio {$domain->domain_name} registrado en Porkbun tras el pago", ClientEventType::Domain, $domain);
    }
}
