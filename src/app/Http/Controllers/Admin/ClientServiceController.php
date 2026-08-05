<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BillingCycle;
use App\Enums\ClientEventType;
use App\Enums\ServiceType;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ClientServiceController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $this->validated($request, $client);

        $service = $client->services()->create($validated);

        ClientEvent::log($client, "Se creó el servicio \"{$service->service_type->label()}\"", ClientEventType::Service, $service);

        return back()->with('success', 'Servicio agregado.');
    }

    public function update(Request $request, Client $client, ClientService $service): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $service->update($this->validated($request, $client));

        return back()->with('success', 'Servicio actualizado.');
    }

    public function destroy(Client $client, ClientService $service): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $service->delete();

        return back()->with('success', 'Servicio eliminado.');
    }

    private function validated(Request $request, Client $client): array
    {
        $validated = $request->validate([
            'project_id'    => 'nullable|exists:projects,id',
            'service_type'  => ['required', Rule::in(array_column(ServiceType::cases(), 'value'))],
            'billing_cycle' => ['required', Rule::in(array_column(BillingCycle::cases(), 'value'))],
            'amount'        => 'required|numeric|min:0',
            'starts_at'     => 'nullable|date',
            'ends_at'       => 'nullable|date',
            'status'        => 'required|in:active,cancelled,completed',
        ]);

        $autoMaintenance = $request->boolean('auto_maintenance_hestia');

        if ($autoMaintenance) {
            $project = $validated['project_id'] ? $client->projects()->find($validated['project_id']) : null;

            if (! $project?->hosting?->account || $project->hosting->account->provider !== 'hestiacp') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'auto_maintenance_hestia' => 'El backup automático necesita un proyecto con hosting HestiaCP vinculado — elegí el proyecto correcto o desmarcá esta opción.',
                ]);
            }
        }

        return array_merge($validated, [
            'auto_renew'              => $request->boolean('auto_renew'),
            'auto_maintenance_hestia' => $autoMaintenance,
        ]);
    }
}
