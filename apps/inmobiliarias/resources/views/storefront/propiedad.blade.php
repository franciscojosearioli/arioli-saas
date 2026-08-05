@extends('layouts.storefront')

@section('title', $propiedad->titulo)

@push('styles')
<style>
    .back-link { display: inline-block; font-size: 13px; color: var(--text3); margin-bottom: 18px; }
    .back-link:hover { color: var(--text); }
    .ficha-grid { display: grid; grid-template-columns: 1fr; gap: 32px; }
    @media (min-width: 900px) { .ficha-grid { grid-template-columns: 2fr 1fr; } }

    .gallery-main { aspect-ratio: 16/9; background: var(--bg2); border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; color: var(--text3); margin-bottom: 10px; }
    .gallery-main img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
    .gallery-strip img { aspect-ratio: 4/3; object-fit: cover; border-radius: 8px; width: 100%; }

    .ficha-title { font-size: 24px; font-weight: 700; margin: 24px 0 4px; }
    .ficha-loc { color: var(--text3); font-size: 14px; margin-bottom: 20px; }
    .ficha-desc { color: var(--text2); line-height: 1.8; white-space: pre-line; margin-bottom: 24px; }

    .specs { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .spec dt { font-size: 11px; color: var(--text3); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px; }
    .spec dd { font-size: 15px; font-weight: 600; }

    .servicios-list { display: flex; flex-wrap: wrap; gap: 8px; }
    .servicios-list li { font-size: 12px; padding: 4px 10px; border-radius: 99px; background: var(--bg2); border: 1px solid var(--border); color: var(--text2); }

    .aside-card { background: var(--bg2); border: 1px solid var(--border); border-radius: 14px; padding: 22px; height: fit-content; position: sticky; top: 84px; }
    .aside-price { font-size: 26px; font-weight: 700; color: #93c5fd; margin-bottom: 4px; }
    .aside-op { font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: var(--text3); margin-bottom: 16px; }
    .aside-row { display: flex; justify-content: space-between; padding: 10px 0; border-top: 1px solid var(--border); font-size: 13px; }
    .aside-row dt { color: var(--text3); } .aside-row dd { color: var(--text); font-weight: 500; }
</style>
@endpush

@section('content')
    <a href="{{ route('storefront.index') }}" class="back-link">&larr; Volver a la búsqueda</a>

    @php
        $tipoLabel = match ($propiedad->tipo) {
            'loteo' => 'Loteo', 'casa' => 'Casa', 'departamento' => 'Departamento', 'local' => 'Local',
            'oficina' => 'Oficina', 'galpon' => 'Galpón', 'campo' => 'Campo', 'cochera' => 'Cochera',
        };
        $operacionActiva = $propiedad->operaciones->first();
        $operacionLabel = match ($operacionActiva?->tipo) {
            'venta' => 'Venta', 'alquiler' => 'Alquiler', 'reserva' => 'Reserva', default => null,
        };
        $fotos = $propiedad->fotos;
    @endphp

    <div class="ficha-grid">
        <div>
            <div class="gallery-main">
                @if ($fotos->isNotEmpty())
                    <img src="{{ $fotos->first()->url }}" alt="{{ $propiedad->titulo }}">
                @else
                    Sin fotos
                @endif
            </div>
            @if ($fotos->count() > 1)
                <div class="gallery-strip">
                    @foreach ($fotos->skip(1)->take(4) as $foto)
                        <img src="{{ $foto->url }}" alt="{{ $propiedad->titulo }}">
                    @endforeach
                </div>
            @endif

            <h1 class="ficha-title">{{ $propiedad->titulo }}</h1>
            <p class="ficha-loc">{{ collect([$propiedad->direccion, $propiedad->barrio, $propiedad->ciudad, $propiedad->provincia])->filter()->implode(', ') }}</p>

            @if ($propiedad->descripcion)
                <p class="ficha-desc">{{ $propiedad->descripcion }}</p>
            @endif

            <dl class="specs">
                @if ($propiedad->ambientes)
                    <div class="spec"><dt>Ambientes</dt><dd>{{ $propiedad->ambientes }}</dd></div>
                @endif
                @if ($propiedad->dormitorios)
                    <div class="spec"><dt>Dormitorios</dt><dd>{{ $propiedad->dormitorios }}</dd></div>
                @endif
                @if ($propiedad->banos)
                    <div class="spec"><dt>Baños</dt><dd>{{ $propiedad->banos }}</dd></div>
                @endif
                @if ($propiedad->cocheras)
                    <div class="spec"><dt>Cocheras</dt><dd>{{ $propiedad->cocheras }}</dd></div>
                @endif
                @if ($propiedad->superficie_total)
                    <div class="spec"><dt>Superficie</dt><dd>{{ $propiedad->superficie_total }} m²</dd></div>
                @endif
            </dl>

            @if (! empty($propiedad->servicios))
                <ul class="servicios-list">
                    @foreach ($propiedad->servicios as $servicio)
                        <li>{{ $servicio }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <aside class="aside-card">
            @if ($operacionLabel || $tipoLabel)
                <div class="aside-op">{{ collect([$operacionLabel, $tipoLabel])->filter()->implode(' · ') }}</div>
            @endif
            @if ($propiedad->precio)
                <div class="aside-price">{{ $propiedad->moneda }} {{ number_format((float) $propiedad->precio, 0, ',', '.') }}</div>
            @endif

            @if ($propiedad->desarrollo)
                <dl class="aside-row">
                    <dt>Desarrollo</dt>
                    <dd><a href="{{ route('storefront.desarrollo', $propiedad->desarrollo) }}" style="color:#93c5fd;">{{ $propiedad->desarrollo->nombre }}</a></dd>
                </dl>
            @endif
            <dl class="aside-row">
                <dt>Estado</dt>
                <dd>{{ ucfirst($propiedad->estado) }}</dd>
            </dl>
        </aside>
    </div>
@endsection
