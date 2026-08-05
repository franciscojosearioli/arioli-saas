<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Stancl\Tenancy\Database\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        // Enterprise authorization check - only admins can view all tickets
        Gate::authorize('admin-access');
        $this->authorize('viewAny', Ticket::class);

        $query = Ticket::with(['tenant', 'user', 'assignedTo']);

        // Filtros
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('tenant_id')) {
            $query->forTenant($request->tenant_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();

        // Datos para los filtros
        $tenants = Tenant::orderBy('id')->get();
        $admins = User::whereNull('tenant_id')->orderBy('name')->get();

        // Estadísticas rápidas
        $stats = [
            'total' => Ticket::count(),
            'abiertos' => Ticket::whereIn('status', ['abierto', 'en_progreso', 'esperando_cliente'])->count(),
            'resueltos' => Ticket::where('status', 'resuelto')->count(),
            'criticos' => Ticket::where('priority', 'critica')->where('status', '!=', 'cerrado')->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'tenants', 'admins', 'stats'));
    }

    public function show(Ticket $ticket): View
    {
        // Enterprise authorization check - admin access and ticket view permission
        Gate::authorize('admin-access');
        $this->authorize('view', $ticket);

        $ticket->load(['tenant', 'user', 'assignedTo']);

        // Obtener licencias activas del tenant
        $licenses = $ticket->tenant->licenses()->with('plan.product')->active()->get();

        return view('admin.tickets.show', compact('ticket', 'licenses'));
    }

    public function create(): View
    {
        // Enterprise authorization check - only admins can create tickets
        Gate::authorize('admin-access');
        // Note: Admin can create tickets on behalf of clients

        $tenants = Tenant::orderBy('id')->get();
        $admins = User::whereNull('tenant_id')->orderBy('name')->get();

        return view('admin.tickets.create', compact('tenants', 'admins'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Enterprise authorization check - only admins can create tickets
        Gate::authorize('admin-access');

        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:abierto,en_progreso,esperando_cliente,resuelto,cerrado',
            'priority' => 'required|in:baja,media,alta,critica',
            'category' => 'required|in:tecnico,facturacion,configuracion,otro',
            'assigned_to' => 'nullable|exists:users,id',
            'admin_notes' => 'nullable|string',
        ]);

        // Obtener el primer usuario del tenant para asignarlo como creador
        $tenantUser = User::where('tenant_id', $request->tenant_id)->first();

        if (!$tenantUser) {
            return back()->withErrors(['tenant_id' => 'El cliente seleccionado no tiene usuarios asociados.']);
        }

        $ticketData = $request->all();
        $ticketData['user_id'] = $tenantUser->id;

        if ($request->status === 'resuelto' && !isset($ticketData['resolved_at'])) {
            $ticketData['resolved_at'] = now();
        }

        Ticket::create($ticketData);

        return redirect()->route('tickets.index')->with('success', 'Ticket creado exitosamente.');
    }

    public function edit(Ticket $ticket): View
    {
        // Enterprise authorization check - admin access and ticket update permission
        Gate::authorize('admin-access');
        $this->authorize('update', $ticket);

        $tenants = Tenant::orderBy('id')->get();
        $admins = User::whereNull('tenant_id')->orderBy('name')->get();

        return view('admin.tickets.edit', compact('ticket', 'tenants', 'admins'));
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        // Enterprise authorization check - admin access and ticket update permission
        Gate::authorize('admin-access');
        $this->authorize('update', $ticket);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:abierto,en_progreso,esperando_cliente,resuelto,cerrado',
            'priority' => 'required|in:baja,media,alta,critica',
            'category' => 'required|in:tecnico,facturacion,configuracion,otro',
            'assigned_to' => 'nullable|exists:users,id',
            'admin_notes' => 'nullable|string',
        ]);

        $ticketData = $request->all();

        // Si se marca como resuelto, establecer fecha de resolución
        if ($request->status === 'resuelto' && $ticket->status !== 'resuelto') {
            $ticketData['resolved_at'] = now();
        } elseif ($request->status !== 'resuelto') {
            $ticketData['resolved_at'] = null;
        }

        $ticket->update($ticketData);

        return redirect()->route('tickets.index')->with('success', 'Ticket actualizado exitosamente.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        // Enterprise authorization check - admin access and ticket deletion permission
        Gate::authorize('admin-access');
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket eliminado exitosamente.');
    }

    /**
     * Métodos AJAX para acciones rápidas
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        // Enterprise authorization check - only admins can update ticket status
        Gate::authorize('admin-access');
        $this->authorize('updateStatus', $ticket);

        $request->validate([
            'status' => 'required|in:abierto,en_progreso,esperando_cliente,resuelto,cerrado'
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'resuelto' && $ticket->status !== 'resuelto') {
            $data['resolved_at'] = now();
        } elseif ($request->status !== 'resuelto') {
            $data['resolved_at'] = null;
        }

        $ticket->update($data);

        return response()->json(['success' => true, 'message' => 'Estado actualizado exitosamente.']);
    }

    public function assignTicket(Request $request, Ticket $ticket)
    {
        // Enterprise authorization check - only admins can assign tickets
        Gate::authorize('admin-access');
        $this->authorize('assign', $ticket);

        $request->validate([
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        $ticket->update(['assigned_to' => $request->assigned_to]);

        return response()->json(['success' => true, 'message' => 'Ticket asignado exitosamente.']);
    }
}