@extends('layouts.app')

@section('title', $ficha->titulo)

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

    <a href="{{ route('busqueda') }}" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Volver a la búsqueda</a>

    <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center text-gray-400">
                @if (! empty($ficha->galeria))
                    <img src="{{ $ficha->galeria[0] }}" alt="{{ $ficha->titulo }}" class="w-full h-full object-cover">
                @else
                    Sin fotos
                @endif
            </div>

            @if (count($ficha->galeria ?? []) > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach (array_slice($ficha->galeria, 1) as $foto)
                        <img src="{{ $foto }}" class="aspect-video object-cover rounded-md">
                    @endforeach
                </div>
            @endif

            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $ficha->titulo }}</h1>
                <p class="text-gray-500">{{ collect([$ficha->direccion, $ficha->barrio, $ficha->ciudad, $ficha->provincia])->filter()->implode(', ') }}</p>
            </div>

            @if ($ficha->descripcion)
                <p class="text-gray-700 whitespace-pre-line">{{ $ficha->descripcion }}</p>
            @endif

            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                @if ($ficha->ambientes)
                    <div><dt class="text-gray-400">Ambientes</dt><dd class="font-medium">{{ $ficha->ambientes }}</dd></div>
                @endif
                @if ($ficha->dormitorios)
                    <div><dt class="text-gray-400">Dormitorios</dt><dd class="font-medium">{{ $ficha->dormitorios }}</dd></div>
                @endif
                @if ($ficha->banos)
                    <div><dt class="text-gray-400">Baños</dt><dd class="font-medium">{{ $ficha->banos }}</dd></div>
                @endif
                @if ($ficha->superficie_total)
                    <div><dt class="text-gray-400">Superficie</dt><dd class="font-medium">{{ $ficha->superficie_total }} m²</dd></div>
                @endif
            </dl>

            @if (! empty($ficha->servicios))
                <div>
                    <h2 class="font-medium text-gray-900 mb-2">Servicios</h2>
                    <ul class="flex flex-wrap gap-2 text-sm">
                        @foreach ($ficha->servicios as $servicio)
                            <li class="bg-gray-100 rounded-full px-3 py-1">{{ $servicio }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <aside class="bg-white border border-gray-200 rounded-lg p-6 h-fit space-y-3">
            <div class="text-xs uppercase tracking-wide text-gray-400">
                {{ $operacionLabel($ficha->tipo_operacion) }} · {{ $tipoLabel($ficha->tipo_propiedad) }}
            </div>
            @if ($ficha->precio)
                <p class="text-2xl font-semibold text-indigo-700">{{ $ficha->moneda }} {{ number_format((float) $ficha->precio, 0, ',', '.') }}</p>
            @endif
            @if ($ficha->nombre_desarrollo)
                <p class="text-sm text-gray-500">Parte de <span class="font-medium text-gray-700">{{ $ficha->nombre_desarrollo }}</span></p>
            @endif
        </aside>
    </div>
@endsection
