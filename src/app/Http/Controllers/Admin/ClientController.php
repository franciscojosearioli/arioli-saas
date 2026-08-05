<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClientEventType;
use App\Enums\CommercialStatus;
use App\Enums\Priority;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Services\Clients\ClientEconomicSummaryService;
use App\Services\Clients\ClientHealthScoreService;
use App\Services\Clients\ClientPendingChargesSummaryService;
use App\Services\Clients\ClientUpcomingRenewalsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-clients');

        $search = $request->query('search', '');
        $status = $request->query('status', '');

        $query = Client::withCount(['projects', 'services', 'licenses'])->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('commercial_status', $status);
        }

        $clients = $query->paginate(15)->withQueryString();

        return view('admin.clients.index', compact('clients', 'search', 'status'));
    }

    public function create()
    {
        Gate::authorize('manage-clients');

        return view('admin.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'commercial_status'   => ['required', Rule::in(array_column(CommercialStatus::cases(), 'value'))],
            'priority'            => ['required', Rule::in(array_column(Priority::cases(), 'value'))],
            'contact_name'        => 'nullable|string|max:255',
            'contact_email'       => 'nullable|email|max:255',
            'contact_phone'       => 'nullable|string|max:50',
        ]);

        $client = Client::create([
            'name'               => $validated['name'],
            'commercial_status'  => $validated['commercial_status'],
            'priority'           => $validated['priority'],
            'slug'               => $this->uniqueSlug($validated['name']),
        ]);

        if (!empty($validated['contact_name'])) {
            $client->contacts()->create([
                'name'       => $validated['contact_name'],
                'email'      => $validated['contact_email'] ?? null,
                'phone'      => $validated['contact_phone'] ?? null,
                'role'       => 'dueño',
                'is_primary' => true,
            ]);
        }

        ClientEvent::log($client, 'Cliente creado', ClientEventType::Created);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Request $request, Client $client)
    {
        Gate::authorize('manage-clients');

        $client->load([
            'contacts',
            'domains.credentials',
            'hostings.credentials',
            'hostings.hostingPlan',
            'hostings.account.credentials',
            'sslCertificates',
            'cloudflareServices',
            'licenses.plan.product',
            'projects.domain',
            'projects.hosting',
            'projects.sslCertificate',
            'projects.cloudflareService',
            'projects.license.plan.product',
            'projects.repositories',
            'projects.technologies',
            'projects.services',
            'projects.tasks',
            'services.project',
            'jobs.project',
            'jobs.charges',
            'jobs.timeEntries',
            'contracts',
            'quotes.items',
            'charges' => fn ($q) => $q->latest(),
            'charges.timeEntries',
            'charges.invoice',
            'charges.payments.createdBy',
            'charges.installments',
            'integrations',
            'events.user',
            'notes.user',
            'documents.user',
            'credentials',
            'tags',
            'tasks' => fn ($q) => $q->orderBy('status')->orderBy('due_date'),
            'portalUsers',
        ]);

        // La credencial de HestiaCP queda intencionalmente en el HostingAccount, no en el
        // Hosting (la usa HostingCredentialController::claim() para mantenerla sincronizada
        // cuando el cliente define su propia contraseña) — pero la ficha del cliente muestra
        // las credenciales del Hosting, así que se combinan acá solo para mostrarlas juntas.
        $client->hostings->each(function ($hosting) {
            if ($hosting->account) {
                $hosting->setRelation('credentials', $hosting->credentials->merge($hosting->account->credentials));
            }
        });

        $healthScore = (new ClientHealthScoreService())->calculate($client);
        $economicSummary = (new ClientEconomicSummaryService())->calculate($client);
        $upcomingRenewals = (new ClientUpcomingRenewalsService())->calculate($client);
        $pendingChargesSummary = (new ClientPendingChargesSummaryService())->calculate($client);

        $tabs = [
            'resumen'         => 'Resumen',
            'proyectos'       => 'Proyectos',
            'sistemas'        => 'Sistemas y licencias',
            'servicios'       => 'Servicios',
            'contable'        => 'Contable',
            'contactos'       => 'Contactos',
            'infraestructura' => 'Infraestructura',
            'propuestas'      => 'Propuestas',
            'documentacion'   => 'Documentación',
            'credenciales'    => 'Credenciales',
        ];
        $activeTab = array_key_exists($request->query('tab'), $tabs) ? $request->query('tab') : 'resumen';

        return view('admin.clients.show', compact(
            'client', 'healthScore', 'economicSummary', 'upcomingRenewals', 'pendingChargesSummary',
            'tabs', 'activeTab'
        ));
    }

    public function edit(Client $client)
    {
        Gate::authorize('manage-clients');

        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Entra al Panel de Cliente (cliente.arioli.dev) como el usuario de
     * portal de este cliente — el login real tiene que pasar en un request
     * recibido POR ESE dominio (no en admin.arioli.dev) para que la cookie
     * de sesión quede seteada ahí; por eso se arma una URL firmada de un
     * solo uso hacia routes/cliente.php en vez de loguear acá mismo.
     */
    public function impersonate(Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $portalUser = $client->portalUsers()->first();

        if (! $portalUser) {
            return back()->with('error', 'Este cliente todavía no tiene un usuario de portal creado — creá uno primero en "Usuarios".');
        }

        Gate::authorize('impersonate', $portalUser);

        $signedUrl = URL::temporarySignedRoute(
            'cliente.impersonate.enter',
            now()->addMinutes(2),
            ['user' => $portalUser->id],
        );

        ClientEvent::log($client, "Admin entró al portal como {$portalUser->name} ({$portalUser->email})", ClientEventType::Note);

        return redirect()->away($signedUrl);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'cuit'                  => 'nullable|string|max:20',
            'condicion_iva'         => 'nullable|string|max:100',
            'commercial_status'     => ['required', Rule::in(array_column(CommercialStatus::cases(), 'value'))],
            'priority'              => ['required', Rule::in(array_column(Priority::cases(), 'value'))],
            'logo'                  => 'nullable|image|max:2048',
            'cover_image_file'      => 'nullable|image|max:4096',
            'category'              => 'nullable|string|max:100',
            'short_description'     => 'nullable|string|max:1000',
            'challenge'             => 'nullable|string|max:1000',
            'solution'              => 'nullable|string|max:1000',
            'results'               => 'nullable|string|max:1000',
            'testimonial_quote'     => 'nullable|string|max:500',
            'testimonial_author'    => 'nullable|string|max:255',
            'testimonial_position'  => 'nullable|string|max:255',
            'display_order'         => 'nullable|integer',
            'show_on_landing'       => 'boolean',
        ]);

        $validated['show_on_landing'] = $request->boolean('show_on_landing');

        if (empty($client->slug)) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $client->id);
        }

        if ($request->hasFile('logo')) {
            if ($client->logo_path) {
                Storage::disk('public')->delete($client->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('clients/logos', 'public');
        }
        unset($validated['logo']);

        if ($request->hasFile('cover_image_file')) {
            if ($client->cover_image) {
                Storage::disk('public')->delete($client->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image_file')->store('clients/covers', 'public');
        }
        unset($validated['cover_image_file']);

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        if ($client->logo_path) {
            Storage::disk('public')->delete($client->logo_path);
        }
        if ($client->cover_image) {
            Storage::disk('public')->delete($client->cover_image);
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (Client::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
