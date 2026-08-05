@extends('layouts.storefront')

@section('title', $desarrollo->nombre)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .back-link { display: inline-block; font-size: 13px; color: var(--text3); margin-bottom: 18px; }
    .back-link:hover { color: var(--text); }
    .desarrollo-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 18px; }
    .desarrollo-loc { color: var(--text3); font-size: 14px; }
    .desarrollo-count { font-size: 13px; color: var(--text3); }
    .desarrollo-desc { color: var(--text2); line-height: 1.8; margin-bottom: 24px; }

    .map-card { background: var(--bg2); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
    .map-legend { display: flex; flex-wrap: wrap; gap: 20px; padding: 10px 18px; border-bottom: 1px solid var(--border); font-size: 12px; color: var(--text2); }
    .legend-dot { display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 6px; vertical-align: middle; }
    #mapa-desarrollo { height: 480px; }
    .empty-map { text-align: center; padding: 40px 20px; color: var(--text3); }
</style>
@endpush

@section('content')
    <a href="{{ route('storefront.index') }}" class="back-link">&larr; Volver a la búsqueda</a>

    @php
        $tipoLabel = match ($desarrollo->tipo) {
            'loteo' => 'Loteo', 'barrio_cerrado' => 'Barrio cerrado',
            'edificio' => 'Edificio', 'emprendimiento' => 'Emprendimiento',
        };
        $unidadLabel = $desarrollo->tipo === 'edificio' ? 'unidad' : 'lote';
        $publicadoLabel = $desarrollo->tipo === 'edificio' ? 'publicada' : 'publicado';
    @endphp

    <div class="desarrollo-head">
        <div>
            <div class="eyebrow">{{ $tipoLabel }}</div>
            <h1 class="title">{{ $desarrollo->nombre }}</h1>
            <p class="desarrollo-loc">{{ collect([$desarrollo->barrio, $desarrollo->ciudad, $desarrollo->provincia])->filter()->implode(', ') }}</p>
        </div>
        <div class="desarrollo-count">{{ $totalUnidades }} {{ $unidadLabel }}{{ $totalUnidades === 1 ? '' : 's' }} {{ $publicadoLabel }}{{ $totalUnidades === 1 ? '' : 's' }}</div>
    </div>

    @if ($desarrollo->descripcion)
        <p class="desarrollo-desc">{{ $desarrollo->descripcion }}</p>
    @endif

    <div class="map-card">
        <div class="map-legend">
            <span><span class="legend-dot" style="background:#22c55e"></span>Disponible</span>
            <span><span class="legend-dot" style="background:#eab308"></span>Reservado</span>
            <span><span class="legend-dot" style="background:#ef4444"></span>Vendido</span>
            <span><span class="legend-dot" style="background:#3b82f6"></span>Alquilado</span>
        </div>
        <div id="mapa-desarrollo"></div>
    </div>

    @if ($unidades->isEmpty())
        <p class="empty-map">
            @if ($totalUnidades > 0)
                Este desarrollo tiene {{ $totalUnidades }} {{ $unidadLabel }}{{ $totalUnidades === 1 ? '' : 's' }} {{ $publicadoLabel }}{{ $totalUnidades === 1 ? '' : 's' }}, pero todavía ninguno tiene su ubicación cargada en el mapa.
            @else
                Este desarrollo todavía no tiene unidades publicadas.
            @endif
        </p>
    @endif
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const unidades = @json($unidades);
    const poligonoGeneral = @json($poligonoGeneral);
    const colorMap = { disponible: '#22c55e', reservado: '#eab308', vendido: '#ef4444', alquilado: '#3b82f6' };

    const map = L.map('mapa-desarrollo');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);

    const capas = [];

    if (poligonoGeneral) {
        capas.push(L.geoJSON(poligonoGeneral, { style: { color: '#60a5fa', weight: 2, dashArray: '6 4', fillOpacity: 0.04 } }).addTo(map));
    }

    function centro(geom) {
        const coords = geom.type === 'Polygon' ? geom.coordinates[0] : [geom.coordinates];
        let lat = 0, lng = 0;
        coords.forEach(c => { lng += c[0]; lat += c[1]; });
        return [lat / coords.length, lng / coords.length];
    }

    unidades.forEach(unidad => {
        const color = colorMap[unidad.estado] || '#94a3b8';
        const precio = unidad.precio ? `${unidad.moneda} ${Number(unidad.precio).toLocaleString('es-AR')}` : '';
        const popup = `
            <div style="font-family:'DM Sans',sans-serif;font-size:13px;min-width:160px;">
                <div style="font-weight:700;margin-bottom:4px;">${unidad.titulo}</div>
                ${precio ? `<div style="color:#1d4ed8;font-weight:700;margin-bottom:4px;">${precio}</div>` : ''}
                <span style="display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;background:${color}22;color:${color};">
                    ${unidad.estado.charAt(0).toUpperCase() + unidad.estado.slice(1)}
                </span><br>
                <a href="${unidad.url}" style="color:#1d4ed8;font-size:12px;">Ver detalle &rarr;</a>
            </div>`;

        const capa = L.geoJSON(unidad.coordenadas, {
            pointToLayer: (feature, latlng) => L.circleMarker(latlng, { radius: 8, color, fillColor: color, fillOpacity: 0.75, weight: 2 }),
            style: { color, fillColor: color, fillOpacity: 0.5, weight: 2 },
        }).bindPopup(popup).addTo(map)
            .on('mouseover', function () { this.setStyle({ weight: 3, fillOpacity: 0.7 }); })
            .on('mouseout', function () { this.setStyle({ weight: 2, fillOpacity: 0.5 }); });

        capas.push(capa);
    });

    if (capas.length > 0) {
        map.fitBounds(L.featureGroup(capas).getBounds(), { padding: [24, 24] });
    } else {
        map.setView([-31.4201, -64.1888], 6);
    }
</script>
@endpush
