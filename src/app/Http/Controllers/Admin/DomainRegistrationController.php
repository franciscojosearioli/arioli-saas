<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ChargeStatus;
use App\Enums\ClientEventType;
use App\Enums\Currency;
use App\Enums\DomainStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDomain;
use App\Models\ClientEvent;
use App\Services\Dns\DnsProviderManager;
use App\Services\ExchangeRate\DolarRateService;
use App\Services\Payments\ChargePaymentLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DomainRegistrationController extends Controller
{
    public function create(Client $client): View
    {
        Gate::authorize('manage-clients');

        return view('admin.domain-registrations.create', compact('client'));
    }

    public function check(Request $request, Client $client, DolarRateService $dolar): JsonResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate(['domain_name' => 'required|string|max:255']);

        $result = DnsProviderManager::driver()->checkAvailability($validated['domain_name']);

        if (! $result['available'] || ! ($result['price_usd'] ?? null)) {
            return response()->json($result);
        }

        $rate = $dolar->getOficialVenta();

        return response()->json([
            'available' => true,
            'message'   => $result['message'],
            'price_usd' => $result['price_usd'],
            'price_ars' => round($result['price_usd'] * $rate, 2),
            'rate'      => $rate,
        ]);
    }

    public function store(Request $request, Client $client, ChargePaymentLinkService $paymentLinks): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'domain_name'     => 'required|string|max:255',
            'confirm_domain'  => 'required|same:domain_name',
            'price_ars'       => 'required|numeric|min:0',
            'management_fee'  => 'nullable|numeric|min:0',
        ]);

        if (ClientDomain::where('domain_name', $validated['domain_name'])->exists()) {
            return back()->withErrors(['domain_name' => 'Ya existe un dominio con ese nombre cargado en el sistema.']);
        }

        $domain = $client->domains()->create([
            'domain_name'    => $validated['domain_name'],
            'status'         => DomainStatus::Pendiente,
            'registrar'      => 'Porkbun',
            'renewal_payer'  => 'cliente',
            'provider_cost'  => $validated['price_ars'],
            'management_fee' => $validated['management_fee'] ?? 0,
        ]);

        $charge = $domain->charges()->create([
            'client_id' => $client->id,
            'concept'   => "Registro de dominio {$domain->domain_name} vía Porkbun",
            'amount'    => $domain->finalPrice(),
            'currency'  => Currency::ARS,
            'status'    => ChargeStatus::Pending,
        ]);

        $paymentLinks->generate($charge);

        ClientEvent::log($client, "Cobro generado para registrar el dominio {$domain->domain_name}", ClientEventType::Domain, $domain);

        return redirect()->route('clients.show', $client)
            ->with('success', "Se generó el cobro para {$domain->domain_name}. El dominio se registra automáticamente en Porkbun apenas el cliente pague — el link está en Cobros.");
    }
}
