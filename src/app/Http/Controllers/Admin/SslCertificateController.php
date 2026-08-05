<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SslCertificateStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDomain;
use App\Models\SslCertificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SslCertificateController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $domainId = $request->integer('domain_id') ?: null;

        $ssl = $client->sslCertificates()->create($this->validated($request));

        // El SSL conceptualmente va con el dominio (no es un servicio aparte) —
        // si el cliente ya tiene un Project para ese dominio, se lo vincula ahí
        // para que se vea junto al resto. Nunca se crea un Project nuevo solo
        // por esto: el admin arma sus Projects a mano, no como efecto secundario
        // de cargar un certificado.
        if ($domainId) {
            $domain = $client->domains()->findOrFail($domainId);
            $domain->projects()->first()?->update(['ssl_certificate_id' => $ssl->id]);
        }

        return back()->with('success', 'Certificado SSL agregado.');
    }

    public function update(Request $request, Client $client, SslCertificate $sslCertificate): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $sslCertificate->update($this->validated($request));

        return back()->with('success', 'Certificado SSL actualizado.');
    }

    public function destroy(Client $client, SslCertificate $sslCertificate): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $sslCertificate->delete();

        return back()->with('success', 'Certificado SSL eliminado.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'provider'       => 'required|string|max:255',
            'status'         => ['required', Rule::in(array_column(SslCertificateStatus::cases(), 'value'))],
            'expires_at'     => 'nullable|date',
            'renewal_payer'  => 'required|in:cliente,arioli,tercero',
            'provider_cost'  => 'nullable|numeric|min:0',
            'management_fee' => 'nullable|numeric|min:0',
        ]);

        return array_merge($validated, [
            'auto_renew' => $request->boolean('auto_renew'),
        ]);
    }
}
