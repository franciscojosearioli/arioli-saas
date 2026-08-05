<?php

namespace App\Jobs;

use App\Enums\ClientEventType;
use App\Models\ClientEvent;
use App\Models\HostingAccount;
use App\Services\Hosting\HestiaCliClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * v-add-letsencrypt-domain puede fallar en el alta si el DNS del cliente
 * todavía no apunta al server (caso real: Estudio BA, requirió reintentarlo
 * a mano días después) — este Job reintenta periódicamente cada cuenta
 * HestiaCP sin SSL hasta que se emite solo, sin depender de que un admin se
 * acuerde de volver a hacerlo una vez que el cliente actualiza su DNS.
 */
class RetryHestiaSslIssuance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(HestiaCliClient $hestia): void
    {
        $accounts = HostingAccount::where('provider', 'hestiacp')
            ->with('hosting.client', 'hosting.projects.domain')
            ->get();

        foreach ($accounts as $account) {
            $hosting = $account->hosting;
            $domain = $hosting?->projects->first()?->domain;

            if (! $hosting || ! $domain) {
                continue;
            }

            $status = $hestia->listUser($account->remote_username);
            $data = json_decode($status['output'], true);
            $hasSsl = ($data[$account->remote_username]['U_WEB_SSL'] ?? '0') !== '0';

            if ($hasSsl) {
                continue;
            }

            $result = $hestia->issueSsl($account->remote_username, $domain->domain_name);

            if ($result['success']) {
                ClientEvent::log($hosting->client, "SSL emitido automáticamente para {$domain->domain_name} (reintento programado)", ClientEventType::Hosting, $hosting);

                Log::info('RetryHestiaSslIssuance: SSL emitido en un reintento programado', [
                    'account' => $account->remote_username,
                    'domain'  => $domain->domain_name,
                ]);
            }
        }
    }
}
