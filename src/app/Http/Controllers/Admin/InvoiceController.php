<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AfipException;
use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\ClientContact;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Setting;
use App\Services\Invoicing\InvoicePdfService;
use App\Services\Invoicing\InvoicingProviderManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-invoices');

        $status = $request->query('status', '');
        $search = $request->query('search', '');

        $query = Invoice::with(['order.plan.product'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('tenant_id', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%");
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        $stats = [
            'draft'   => Invoice::draft()->count(),
            'issued'  => Invoice::issued()->count(),
            'voided'  => Invoice::where('status', 'voided')->count(),
            'total'   => Invoice::issued()->sum('amount'),
        ];

        return view('admin.invoices.index', compact('invoices', 'status', 'search', 'stats'));
    }

    public function create(): View
    {
        Gate::authorize('manage-invoices');

        $orders = Order::with('plan.product')
            ->where('status', 'approved')
            ->latest()
            ->limit(200)
            ->get();

        return view('admin.invoices.create', compact('orders'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-invoices');

        $validated = $request->validate([
            'order_id'      => 'nullable|exists:orders,id',
            'tenant_id'     => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_cuit' => 'nullable|string|max:20',
            'amount'        => 'required|numeric|min:0',
            'currency'      => 'nullable|string|max:3',
            'notes'         => 'nullable|string|max:2000',
        ]);

        $invoice = Invoice::create([
            ...$validated,
            'currency' => $validated['currency'] ?? 'ARS',
            'status'   => 'draft',
        ]);

        return redirect()->route('finanzas.facturacion.show', $invoice)
            ->with('success', 'Factura creada como borrador.');
    }

    public function show(Invoice $invoice): View
    {
        Gate::authorize('manage-invoices');

        $invoice->load(['order.plan.product', 'license.plan.product']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function markIssued(Invoice $invoice): RedirectResponse
    {
        Gate::authorize('manage-invoices');

        if (! $invoice->isDraft()) {
            return back()->withErrors(['status' => 'Solo se pueden emitir facturas en borrador.']);
        }

        $afipConfigurada = Setting::get('afip.cuit') && Setting::get('afip.certificado_path') && Setting::get('afip.clave_privada_path');

        if (! $afipConfigurada) {
            $invoice->markAsIssued();

            return back()->with('success', "Factura {$invoice->number} emitida con numeración interna (AFIP no está configurado).");
        }

        try {
            $resultado = InvoicingProviderManager::driver()->issueInvoice(['invoice' => $invoice]);
        } catch (AfipException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        $invoice->update([
            'status'          => 'issued',
            'number'          => $resultado['number'],
            'cbte_tipo'       => $resultado['cbte_tipo'],
            'doc_tipo'        => $resultado['doc_tipo'],
            'doc_nro'         => $resultado['doc_nro'],
            'cae'             => $resultado['cae'],
            'cae_vencimiento' => $resultado['cae_vencimiento'],
            'issued_at'       => now(),
        ]);

        return back()->with('success', "Factura {$resultado['number']} emitida ante AFIP — CAE {$resultado['cae']}.");
    }

    public function void(Invoice $invoice): RedirectResponse
    {
        Gate::authorize('manage-invoices');

        if (! $invoice->isIssued()) {
            return back()->withErrors(['status' => 'Solo se pueden anular facturas emitidas.']);
        }

        $invoice->void();

        return back()->with('success', "Factura {$invoice->number} anulada.");
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        Gate::authorize('manage-invoices');

        if (! $invoice->isDraft()) {
            return back()->withErrors(['status' => 'Solo se pueden eliminar borradores. Las facturas emitidas se anulan, no se borran.']);
        }

        $invoice->delete();

        return redirect()->route('finanzas.facturacion.index')->with('success', 'Borrador eliminado.');
    }

    public function print(Invoice $invoice): View
    {
        Gate::authorize('manage-invoices');

        $invoice->load(['order.plan.product', 'license.plan.product']);

        return view('admin.invoices.print', compact('invoice'));
    }

    public function download(Invoice $invoice, InvoicePdfService $pdf): Response
    {
        Gate::authorize('manage-invoices');

        return $pdf->download($invoice);
    }

    public function sendEmail(Request $request, Invoice $invoice, InvoicePdfService $pdf): RedirectResponse
    {
        Gate::authorize('manage-invoices');

        $validated = $request->validate([
            'contact_id' => 'required|integer',
        ]);

        $contact = ClientContact::where('client_id', $invoice->client_id)->findOrFail($validated['contact_id']);

        if (! $contact->email) {
            return back()->withErrors(['contact_id' => 'Ese contacto no tiene email cargado.']);
        }

        Mail::to($contact->email)->send(new InvoiceMail($invoice, $pdf->renderPdf($invoice)));

        return back()->with('success', "Factura enviada a {$contact->email}.");
    }
}
