<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ChargeStatus;
use App\Enums\ClientEventType;
use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\ClientJob;
use App\Services\Payments\ChargePaymentLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Agrupa varios Charges pendientes + Trabajos puntuales sin cobro todavía
 * en UN Charge nuevo (con un solo link de Mercado Pago), sin perder el
 * historial de los originales — ver Charge::bundledItems()/markAsPaid().
 */
class PaymentOrderController extends Controller
{
    public function store(Request $request, Client $client, ChargePaymentLinkService $paymentLinks): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'charge_ids'   => 'nullable|array',
            'charge_ids.*' => 'integer',
            'job_ids'      => 'nullable|array',
            'job_ids.*'    => 'integer',
        ]);

        $charges = $client->charges()
            ->whereIn('id', $validated['charge_ids'] ?? [])
            ->where('status', ChargeStatus::Pending)
            ->whereNull('bundled_into_charge_id')
            ->get();

        $jobs = $client->jobs()
            ->whereIn('id', $validated['job_ids'] ?? [])
            ->whereDoesntHave('charges')
            ->get();

        if ($charges->isEmpty() && $jobs->isEmpty()) {
            return back()->with('error', 'Elegí al menos un cobro o trabajo disponible para armar la orden.');
        }

        // Los Trabajos puntuales no tienen moneda propia — se asumen en ARS,
        // igual que el resto de los cobros generados desde un ClientJob.
        $currencies = $charges->pluck('currency')->unique();
        if ($jobs->isNotEmpty()) {
            $currencies->push(Currency::ARS);
        }
        $currencies = $currencies->unique();

        if ($currencies->count() > 1) {
            throw ValidationException::withMessages([
                'charge_ids' => 'No se puede combinar cobros de distinta moneda (' . $currencies->map(fn ($c) => $c->value)->implode(', ') . ') en una misma orden — armá una orden por moneda.',
            ]);
        }

        $currency = $currencies->first();

        foreach ($jobs as $job) {
            $jobCharge = $client->charges()->create([
                'chargeable_type' => ClientJob::class,
                'chargeable_id'   => $job->id,
                'concept'         => $job->title,
                'amount'          => $job->amount,
                'currency'        => $currency,
                'status'          => ChargeStatus::Pending,
            ]);

            $charges->push($jobCharge);
        }

        $order = $client->charges()->create([
            'concept'  => 'Orden de pago — ' . $charges->pluck('concept')->implode(', '),
            'amount'   => $charges->sum('amount'),
            'currency' => $currency,
            'status'   => ChargeStatus::Pending,
        ]);

        $paymentLinks->generate($order);

        foreach ($charges as $charge) {
            $charge->update(['bundled_into_charge_id' => $order->id]);
        }

        ClientEvent::log($client, "Se generó la orden de pago \"{$order->concept}\"", ClientEventType::Charge, $order);

        return back()->with('success', 'Orden de pago generada.');
    }
}
