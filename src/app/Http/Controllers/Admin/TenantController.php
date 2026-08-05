<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FindOrCreateClientAction;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Tenant;

class TenantController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // Enterprise authorization check - only admins can view all tenants
        Gate::authorize('admin-access');
        $this->authorize('viewAny', Tenant::class);

        $search = $request->query('search', '');

        $query = Tenant::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.name')) like ?", ["%{$search}%"])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.email')) like ?", ["%{$search}%"]);
            });
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();

        $domains = \Stancl\Tenancy\Database\Models\Domain::whereIn(
            'tenant_id', $tenants->pluck('id')
        )->get()->groupBy('tenant_id');

        $totalTenants   = Tenant::count();
        $activeLicenses = \App\Models\License::where('active', true)
                    ->where('expires_at', '>=', now())->count();
                    
        $monthlyNew     = Tenant::where('created_at', '>=', now()->startOfMonth())->count();

        if ($request->ajax()) {
            return response()->json([
                'tableBody'  => view('admin.tenants.partials.tenant-table-body',
                    compact('tenants', 'domains', 'search'))->render(),
                'pagination' => view('admin.tenants.partials.tenant-pagination',
                    compact('tenants', 'search'))->render(),
                'total'      => $tenants->total(),
            ]);
        }

        return view('admin.tenants.index',
            compact('tenants', 'domains', 'search', 'totalTenants', 'activeLicenses', 'monthlyNew'));
    }

    public function create(Request $request)
    {
        // Enterprise authorization check - only admins can create tenants
        Gate::authorize('admin-access');
        $this->authorize('create', Tenant::class);

        $plans = Plan::assignableByAdmin()
            ->with('product')
            ->orderBy('product_id')
            ->orderBy('period_months')
            ->get();

        $clients = \App\Models\Client::orderBy('name')->get();

        $selectedClient = $request->filled('client_id')
            ? \App\Models\Client::find($request->integer('client_id'))
            : null;

        return view('admin.tenants.create', compact('plans', 'clients', 'selectedClient'));
    }

    public function store(Request $request, \App\Services\Contracts\LicenseContractService $licenseContracts, FindOrCreateClientAction $findOrCreateClient)
    {
        // Enterprise authorization check - only admins can create tenants
        Gate::authorize('admin-access');
        $this->authorize('create', Tenant::class);

        $baseDomain = config('app.tenant_domain');

        // Resolve product domain prefix from the selected plan — assignableByAdmin() acá es la
        // protección real: un plan_id de un plan Demo (reservado al tenant "demo") nunca matchea,
        // sin importar qué se mande en el POST. Sí permite planes "indefinida" (is_perpetual).
        $plan = Plan::assignableByAdmin()->with('product')->find($request->input('plan_id'));
        $productDomain = $plan?->product?->public_domain;
        $tenantDomain  = $productDomain
            ? "{$productDomain}.{$baseDomain}"
            : $baseDomain;

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'subdomain' => [
                'required', 'string', 'max:63', 'alpha_dash',
                'unique:tenants,id',
                function ($attribute, $value, $fail) use ($tenantDomain) {
                    if (\Stancl\Tenancy\Database\Models\Domain::where('domain', $value . '.' . $tenantDomain)->exists()) {
                        $fail('Este subdominio ya está en uso.');
                    }
                },
            ],
            'plan_id' => [
                'required',
                function ($attribute, $value, $fail) use ($plan) {
                    if (! $plan || (int) $plan->id !== (int) $value) {
                        $fail('El plan seleccionado no es válido para dar de alta un cliente nuevo.');
                    }
                },
            ],
            'email'                => 'required|email|unique:users,email',
            'password'             => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'client_id'            => 'nullable|integer|exists:clients,id',
        ]);

        $tenant = null;

        try {
            $tenant = Tenant::create([
                'id'    => $validated['subdomain'],
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ]);

            \Stancl\Tenancy\Database\Models\Domain::create([
                'domain'    => $validated['subdomain'] . '.' . $tenantDomain,
                'tenant_id' => $tenant->id,
            ]);

            // Si se eligió un Client existente (ej. desde su ficha), usarlo tal cual —
            // evita que FindOrCreateClientAction cree un duplicado cuando el email no
            // matchea ningún contacto ya cargado. Sin selección explícita, mantiene el
            // comportamiento histórico de detectar/crear por email.
            $client = ! empty($validated['client_id'])
                ? \App\Models\Client::find($validated['client_id'])
                : $findOrCreateClient->execute($validated['name'], $validated['email']);

            $license = \App\Models\License::create([
                'tenant_id'  => $tenant->id,
                'client_id'  => $client->id,
                'plan_id'    => $validated['plan_id'],
                'active'     => true,
                'starts_at'  => now(),
                'expires_at' => $plan->is_perpetual ? null : now()->addMonths($plan->period_months),
            ]);

            $licenseContracts->generateForLicense($license, $client);

            \App\Models\User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => $validated['password'],
                'tenant_id' => $tenant->id,
            ]);

            // Provision the product-specific database (async via Horizon)
            $jobClass = match($plan->product->slug) {
                'loteos'             => \App\Jobs\ProvisionLoteosInstance::class,
                'historias-clinicas' => \App\Jobs\ProvisionHistoriasInstance::class,
                'tallerpro'          => \App\Jobs\ProvisionTallerProInstance::class,
                'chatia'             => \App\Jobs\ProvisionChatiaInstance::class,
                default              => null,
            };

            if ($jobClass) {
                $jobClass::dispatch(
                    $tenant->id,
                    $plan->product->slug,
                    $plan->product->public_domain . '.' . $baseDomain,
                    $validated['name'],
                    $validated['email'],
                    $validated['password'],
                );
            }

        } catch (\Throwable $e) {
            if ($tenant) {
                \App\Models\User::where('tenant_id', $tenant->id)->delete();
                \Stancl\Tenancy\Database\Models\Domain::where('tenant_id', $tenant->id)->delete();
                \App\Models\License::where('tenant_id', $tenant->id)->delete();
                $tenant->delete();
            }
            return back()->withInput()->withErrors(['subdomain' => 'Error al crear el cliente: ' . $e->getMessage()]);
        }

        if (! empty($validated['client_id'])) {
            return redirect()->route('clients.show', ['client' => $validated['client_id'], 'tab' => 'sistemas'])
                ->with('success', 'Sistema creado correctamente.');
        }

        return redirect()->route('tenants.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(string $id)
    {
        $tenant = Tenant::findOrFail($id);

        // Enterprise authorization check - admin access and specific tenant view
        Gate::authorize('admin-access');
        $this->authorize('view', $tenant);

        $domain = \Stancl\Tenancy\Database\Models\Domain::where('tenant_id', $id)->first();

        return view('admin.tenants.show', compact('tenant', 'domain'));
    }

    public function edit(string $id)
    {
        $tenant = Tenant::findOrFail($id);

        // Enterprise authorization check - admin access and tenant update permission
        Gate::authorize('admin-access');
        $this->authorize('update', $tenant);
        $domain = \Stancl\Tenancy\Database\Models\Domain::where('tenant_id', $id)->first();
        $plans  = Plan::where('active', true)->get();

        return view('admin.tenants.edit', compact('tenant', 'domain', 'plans'));
    }

    public function update(Request $request, string $id)
    {
        $tenant = Tenant::findOrFail($id);

        // Enterprise authorization check - admin access and tenant update permission
        Gate::authorize('admin-access');
        $this->authorize('update', $tenant);

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email',
            'is_custom' => 'nullable|boolean',
        ]);

        $tenant->name      = $validated['name'];
        $tenant->email     = $validated['email'];
        $tenant->is_custom = $request->boolean('is_custom');
        $tenant->save();

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $tenant = Tenant::findOrFail($id);

        // Enterprise authorization check - admin access and tenant deletion permission
        Gate::authorize('admin-access');
        $this->authorize('delete', $tenant);

        // Resolve product databases to drop BEFORE deleting licenses
        $dbsToDrop = \App\Models\License::where('tenant_id', $id)
            ->with('plan.product')
            ->get()
            ->map(fn($license) => $this->productDbName($license->plan?->product?->slug, $id))
            ->filter()
            ->unique()
            ->values();

        // Delete related central-app records
        \App\Models\User::where('tenant_id', $id)->delete();
        \Stancl\Tenancy\Database\Models\Domain::where('tenant_id', $id)->delete();
        \App\Models\License::where('tenant_id', $id)->delete();

        $tenant->delete();

        // Drop each product database
        foreach ($dbsToDrop as $dbName) {
            try {
                \Illuminate\Support\Facades\DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
                \Illuminate\Support\Facades\Log::info("Dropped tenant database: {$dbName}");
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Could not drop database {$dbName}: " . $e->getMessage());
            }
        }

        return redirect()->route('tenants.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }

    private function productDbName(?string $slug, string $tenantId): ?string
    {
        return match($slug) {
            'historias-clinicas' => "historias_{$tenantId}",
            'loteos'             => "loteos_{$tenantId}",
            'tallerpro'          => "tallerpro_{$tenantId}",
            'chatia'             => "chatia_{$tenantId}",
            default              => null,
        };
    }
}