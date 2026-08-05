<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClientEventType;
use App\Enums\HostingStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\Hosting;
use App\Services\Dns\DnsProviderManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class HostingController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $client->hostings()->create($this->validated($request));

        return back()->with('success', 'Hosting agregado.');
    }

    public function update(Request $request, Client $client, Hosting $hosting): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $hosting->update($this->validated($request));

        return back()->with('success', 'Hosting actualizado.');
    }

    public function destroy(Client $client, Hosting $hosting): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $hosting->delete();

        return back()->with('success', 'Hosting eliminado.');
    }

    /**
     * Configura todo el DNS necesario para que el dominio use este hosting
     * (web + mail): A de raíz/www/mail apuntando al server, MX y SPF — solo
     * tiene sentido para dominios administrados en Porkbun (única API de DNS
     * integrada hoy). Para otros proveedores el admin lo carga a mano con la
     * checklist que se muestra igual en esta pantalla.
     */
    public function pointDns(Client $client, Hosting $hosting): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $domain = $hosting->domain;

        abort_if(! $domain || $domain->dns_provider !== 'porkbun', 404);

        $serverIp = config('hosting_panel.hestiacp.server_ip');
        $driver = DnsProviderManager::driver('porkbun');
        $domainName = $domain->domain_name;

        $existing = $driver->listDnsRecords($domainName);
        $records = $existing['success'] ? collect($existing['records']) : collect();
        $errors = [];

        $upsertA = function (string $name, string $label) use ($driver, $domainName, $records, $serverIp, &$errors) {
            $fullName = $name === '' ? $domainName : "{$name}.{$domainName}";
            $current = $records->first(fn ($r) => $r['type'] === 'A' && $r['name'] === $fullName);

            $result = $current
                ? $driver->updateDnsRecord($domainName, $current['id'], ['type' => 'A', 'name' => $name, 'content' => $serverIp])
                : $driver->createDnsRecord($domainName, ['type' => 'A', 'name' => $name, 'content' => $serverIp]);

            if (! $result['success']) {
                $errors[] = "{$label}: {$result['message']}";
            }
        };

        $upsertA('', 'raíz');
        $upsertA('www', 'www');
        $upsertA('mail', 'mail');

        $currentMx = $records->first(fn ($r) => $r['type'] === 'MX');
        $mxResult = $currentMx
            ? $driver->updateDnsRecord($domainName, $currentMx['id'], ['type' => 'MX', 'name' => '', 'content' => "mail.{$domainName}", 'prio' => 10])
            : $driver->createDnsRecord($domainName, ['type' => 'MX', 'name' => '', 'content' => "mail.{$domainName}", 'prio' => 10]);

        if (! $mxResult['success']) {
            $errors[] = "MX: {$mxResult['message']}";
        }

        $currentSpf = $records->first(fn ($r) => $r['type'] === 'TXT' && str_contains($r['content'], 'v=spf1'));
        $spfContent = 'v=spf1 +a +mx ~all';
        $spfResult = $currentSpf
            ? $driver->updateDnsRecord($domainName, $currentSpf['id'], ['type' => 'TXT', 'name' => '', 'content' => $spfContent])
            : $driver->createDnsRecord($domainName, ['type' => 'TXT', 'name' => '', 'content' => $spfContent]);

        if (! $spfResult['success']) {
            $errors[] = "SPF: {$spfResult['message']}";
        }

        if ($errors) {
            return back()->with('error', 'Algunos registros no se pudieron actualizar — ' . implode(' | ', $errors));
        }

        ClientEvent::log($client, "DNS de {$domainName} configurado completo (web + mail) apuntando a {$serverIp} desde el admin", ClientEventType::Domain, $domain);

        return back()->with('success', "Listo — {$domainName} (raíz, www, mail, MX y SPF) apunta a {$serverIp}.");
    }

    /**
     * Reintenta emitir el certificado SSL solo — separado de setupMail()
     * porque esa acción exige que TODOS los pasos (correo + webmail + SSL)
     * salgan bien para marcar éxito, y v-add-mail-domain/v-add-web-domain-
     * alias pueden seguir sin estar en la whitelist del wrapper aunque el
     * SSL ya funcione perfecto (DNS ya apunta acá). Este botón es el que
     * hay que apretar después de cambiar el DNS de un dominio — sin
     * depender de esperar las 6 horas del job programado.
     */
    public function retrySsl(Client $client, Hosting $hosting): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $account = $hosting->account;
        $domain = $hosting->domain;

        abort_if(! $account || ! $domain, 404);

        $hestia = app(\App\Services\Hosting\HestiaCliClient::class);
        $result = $hestia->issueSsl($account->remote_username, $domain->domain_name);

        if (! $result['success']) {
            return back()->with('error', "No se pudo emitir el SSL todavía — {$result['output']}");
        }

        ClientEvent::log($client, "SSL emitido para {$domain->domain_name} (reintento manual desde el admin)", ClientEventType::Hosting, $hosting);

        return back()->with('success', "Listo — SSL emitido para {$domain->domain_name}.");
    }

    /**
     * Reintenta crear el dominio de correo + alias de webmail + certificado
     * SSL en HestiaCP — pensado para cuentas ya provisionadas antes de que
     * createHostingAccount() empezara a intentarlo solo, o para cuando la
     * whitelist del wrapper remoto todavía no permitía v-add-mail-domain /
     * v-add-web-domain-alias en el momento del alta original.
     */
    public function setupMail(Client $client, Hosting $hosting): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $account = $hosting->account;
        $domain = $hosting->domain;

        abort_if(! $account || ! $domain, 404);

        $hestia = app(\App\Services\Hosting\HestiaCliClient::class);

        $mailDomain = $hestia->addMailDomain($account->remote_username, $domain->domain_name);
        $alias = $hestia->addWebDomainAlias($account->remote_username, $domain->domain_name, "webmail.{$domain->domain_name}");
        $ssl = $hestia->issueSsl($account->remote_username, $domain->domain_name);

        $messages = [];
        if (! $mailDomain['success']) $messages[] = "Dominio de correo: {$mailDomain['output']}";
        if (! $alias['success']) $messages[] = "Alias webmail: {$alias['output']}";
        if (! $ssl['success']) $messages[] = "SSL: {$ssl['output']}";

        if ($messages) {
            return back()->with('error', 'Algunos pasos no se pudieron completar (puede ser que el wrapper remoto todavía no permita estos comandos) — ' . implode(' | ', $messages));
        }

        ClientEvent::log($client, "Correo y webmail configurados en HestiaCP para {$domain->domain_name} desde el admin", ClientEventType::Hosting, $hosting);

        return back()->with('success', 'Listo — dominio de correo, alias de webmail y SSL configurados.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'provider'       => 'required|string|max:255',
            'plan'           => 'nullable|string|max:255',
            'type'           => 'nullable|in:shared,vps,cloud,dedicated,docker',
            'status'         => ['required', Rule::in(array_column(HostingStatus::cases(), 'value'))],
            'account_holder' => 'nullable|string|max:255',
            'account_email'  => 'nullable|email|max:255',
            'registered_at'  => 'nullable|date',
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
