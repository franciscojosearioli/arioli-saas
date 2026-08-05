<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BillingCycle;
use App\Enums\Currency;
use App\Enums\ProjectType;
use App\Enums\QuoteItemType;
use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Quote;
use App\Services\Quotes\QuoteAcceptanceService;
use App\Services\Quotes\QuotePdfService;
use App\Services\Quotes\QuoteNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class QuoteController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-clients');

        $status = $request->query('status', '');
        $query = Quote::with(['client', 'items'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $quotes = $query->paginate(15)->withQueryString();

        return view('admin.cotizaciones.index', compact('quotes', 'status'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('manage-clients');

        $clients = Client::orderBy('name')->get();
        $selectedClientId = $request->query('client_id');

        return view('admin.cotizaciones.create', compact('clients', 'selectedClientId'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $this->validateQuote($request);

        $quote = Quote::create([
            'client_id'             => $validated['client_id'],
            'title'                 => $validated['title'],
            'summary'               => $validated['summary'] ?? null,
            'introduction'          => $validated['introduction'] ?? null,
            'project_scope'         => $validated['project_scope'] ?? null,
            'benefits'              => $validated['benefits'] ?? null,
            'exclusions'            => $validated['exclusions'] ?? null,
            'warranty'              => $validated['warranty'] ?? null,
            'payment_terms'         => $validated['payment_terms'] ?? null,
            'timeline_estimate'     => $validated['timeline_estimate'] ?? null,
            'observations'          => $validated['observations'] ?? null,
            'final_message'         => $validated['final_message'] ?? null,
            'valid_until'           => $validated['valid_until'] ?? null,
            'creates_project'       => $request->boolean('creates_project'),
            'project_name'          => $validated['project_name'] ?? null,
            'project_type'          => $validated['project_type'] ?? null,
            'initial_charge_amount' => $validated['initial_charge_amount'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $quote->items()->create($item);
        }

        return redirect()->route('cotizaciones.show', $quote)
            ->with('success', 'Propuesta creada como borrador.');
    }

    public function show(Quote $quote): View
    {
        Gate::authorize('manage-clients');

        $quote->load(['client', 'items', 'project', 'contract']);

        return view('admin.cotizaciones.show', compact('quote'));
    }

    public function edit(Quote $quote): View|RedirectResponse
    {
        Gate::authorize('manage-clients');

        if ($quote->status !== QuoteStatus::Borrador) {
            return redirect()->route('cotizaciones.show', $quote)
                ->with('error', 'Solo se puede editar una propuesta en borrador.');
        }

        $quote->load('items');
        $clients = Client::orderBy('name')->get();

        return view('admin.cotizaciones.edit', compact('quote', 'clients'));
    }

    public function update(Request $request, Quote $quote): RedirectResponse
    {
        Gate::authorize('manage-clients');

        if ($quote->status !== QuoteStatus::Borrador) {
            return redirect()->route('cotizaciones.show', $quote)
                ->with('error', 'Solo se puede editar una propuesta en borrador.');
        }

        $validated = $this->validateQuote($request);

        $quote->update([
            'client_id'             => $validated['client_id'],
            'title'                 => $validated['title'],
            'summary'               => $validated['summary'] ?? null,
            'introduction'          => $validated['introduction'] ?? null,
            'project_scope'         => $validated['project_scope'] ?? null,
            'benefits'              => $validated['benefits'] ?? null,
            'exclusions'            => $validated['exclusions'] ?? null,
            'warranty'              => $validated['warranty'] ?? null,
            'payment_terms'         => $validated['payment_terms'] ?? null,
            'timeline_estimate'     => $validated['timeline_estimate'] ?? null,
            'observations'          => $validated['observations'] ?? null,
            'final_message'         => $validated['final_message'] ?? null,
            'valid_until'           => $validated['valid_until'] ?? null,
            'creates_project'       => $request->boolean('creates_project'),
            'project_name'          => $validated['project_name'] ?? null,
            'project_type'          => $validated['project_type'] ?? null,
            'initial_charge_amount' => $validated['initial_charge_amount'] ?? null,
        ]);

        $quote->items()->delete();
        foreach ($validated['items'] as $item) {
            $quote->items()->create($item);
        }

        return redirect()->route('cotizaciones.show', $quote)
            ->with('success', 'Propuesta actualizada.');
    }

    private function validateQuote(Request $request): array
    {
        return $request->validate([
            'client_id'              => 'required|exists:clients,id',
            'title'                  => 'required|string|max:255',
            'summary'                => 'nullable|string|max:500',
            'introduction'           => 'nullable|string',
            'project_scope'          => 'nullable|string',
            'benefits'               => 'nullable|string',
            'exclusions'             => 'nullable|string',
            'warranty'               => 'nullable|string',
            'payment_terms'          => 'nullable|string',
            'timeline_estimate'      => 'nullable|string|max:255',
            'observations'           => 'nullable|string',
            'final_message'          => 'nullable|string',
            'valid_until'            => 'nullable|date',
            'creates_project'        => 'boolean',
            'project_name'           => 'nullable|string|max:255|required_if:creates_project,1',
            'project_type'           => 'nullable|in:' . implode(',', array_column(ProjectType::cases(), 'value')),
            'initial_charge_amount'  => 'nullable|numeric|min:0',
            'items'                  => 'required|array|min:1',
            'items.*.description'    => 'required|string|max:255',
            'items.*.item_type'      => ['required', 'in:' . implode(',', array_column(QuoteItemType::cases(), 'value'))],
            'items.*.service_type'   => 'nullable|string',
            'items.*.billing_cycle'  => ['required', 'in:' . implode(',', array_column(BillingCycle::cases(), 'value'))],
            'items.*.quantity'       => 'required|numeric|min:0.01',
            'items.*.unit_label'     => 'nullable|string|max:50',
            'items.*.unit_price'     => 'required|numeric|min:0',
            'items.*.currency'       => ['required', 'in:' . implode(',', array_column(Currency::cases(), 'value'))],
        ]);
    }

    public function send(Quote $quote, QuoteNotificationService $notifier): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $notifier->sendForApproval($quote);

        return back()->with('success', 'Propuesta enviada al cliente.');
    }

    public function markAccepted(Quote $quote, QuoteAcceptanceService $acceptance): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $contract = $acceptance->accept($quote);

        return redirect()->route('legales.contratos.show', $contract)
            ->with('success', 'Propuesta aceptada — se generó el proyecto/servicios y el contrato a firmar.');
    }

    public function reject(Quote $quote, QuoteAcceptanceService $acceptance): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $acceptance->reject($quote);

        return back()->with('success', 'Propuesta marcada como rechazada.');
    }

    public function print(Quote $quote, QuotePdfService $pdf): View
    {
        Gate::authorize('manage-clients');

        return $pdf->render($quote);
    }

    public function download(Quote $quote, QuotePdfService $pdf): Response
    {
        Gate::authorize('manage-clients');

        return $pdf->download($quote);
    }
}
