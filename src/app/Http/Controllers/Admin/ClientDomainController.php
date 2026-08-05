<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DomainStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ClientDomainController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $this->validated($request);

        $client->domains()->create($validated);

        return back()->with('success', 'Dominio agregado.');
    }

    public function update(Request $request, Client $client, ClientDomain $domain): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $domain->update($this->validated($request));

        return back()->with('success', 'Dominio actualizado.');
    }

    public function destroy(Client $client, ClientDomain $domain): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $domain->delete();

        return back()->with('success', 'Dominio eliminado.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'domain_name'    => 'required|string|max:255',
            'status'         => ['required', Rule::in(array_column(DomainStatus::cases(), 'value'))],
            'registrar'      => 'nullable|string|max:255',
            'dns_provider'   => 'nullable|string|max:255',
            'account_holder' => 'nullable|string|max:255',
            'account_email'  => 'nullable|email|max:255',
            'registered_at'  => 'nullable|date',
            'expires_at'     => 'nullable|date',
            'renewal_payer'  => 'required|in:cliente,arioli,tercero',
            'provider_cost'  => 'nullable|numeric|min:0',
            'management_fee' => 'nullable|numeric|min:0',
        ]);

        return array_merge($validated, [
            'is_primary' => $request->boolean('is_primary'),
            'auto_renew' => $request->boolean('auto_renew'),
        ]);
    }
}
