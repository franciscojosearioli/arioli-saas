{{-- Licencias y sistemas (Tenants) --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Sistemas y licencias</h3>
        <a href="{{ route('tenants.create', ['client_id' => $client->id]) }}" class="btn btn-secondary" style="font-size:11.5px; padding:5px 10px;">+ Nuevo sistema</a>
    </div>
    <p style="font-size:12px; color:var(--text-muted); margin:0 0 16px;">
        Cada licencia corresponde a un sistema (Tenant) contratado — un cliente puede no tener ninguno (solo mantenimiento/web) o tener varios.
    </p>
    @forelse($client->licenses as $license)
        @php
            $licenseState = match(true) {
                ! $license->active => ['bg' => '#f3f4f6', 'fg' => '#374151', 'label' => 'Inactiva'],
                $license->isExpired() => ['bg' => '#fee2e2', 'fg' => '#991b1b', 'label' => 'Vencida'],
                default => ['bg' => '#d1fae5', 'fg' => '#065f46', 'label' => 'Activa'],
            };
        @endphp
        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div>
                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $license->plan?->product?->name }}</div>
                <div style="font-size:11.5px; color:var(--text-muted);">
                    @if($license->tenant_id)
                        Sistema: <a href="{{ route('tenants.show', $license->tenant_id) }}" style="color:var(--accent);">{{ $license->tenant_id }}</a> ·
                    @endif
                    Vence {{ $license->expires_at?->format('d/m/Y') ?? 'Sin vencimiento' }}
                </div>
            </div>
            <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $licenseState['bg'] }}; color:{{ $licenseState['fg'] }};">{{ $licenseState['label'] }}</span>
        </div>
    @empty
        <p style="font-size:12.5px; color:var(--text-muted);">Sin licencias — este cliente todavía no tiene ningún sistema contratado.</p>
    @endforelse
</div>
