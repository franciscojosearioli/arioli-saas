@extends('layouts.app')

@section('title', $desarrollo->nombre)

@section('content')
    @php
        $tipoLabel = fn (string $tipo) => match ($tipo) {
            'loteo' => 'Loteo', 'barrio_cerrado' => 'Barrio cerrado',
            'edificio' => 'Edificio', 'emprendimiento' => 'Emprendimiento',
        };
        $unidadLabel = $desarrollo->tipo === 'edificio' ? 'unidad' : 'lote';
    @endphp

    <a href="{{ route('busqueda') }}" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Volver a la búsqueda</a>

    <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="text-xs uppercase tracking-wide text-gray-400">{{ $tipoLabel($desarrollo->tipo) }}</div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $desarrollo->nombre }}</h1>
            <p class="text-gray-500">{{ collect([$desarrollo->barrio, $desarrollo->ciudad, $desarrollo->provincia])->filter()->implode(', ') }}</p>
        </div>
        <div class="text-sm text-gray-500">{{ $totalUnidades }} {{ $unidadLabel }}{{ $totalUnidades === 1 ? '' : 's' }} publicado{{ $totalUnidades === 1 ? '' : 's' }}</div>
    </div>

    @if ($desarrollo->descripcion)
        <p class="mt-4 text-gray-700 whitespace-pre-line">{{ $desarrollo->descripcion }}</p>
    @endif

    <div class="mt-6 bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="flex flex-wrap items-center gap-5 px-4 py-2 border-b border-gray-100 text-xs text-gray-500">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background:#22c55e"></span> Disponible</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background:#eab308"></span> Reservado</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background:#ef4444"></span> Vendido</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background:#3b82f6"></span> Alquilado</span>
        </div>
        <div id="mapa-desarrollo" style="height: 480px;"></div>
    </div>

    @if ($unidades->isEmpty())
        <p class="mt-4 text-sm text-gray-400">
            @if ($totalUnidades > 0)
                Este desarrollo tiene {{ $totalUnidades }} {{ $unidadLabel }}{{ $totalUnidades === 1 ? '' : 's' }} publicado{{ $totalUnidades === 1 ? '' : 's' }}, pero todavía ninguno tiene su ubicación cargada en el mapa.
            @else
                Este desarrollo todavía no tiene unidades publicadas.
            @endif
        </p>
    @endif
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const unidades = @json($unidades);
        const poligonoGeneral = @json($poligonoGeneral);
        const colorMap = { disponible: '#22c55e', reservado: '#eab308', vendido: '#ef4444', alquilado: '#3b82f6' };

        const map = L.map('mapa-desarrollo');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);

        const capas = [];

        if (poligonoGeneral) {
            const contorno = L.geoJSON(poligonoGeneral, {
                style: { color: '#6366f1', weight: 2, dashArray: '6 4', fillOpacity: 0.03 },
            }).addTo(map);
            capas.push(contorno);
        }

        function centro(geom) {
            const coords = geom.type === 'Polygon' ? geom.coordinates[0] : [geom.coordinates];
            let lat = 0, lng = 0;
            coords.forEach(c => { lng += c[0]; lat += c[1]; });
            return [lat / coords.length, lng / coords.length];
        }

        unidades.forEach(unidad => {
            const color = colorMap[unidad.estado] || '#94a3b8';
            const precio = unidad.precio
                ? `${unidad.moneda} ${Number(unidad.precio).toLocaleString('es-AR')}`
                : '';
            const popup = `
                <div style="font-family:inherit;font-size:13px;min-width:160px;">
                    <div style="font-weight:600;margin-bottom:4px;">${unidad.titulo}</div>
                    ${precio ? `<div style="color:#6366f1;font-weight:600;margin-bottom:4px;">${precio}</div>` : ''}
                    <span style="display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;background:${color}22;color:${color};">
                        ${unidad.estado.charAt(0).toUpperCase() + unidad.estado.slice(1)}
                    </span><br>
                    <a href="/propiedades/${unidad.slug}" style="color:#4f46e5;font-size:12px;">Ver detalle &rarr;</a>
                </div>`;

            const capa = L.geoJSON(unidad.coordenadas, {
                pointToLayer: (feature, latlng) => L.circleMarker(latlng, {
                    radius: 8, color, fillColor: color, fillOpacity: 0.7, weight: 2,
                }),
                style: { color, fillColor: color, fillOpacity: 0.45, weight: 2 },
            }).bindPopup(popup).addTo(map)
                .on('mouseover', function () { this.setStyle({ weight: 3, fillOpacity: 0.65 }); })
                .on('mouseout', function () { this.setStyle({ weight: 2, fillOpacity: 0.45 }); });

            capas.push(capa);
        });

        if (capas.length > 0) {
            const grupo = L.featureGroup(capas);
            map.fitBounds(grupo.getBounds(), { padding: [24, 24] });
        } else {
            map.setView([-31.4201, -64.1888], 6);
        }
    </script>
@endpush
