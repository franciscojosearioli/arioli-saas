{{-- Dominios --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:8px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Dominios</h3>
        <div style="display:flex; gap:6px;">
            <x-admin.modal id="add-domain" title="Agregar dominio (manual)" trigger-label="+ Agregar manual" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
                <form method="POST" action="{{ route('clients.domains.store', $client) }}">
                    @csrf
                    <input type="text" name="domain_name" class="form-input" placeholder="midominio.com" style="margin-bottom:8px;" required>
                    <input type="text" name="registrar" class="form-input" placeholder="Proveedor (NutHost, NIC Argentina...)" style="margin-bottom:8px;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                        <input type="date" name="expires_at" class="form-input" placeholder="Vencimiento">
                        <select name="renewal_payer" class="form-select">
                            <option value="cliente">Paga cliente</option>
                            <option value="arioli">Paga Arioli</option>
                            <option value="tercero">Tercero</option>
                        </select>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">
                        <input type="number" step="0.01" name="provider_cost" class="form-input" placeholder="Costo proveedor">
                        <input type="number" step="0.01" name="management_fee" class="form-input" placeholder="Honorario gestión">
                    </div>
                    <input type="hidden" name="status" value="activo">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Agregar dominio</button>
                </form>
            </x-admin.modal>
            <a href="{{ route('clients.domain-registrations.create', $client) }}" class="btn btn-secondary" style="font-size:11.5px; padding:5px 10px;">+ Registrar dominio (Porkbun)</a>
        </div>
    </div>
    @foreach($client->domains as $domain)
        @php $dc = $badge($domain->status); $rc = $statusColors[$domain->renewalStatusColor()] ?? $statusColors['gray']; @endphp
        <div style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $domain->domain_name }} @if($domain->is_primary) <span style="font-size:10px; color:var(--accent);">★</span> @endif</div>
                <div style="display:flex; gap:4px; align-items:center;">
                    <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $dc['bg'] }}; color:{{ $dc['fg'] }};">{{ $domain->status->label() }}</span>
                    @if($domain->renewalStatusLabel() !== 'Activo')
                        <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $rc['bg'] }}; color:{{ $rc['fg'] }};">{{ $domain->renewalStatusLabel() }}</span>
                    @endif
                    <form method="POST" action="{{ route('clients.domains.destroy', [$client, $domain]) }}" onsubmit="return confirm('¿Eliminar el dominio {{ $domain->domain_name }}? El historial de cobros asociado se conserva, pero se pierde el vínculo con este dominio. No se puede deshacer.');">
                        @csrf @method('DELETE')
                        <button type="submit" style="border:none; background:none; color:#dc2626; cursor:pointer; font-size:13px; padding:0 2px;" title="Eliminar dominio">×</button>
                    </form>
                </div>
            </div>
            <div style="font-size:11.5px; color:var(--text-muted);">
                {{ $domain->registrar ? "Proveedor: {$domain->registrar} · " : '' }}Vence {{ $domain->expires_at?->format('d/m/Y') ?? '—' }} · Paga: {{ $domain->renewal_payer }}
            </div>
            <div style="font-size:11.5px; color:var(--text-muted);">
                Costo prov. ${{ number_format($domain->provider_cost ?? 0, 0, ',', '.') }} + Gestión ${{ number_format($domain->management_fee ?? 0, 0, ',', '.') }} = <strong>${{ number_format($domain->finalPrice(), 0, ',', '.') }}</strong>
            </div>

            @php $domainSsl = $client->projects->firstWhere('domain_id', $domain->id)?->sslCertificate; @endphp
            <div style="margin-top:8px; padding:8px 10px; background:#f9fafb; border-radius:8px;">
                @if($domainSsl)
                    @php $ssc = $badge($domainSsl->status); @endphp
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:11.5px; color:var(--text-primary);">🔒 SSL: {{ $domainSsl->provider }}</span>
                        <span style="padding:2px 8px; border-radius:99px; font-size:10.5px; font-weight:600; background:{{ $ssc['bg'] }}; color:{{ $ssc['fg'] }};">{{ $domainSsl->status->label() }}</span>
                    </div>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                        {{ strcasecmp($domainSsl->provider, 'Cloudflare') === 0 ? 'Incluido, se renueva solo' : ('Vence ' . ($domainSsl->expires_at?->format('d/m/Y') ?? '—')) }}
                        · Paga: {{ $domainSsl->renewal_payer }}
                    </div>
                    <x-admin.modal id="edit-ssl-{{ $domainSsl->id }}" title="Editar SSL — {{ $domain->domain_name }}" trigger-label="✎ Editar SSL" trigger-style="margin-top:4px;">
                        <form method="POST" action="{{ route('clients.ssl-certificates.update', [$client, $domainSsl]) }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            @csrf @method('PATCH')
                            <input type="text" name="provider" class="form-input" value="{{ $domainSsl->provider }}" required>
                            <select name="status" class="form-select">
                                @foreach(\App\Enums\SslCertificateStatus::cases() as $s)
                                    <option value="{{ $s->value }}" {{ $domainSsl->status === $s ? 'selected' : '' }}>{{ $s->label() }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="expires_at" class="form-input" value="{{ $domainSsl->expires_at?->format('Y-m-d') }}">
                            <select name="renewal_payer" class="form-select">
                                <option value="arioli" {{ $domainSsl->renewal_payer === 'arioli' ? 'selected' : '' }}>Paga Arioli</option>
                                <option value="cliente" {{ $domainSsl->renewal_payer === 'cliente' ? 'selected' : '' }}>Paga cliente</option>
                                <option value="tercero" {{ $domainSsl->renewal_payer === 'tercero' ? 'selected' : '' }}>Tercero</option>
                            </select>
                            <input type="number" step="0.01" name="provider_cost" class="form-input" value="{{ $domainSsl->provider_cost }}" placeholder="Costo proveedor">
                            <input type="number" step="0.01" name="management_fee" class="form-input" style="grid-column:span 2;" value="{{ $domainSsl->management_fee }}" placeholder="Honorario gestión">
                            <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Guardar cambios</button>
                        </form>
                        <form method="POST" action="{{ route('clients.ssl-certificates.destroy', [$client, $domainSsl]) }}" onsubmit="return confirm('¿Eliminar el SSL de este dominio?');" style="margin-top:10px; padding-top:10px; border-top:1px solid var(--card-border);">
                            @csrf @method('DELETE')
                            <button type="submit" style="border:none; background:none; color:#dc2626; cursor:pointer; font-size:12px;">Eliminar SSL</button>
                        </form>
                    </x-admin.modal>
                @else
                    <x-admin.modal id="add-ssl-{{ $domain->id }}" title="Agregar SSL — {{ $domain->domain_name }}" trigger-label="🔒 Sin SSL — agregar">
                        <form method="POST" action="{{ route('clients.ssl-certificates.store', $client) }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            @csrf
                            <input type="hidden" name="domain_id" value="{{ $domain->id }}">
                            <input type="text" name="provider" class="form-input" value="Cloudflare" required>
                            <input type="date" name="expires_at" class="form-input" placeholder="Vencimiento">
                            <select name="renewal_payer" class="form-select">
                                <option value="arioli">Paga Arioli</option>
                                <option value="cliente">Paga cliente</option>
                                <option value="tercero">Tercero</option>
                            </select>
                            <input type="number" step="0.01" name="provider_cost" class="form-input" placeholder="Costo proveedor" value="0">
                            <input type="hidden" name="status" value="activo">
                            <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Agregar SSL</button>
                        </form>
                    </x-admin.modal>
                @endif
            </div>

            @include('admin.partials.credentials-card', ['credentialable' => $domain, 'credentialableType' => 'domain'])

            <x-admin.modal id="edit-domain-{{ $domain->id }}" title="Editar dominio — {{ $domain->domain_name }}" trigger-label="✎ Editar dominio" trigger-style="margin-top:6px;">
                <form method="POST" action="{{ route('clients.domains.update', [$client, $domain]) }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    @csrf @method('PATCH')
                    <input type="text" name="domain_name" class="form-input" style="grid-column:span 2;" value="{{ $domain->domain_name }}" required>
                    <select name="status" class="form-select">
                        @foreach(\App\Enums\DomainStatus::cases() as $s)
                            <option value="{{ $s->value }}" {{ $domain->status === $s ? 'selected' : '' }}>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="registrar" class="form-input" value="{{ $domain->registrar }}" placeholder="Registrador">
                    <input type="text" name="dns_provider" class="form-input" value="{{ $domain->dns_provider }}" placeholder="Proveedor DNS (porkbun, manual...)">
                    <input type="text" name="account_holder" class="form-input" value="{{ $domain->account_holder }}" placeholder="Titular de la cuenta">
                    <input type="email" name="account_email" class="form-input" value="{{ $domain->account_email }}" placeholder="Email de la cuenta">
                    <label style="font-size:11px; color:var(--text-muted);">Registrado<input type="date" name="registered_at" class="form-input" style="margin-top:2px;" value="{{ $domain->registered_at?->format('Y-m-d') }}"></label>
                    <label style="font-size:11px; color:var(--text-muted);">Vencimiento<input type="date" name="expires_at" class="form-input" style="margin-top:2px;" value="{{ $domain->expires_at?->format('Y-m-d') }}"></label>
                    <select name="renewal_payer" class="form-select">
                        <option value="cliente" {{ $domain->renewal_payer === 'cliente' ? 'selected' : '' }}>Paga cliente</option>
                        <option value="arioli" {{ $domain->renewal_payer === 'arioli' ? 'selected' : '' }}>Paga Arioli</option>
                        <option value="tercero" {{ $domain->renewal_payer === 'tercero' ? 'selected' : '' }}>Tercero</option>
                    </select>
                    <input type="number" step="0.01" name="provider_cost" class="form-input" value="{{ $domain->provider_cost }}" placeholder="Costo proveedor">
                    <input type="number" step="0.01" name="management_fee" class="form-input" value="{{ $domain->management_fee }}" placeholder="Honorario gestión">
                    <label style="font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:5px;"><input type="checkbox" name="is_primary" value="1" {{ $domain->is_primary ? 'checked' : '' }}> Dominio principal</label>
                    <label style="font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:5px;"><input type="checkbox" name="auto_renew" value="1" {{ $domain->auto_renew ? 'checked' : '' }}> Renovación automática</label>
                    <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Guardar cambios</button>
                </form>
            </x-admin.modal>
        </div>
    @endforeach
</div>

{{-- Hostings --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:8px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Hostings</h3>
        <div style="display:flex; gap:6px;">
            <x-admin.modal id="add-hosting" title="Agregar hosting" trigger-label="+ Agregar manual" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
                <form method="POST" action="{{ route('clients.hostings.store', $client) }}">
                    @csrf
                    <input type="text" name="provider" class="form-input" placeholder="Proveedor (NutHost...)" style="margin-bottom:8px;" required>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                        <input type="text" name="plan" class="form-input" placeholder="Plan (Cloud Hosting Premium...)">
                        <select name="type" class="form-select">
                            <option value="">Tipo (opcional)</option>
                            <option value="shared">Shared</option>
                            <option value="vps">VPS</option>
                            <option value="cloud">Cloud</option>
                            <option value="dedicated">Dedicado</option>
                            <option value="docker">Docker</option>
                        </select>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                        <input type="date" name="expires_at" class="form-input">
                        <select name="renewal_payer" class="form-select">
                            <option value="cliente">Paga cliente</option>
                            <option value="arioli">Paga Arioli</option>
                            <option value="tercero">Tercero</option>
                        </select>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">
                        <input type="number" step="0.01" name="provider_cost" class="form-input" placeholder="Costo proveedor">
                        <input type="number" step="0.01" name="management_fee" class="form-input" placeholder="Honorario gestión">
                    </div>
                    <input type="hidden" name="status" value="activo">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Agregar hosting</button>
                </form>
            </x-admin.modal>
            <a href="{{ route('clients.hosting-orders.create', $client) }}" class="btn btn-secondary" style="font-size:11.5px; padding:5px 10px;">+ Nueva orden con pago</a>
        </div>
    </div>
    @foreach($client->hostings as $hosting)
        @php $hc = $badge($hosting->status); $rc = $statusColors[$hosting->renewalStatusColor()] ?? $statusColors['gray']; @endphp
        <div style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">
                    {{ $hosting->provider }}{{ $hosting->plan ? " — {$hosting->plan}" : '' }}
                    @if($hosting->type === 'vps') <span style="font-size:10px; color:var(--accent);">VPS</span> @endif
                </div>
                <div style="display:flex; gap:4px; align-items:center;">
                    <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $hc['bg'] }}; color:{{ $hc['fg'] }};">{{ $hosting->status->label() }}</span>
                    @if($hosting->renewalStatusLabel() !== 'Activo')
                        <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $rc['bg'] }}; color:{{ $rc['fg'] }};">{{ $hosting->renewalStatusLabel() }}</span>
                    @endif
                    <form method="POST" action="{{ route('clients.hostings.destroy', [$client, $hosting]) }}" onsubmit="return confirm('¿Eliminar este hosting ({{ $hosting->provider }})? El historial de cobros asociado se conserva, pero se pierde el vínculo con este hosting. No se puede deshacer.');">
                        @csrf @method('DELETE')
                        <button type="submit" style="border:none; background:none; color:#dc2626; cursor:pointer; font-size:13px; padding:0 2px;" title="Eliminar hosting">×</button>
                    </form>
                </div>
            </div>
            @if($hosting->account)
                <div style="font-size:11.5px; color:var(--text-muted);">Cuenta técnica: <strong>{{ $hosting->account->remote_username }}</strong> ({{ $hosting->account->provider }})</div>
            @endif

            @php $hostingSiteDomain = $client->projects->firstWhere('hosting_id', $hosting->id)?->domain; @endphp
            <div style="display:flex; gap:6px; flex-wrap:wrap; margin:8px 0;">
                @if($hostingSiteDomain)
                    <a href="https://{{ $hostingSiteDomain->domain_name }}" target="_blank" class="action-btn action-view">Ver sitio ↗</a>
                    <a href="https://webmail.{{ $hostingSiteDomain->domain_name }}" target="_blank" class="action-btn action-view">Webmail ↗</a>
                @endif
                @if($hosting->account?->panel_url)
                    <a href="{{ $hosting->account->panel_url }}" target="_blank" class="action-btn action-view">Panel de hosting ↗</a>
                @endif
            </div>

            @if($hosting->account?->provider === 'hestiacp')
                @php
                    $hostingDomain = $client->projects->firstWhere('hosting_id', $hosting->id)?->domain;
                    $serverIp = config('hosting_panel.hestiacp.server_ip');
                    $dn = $hostingDomain?->domain_name;
                @endphp
                <div style="margin:8px 0; padding:10px 12px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px;">
                    <div style="font-size:11.5px; font-weight:700; color:#1e40af; margin-bottom:6px;">🌐 Checklist de DNS (web + correo)</div>

                    <table style="width:100%; border-collapse:collapse; font-size:11px; color:#1e3a8a;">
                        <tbody>
                            <tr><td style="padding:2px 8px 2px 0; color:#3b5998;">A</td><td style="padding:2px 8px 2px 0;">@ (raíz)</td><td style="padding:2px 0;"><code style="background:#dbeafe; padding:1px 6px; border-radius:4px;">{{ $serverIp }}</code></td></tr>
                            <tr><td style="padding:2px 8px 2px 0; color:#3b5998;">A</td><td style="padding:2px 8px 2px 0;">www</td><td style="padding:2px 0;"><code style="background:#dbeafe; padding:1px 6px; border-radius:4px;">{{ $serverIp }}</code></td></tr>
                            <tr><td style="padding:2px 8px 2px 0; color:#3b5998;">A</td><td style="padding:2px 8px 2px 0;">mail</td><td style="padding:2px 0;"><code style="background:#dbeafe; padding:1px 6px; border-radius:4px;">{{ $serverIp }}</code></td></tr>
                            <tr><td style="padding:2px 8px 2px 0; color:#3b5998;">MX</td><td style="padding:2px 8px 2px 0;">@ (prio 10)</td><td style="padding:2px 0;"><code style="background:#dbeafe; padding:1px 6px; border-radius:4px;">mail.{{ $dn }}</code></td></tr>
                            <tr><td style="padding:2px 8px 2px 0; color:#3b5998;">TXT</td><td style="padding:2px 8px 2px 0;">@ (SPF)</td><td style="padding:2px 0;"><code style="background:#dbeafe; padding:1px 6px; border-radius:4px;">v=spf1 +a +mx ~all</code></td></tr>
                        </tbody>
                    </table>
                    <div style="font-size:10.5px; color:#3b5998; margin-top:6px;">
                        Todos "DNS only" / sin proxy — si el registro A de la raíz va proxeado (ej. Cloudflare naranja) para aprovechar su CDN, el "mail" tiene que quedar igual sin proxy para que el correo funcione.
                    </div>

                    @if($dn && $hostingDomain->dns_provider === 'porkbun')
                        <form method="POST" action="{{ route('clients.hostings.point-dns', [$client, $hosting]) }}" style="margin-top:8px; display:inline-block;" onsubmit="return confirm('¿Configurar en Porkbun todo el DNS (A raíz/www/mail, MX y SPF) de {{ $dn }} apuntando a {{ $serverIp }}?');">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="font-size:11px; padding:5px 12px;">Configurar DNS automáticamente ({{ $dn }})</button>
                        </form>
                    @elseif($dn)
                        <div style="font-size:11px; color:#3b5998; margin-top:4px;">Dominio {{ $dn }} gestionado fuera de Porkbun — hay que cargar estos registros a mano en su panel de DNS.</div>
                    @endif

                    <div style="margin-top:8px;">
                        <form method="POST" action="{{ route('clients.hostings.retry-ssl', [$client, $hosting]) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="font-size:11px; padding:5px 12px;">🔒 Reintentar SSL ahora</button>
                        </form>
                        <div style="font-size:10px; color:#3b5998; margin-top:4px;">
                            Apretá esto después de cambiar el DNS del dominio (no hace falta esperar el reintento automático de cada 6hs). Si el DNS todavía no propagó, va a fallar — probá de nuevo en un rato.
                        </div>
                    </div>

                    <div style="margin-top:8px; padding-top:8px; border-top:1px solid #bfdbfe;">
                        <form method="POST" action="{{ route('clients.hostings.setup-mail', [$client, $hosting]) }}" onsubmit="return confirm('¿Crear el dominio de correo, el alias webmail y renovar el SSL en HestiaCP para {{ $dn }}?');">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="font-size:11px; padding:5px 12px;">Configurar correo en HestiaCP (dominio + webmail + SSL)</button>
                        </form>
                        <div style="font-size:10px; color:#3b5998; margin-top:4px;">
                            Crea el contenedor de correo (las casillas puntuales se cargan a mano desde el panel) y agrega webmail.{{ $dn }} al certificado SSL.
                        </div>
                    </div>
                </div>
            @endif

            <div style="font-size:11.5px; color:var(--text-muted);">Vence {{ $hosting->expires_at?->format('d/m/Y') ?? '—' }} · Paga: {{ $hosting->renewal_payer }}</div>
            <div style="font-size:11.5px; color:var(--text-muted);">
                Costo prov. ${{ number_format($hosting->provider_cost ?? 0, 0, ',', '.') }} + Gestión ${{ number_format($hosting->management_fee ?? 0, 0, ',', '.') }} = <strong>${{ number_format($hosting->finalPrice(), 0, ',', '.') }}</strong>
            </div>
            @include('admin.partials.credentials-card', ['credentialable' => $hosting, 'credentialableType' => 'hosting'])

            <x-admin.modal id="edit-hosting-{{ $hosting->id }}" title="Editar hosting — {{ $hosting->provider }}" trigger-label="✎ Editar hosting" trigger-style="margin-top:6px;">
                <form method="POST" action="{{ route('clients.hostings.update', [$client, $hosting]) }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    @csrf @method('PATCH')
                    <input type="text" name="provider" class="form-input" value="{{ $hosting->provider }}" required>
                    <input type="text" name="plan" class="form-input" value="{{ $hosting->plan }}" placeholder="Plan">
                    <select name="type" class="form-select">
                        <option value="">Tipo (opcional)</option>
                        @foreach(['shared' => 'Shared', 'vps' => 'VPS', 'cloud' => 'Cloud', 'dedicated' => 'Dedicado', 'docker' => 'Docker'] as $v => $l)
                            <option value="{{ $v }}" {{ $hosting->type === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-select">
                        @foreach(\App\Enums\HostingStatus::cases() as $s)
                            <option value="{{ $s->value }}" {{ $hosting->status === $s ? 'selected' : '' }}>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="account_holder" class="form-input" value="{{ $hosting->account_holder }}" placeholder="Titular de la cuenta">
                    <input type="email" name="account_email" class="form-input" value="{{ $hosting->account_email }}" placeholder="Email de la cuenta">
                    <label style="font-size:11px; color:var(--text-muted);">Registrado<input type="date" name="registered_at" class="form-input" style="margin-top:2px;" value="{{ $hosting->registered_at?->format('Y-m-d') }}"></label>
                    <label style="font-size:11px; color:var(--text-muted);">Vencimiento<input type="date" name="expires_at" class="form-input" style="margin-top:2px;" value="{{ $hosting->expires_at?->format('Y-m-d') }}"></label>
                    <select name="renewal_payer" class="form-select">
                        <option value="cliente" {{ $hosting->renewal_payer === 'cliente' ? 'selected' : '' }}>Paga cliente</option>
                        <option value="arioli" {{ $hosting->renewal_payer === 'arioli' ? 'selected' : '' }}>Paga Arioli</option>
                        <option value="tercero" {{ $hosting->renewal_payer === 'tercero' ? 'selected' : '' }}>Tercero</option>
                    </select>
                    <input type="number" step="0.01" name="provider_cost" class="form-input" value="{{ $hosting->provider_cost }}" placeholder="Costo proveedor">
                    <input type="number" step="0.01" name="management_fee" class="form-input" value="{{ $hosting->management_fee }}" placeholder="Honorario gestión">
                    <label style="font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:5px;"><input type="checkbox" name="auto_renew" value="1" {{ $hosting->auto_renew ? 'checked' : '' }}> Renovación automática</label>
                    <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Guardar cambios</button>
                </form>
            </x-admin.modal>
        </div>
    @endforeach
</div>

{{-- SSL: ya no es una sección aparte — se muestra dentro de cada Dominio,
     porque conceptualmente va con el dominio (ver más arriba). Acá solo
     queda un fallback para certificados que quedaron sin vincular a
     ningún dominio (no debería pasar desde esta pantalla en adelante). --}}
@php
    $linkedSslIds = $client->projects->pluck('ssl_certificate_id')->filter();
    $orphanSsl = $client->sslCertificates->whereNotIn('id', $linkedSslIds);
@endphp
@if($orphanSsl->isNotEmpty())
    <div class="card" style="padding:24px; margin-bottom:20px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">SSL sin dominio asignado</h3>
        @foreach($orphanSsl as $ssl)
            @php $sc = $badge($ssl->status); @endphp
            <div style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $ssl->provider }}</div>
                    <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }};">{{ $ssl->status->label() }}</span>
                </div>
                <div style="font-size:11.5px; color:var(--text-muted);">Vence {{ $ssl->expires_at?->format('d/m/Y') ?? '—' }}</div>
                <form method="POST" action="{{ route('clients.ssl-certificates.destroy', [$client, $ssl]) }}" onsubmit="return confirm('¿Eliminar este SSL?');" style="margin-top:4px;">
                    @csrf @method('DELETE')
                    <button type="submit" style="border:none; background:none; color:#dc2626; cursor:pointer; font-size:11px;">Eliminar</button>
                </form>
            </div>
        @endforeach
    </div>
