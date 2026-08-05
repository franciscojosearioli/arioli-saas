@extends('layouts.app')

@section('title', $perfil->nombre)

@section('content')
    <div class="bg-white border border-gray-200 rounded-lg p-6 flex items-start gap-4">
        @if ($perfil->logo_url)
            <img src="{{ $perfil->logo_url }}" alt="{{ $perfil->nombre }}" class="w-16 h-16 rounded-md object-cover">
        @endif
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $perfil->nombre }}</h1>
            @if ($perfil->descripcion)
                <p class="text-gray-600 mt-1">{{ $perfil->descripcion }}</p>
            @endif
        </div>
    </div>

    @if ($desarrollos->isNotEmpty())
        <div class="mt-8">
            <h2 class="font-medium text-gray-900 mb-3">Desarrollos a su cargo</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($desarrollos as $desarrollo)
                    <a href="{{ route('desarrollos.show', $desarrollo) }}" class="block bg-white border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors">
                        <div class="font-medium text-gray-900">{{ $desarrollo->nombre }}</div>
                        <div class="text-sm text-gray-500">{{ collect([$desarrollo->ciudad, $desarrollo->provincia])->filter()->implode(', ') }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
