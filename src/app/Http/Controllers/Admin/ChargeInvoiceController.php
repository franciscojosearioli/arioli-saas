<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ChargeStatus;
use App\Enums\ClientEventType;
use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Genera la Invoice de un Charge ya pagado, directamente desde la ficha del
 * cliente — el vínculo queda en charges.invoice_id (Charge::invoice()), no
 * hace falta una columna nueva. El resto del ciclo de vida (emitir/anular/
 * descargar/enviar) reutiliza las rutas ya existentes de Facturación.
 */
class ChargeInvoiceController extends Controller
{
    public function store(Request $request, Client $client, Charge $charge): RedirectResponse
    {
        Gate::authorize('manage-invoices');

        abort_if($charge->client_id !== $client->id, 404);

        if ($charge->status !== ChargeStatus::Paid) {
            return back()->with('error', 'Solo se pueden facturar cobros ya pagados.');
        }

        if ($charge->invoice_id) {
            return back()->with('error', 'Este cobro ya tiene una factura generada.');
        }

        $validated = $request->validate([
            'customer_cuit' => 'nullable|string|max:20',
        ]);

        $invoice = Invoice::create([
            'client_id'     => $client->id,
            'type'          => 'factura',
            'status'        => 'draft',
            'customer_name' => $client->name,
            'customer_cuit' => $validated['customer_cuit'] ?: $client->cuit,
            'amount'        => $charge->amount,
            'currency'      => $charge->currency->value,
            'notes'         => $charge->concept,
        ]);

        $charge->update(['invoice_id' => $invoice->id]);

        ClientEvent::log($client, "Se generó la factura del cobro \"{$charge->concept}\"", ClientEventType::Charge, $charge);

        return back()->with('success', 'Factura generada como borrador.');
    }
}
