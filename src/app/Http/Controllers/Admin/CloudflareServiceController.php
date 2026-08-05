<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CloudflareServiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CloudflareService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CloudflareServiceController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $client->cloudflareServices()->create($this->validated($request));

        return back()->with('success', 'Servicio Cloudflare agregado.');
    }

    public function update(Request $request, Client $client, CloudflareService $cloudflareService): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $cloudflareService->update($this->validated($request));

        return back()->with('success', 'Servicio Cloudflare actualizado.');
    }

    public function destroy(Client $client, CloudflareService $cloudflareService): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $cloudflareService->delete();

        return back()->with('success', 'Servicio Cloudflare eliminado.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'managed_by'     => 'nullable|string|max:255',
            'status'         => ['required', Rule::in(array_column(CloudflareServiceStatus::cases(), 'value'))],
            'expires_at'     => 'nullable|date',
            'renewal_payer'  => 'required|in:cliente,arioli,tercero',
            'provider_cost'  => 'nullable|numeric|min:0',
            'management_fee' => 'nullable|numeric|min:0',
        ]);

        return array_merge($validated, [
            'provider'   => 'Cloudflare',
            'auto_renew' => $request->boolean('auto_renew'),
        ]);
    }
}
