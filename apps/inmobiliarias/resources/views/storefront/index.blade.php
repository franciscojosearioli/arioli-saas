@extends('layouts.storefront')

@push('styles')
<style>
    .search-box { background: var(--bg2); border: 1px solid var(--border); border-radius: 14px; padding: 22px; margin-bottom: 36px; }
    .search-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
    .search-grid .wide { grid-column: 1 / -1; }
    label.f-label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--text3); margin-bottom: 6px; }
    .f-input, .f-select {
        width: 100%; background: var(--bg3); border: 1px solid var(--border); border-radius: 8px;
        color: var(--text); padding: 9px 12px; font-size: 14px; font-family: 'DM Sans', sans-serif;
    }
    .f-input:focus, .f-select:focus { outline: none; border-color: var(--accent); }
    .search-actions { display: flex; justify-content: flex-end; margin-top: 16px; gap: 10px; }

    .grid-propiedades { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .card { background: var(--bg2); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; transition: transform .15s, box-shadow .15s; }
    .card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,.35); }
    .card-img { aspect-ratio: 4/3; background: var(--bg3); display: flex; align-items: center; justify-content: center; color: var(--text3); font-size: 13px; overflow: hidden; }
    .card-img img { width: 100%; height: 100%; object-fit: cover; }
    .card-body { padding: 16px; }
    .card-price { font-size: 18px; font-weight: 700; color: #93c5fd; margin-bottom: 4px; }
    .card-title { font-size: 15px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
    .card-loc { font-size: 13px; color: var(--text3); margin-bottom: 10px; }
    .card-tags { display: flex; gap: 8px; flex-wrap: wrap; }
    .tag { font-size: 11px; padding: 3px 8px; border-radius: 99px; background: rgba(255,255,255,.06); color: var(--text2); }

    .empty { text-align: center; padding: 60px 20px; color: var(--text3); }
    .pagination-wrap { margin-top: 32px; }
    .pagination-wrap nav { color: var(--text2); }
</style>
@endpush

@section('content')
    <div class="eyebrow">{{ $configuracion->nombre_comercial ?? config('app.name') }}</div>
    <h1 class="title">Propiedades disponibles</h1>
    <p class="sub">{{ $propiedades->total() }} publicada{{ $propiedades->total() === 1 ? '' : 's' }}</p>

    <form method="GET" class="search-box">
        <div class="search-grid">
            <div class="wide">
                <label class="f-label" for="q">Buscar</label>
                <input class="f-input" type="search" id="q" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Título, barrio...">
            </div>
            <div>
                <label class="f-label" for="tipo">Tipo</label>
                <select class="f-select" id="tipo" name="tipo">
                    <option value="">Cualquiera</option>
                    @foreach (['loteo' => 'Loteo', 'casa' => 'Casa', 'departamento' => 'Departamento', 'local' => 'Local', 'oficina' => 'Oficina', 'galpon' => 'Galpón', 'campo' => 'Campo', 'cochera' => 'Cochera'] as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="f-label" for="tipo_operacion">Operación</label>
                <select class="f-select" id="tipo_operacion" name="tipo_operacion">
                    <option value="">Cualquiera</option>
                    <option value="venta" @selected(($filtros['tipo_operacion'] ?? '') === 'venta')>Venta</option>
                    <option value="alquiler" @selected(($filtros['tipo_operacion'] ?? '') === 'alquiler')>Alquiler</option>
                    <option value="reserva" @selected(($filtros['tipo_operacion'] ?? '') === 'reserva')>Reserva</option>
                </select>
            </div>
            <div>
                <label class="f-label" for="provincia">Provincia</label>
                <select class="f-select" id="provincia" name="provincia">
                    <option value="">Cualquiera</option>
                    @foreach (config('argentina.provincias') as $provincia)
                        <option value="{{ $provincia }}" @selected(($filtros['provincia'] ?? '') === $provincia)>{{ $provincia }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="f-label" for="ciudad">Ciudad</label>
                <input class="f-input" type="text" id="ciudad" name="ciudad" value="{{ $filtros['ciudad'] ?? '' }}">
            </div>
            <div>
                <label class="f-label" for="precio_min">Precio mín.</label>
                <input class="f-input" type="number" id="precio_min" name="precio_min" value="{{ $filtros['precio_min'] ?? '' }}">
            </div>
            <div>
                <label class="f-label" for="precio_max">Precio máx.</label>
                <input class="f-input" type="number" id="precio_max" name="precio_max" value="{{ $filtros['precio_max'] ?? '' }}">
            </div>
            <div>
                <label class="f-label" for="ambientes">Ambientes mín.</label>
                <input class="f-input" type="number" id="ambientes" name="ambientes" value="{{ $filtros['ambientes'] ?? '' }}">
            </div>
        </div>
        <div class="search-actions">
            <a href="{{ route('storefront.index') }}" class="btn-ghost">Limpiar</a>
            <button type="submit" class="btn-primary">Buscar</button>
        </div>
    </form>

    @if ($propiedades->isEmpty())
        <div class="empty">No hay propiedades publicadas que coincidan con la búsqueda.</div>
    @else
        <div class="grid-propiedades">
            @foreach ($propiedades as $propiedad)
                @php
                    $foto = $propiedad->fotos->firstWhere('es_principal', true) ?? $propiedad->fotos->first();
                    $tipoLabel = match ($propiedad->tipo) {
                        'loteo' => 'Loteo', 'casa' => 'Casa', 'departamento' => 'Departamento', 'local' => 'Local',
                        'oficina' => 'Oficina', 'galpon' => 'Galpón', 'campo' => 'Campo', 'cochera' => 'Cochera',
                    };
                @endphp
                <a href="{{ route('storefront.propiedad', $propiedad) }}" class="card">
                    <div class="card-img">
                        @if ($foto)
                            <img src="{{ $foto->url }}" alt="{{ $propiedad->titulo }}">
                        @else
                            Sin foto
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($propiedad->precio)
                            <div class="card-price">{{ $propiedad->moneda }} {{ number_format((float) $propiedad->precio, 0, ',', '.') }}</div>
                        @endif
                        <div class="card-title">{{ $propiedad->titulo }}</div>
                        <div class="card-loc">{{ collect([$propiedad->barrio, $propiedad->ciudad, $propiedad->provincia])->filter()->implode(', ') ?: '—' }}</div>
                        <div class="card-tags">
                            <span class="tag">{{ $tipoLabel }}</span>
                            @if ($propiedad->ambientes)
                                <span class="tag">{{ $propiedad->ambientes }} amb.</span>
                            @endif
                            @if ($propiedad->desarrollo)
                                <span class="tag">{{ $propiedad->desarrollo->nombre }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pagination-wrap">
            {{ $propiedades->links() }}
        </div>
    @endif
@endsection
