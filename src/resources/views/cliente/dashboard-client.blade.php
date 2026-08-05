<x-cliente-layout title="Inicio">

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @php
        $firstName = explode(' ', trim($user->name))[0] ?? $user->name;
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Buenos días' : ($hour < 19 ? 'Buenas tardes' : 'Buenas noches');

        // URL real de acceso a un sistema con licencia — mismo criterio que
        // usa el portal SaaS (dominio personalizado si lo definió, si no el
        // subdominio de Arioli que ya trae la licencia).
        $systemUrl = function ($license) {
            if (! $license) return null;
            if ($license->custom_domain) return 'https://' . $license->custom_domain;
            if ($license->domain?->domain) return 'http://' . $license->domain->domain;
            return null;
        };
        $hostingReady = fn ($hosting) => $hosting?->account?->credential_claimed_at !== null;

        $accessCards = collect();
        foreach ($systems as $system) {
            $accessCards->push([
                'name'    => $system->name,
                'domain'  => $system->domain?->domain_name,
                'license' => $system->license,
                'hosting' => $system->hosting,
                'url'     => $systemUrl($system->license),
            ]);
        }
        foreach ($standaloneHostings as $hosting) {
            $accessCards->push([
                'name'    => $hosting->hostingPlan?->name ?? $hosting->provider,
                'domain'  => null,
                'license' => null,
                'hosting' => $hosting,
                'url'     => null,
            ]);
        }
        foreach ($standaloneLicenses as $license) {
            $accessCards->push([
                'name'    => $license->plan?->product?->name ?? 'Sistema',
                'domain'  => null,
                'license' => $license,
                'hosting' => null,
                'url'     => $systemUrl($license),
            ]);
        }
    @endphp

    {{-- Hero --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <p style="font-size:13px; color:var(--text-muted); margin-bottom:4px;">{{ $greeting }}</p>
            <h1 class="page-title" style="margin-bottom:6px;">{{ $firstName }} 👋</h1>
            <p class="page-subtitle" style="margin-top:0;">{{ $client->name }} — así está tu cuenta hoy</p>
        </div>
        <div style="display:flex; align-items:center; gap:8px; padding:10px 16px; border-radius:99px; background:{{ $overallHealth === 'operativo' ? 'var(--success-bg)' : 'rgba(245,158,11,.1)' }}; border:1px solid {{ $overallHealth === 'operativo' ? 'var(--success-border)' : 'rgba(245,158,11,.3)' }};">
            <span style="width:8px; height:8px; border-radius:50%; background:{{ $overallHealth === 'operativo' ? 'var(--success)' : 'var(--warning)' }}; flex-shrink:0;"></span>
            <span style="font-size:13px; font-weight:600; color:{{ $overallHealth === 'operativo' ? 'var(--success)' : 'var(--warning)' }};">
                {{ $overallHealth === 'operativo' ? 'Todo operativo' : 'Necesita atención' }}
            </span>
            @if($overallHealth !== 'operativo')
                <a href="{{ route('cliente.service-status.index') }}" style="font-size:12px; color:inherit; text-decoration:underline;">ver detalle</a>
            @endif
        </div>
    </div>

    {{-- Tus accesos: el centro de la página — entrar a un sistema o al hosting con un clic --}}
    @if($accessCards->isNotEmpty())
        <div style="margin-bottom:28px;">
            <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:12px;">
                <h2 style="font-size:15px; font-weight:700; color:var(--text-primary);">Tus accesos</h2>
                <span style="font-size:12px; color:var(--text-muted);">{{ $accessCards->count() }} {{ Str::plural('sistema', $accessCards->count()) }}</span>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px;">
                @foreach($accessCards as $card)
                    <div class="card" style="padding:20px; display:flex; flex-direction:column; gap:14px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:40px; height:40px; border-radius:10px; background:var(--accent-light); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                @if($card['license'])
                                    <svg width="20" height="20" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @else
                                    <svg width="20" height="20" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-14 5h.01M9 17h.01"/></svg>
                                @endif
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:14px; font-weight:700; color:var(--text-primary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $card['name'] }}</div>
                                <div style="font-size:12px; color:var(--text-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $card['domain'] ?? ($card['license'] ? 'Sistema con licencia' : 'Hosting') }}
                                </div>
                            </div>
                        </div>

                        <div style="display:flex; flex-wrap:wrap; gap:6px;">
                            @if($card['license'])
                                <span class="badge {{ $card['license']->active ? 'badge-green' : 'badge-red' }}">{{ $card['license']->active ? 'Licencia activa' : 'Licencia inactiva' }}</span>
                            @endif
                            @if($card['hosting'])
                                <span class="badge badge-gray">Hosting: {{ $card['hosting']->provider }}</span>
                            @endif
                        </div>

                        <div style="display:flex; gap:8px; margin-top:auto; padding-top:2px;">
                            @if($card['url'])
                                <a href="{{ $card['url'] }}" target="_blank" class="btn btn-primary" style="flex:1; justify-content:center; font-size:12.5px; padding:8px 14px;">
                                    Ingresar al sistema
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @endif
                            @if($card['hosting'])
                                @if($hostingReady($card['hosting']))
                                    <a href="{{ $card['hosting']->account->panel_url }}" target="_blank" class="btn {{ $card['url'] ? 'btn-secondary' : 'btn-primary' }}" style="flex:1; justify-content:center; font-size:12.5px; padding:8px 14px;">
                                        Acceder al hosting
                                    </a>
                                @else
                                    <a href="{{ route('cliente.hosting.index') }}" class="btn btn-secondary" style="flex:1; justify-content:center; font-size:12.5px; padding:8px 14px;">
                                        Configurar acceso
                                    </a>
                                @endif
                            @endif
                            @if(! $card['url'] && ! $card['hosting'])
                                <span style="font-size:12px; color:var(--text-muted);">Sin acceso directo configurado todavía.</span>
                            @endif
                        </div>

                        @if($card['domain'])
                            <div style="display:flex; gap:12px;">
                                <a href="https://{{ $card['domain'] }}" target="_blank" style="font-size:11.5px; color:var(--accent); text-decoration:none;">Ver sitio →</a>
                                <a href="https://webmail.{{ $card['domain'] }}" target="_blank" style="font-size:11.5px; color:var(--accent); text-decoration:none;">Webmail →</a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Resumen de tu cuenta --}}
    @php
        $quickLinks = [
            ['label' => 'Hosting', 'count' => $activeHostingCount, 'route' => route('cliente.hosting.index'), 'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-14 5h.01M9 17h.01'],
            ['label' => 'Dominios', 'count' => $domainCount, 'route' => route('cliente.domains.index'), 'icon' => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 010 18M12 3a15 15 0 000 18'],
            ['label' => 'Servicios contratados', 'count' => $serviceCount, 'route' => route('cliente.services.index'), 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['label' => 'Cobros pendientes', 'count' => $pendingChargesCount, 'route' => route('cliente.billing.index'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'warn' => $pendingChargesCount > 0],
        ];
    @endphp
    <div class="stats-grid stats-grid-4">
        @foreach($quickLinks as $link)
            <a href="{{ $link['route'] }}" class="stat-card" style="text-decoration:none; display:block; transition:all .15s;"
               onmouseover="this.style.borderColor='var(--accent)'; this.style.transform='translateY(-2px)';"
               onmouseout="this.style.borderColor='var(--card-border)'; this.style.transform='none';">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div class="stat-label">{{ $link['label'] }}</div>
                    <svg width="16" height="16" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                </div>
                <div class="stat-value" style="color:{{ ($link['warn'] ?? false) ? 'var(--warning)' : 'var(--text-primary)' }};">{{ $link['count'] }}</div>
            </a>
        @endforeach
    </div>

    <style>
        .dash-grid { display:grid; grid-template-columns:1.4fr 1fr; gap:20px; align-items:start; }
        @media (max-width: 900px) {
            .dash-grid { grid-template-columns:1fr; }
        }
        /* .stats-grid es de 3 columnas por defecto (layout compartido) — acá
           son 4 tarjetas (Hosting/Dominios/Servicios/Cobros), no 3, así que
           se pisa el ancho de columnas solo para esta grilla puntual. */
        .stats-grid.stats-grid-4 { grid-template-columns: repeat(4,1fr); }
        @media (max-width: 900px) {
            .stats-grid.stats-grid-4 { grid-template-columns: repeat(2,1fr); }
        }
        @media (max-width: 480px) {
            .stats-grid.stats-grid-4 { grid-template-columns: 1fr; }
        }
    </style>

    <div class="dash-grid">
        {{-- Próximos vencimientos --}}
        <div class="card">
            <div class="card-body">
                <div class="card-title" style="margin-bottom:16px;">Próximos vencimientos</div>
                @forelse($upcomingRenewals as $asset)
                    <div class="detail-row">
                        <span class="detail-label">{{ $asset->label() }}</span>
                        <span style="display:flex; align-items:center; gap:8px;">
                            <span class="badge {{ $asset->renewalStatusLabel() === 'Vencido' ? 'badge-red' : 'badge-yellow' }}">{{ $asset->renewalStatusLabel() }}</span>
                            <span class="detail-value">{{ $asset->expiresAt()->format('d/m/Y') }}</span>
                        </span>
                    </div>
                @empty
                    <p style="color:var(--text-secondary); font-size:13px;">Nada por vencer pronto.</p>
                @endforelse

                @foreach($standaloneLicenses as $license)
                    @if($license->expires_at)
                        <div class="detail-row">
                            <span class="detail-label">Licencia: {{ $license->plan?->product?->name ?? 'Sistema' }}</span>
                            <span style="display:flex; align-items:center; gap:8px;">
                                <span class="badge {{ $license->isExpired() ? 'badge-red' : 'badge-yellow' }}">{{ $license->isExpired() ? 'Vencida' : ($license->daysRemaining() . ' días') }}</span>
                                <span class="detail-value">{{ $license->expires_at->format('d/m/Y') }}</span>
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Servicios contratados --}}
        <div class="card">
            <div class="card-body">
                <div class="card-title" style="margin-bottom:16px;">Servicios contratados</div>
                @forelse($services as $service)
                    <div class="detail-row" style="flex-direction:column; align-items:flex-start; gap:2px;">
                        <span class="detail-label">{{ $service->service_type->label() }}</span>
                        <span style="font-size:12px; color:var(--text-muted);">{{ $service->billing_cycle->label() }} — ${{ number_format($service->amount, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p style="color:var(--text-secondary); font-size:13px;">No tenés servicios contratados todavía.</p>
                @endforelse
            </div>
        </div>
    </div>

</x-cliente-layout>
