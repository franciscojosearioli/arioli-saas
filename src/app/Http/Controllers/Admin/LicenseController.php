<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Stancl\Tenancy\Database\Models\Tenant;

class LicenseController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // Enterprise authorization check - only admins can view all licenses
        Gate::authorize('admin-access');
        Gate::authorize('manage-licenses');
        $this->authorize('viewAny', License::class);

        $search = $request->query('search', '');

        $query = License::with(['plan', 'tenant'])->notDemo()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tenant_id', 'like', "%{$search}%")
                ->orWhereHas('plan', fn($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $licenses = $query->paginate(15)->withQueryString();

        $totalLicenses   = License::notDemo()->count();
        $activeLicenses  = License::notDemo()->where('active', true)->where('expires_at', '>=', now())->count();
        $expiredLicenses = License::notDemo()->where('expires_at', '<', now())->count();

        if ($request->ajax()) {
            return response()->json([
                'tableBody'  => view('admin.licenses.partials.license-table-body',
                    compact('licenses', 'search'))->render(),
                'pagination' => view('admin.licenses.partials.license-pagination',
                    compact('licenses', 'search'))->render(),
                'total'      => $licenses->total(),
            ]);
        }

        return view('admin.licenses.index',
            compact('licenses', 'search', 'totalLicenses', 'activeLicenses', 'expiredLicenses'));
    }

    public function create()
    {
        // Enterprise authorization check - only admins can create licenses
        Gate::authorize('admin-access');
        Gate::authorize('manage-licenses');
        $this->authorize('create', License::class);

        $plans   = Plan::assignableByAdmin()->get();
        $tenants = Tenant::all();

        return view('admin.licenses.create', compact('plans', 'tenants'));
    }

    public function store(Request $request)
    {
        // Enterprise authorization check - only admins can create licenses
        Gate::authorize('admin-access');
        Gate::authorize('manage-licenses');
        $this->authorize('create', License::class);

        $validated = $request->validate([
            'tenant_id'  => 'required|exists:tenants,id',
            // assignableByAdmin() = nunca un plan Demo — esos son solo para el tenant "demo" y
            // se gestionan por su propio flujo (DemoController/ProvisionDemoInstance). Sí
            // permite planes "indefinida" (is_perpetual).
            'plan_id'    => 'required|exists:plans,id',
            'starts_at'  => 'required|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'active'     => 'boolean',
        ]);

        $plan = Plan::assignableByAdmin()->whereKey($validated['plan_id'])->first();

        if (! $plan) {
            return back()->withErrors(['plan_id' => 'Ese plan no está disponible para asignar a una licencia nueva.'])->withInput();
        }

        if (! $plan->is_perpetual && ! $validated['expires_at']) {
            return back()->withErrors(['expires_at' => 'La fecha de vencimiento es obligatoria para un plan que no es indefinido.'])->withInput();
        }

        // Hereda el dueño (client_id) de otra licencia ya existente de este mismo
        // tenant — sin esto, una segunda licencia sobre un tenant existente quedaba
        // huérfana (invisible en la ficha del cliente, ver plan de reorganización).
        $clientId = License::where('tenant_id', $validated['tenant_id'])
            ->whereNotNull('client_id')
            ->value('client_id');

        License::create([
            'tenant_id'  => $validated['tenant_id'],
            'client_id'  => $clientId,
            'plan_id'    => $validated['plan_id'],
            'starts_at'  => $validated['starts_at'],
            // Una licencia indefinida nunca vence, sin importar qué venga en el POST.
            'expires_at' => $plan->is_perpetual ? null : $validated['expires_at'],
            'active'     => $request->boolean('active', true),
        ]);

        return redirect()->route('licenses.index')
            ->with('success', 'Licencia creada correctamente.');
    }

    public function show(string $id)
    {
        $license = License::with('plan.product')->findOrFail($id);

        Gate::authorize('admin-access');
        Gate::authorize('manage-licenses');
        $this->authorize('view', $license);
        $tenant = Tenant::find($license->tenant_id);

        $productSlug     = $license->plan?->product?->slug;
        $demoCredentials = $license->isDemo() && $productSlug
            ? config("demo.credentials.{$productSlug}", [])
            : [];

        return view('admin.licenses.show', compact('license', 'tenant', 'demoCredentials', 'productSlug'));
    }

    public function resetDemo(Request $request, string $id)
    {
        $license = License::with('plan.product')->findOrFail($id);

        Gate::authorize('admin-access');
        Gate::authorize('manage-licenses');

        if (!$license->isDemo()) {
            return back()->with('error', 'Solo se puede resetear una licencia de tipo demo.');
        }

        $slug = $license->plan?->product?->slug;
        if (!$slug) {
            return back()->with('error', 'No se pudo determinar el producto asociado a esta licencia.');
        }

        \App\Jobs\ResetDemoInstance::dispatch($slug);

        return back()->with('success', "Reset de demo '{$slug}' encolado. Los datos se restaurarán en breve.");
    }

    public function edit(string $id)
    {
        $license = License::findOrFail($id);

        // Enterprise authorization check - admin access and license update permission
        Gate::authorize('admin-access');
        Gate::authorize('manage-licenses');
        $this->authorize('update', $license);
        // El plan actual se incluye aunque sea Demo, para no romper la edición de una
        // licencia demo ya existente — pero no se puede CAMBIAR a un plan Demo distinto
        // (eso se valida en update()). Agrupado explícitamente para no depender de la
        // precedencia AND/OR de SQL.
        $plans = Plan::query()
            ->where(fn ($q) => $q->assignableByAdmin())
            ->orWhere('id', $license->plan_id)
            ->get();
        $tenants = Tenant::all();

        return view('admin.licenses.edit', compact('license', 'plans', 'tenants'));
    }

    public function update(Request $request, string $id)
    {
        $license = License::findOrFail($id);

        // Enterprise authorization check - admin access and license update permission
        Gate::authorize('admin-access');
        Gate::authorize('manage-licenses');
        $this->authorize('update', $license);

        $validated = $request->validate([
            'plan_id'    => 'required|exists:plans,id',
            'starts_at'  => 'required|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'active'     => 'boolean',
        ]);

        $planUnchanged = (int) $validated['plan_id'] === (int) $license->plan_id;

        if (! $planUnchanged && ! Plan::assignableByAdmin()->whereKey($validated['plan_id'])->exists()) {
            return back()->withErrors(['plan_id' => 'Ese plan no está disponible para asignar a esta licencia.'])->withInput();
        }

        $plan = Plan::find($validated['plan_id']);

        if (! $plan->is_perpetual && ! $validated['expires_at']) {
            return back()->withErrors(['expires_at' => 'La fecha de vencimiento es obligatoria para un plan que no es indefinido.'])->withInput();
        }

        $license->update([
            'plan_id'    => $validated['plan_id'],
            'starts_at'  => $validated['starts_at'],
            'expires_at' => $plan->is_perpetual ? null : $validated['expires_at'],
            'active'     => $request->boolean('active'),
        ]);

        return redirect()->route('licenses.index')
            ->with('success', 'Licencia actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        $license = License::findOrFail($id);

        // Enterprise authorization check - admin access and license deletion permission
        Gate::authorize('admin-access');
        Gate::authorize('manage-licenses');
        $this->authorize('delete', $license);
        $license->delete();

        return redirect()->route('licenses.index')
            ->with('success', 'Licencia eliminada correctamente.');
    }
}