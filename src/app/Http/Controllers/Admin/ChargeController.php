<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ChargePaymentMethod;
use App\Enums\ChargeStatus;
use App\Enums\ClientEventType;
use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\ChargePayment;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\ClientJob;
use App\Models\ClientService;
use App\Models\ClientDomain;
use App\Models\Hosting;
use App\Services\Payments\ChargePaymentLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ChargeController extends Controller
{
    public function store(Request $request, Client $client, ChargePaymentLinkService $paymentLinks): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'chargeable_type'     => 'nullable|in:service,job,domain,hosting',
            'chargeable_id'       => 'nullable|integer',
            'concept'             => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'currency'            => ['required', Rule::in(array_column(Currency::cases(), 'value'))],
            'due_date'            => 'nullable|date',
            'already_paid'        => 'boolean',
            'payment_method'      => ['nullable', Rule::in(array_column(ChargePaymentMethod::cases(), 'value'))],
            'installments_count'  => 'nullable|integer|min:2|max:60',
            'installment_amount'  => 'nullable|numeric|min:0',
        ]);

        $chargeable = match ($validated['chargeable_type'] ?? null) {
            'service' => ClientService::find($validated['chargeable_id']),
            'job'     => ClientJob::find($validated['chargeable_id']),
            'domain'  => ClientDomain::find($validated['chargeable_id']),
            'hosting' => Hosting::find($validated['chargeable_id']),
            default   => null,
        };

        $alreadyPaid = $request->boolean('already_paid');

        $charge = $client->charges()->create([
            'chargeable_type'     => $chargeable ? get_class($chargeable) : null,
            'chargeable_id'       => $chargeable?->id,
            'concept'             => $validated['concept'],
            'amount'              => $validated['amount'],
            'currency'            => $validated['currency'],
            'due_date'            => $validated['due_date'] ?? null,
            'status'              => $alreadyPaid ? ChargeStatus::Paid : ChargeStatus::Pending,
            'payment_method'      => $alreadyPaid ? ($validated['payment_method'] ?? null) : null,
            'paid_at'             => $alreadyPaid ? now() : null,
            'installments_count'  => $validated['installments_count'] ?? null,
            'installment_amount'  => $validated['installment_amount'] ?? null,
        ]);

        if ($charge->hasInstallmentPlan()) {
            $charge->generateInstallments();
        }

        if ($alreadyPaid) {
            $charge->markAsPaid($validated['payment_method'] ?? null);
        } else {
            $paymentLinks->generate($charge);
        }

        ClientEvent::log($client, "Se generó el cobro \"{$charge->concept}\"", ClientEventType::Charge, $charge);

        return back()->with('success', 'Cobro creado.');
    }

    public function markPaid(Request $request, Client $client, Charge $charge): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'payment_method' => ['nullable', Rule::in(array_column(ChargePaymentMethod::cases(), 'value'))],
        ]);

        $charge->markAsPaid($validated['payment_method'] ?? null);

        return back()->with('success', 'Cobro marcado como pagado.');
    }

    /**
     * Registra un pago parcial o total contra un cobro — para cuando el
     * cliente paga de manera informal y de a partes (efectivo/transferencia),
     * ej. $200 hoy, $300 más adelante, hasta saldar el total.
     */
    public function storePayment(Request $request, Client $client, Charge $charge, ChargePaymentLinkService $paymentLinks): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $saldo = $charge->balance();

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($saldo) {
                    if ((float) $value > $saldo + 0.01) {
                        $fail('El monto supera el saldo pendiente ($'.number_format($saldo, 2, ',', '.').').');
                    }
                },
            ],
            'payment_method'    => ['nullable', Rule::in(array_column(ChargePaymentMethod::cases(), 'value'))],
            'paid_at'           => 'nullable|date',
            'notes'             => 'nullable|string|max:500',
            'installment_ids'   => 'nullable|array',
            'installment_ids.*' => ['integer', Rule::exists('charge_installments', 'id')->where('charge_id', $charge->id)],
        ]);

        $payment = $charge->registerPayment(
            amount: (float) $validated['amount'],
            method: isset($validated['payment_method']) ? ChargePaymentMethod::from($validated['payment_method']) : null,
            notes: $validated['notes'] ?? null,
            userId: $request->user()->id,
            paidAt: $validated['paid_at'] ?? null,
        );

        $charge->markInstallmentsPaid($validated['installment_ids'] ?? [], $payment);

        // El link de MP quedó generado por el saldo viejo — si todavía queda algo
        // por cobrar, se regenera solo para que nunca cobre de más (ver docblock
        // de ChargePaymentLinkService). Si ya se saldó, no hace falta link.
        $charge->refresh();
        if ($charge->payment_url && $charge->balance() > 0.01) {
            $paymentLinks->generate($charge);
        }

        $montoFormateado = number_format((float) $validated['amount'], 2, ',', '.');
        ClientEvent::log($client, "Se registró un pago de \${$montoFormateado} para \"{$charge->concept}\"", ClientEventType::Charge, $charge);

        return back()->with('success', 'Pago registrado.');
    }

    public function destroyPayment(Client $client, Charge $charge, ChargePayment $payment): RedirectResponse
    {
        Gate::authorize('manage-clients');
        abort_if($payment->charge_id !== $charge->id, 404);

        $charge->releaseInstallmentsForPayment($payment);
        $payment->delete();

        // Si el cobro se había marcado Paid automáticamente y al borrar este
        // pago vuelve a tener saldo, lo reabrimos para que no quede "pagado"
        // con plata pendiente.
        $charge->refresh();
        if ($charge->status === ChargeStatus::Paid && $charge->balance() > 0.01) {
            $charge->update(['status' => ChargeStatus::Pending, 'paid_at' => null]);
        }

        return back()->with('success', 'Pago eliminado.');
    }

    public function regeneratePaymentLink(Client $client, Charge $charge, ChargePaymentLinkService $paymentLinks): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $ok = $paymentLinks->generate($charge);

        return back()->with($ok ? 'success' : 'error', $ok ? 'Link de pago generado.' : 'No se pudo generar el link de pago — revisá la configuración de Mercado Pago.');
    }

    public function destroy(Client $client, Charge $charge): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $charge->delete();

        return back()->with('success', 'Cobro eliminado.');
    }
}
