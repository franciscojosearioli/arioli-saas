<x-cliente-layout title="Mis Licencias">

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">Mis Licencias</h1>
            <p class="page-subtitle">Administrá tus sistemas contratados</p>
        </div>
        <a href="http://{{ config('app.landing_domain') }}#productos" class="btn btn-primary" target="_blank">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Contratar nuevo sistema
        </a>
    </div>

    @forelse($licenses as $license)
        @php
            $isValid     = $license->isValid();
            $isExpired   = $license->isExpired();
            $isPerpetual = $license->expires_at === null;
            $days        = $license->daysRemaining();
            $domain      = $domains->get($license->id);
        @endphp

        <div class="card" style="margin-bottom:16px;">
            <div class="card-body">

                {{-- Header --}}
                <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                    <div style="display:flex; align-items:center; gap:14px;">
                        <div style="width:44px; height:44px; border-radius:12px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:17px; font-weight:700; color:var(--text-primary);">
                                {{ $license->plan->product->name ?? '-' }}
                            </div>
                            <div style="font-size:13px; color:var(--text-muted); margin-top:2px;">
                                Plan {{ $license->plan->period_label ?? '-' }}
                            </div>
                        </div>
                    </div>
                    @if($isPerpetual)
                        <span class="badge badge-green">● Activa (sin vencimiento)</span>
                    @elseif($isExpired)
                        <span class="badge badge-red">Expirada</span>
                    @elseif($days <= 7)
                        <span class="badge badge-yellow">⚠ {{ $days }} días</span>
                    @elseif($days <= 30)
                        <span class="badge badge-yellow">{{ $days }} días restantes</span>
                    @else
                        <span class="badge badge-green">● Activa</span>
                    @endif
                </div>

                {{-- Details grid --}}
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px;">
                    <div style="background:var(--body-bg); border-radius:10px; padding:14px;">
                        <div class="stat-label">Inicio</div>
                        <div style="font-size:14px; font-weight:600; color:var(--text-primary); margin-top:4px;">
                            {{ $license->starts_at->format('d/m/Y') }}
                        </div>
                    </div>
                    <div style="background:var(--body-bg); border-radius:10px; padding:14px;">
                        <div class="stat-label">Vencimiento</div>
                        <div style="font-size:14px; font-weight:600; color:{{ !$isPerpetual && $days <= 30 ? 'var(--warning)' : 'var(--text-primary)' }}; margin-top:4px;">
                            {{ $isPerpetual ? 'Sin vencimiento' : $license->expires_at->format('d/m/Y') }}
                        </div>
                    </div>
                    <div style="background:var(--body-bg); border-radius:10px; padding:14px;">
                        <div class="stat-label">Días restantes</div>
                        <div style="font-size:14px; font-weight:700; color:{{ $isPerpetual ? 'var(--success)' : ($days > 30 ? 'var(--success)' : ($days > 7 ? 'var(--warning)' : 'var(--danger)')) }}; margin-top:4px;">
                            {{ $isPerpetual ? '∞' : $days . ' días' }}
                        </div>
                    </div>
                    <div style="background:var(--body-bg); border-radius:10px; padding:14px;">
                        <div class="stat-label">Precio del plan</div>
                        <div style="font-size:14px; font-weight:600; color:var(--text-primary); margin-top:4px;">
                            ${{ number_format($license->plan->price, 0, ',', '.') }} ARS
                        </div>
                    </div>
                </div>

                @if($domain)
                    <div style="background:var(--body-bg); border-radius:10px; padding:14px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <div class="stat-label">URL del sistema</div>
                            <div style="font-size:13px; font-family:var(--font-mono); color:var(--accent); margin-top:4px;">
                                {{ $domain->domain }}
                            </div>
                        </div>
                        <a href="http://{{ $domain->domain }}" target="_blank" class="btn btn-primary" style="font-size:13px; padding:8px 14px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Acceder
                        </a>
                    </div>
                @endif

                {{-- Acciones --}}
                <div style="display:flex; gap:10px;">
                    <a href="{{ route('cliente.licencia.show', $license->id) }}" class="btn btn-primary" style="font-size:13px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Gestionar
                    </a>
                    @if($domain)
                        <a href="http://{{ $domain->domain }}" target="_blank" class="btn btn-secondary" style="font-size:13px;">
                            Acceder
                        </a>
                    @endif
                </div>

            </div>
        </div>

    @empty
        <div class="card">
            <div style="padding:60px; text-align:center;">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="margin:0 auto 16px; color:var(--text-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <p style="font-size:15px; font-weight:600; color:var(--text-secondary); margin-bottom:8px;">
                    No tenés licencias activas
                </p>
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">
                    Contratá un sistema para empezar a usarlo.
                </p>
                <a href="http://{{ config('app.landing_domain') }}#productos" class="btn btn-primary" target="_blank">
                    Ver sistemas disponibles
                </a>
            </div>
        </div>
    @endforelse

</x-cliente-layout>