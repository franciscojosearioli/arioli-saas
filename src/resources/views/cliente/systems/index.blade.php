<x-cliente-layout title="Sistemas">

    @php
        $systemUrl = function ($license) {
            if ($license->custom_domain) return 'https://' . $license->custom_domain;
            if ($license->domain?->domain) return 'http://' . $license->domain->domain;
            return null;
        };
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">Sistemas</h1>
            <p class="page-subtitle">Los sistemas con licencia que tenés contratados con Arioli.dev</p>
        </div>
    </div>

    @if($licenses->isEmpty())
        <div class="card">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <strong>Todavía no tenés sistemas con licencia</strong>
            </div>
        </div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:16px;">
            @foreach($licenses as $license)
                @php
                    $url = $systemUrl($license);
                    $status = ! $license->active
                        ? ['badge-gray', 'Inactiva']
                        : ($license->isExpired() ? ['badge-red', 'Vencida'] : ['badge-green', 'Activa']);
                    $days = $license->daysRemaining();
                @endphp
                <div class="card" style="padding:20px; display:flex; flex-direction:column; gap:14px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                        <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                            <div style="width:40px; height:40px; border-radius:10px; background:var(--accent-light); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="20" height="20" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:14px; font-weight:700; color:var(--text-primary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $license->plan?->product?->name ?? 'Sistema' }}
                                </div>
                                <div style="font-size:12px; color:var(--text-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $license->plan?->name ?? 'Plan sin nombre' }}
                                </div>
                            </div>
                        </div>
                        <span class="badge {{ $status[0] }}" style="flex-shrink:0;">{{ $status[1] }}</span>
                    </div>

                    <div>
                        <div class="detail-row">
                            <span class="detail-label">Inicio</span>
                            <span class="detail-value">{{ $license->starts_at?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Vencimiento</span>
                            <span class="detail-value">
                                @if($license->expires_at)
                                    {{ $license->expires_at->format('d/m/Y') }}
                                    @if($days !== null)
                                        <span class="badge {{ $license->isExpired() ? 'badge-red' : ($days <= 7 ? 'badge-yellow' : 'badge-gray') }}" style="margin-left:6px;">
                                            {{ $license->isExpired() ? 'Vencida' : $days . ' días' }}
                                        </span>
                                    @endif
                                @else
                                    Sin vencimiento
                                @endif
                            </span>
                        </div>
                        @if($license->domain?->domain)
                            <div class="detail-row">
                                <span class="detail-label">Dirección</span>
                                <span class="detail-value">{{ $license->custom_domain ?? $license->domain->domain }}</span>
                            </div>
                        @endif
                    </div>

                    <div style="margin-top:auto; padding-top:2px;">
                        @if($url)
                            <a href="{{ $url }}" target="_blank" class="btn btn-primary" style="width:100%; justify-content:center;">
                                Ingresar al sistema
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @else
                            <span style="font-size:12px; color:var(--text-muted);">Sin acceso directo configurado todavía.</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-cliente-layout>
