<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\Invoice;
use App\Models\Quote;
use App\Services\Quotes\QuotePdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class BillingController extends Controller
{
    public function index(): View
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id, 404);

        $client = $user->client()->with([
            'invoices' => fn ($q) => $q->where('status', '!=', 'draft')->latest(),
            'quotes.items',
            'charges' => fn ($q) => $q->latest(),
            'charges.payments',
            'charges.installments',
        ])->firstOrFail();

        return view('cliente.billing.index', [
            'invoices' => $client->invoices,
            'quotes'   => $client->quotes,
            'charges'  => $client->charges,
        ]);
    }

    public function showCharge(Charge $charge): View
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id || $charge->client_id !== $user->client_id, 403);

        $charge->load(['payments' => fn ($q) => $q->orderByDesc('paid_at'), 'installments', 'invoice']);

        return view('cliente.billing.charge', [
            'charge'         => $charge,
            'transferAlias'  => \App\Models\Setting::get('mercadopago.transfer_alias'),
        ]);
    }

    public function printInvoice(Invoice $invoice): View
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id || $invoice->client_id !== $user->client_id, 403);
        abort_if($invoice->isDraft(), 404);

        $invoice->load(['order.plan.product', 'license.plan.product']);

        return view('admin.invoices.print', compact('invoice'));
    }

    public function downloadQuote(Quote $quote, QuotePdfService $pdf): Response
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id || $quote->client_id !== $user->client_id, 403);

        return $pdf->download($quote);
    }
}
