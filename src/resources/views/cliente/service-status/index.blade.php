<x-cliente-layout title="Estado de Servicios">

    @php
        $badgeClass = fn ($color) => match ($color) {
            'green' => 'badge-green',
            'red' => 'badge-red',
            'amber' => 'badge-yellow',
            default => 'badge-gray',
        };
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">Estado de Servicios</h1>
            <p class="page-subtitle">Estado actual de tu infraestructura con Arioli.dev</p>
        </div>
    </div>

    @if($overall === 'operativo')
        <div class="alert alert-success" style="margin-bottom:20px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Todo operativo — no hay nada que requiera tu atención.
        </div>
    @else
        <div class="alert alert-warning" style="margin-bottom:20px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Algunos servicios requieren atención — revisá el detalle abajo.
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="card-title" style="margin-bottom:16px;">Detalle por servicio</div>
            @forelse($assets as $asset)
                <div class="detail-row">
                    <span class="detail-label">{{ $asset['label'] }}</span>
                    <span class="badge {{ $badgeClass($asset['color']) }}">{{ $asset['health'] }}</span>
                </div>
            @empty
                <p style="color:var(--text-secondary); font-size:13px;">Todavía no tenés servicios cargados.</p>
            @endforelse
        </div>
    </div>

</x-cliente-layout>
