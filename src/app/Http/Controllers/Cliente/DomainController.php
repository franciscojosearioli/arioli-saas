<?php

namespace App\Http\Controllers\Cliente;

use App\Enums\ChargeStatus;
use App\Enums\ClientEventType;
use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\Models\ClientDomain;
use App\Models\ClientEvent;
use App\Services\Dns\DnsProviderManager;
use App\Services\Payments\ChargePaymentLinkService;
use App\Support\NotificationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function index(): View
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id, 404);

        $client = $user->client()->with([
            'domains.charges', 'domains.projects.hosting', 'domains.projects.license.plan.product',
        ])->firstOrFail();

        return view('cliente.domains.index', [
            'domains' => $client->domains,
        ]);
    }

    public function renew(ClientDomain $domain, ChargePaymentLinkService $paymentLinks): RedirectResponse
    {
        Gate::authorize('client-access');

        $this->authorizeDomain($domain);

        if ($domain->finalPrice() <= 0) {
            return back()->with('error', 'Este dominio todavía no tiene un monto de renovación configurado. Escribinos y te ayudamos a renovarlo.');
        }

        $charge = $domain->charges()
            ->where('status', ChargeStatus::Pending)
            ->whereNotNull('payment_url')
            ->latest()
            ->first();

        if (! $charge) {
            $charge = $domain->charges()->create([
                'client_id' => $domain->client_id,
                'concept'   => "Renovación de dominio {$domain->domain_name}",
                'amount'    => $domain->finalPrice(),
                'currency'  => Currency::ARS,
                'status'    => ChargeStatus::Pending,
            ]);

            $ok = $paymentLinks->generate($charge);

            ClientEvent::log($domain->client, 'Renovación de dominio solicitada desde el portal', ClientEventType::Charge, $charge);

            if (! $ok) {
                return back()->with('error', 'No pudimos generar el link de pago, intentá de nuevo en unos minutos.');
            }
        }

        return redirect($charge->payment_url);
    }

    public function dns(ClientDomain $domain): View|RedirectResponse
    {
        Gate::authorize('client-access');

        $this->authorizeDomain($domain);

        if ($domain->dns_provider !== 'porkbun') {
            return redirect()->route('cliente.domains.index')->with('error', $this->externalRegistrarMessage());
        }

        $result = DnsProviderManager::driver($domain->dns_provider)->listDnsRecords($domain->domain_name);

        return view('cliente.domains.dns', [
            'domain'  => $domain,
            'records' => $result['success'] ? $result['records'] : [],
            'error'   => $result['success'] ? null : $result['message'],
        ]);
    }

    public function storeDnsRecord(Request $request, ClientDomain $domain): RedirectResponse
    {
        Gate::authorize('client-access');

        $this->authorizeDomain($domain);
        abort_unless($domain->dns_provider === 'porkbun', 404);

        $validated = $request->validate([
            'type'    => 'required|in:A,AAAA,CNAME,MX,TXT,NS,SRV,TLSA,CAA,ALIAS',
            'name'    => 'nullable|string|max:255',
            'content' => 'required|string|max:2000',
            'ttl'     => 'nullable|integer|min:600',
            'prio'    => 'nullable|integer|min:0',
        ]);

        $result = DnsProviderManager::driver('porkbun')->createDnsRecord($domain->domain_name, $validated);

        ClientEvent::log($domain->client, "Cliente {$this->eventVerb($result)} un registro DNS ({$validated['type']}) para {$domain->domain_name} desde el portal", ClientEventType::Domain, $domain);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function updateDnsRecord(Request $request, ClientDomain $domain, string $record): RedirectResponse
    {
        Gate::authorize('client-access');

        $this->authorizeDomain($domain);
        abort_unless($domain->dns_provider === 'porkbun', 404);

        $validated = $request->validate([
            'type'    => 'required|in:A,AAAA,CNAME,MX,TXT,NS,SRV,TLSA,CAA,ALIAS',
            'name'    => 'nullable|string|max:255',
            'content' => 'required|string|max:2000',
            'ttl'     => 'nullable|integer|min:600',
            'prio'    => 'nullable|integer|min:0',
        ]);

        $result = DnsProviderManager::driver('porkbun')->updateDnsRecord($domain->domain_name, $record, $validated);

        ClientEvent::log($domain->client, "Cliente editó un registro DNS para {$domain->domain_name} desde el portal", ClientEventType::Domain, $domain);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function destroyDnsRecord(ClientDomain $domain, string $record): RedirectResponse
    {
        Gate::authorize('client-access');

        $this->authorizeDomain($domain);
        abort_unless($domain->dns_provider === 'porkbun', 404);

        $result = DnsProviderManager::driver('porkbun')->deleteDnsRecord($domain->domain_name, $record);

        ClientEvent::log($domain->client, "Cliente eliminó un registro DNS de {$domain->domain_name} desde el portal", ClientEventType::Domain, $domain);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function nameservers(ClientDomain $domain): View|RedirectResponse
    {
        Gate::authorize('client-access');

        $this->authorizeDomain($domain);

        if ($domain->dns_provider !== 'porkbun') {
            return redirect()->route('cliente.domains.index')->with('error', $this->externalRegistrarMessage());
        }

        $result = DnsProviderManager::driver('porkbun')->getNameservers($domain->domain_name);

        return view('cliente.domains.nameservers', [
            'domain'      => $domain,
            'nameservers' => $result['success'] ? $result['nameservers'] : [],
            'error'       => $result['success'] ? null : $result['message'],
        ]);
    }

    public function updateNameservers(Request $request, ClientDomain $domain): RedirectResponse
    {
        Gate::authorize('client-access');

        $this->authorizeDomain($domain);
        abort_unless($domain->dns_provider === 'porkbun', 404);

        $validated = $request->validate([
            'nameservers'   => 'required|array|min:2|max:6',
            'nameservers.*' => 'required|string|max:255',
        ]);

        $result = DnsProviderManager::driver('porkbun')->updateNameservers($domain->domain_name, $validated['nameservers']);

        ClientEvent::log($domain->client, "Cliente cambió los nameservers de {$domain->domain_name} desde el portal", ClientEventType::Domain, $domain);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function requestTransfer(ClientDomain $domain): RedirectResponse
    {
        Gate::authorize('client-access');

        $this->authorizeDomain($domain);

        ClientEvent::log($domain->client, "Cliente solicitó la transferencia de {$domain->domain_name} a otro registrador desde el portal", ClientEventType::Domain, $domain);
        NotificationHelper::domainTransferRequested($domain);

        return back()->with('success', 'Recibimos tu solicitud de transferencia — te vamos a contactar con los datos (código de autorización) en las próximas horas.');
    }

    private function authorizeDomain(ClientDomain $domain): void
    {
        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id || $domain->client_id !== $user->client_id, 403);
    }

    private function externalRegistrarMessage(): string
    {
        return 'Para hacer cualquier gestión sobre este dominio (DNS, nameservers, etc.) envianos un ticket de soporte solicitando el trámite.';
    }

    private function eventVerb(array $result): string
    {
        return $result['success'] ? 'creó' : 'intentó crear';
    }
}
