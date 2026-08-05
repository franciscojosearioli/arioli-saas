<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CredentialOwner;
use App\Enums\CredentialType;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDomain;
use App\Models\CloudflareService;
use App\Models\Credential;
use App\Models\Hosting;
use App\Models\Project;
use App\Models\SslCertificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CredentialController extends Controller
{
    private const CREDENTIALABLE_TYPES = [
        'client'     => Client::class,
        'domain'     => ClientDomain::class,
        'hosting'    => Hosting::class,
        'project'    => Project::class,
        'ssl'        => SslCertificate::class,
        'cloudflare' => CloudflareService::class,
    ];

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'credentialable_type' => 'required|in:client,domain,hosting,project,ssl,cloudflare',
            'credentialable_id'   => 'required|integer',
            'type'                => ['required', Rule::in(array_column(CredentialType::cases(), 'value'))],
            'label'               => 'nullable|string|max:255',
            'username'            => 'nullable|string|max:255',
            'secret'              => 'required|string',
            'url'                 => 'nullable|string|max:255',
            'owner'               => ['required', Rule::in(array_column(CredentialOwner::cases(), 'value'))],
        ]);

        $modelClass = self::CREDENTIALABLE_TYPES[$validated['credentialable_type']];
        $credentialable = $modelClass::findOrFail($validated['credentialable_id']);

        Credential::create([
            'credentialable_type' => $modelClass,
            'credentialable_id'   => $credentialable->id,
            'type'                => $validated['type'],
            'label'               => $validated['label'] ?? null,
            'username'            => $validated['username'] ?? null,
            'secret'              => $validated['secret'],
            'url'                 => $validated['url'] ?? null,
            'owner'               => $validated['owner'],
        ]);

        return back()->with('success', 'Credencial agregada.');
    }

    public function update(Request $request, Credential $credential): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'type'     => ['required', Rule::in(array_column(CredentialType::cases(), 'value'))],
            'label'    => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'secret'   => 'nullable|string',
            'url'      => 'nullable|string|max:255',
            'owner'    => ['required', Rule::in(array_column(CredentialOwner::cases(), 'value'))],
        ]);

        // El secreto solo se actualiza si se mandó uno nuevo — dejarlo en blanco
        // conserva el valor cifrado existente, igual que un campo de contraseña.
        if (empty($validated['secret'])) {
            unset($validated['secret']);
        }

        $credential->update($validated);

        return back()->with('success', 'Credencial actualizada.');
    }

    public function markVerified(Credential $credential): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $credential->markVerified();

        return back()->with('success', 'Credencial marcada como verificada.');
    }

    /**
     * El secreto nunca se renderiza inline en el HTML — se pide bajo
     * demanda vía fetch(), así el código fuente de la página no lo contiene
     * apenas se carga.
     */
    public function reveal(Credential $credential): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('manage-clients');

        return response()->json(['secret' => $credential->secret]);
    }

    public function destroy(Credential $credential): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $credential->delete();

        return back()->with('success', 'Credencial eliminada.');
    }
}
