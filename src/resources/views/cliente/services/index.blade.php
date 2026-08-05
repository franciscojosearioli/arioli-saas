<x-cliente-layout title="Servicios Contratados">

    <div class="page-header">
        <div>
            <h1 class="page-title">Servicios Contratados</h1>
            <p class="page-subtitle">Mantenimiento, SEO y otros servicios recurrentes con Arioli.dev</p>
        </div>
    </div>

    @forelse($services as $service)
        <div class="card" style="margin-bottom:16px;">
            <div class="card-body" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div>
                    <div style="font-weight:700; color:var(--text-primary); font-size:15px;">{{ $service->service_type->label() }}</div>
                    <div style="font-size:12.5px; color:var(--text-muted); margin-top:4px;">
                        {{ $service->billing_cycle->label() }}
                        @if($service->starts_at) · Desde {{ $service->starts_at->format('d/m/Y') }} @endif
                        @if($service->ends_at) · Hasta {{ $service->ends_at->format('d/m/Y') }} @endif
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:18px; font-weight:800; color:var(--text-primary);">${{ number_format($service->amount, 0, ',', '.') }}</div>
                    <div style="font-size:11.5px; color:var(--text-muted);">{{ $service->auto_renew ? 'Renovación automática' : 'Sin renovación automática' }}</div>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <strong>No tenés servicios contratados</strong>
                <p style="margin-top:4px;">Escribinos si querés contratar mantenimiento, SEO u otro servicio.</p>
            </div>
        </div>
    @endforelse

</x-cliente-layout>
