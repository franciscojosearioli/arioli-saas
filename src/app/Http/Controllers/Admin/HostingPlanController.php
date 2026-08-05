<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HostingPlanController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-clients');

        $plans = HostingPlan::withCount('hostings')->latest()->paginate(15);

        return view('admin.hosting-plans.index', compact('plans'));
    }

    public function create()
    {
        Gate::authorize('manage-clients');

        return view('admin.hosting-plans.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-clients');

        $validated = $this->validated($request);

        HostingPlan::create([
            ...$validated,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('hosting-plans.index')->with('success', 'Plan de hosting creado correctamente.');
    }

    public function edit(HostingPlan $hostingPlan)
    {
        Gate::authorize('manage-clients');

        return view('admin.hosting-plans.edit', ['plan' => $hostingPlan]);
    }

    public function update(Request $request, HostingPlan $hostingPlan)
    {
        Gate::authorize('manage-clients');

        $validated = $this->validated($request);

        $hostingPlan->update([
            ...$validated,
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('hosting-plans.index')->with('success', 'Plan de hosting actualizado correctamente.');
    }

    public function destroy(HostingPlan $hostingPlan)
    {
        Gate::authorize('manage-clients');

        if ($hostingPlan->hostings()->count() > 0) {
            return redirect()->route('hosting-plans.index')->with('error', 'No se puede eliminar un plan con hostings asociados.');
        }

        $hostingPlan->delete();

        return redirect()->route('hosting-plans.index')->with('success', 'Plan de hosting eliminado correctamente.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'marketing_description'  => 'nullable|string|max:255',
            'price'                  => 'required|numeric|min:0',
            'currency'               => 'required|in:ARS,USD,EUR',
            'billing_cycle'          => 'required|in:mensual,anual,unico',
            'hestia_package'         => 'nullable|string|max:255',
            'specs'                  => 'nullable|array',
            'specs.*.key'            => 'nullable|string|max:100',
            'specs.*.value'          => 'nullable|string|max:100',
        ]);

        $specsJson = collect($request->input('specs', []))
            ->filter(fn ($row) => filled($row['key'] ?? null))
            ->mapWithKeys(fn ($row) => [$row['key'] => $row['value'] ?? null])
            ->all();

        return [
            'name'                  => $validated['name'],
            'marketing_description' => $validated['marketing_description'] ?? null,
            'price'                 => $validated['price'],
            'currency'              => $validated['currency'],
            'billing_cycle'         => $validated['billing_cycle'],
            'hestia_package'        => $validated['hestia_package'] ?? null,
            'specs_json'            => $specsJson ?: null,
        ];
    }
}
