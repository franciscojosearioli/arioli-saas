<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContractEventType;
use App\Enums\ContractStatus;
use App\Enums\SignerRole;
use App\Enums\SignerStatus;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractEvent;
use App\Models\ContractSignature;
use App\Models\ContractSigner;
use App\Models\ContractTemplate;
use App\Models\License;
use App\Models\Order;
use App\Services\Contracts\ContractNotificationService;
use App\Services\Contracts\ContractPdfService;
use App\Services\Contracts\ContractTemplateRenderer;
use App\Services\Signatures\SignatureProviderManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-legal');

        $status = $request->query('status', '');
        $search = $request->query('search', '');

        $query = Contract::with('signers')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('tenant_id', 'like', "%{$search}%");
            });
        }

        $contracts = $query->paginate(15)->withQueryString();

        $stats = [
            'draft'   => Contract::where('status', ContractStatus::Draft)->count(),
            'pending' => Contract::where('status', ContractStatus::PendingSignature)->count(),
            'signed'  => Contract::where('status', ContractStatus::Signed)->count(),
        ];

        return view('admin.legales.contratos.index', compact('contracts', 'status', 'search', 'stats'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('manage-legal');

        $templates = ContractTemplate::where('active', true)->orderBy('name')->get();
        $orders    = Order::with('plan.product')->where('status', 'approved')->latest()->limit(200)->get();
        $licenses  = License::with('plan.product')->notDemo()->where('active', true)->latest()->limit(200)->get();

        return view('admin.legales.contratos.create', compact('templates', 'orders', 'licenses'));
    }

    public function store(Request $request, ContractTemplateRenderer $renderer): RedirectResponse
    {
        Gate::authorize('manage-legal');

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'type'               => 'required|in:licencia,servicio,otro',
            'contract_template_id' => 'nullable|exists:contract_templates,id',
            'contractable_type'  => 'nullable|in:order,license',
            'contractable_id'    => 'nullable|integer',
            'tenant_id'          => 'required|string|max:255',
            'customer_name'      => 'required|string|max:255',
            'customer_email'     => 'nullable|email|max:255',
            'customer_cuit'      => 'nullable|string|max:20',
            'content'            => 'nullable|string',
        ]);

        $contractable = match ($validated['contractable_type'] ?? null) {
            'order'   => Order::find($validated['contractable_id']),
            'license' => License::find($validated['contractable_id']),
            default   => null,
        };

        $template = isset($validated['contract_template_id'])
            ? ContractTemplate::find($validated['contract_template_id'])
            : null;

        $rawContent = $template->content ?? ($validated['content'] ?? '');

        $renderedContent = $renderer->render($rawContent, [
            'tenant_id'      => $validated['tenant_id'],
            'customer_name'  => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_cuit'  => $validated['customer_cuit'] ?? null,
            'contractable'   => $contractable,
        ]);

        $contract = Contract::create([
            'tenant_id'            => $validated['tenant_id'],
            'contractable_type'    => $contractable ? get_class($contractable) : null,
            'contractable_id'      => $contractable?->id,
            'contract_template_id' => $template?->id,
            'title'                => $validated['title'],
            'type'                 => $validated['type'],
            'content'              => $renderedContent,
            'status'               => ContractStatus::Draft,
        ]);

        ContractEvent::log($contract, ContractEventType::Created);
        ContractEvent::log($contract, ContractEventType::Generated);

        // Firmante "cliente" por defecto, a partir de los datos ingresados.
        $contract->signers()->create([
            'role'   => SignerRole::Cliente,
            'name'   => $validated['customer_name'],
            'email'  => $validated['customer_email'] ?? '',
            'status' => SignerStatus::Pending,
        ]);

        return redirect()->route('legales.contratos.show', $contract)
            ->with('success', 'Contrato creado como borrador.');
    }

    public function show(Contract $contract): View
    {
        Gate::authorize('manage-legal');

        $contract->load(['signers.signature', 'events.user', 'events.signer', 'template', 'contractable']);

        return view('admin.legales.contratos.show', compact('contract'));
    }

    public function addSigner(Request $request, Contract $contract): RedirectResponse
    {
        Gate::authorize('manage-legal');

        $validated = $request->validate([
            'role'  => 'required|in:cliente,representante_legal,segundo_firmante,testigo,administrador,otro',
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $contract->signers()->create([
            ...$validated,
            'order'  => $contract->signers()->count(),
            'status' => SignerStatus::Pending,
        ]);

        return back()->with('success', 'Firmante agregado.');
    }

    public function sendForSignature(Contract $contract): RedirectResponse
    {
        Gate::authorize('manage-legal');

        $pendingSigners = $contract->signers()->where('status', SignerStatus::Pending)->get();

        if ($pendingSigners->isEmpty()) {
            return back()->withErrors(['signers' => 'No hay firmantes pendientes de enviar.']);
        }

        $driver = SignatureProviderManager::driver();

        foreach ($pendingSigners as $signer) {
            $driver->sendForSignature(['signer' => $signer]);
        }

        $contract->update(['status' => ContractStatus::PendingSignature]);

        return back()->with('success', 'Se envió la solicitud de firma a '.$pendingSigners->count().' firmante(s).');
    }

    public function markSignedManually(Contract $contract, ContractNotificationService $notifier): RedirectResponse
    {
        Gate::authorize('manage-legal');

        foreach ($contract->signers()->where('status', SignerStatus::Pending)->get() as $signer) {
            $signer->update(['status' => SignerStatus::Signed, 'signed_at' => now(), 'signing_token' => null]);

            ContractSignature::create([
                'contract_signer_id' => $signer->id,
                'ip_address'         => request()->ip(),
                'user_agent'         => 'Marcado manualmente por admin',
                'content_hash'       => hash('sha256', $contract->content),
            ]);

            ContractEvent::log($contract, ContractEventType::Signed, $signer, ['manual' => true]);
        }

        $contract->recalculateStatus();

        if ($contract->fresh()->status === ContractStatus::Signed) {
            $notifier->notifySigned($contract);
        }

        return back()->with('success', 'Contrato marcado como firmado manualmente.');
    }

    public function cancel(Contract $contract): RedirectResponse
    {
        Gate::authorize('manage-legal');

        $contract->update(['status' => ContractStatus::Cancelled]);
        ContractEvent::log($contract, ContractEventType::Cancelled);

        return back()->with('success', 'Contrato cancelado.');
    }

    public function print(Contract $contract, ContractPdfService $pdf): View
    {
        Gate::authorize('manage-legal');

        return $pdf->render($contract);
    }
}
