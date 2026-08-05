@extends('layouts.app')

@section('title', $perfil->nombre_comercial)

@section('content')
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8 flex items-start gap-4">
        @if ($perfil->logo_url)
            <img src="{{ $perfil->logo_url }}" alt="{{ $perfil->nombre_comercial }}" class="w-16 h-16 rounded-md object-cover">
        @endif
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $perfil->nombre_comercial }}</h1>
            @if ($perfil->descripcion)
                <p class="text-gray-600 mt-1">{{ $perfil->descripcion }}</p>
            @endif
        </div>
    </div>

    <h2 class="font-medium text-gray-900 mb-4">Propiedades publicadas</h2>

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
                <div class="p-4">
                    <h3 class="font-medium text-gray-900 line-clamp-1">{{ $ficha->titulo }}</h3>
                    @if ($ficha->precio)
                        <p class="font-semibold text-indigo-700">{{ $ficha->moneda }} {{ number_format((float) $ficha->precio, 0, ',', '.') }}</p>
                    @endif
                </div>
            </a>
        @empty
            <p class="col-span-full text-center text-gray-500 py-12">Todavía no hay propiedades publicadas.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $fichas->links() }}
    </div>
@endsection
