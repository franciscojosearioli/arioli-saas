<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;

    // No necesitamos middleware aquí, ya se aplica 'auth.cliente' desde las rutas

    private function getUser()
    {
        return Auth::guard('cliente')->user();
    }

    public function index(Request $request): View
    {
        // Enterprise authorization check - client portal access and ticket viewing
        Gate::authorize('client-access');
        $this->authorize('viewAny', Ticket::class);

        $user = $this->getUser();

        $query = Ticket::with(['assignedTo'])
            ->forPortalUser($user)
            ->where('user_id', $user->id);

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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        // Estadísticas del cliente
        $stats = [
            'total' => Ticket::forPortalUser($user)->where('user_id', $user->id)->count(),
            'abiertos' => Ticket::forPortalUser($user)
                ->where('user_id', $user->id)
                ->whereIn('status', ['abierto', 'en_progreso', 'esperando_cliente'])
                ->count(),
            'resueltos' => Ticket::forPortalUser($user)
                ->where('user_id', $user->id)
                ->where('status', 'resuelto')
                ->count(),
        ];

        return view('cliente.tickets.index', compact('tickets', 'stats'));
    }

    public function show(Ticket $ticket): View
    {
        // Enterprise authorization check - client access and specific ticket view
        Gate::authorize('client-access');
        $this->authorize('view', $ticket);

        $user = $this->getUser();

        $ticket->load(['assignedTo', 'related']);

        return view('cliente.tickets.show', compact('ticket'));
    }

    public function create(Request $request): View
    {
        // Enterprise authorization check - client access and ticket creation
        Gate::authorize('client-access');
        $this->authorize('create', Ticket::class);

        $user = $this->getUser();

        return view('cliente.tickets.create', [
            'relatedOptions' => $this->relatedOptions($user),
            'preselected'    => $request->query('related'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Enterprise authorization check - client access and ticket creation
        Gate::authorize('client-access');
        $this->authorize('create', Ticket::class);

        $user = $this->getUser();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:baja,media,alta,critica',
            'category' => 'required|in:tecnico,facturacion,configuracion,otro',
            'related' => 'nullable|string',
        ]);

        [$relatedType, $relatedId] = $this->parseRelated($user, $validated['related'] ?? null);

        Ticket::create([
            'tenant_id' => $user->tenant_id,
            'client_id' => $user->client_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => 'abierto',
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);

        return redirect()->route('cliente.tickets.index')->with('success', 'Ticket creado exitosamente. Nuestro equipo de soporte lo revisará pronto.');
    }

    /**
     * Lista combinada de activos del cliente para el selector "Servicio
     * relacionado" — cada opción codifica tipo+id como "Clase:id" porque un
     * <select> HTML no puede enviar dos valores en un solo campo.
     *
     * @return array<string, string>
     */
    private function relatedOptions($user): array
    {
        if (! $user->client_id) {
            return [];
        }

        $client = $user->client()->with(['hostings', 'domains', 'services'])->first();

        if (! $client) {
            return [];
        }

        $options = [];

        foreach ($client->hostings as $hosting) {
            $options[\App\Models\Hosting::class . ':' . $hosting->id] = $hosting->label();
        }

        foreach ($client->domains as $domain) {
            $options[\App\Models\ClientDomain::class . ':' . $domain->id] = $domain->label();
        }

        foreach ($client->services as $service) {
            $options[\App\Models\ClientService::class . ':' . $service->id] = 'Servicio: ' . $service->service_type->label();
        }

        return $options;
    }

    /**
     * @return array{0: ?string, 1: ?int}
     */
    private function parseRelated($user, ?string $related): array
    {
        if (! $related || ! str_contains($related, ':')) {
            return [null, null];
        }

        [$type, $id] = explode(':', $related, 2);

        if (! array_key_exists($type . ':' . $id, $this->relatedOptions($user))) {
            return [null, null];
        }

        return [$type, (int) $id];
    }

    public function edit(Ticket $ticket): View
    {
        // Enterprise authorization check - client access and ticket update permission
        Gate::authorize('client-access');
        $this->authorize('update', $ticket);

        $user = $this->getUser();

        return view('cliente.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        // Enterprise authorization check - client access and ticket update permission
        Gate::authorize('client-access');
        $this->authorize('update', $ticket);

        $user = $this->getUser();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:baja,media,alta,critica',
            'category' => 'required|in:tecnico,facturacion,configuracion,otro',
        ]);

        $ticket->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'category' => $request->category,
        ]);

        return redirect()->route('cliente.tickets.show', $ticket)
            ->with('success', 'Ticket actualizado exitosamente.');
    }

    /**
     * Cerrar ticket (cliente puede cerrar sus propios tickets)
     */
    public function close(Ticket $ticket): RedirectResponse
    {
        // Enterprise authorization check - client access and ticket close permission
        Gate::authorize('client-access');
        $this->authorize('close', $ticket);

        $user = $this->getUser();

        $ticket->update(['status' => 'cerrado']);

        return redirect()->route('cliente.tickets.index')
            ->with('success', 'Ticket cerrado exitosamente.');
    }
}