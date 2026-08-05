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
@endsection
