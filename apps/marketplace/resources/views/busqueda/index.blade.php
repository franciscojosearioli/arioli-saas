@extends('layouts.app')

@section('title', 'Buscar propiedades')

@section('content')
    @php
        $tipoLabel = fn (?string $tipo) => match ($tipo) {
            'loteo' => 'Loteo', 'casa' => 'Casa', 'departamento' => 'Departamento', 'local' => 'Local',
            'oficina' => 'Oficina', 'galpon' => 'Galpón', 'campo' => 'Campo', 'cochera' => 'Cochera',
            default => null,
        };
        $operacionLabel = fn (?string $op) => match ($op) {
            'venta' => 'Venta', 'alquiler' => 'Alquiler', 'reserva' => 'Reserva', default => null,
        };
    @endphp

    <form method="GET" class="bg-white border border-gray-200 rounded-lg p-4 mb-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Buscar..." class="col-span-2 lg:col-span-2 rounded-md border-gray-300 text-sm">

        <select name="tipo_operacion" class="rounded-md border-gray-300 text-sm">
            <option value="">Operación</option>
            <option value="venta" @selected(($filtros['tipo_operacion'] ?? '') === 'venta')>Venta</option>
            <option value="alquiler" @selected(($filtros['tipo_operacion'] ?? '') === 'alquiler')>Alquiler</option>
            <option value="reserva" @selected(($filtros['tipo_operacion'] ?? '') === 'reserva')>Reserva</option>
        </select>

        <select name="tipo_propiedad" class="rounded-md border-gray-300 text-sm">
            <option value="">Tipo</option>
            @foreach (['loteo', 'casa', 'departamento', 'local', 'oficina', 'galpon', 'campo', 'cochera'] as $tipo)
                <option value="{{ $tipo }}" @selected(($filtros['tipo_propiedad'] ?? '') === $tipo)>{{ $tipoLabel($tipo) }}</option>
            @endforeach
        </select>

        <input type="text" name="provincia" value="{{ $filtros['provincia'] ?? '' }}" placeholder="Provincia" class="rounded-md border-gray-300 text-sm">
        <input type="text" name="ciudad" value="{{ $filtros['ciudad'] ?? '' }}" placeholder="Ciudad" class="rounded-md border-gray-300 text-sm">

        <input type="number" name="precio_min" value="{{ $filtros['precio_min'] ?? '' }}" placeholder="Precio mín." class="rounded-md border-gray-300 text-sm">
        <input type="number" name="precio_max" value="{{ $filtros['precio_max'] ?? '' }}" placeholder="Precio máx." class="rounded-md border-gray-300 text-sm">

        <button type="submit" class="col-span-2 sm:col-span-1 bg-indigo-600 text-white rounded-md text-sm font-medium py-2 hover:bg-indigo-700">
            Buscar
        </button>
    </form>

    <p class="text-sm text-gray-500 mb-4">{{ $fichas->total() }} propiedades encontradas.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($fichas as $ficha)
            <a href="{{ route('fichas.show', $ficha) }}" class="block bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                <div class="aspect-video bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                    @if (! empty($ficha->galeria))
                        <img src="{{ $ficha->galeria[0] }}" alt="{{ $ficha->titulo }}" class="w-full h-full object-cover">
                    @else
                        Sin fotos
                    @endif
                </div>
                <div class="p-4 space-y-1">
                    <div class="text-xs uppercase tracking-wide text-gray-400">
                        {{ $operacionLabel($ficha->tipo_operacion) }} · {{ $tipoLabel($ficha->tipo_propiedad) }}
                    </div>
                    <h3 class="font-medium text-gray-900 line-clamp-1">{{ $ficha->titulo }}</h3>
                    <p class="text-sm text-gray-500">{{ collect([$ficha->barrio, $ficha->ciudad, $ficha->provincia])->filter()->implode(', ') }}</p>
                    @if ($ficha->precio)
                        <p class="font-semibold text-indigo-700">{{ $ficha->moneda }} {{ number_format((float) $ficha->precio, 0, ',', '.') }}</p>
                    @endif
                </div>
            </a>
        @empty
            <p class="col-span-full text-center text-gray-500 py-12">No hay propiedades que coincidan con la búsqueda.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $fichas->links() }}
    </div>
@endsection