@endif

{{-- Cloudflare --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Cloudflare</h3>
        <x-admin.modal id="add-cloudflare" title="Agregar Cloudflare" trigger-label="+ Agregar" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
            <form method="POST" action="{{ route('clients.cloudflare-services.store', $client) }}">
                @csrf
                <input type="text" name="managed_by" class="form-input" value="Arioli.dev" style="margin-bottom:8px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                    <input type="date" name="expires_at" class="form-input" placeholder="Vencimiento (opcional)">
                    <select name="renewal_payer" class="form-select">
                        <option value="arioli">Paga Arioli</option>
                        <option value="cliente">Paga cliente</option>
                        <option value="tercero">Tercero</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">
                    <input type="number" step="0.01" name="provider_cost" class="form-input" placeholder="Costo proveedor" value="0">
                    <input type="number" step="0.01" name="management_fee" class="form-input" placeholder="Honorario gestión">
                </div>
                <input type="hidden" name="status" value="activo">
                <button type="submit" class="btn btn-primary" style="width:100%;">Agregar Cloudflare</button>
            </form>
        </x-admin.modal>
    </div>
    @foreach($client->cloudflareServices as $cf)
        @php $cc = $badge($cf->status); @endphp
        <div style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">Cloudflare DNS y Seguridad</div>
                <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $cc['bg'] }}; color:{{ $cc['fg'] }};">{{ $cf->status->label() }}</span>
            </div>
            <div style="font-size:11.5px; color:var(--text-muted);">Gestión: {{ $cf->managed_by }}{{ $cf->expires_at ? ' · Vence ' . $cf->expires_at->format('d/m/Y') : '' }}</div>
            <div style="font-size:11.5px; color:var(--text-muted);">
                Costo prov. ${{ number_format($cf->provider_cost ?? 0, 0, ',', '.') }} + Gestión ${{ number_format($cf->management_fee ?? 0, 0, ',', '.') }} = <strong>${{ number_format($cf->finalPrice(), 0, ',', '.') }}</strong>
            </div>
            <x-admin.modal id="edit-cf-{{ $cf->id }}" title="Editar Cloudflare" trigger-label="✎ Editar" trigger-style="margin-top:6px;">
                <form method="POST" action="{{ route('clients.cloudflare-services.update', [$client, $cf]) }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    @csrf @method('PATCH')
                    <input type="text" name="managed_by" class="form-input" value="{{ $cf->managed_by }}" placeholder="Gestión">
                    <select name="status" class="form-select">
                        @foreach(\App\Enums\CloudflareServiceStatus::cases() as $s)
                            <option value="{{ $s->value }}" {{ $cf->status === $s ? 'selected' : '' }}>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="expires_at" class="form-input" value="{{ $cf->expires_at?->format('Y-m-d') }}">
                    <select name="renewal_payer" class="form-select">
                        <option value="arioli" {{ $cf->renewal_payer === 'arioli' ? 'selected' : '' }}>Paga Arioli</option>
                        <option value="cliente" {{ $cf->renewal_payer === 'cliente' ? 'selected' : '' }}>Paga cliente</option>
                        <option value="tercero" {{ $cf->renewal_payer === 'tercero' ? 'selected' : '' }}>Tercero</option>
                    </select>
                    <input type="number" step="0.01" name="provider_cost" class="form-input" value="{{ $cf->provider_cost }}" placeholder="Costo proveedor">
                    <input type="number" step="0.01" name="management_fee" class="form-input" value="{{ $cf->management_fee }}" placeholder="Honorario gestión">
                    <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Guardar cambios</button>
                </form>
                <form method="POST" action="{{ route('clients.cloudflare-services.destroy', [$client, $cf]) }}" onsubmit="return confirm('¿Eliminar este servicio Cloudflare?');" style="margin-top:10px; padding-top:10px; border-top:1px solid var(--card-border);">
                    @csrf @method('DELETE')
                    <button type="submit" style="border:none; background:none; color:#dc2626; cursor:pointer; font-size:12px;">Eliminar</button>
                </form>
            </x-admin.modal>
        </div>
    @endforeach
</div>
