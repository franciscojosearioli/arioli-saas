<?php

namespace App\Jobs;

use App\Enums\ClientEventType;
use App\Enums\CredentialOwner;
use App\Enums\CredentialType;
use App\Enums\DomainStatus;
use App\Enums\HostingStatus;
use App\Mail\HostingReadyMail;
use App\Models\ClientEvent;
use App\Models\Credential;
use App\Models\Hosting;
use App\Models\HostingAccount;
use App\Services\Hosting\HostingPanelManager;
use App\Services\Hosting\HostingUsernameGenerator;
use App\Support\NotificationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Se dispara al crear la orden de hosting desde el admin (inmediato, no
 * espera confirmación de pago — ver Admin\HostingOrderController::store()),
 * y también queda enganchado a ChargeFulfillmentService por si el pago
 * confirma más tarde. Por eso es idempotente: si el Hosting ya tiene una
 * HostingAccount, no vuelve a provisionar. Si el provisioning falla, no
 * queda ningún HostingAccount creado — solo si termina bien se crea el
 * registro técnico.
 */
class ProvisionHostingAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public readonly Hosting $hosting)
    {
    }

    public function handle(HostingUsernameGenerator $usernames): void
    {
        $hosting = $this->hosting;

        if ($hosting->account) {
            Log::info('ProvisionHostingAccount: ya tiene HostingAccount, no se reprovisiona', ['hosting_id' => $hosting->id]);

            return;
        }

        $client = $hosting->client;
        $contact = $client->contacts()->where('is_primary', true)->first();

        // El dominio de esta cuenta es el que ya quedó vinculado directo en
        // Hosting.domain_id (ver HostingOrderController::store()) — no se
        // adivina por status, así también funciona para dominios ya Activo
        // (ej. migraciones de un hosting externo existente).
        $domain = $hosting->domain;

        // HestiaCP exige un email para crear el usuario (v-add-user). Si el
        // cliente no tiene contacto o el contacto no tiene email cargado, se
        // usa un email de reemplazo basado en el dominio para no bloquear el
        // alta — más abajo no se envía ningún mail de aviso en ese caso,
        // porque no hay un destinatario real a quien avisarle.
        $hasRealContactEmail = (bool) $contact?->email;

        if (! $hasRealContactEmail) {
            Log::info('ProvisionHostingAccount: sin contacto con email, se crea igual con un email de reemplazo (sin enviar aviso)', ['hosting_id' => $hosting->id]);
        }

        $accountEmail = $contact->email
            ?? ($domain?->domain_name ? "admin@{$domain->domain_name}" : "hosting-{$hosting->id}@arioli.dev");

        $username = $usernames->forHosting($hosting);
        $password = Str::random(16);

        $result = HostingPanelManager::driver()->createAccount([
            'username'    => $username,
            'password'    => $password,
            'email'       => $accountEmail,
            'domain'      => $domain?->domain_name ?? '',
            'client_name' => $client->name,
            'package'     => $hosting->hostingPlan?->hestia_package,
        ]);

        if (! $result->success) {
            ClientEvent::log($client, 'Error al crear la cuenta de hosting', ClientEventType::Hosting, $hosting);
            Log::error('ProvisionHostingAccount: fallo al crear la cuenta', [
                'hosting_id' => $hosting->id,
                'message'    => $result->message,
            ]);
            $this->fail($result->message ?? 'Fallo desconocido al crear la cuenta de hosting');

            return;
        }

        $account = HostingAccount::create([
            'hosting_id'      => $hosting->id,
            'provider'        => \App\Models\Setting::get('hosting_panel.driver', 'manual'),
            'remote_username' => $result->username,
            'panel_url'       => $result->panelUrl,
            'status'          => 'activo',
            'last_sync_at'    => now(),
        ]);

        Credential::create([
            'credentialable_type' => HostingAccount::class,
            'credentialable_id'   => $account->id,
            'type'                => CredentialType::Hosting,
            'label'               => 'Acceso HestiaCP',
            'username'            => $username,
            'secret'              => $password,
            'url'                 => $result->panelUrl,
            'owner'               => CredentialOwner::Cliente,
        ]);

        $billingCycle = $hosting->hostingPlan?->billing_cycle?->value;
        $expiresAt = match ($billingCycle) {
            'anual' => now()->addYear(),
            default => now()->addMonth(),
        };

        $hosting->update([
            'status'         => HostingStatus::Activo,
            'provisioned_at' => now(),
            'expires_at'     => $expiresAt,
        ]);

        ClientEvent::log($client, 'Cuenta de hosting creada', ClientEventType::Hosting, $hosting);

        if ($domain && $domain->status === DomainStatus::Pendiente) {
            // El registro real (Porkbun, con costo) es un paso explícito y separado
            // (Admin\DomainRegistrationController) — nunca automático acá, para no
            // gastar plata sin que el cliente haya pagado primero.
            ClientEvent::log($client, 'Dominio pendiente de registro', ClientEventType::Domain, $hosting);
            NotificationHelper::domainPendingManualRegistration($domain);
        } elseif ($domain) {
            // Dominio ya activo (ej. migración de un hosting externo existente) —
            // no hace falta registrarlo, solo se vinculó a la cuenta nueva.
            ClientEvent::log($client, "Hosting vinculado al dominio existente {$domain->domain_name}", ClientEventType::Domain, $hosting);
        }

        if ($hasRealContactEmail) {
            $credentialsUrl = URL::temporarySignedRoute(
                'hosting.credentials.show',
                now()->addDays(7),
                ['account' => $account->id],
            );

            try {
                Mail::to($contact->email)->send(new HostingReadyMail($account, $contact, $credentialsUrl));
                ClientEvent::log($client, 'Cliente notificado', ClientEventType::Hosting, $hosting);
            } catch (\Throwable $e) {
                Log::error('ProvisionHostingAccount: fallo al enviar HostingReadyMail', [
                    'hosting_id' => $hosting->id,
                    'message'    => $e->getMessage(),
                ]);
            }
        } else {
            ClientEvent::log($client, "Cuenta creada sin contacto con email — no se envió aviso (email usado en HestiaCP: {$accountEmail})", ClientEventType::Hosting, $hosting);
        }
    }
}
