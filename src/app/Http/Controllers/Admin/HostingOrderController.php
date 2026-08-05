<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClientEventType;
use App\Enums\ContactRole;
use App\Enums\DomainStatus;
use App\Enums\HostingStatus;
use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\ClientDomain;
use App\Models\ClientEvent;
use App\Models\Hosting;
use App\Models\HostingPlan;
use App\Services\Fulfillment\ChargeFulfillmentService;
use App\Services\Payments\ChargePaymentLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * Alta de una orden de hosting para un cliente ya existente — solo el admin
 * la inicia (no hay checkout público). El provisioning en HestiaCP se
 * dispara de inmediato al crear la orden (no espera confirmación de pago) —
 * el Charge/link de pago se genera igual, para cobrar, pero no bloquea el
 * alta técnica.
 */
class HostingOrderController extends Controller
{
    public function create(Client $client)
    {
        Gate::authorize('manage-clients');

        $plans = HostingPlan::sellable()->orderBy('price')->get();
        $contact = $client->contacts()->where('is_primary', true)->first();

        return view('admin.hosting-orders.create', compact('client', 'plans', 'contact'));
    }

    public function store(Request $request, Client $client, ChargePaymentLinkService $paymentLinks, ChargeFulfillmentService $fulfillment)
    {
        Gate::authorize('manage-clients');

        $hasContact = $client->contacts()->where('is_primary', true)->exists();

        $validated = $request->validate([
            'hosting_plan_id' => 'required|exists:hosting_plans,id',
            'domain_name'     => 'required|string|max:255',
            'contact_name'    => $hasContact ? 'nullable|string|max:255' : 'required|string|max:255',
            'contact_email'   => $hasContact ? 'nullable|email|max:255' : 'required|email|max:255',
        ]);

        $plan = HostingPlan::findOrFail($validated['hosting_plan_id']);
        $alreadyPaid = $request->boolean('already_paid');

        $domainOwnedByOther = ClientDomain::where('domain_name', $validated['domain_name'])
            ->where('client_id', '!=', $client->id)
            ->exists();

        if ($domainOwnedByOther) {
            return back()->withErrors(['domain_name' => 'Ese dominio ya está cargado en otro cliente.']);
        }

        $charge = DB::transaction(function () use ($client, $plan, $validated, $hasContact) {
            if (! $hasContact) {
                ClientContact::create([
                    'client_id'  => $client->id,
                    'name'       => $validated['contact_name'],
                    'email'      => $validated['contact_email'],
                    'role'       => ContactRole::Dueno,
                    'is_primary' => true,
                ]);
            }

            // Reusa el dominio si el cliente ya lo tiene cargado (ej. migración de un
            // hosting existente) — nunca duplica un ClientDomain para el mismo nombre.
            $domain = $client->domains()->where('domain_name', $validated['domain_name'])->first()
                ?? ClientDomain::create([
                    'client_id'   => $client->id,
                    'domain_name' => $validated['domain_name'],
                    'status'      => DomainStatus::Pendiente,
                ]);

            // El Hosting se vincula directo al dominio (domain_id) — no se crea ningún
            // Project automáticamente. El Project es una entidad que el admin arma a
            // mano cuando quiere agrupar trabajo, nunca un efecto secundario del alta.
            $hosting = Hosting::create([
                'client_id'       => $client->id,
                'domain_id'       => $domain->id,
                'hosting_plan_id' => $plan->id,
                'provider'        => 'Arioli.dev',
                'plan'            => $plan->name,
                'status'          => HostingStatus::Pendiente,
                'renewal_payer'   => 'arioli',
            ]);

            $charge = Charge::create([
                'client_id'       => $client->id,
                'chargeable_type' => Hosting::class,
                'chargeable_id'   => $hosting->id,
                'concept'         => $plan->name,
                'amount'          => $plan->price,
                'currency'        => $plan->currency,
                'status'          => 'pending',
            ]);

            ClientEvent::log($client, 'Hosting contratado (alta desde admin)', ClientEventType::Hosting, $hosting, [
                'hosting_plan' => $plan->name,
                'domain'       => $domain->domain_name,
            ]);

            return $charge;
        });

        if ($alreadyPaid) {
            $charge->markAsPaid();
            ClientEvent::log($client, "Cobro \"{$charge->concept}\" marcado como ya pagado al crear la orden", ClientEventType::Charge, $charge);
        } else {
            $paymentLinks->generate($charge);
        }

        // Provisioning inmediato — no espera a que se confirme el pago.
        $fulfillment->fulfill($charge);

        $message = $alreadyPaid
            ? 'Orden de hosting creada — la cuenta se está provisionando en HestiaCP, el cobro ya quedó registrado como pagado.'
            : 'Orden de hosting creada — la cuenta se está provisionando en HestiaCP, y el link de pago ya está disponible en Cobros.';

        return redirect()->route('clients.show', $client)->with('success', $message);
    }
}
