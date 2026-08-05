@extends('layouts.storefront')

@section('title', $constructora->nombre)

@push('styles')
<style>
    .back-link { display: inline-block; font-size: 13px; color: var(--text3); margin-bottom: 18px; }
    .back-link:hover { color: var(--text); }
    .constructora-head { display: flex; align-items: center; gap: 16px; margin-bottom: 8px; }
    .constructora-logo { width: 64px; height: 64px; border-radius: 10px; object-fit: cover; background: var(--bg2); }
    .constructora-desc { color: var(--text2); line-height: 1.8; margin: 16px 0 32px; }

    .desarrollos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
    .desarrollo-card { background: var(--bg2); border: 1px solid var(--border); border-radius: 12px; padding: 18px; transition: border-color .15s, transform .15s; }
    .desarrollo-card:hover { border-color: var(--accent); transform: translateY(-2px); }
    .desarrollo-card .nombre { font-weight: 600; margin-bottom: 4px; }
    .desarrollo-card .loc { font-size: 13px; color: var(--text3); }
</style>
@endpush

@section('content')
    <a href="{{ route('storefront.index') }}" class="back-link">&larr; Volver a la búsqueda</a>

    <div class="constructora-head">
        @if ($constructora->logo)
            <img src="{{ $constructora->logo }}" alt="{{ $constructora->nombre }}" class="constructora-logo">
        @endif
        <div>
            <div class="eyebrow">Constructora</div>
            <h1 class="title" style="margin-bottom:0;">{{ $constructora->nombre }}</h1>
        </div>
    </div>

    @if ($constructora->descripcion)
        <p class="constructora-desc">{{ $constructora->descripcion }}</p>
    @endif

    @if ($constructora->desarrollos->isNotEmpty())
        <h2 style="font-size:18px; font-weight:600; margin-bottom:14px;">Desarrollos a su cargo</h2>
        <div class="desarrollos-grid">
            @foreach ($constructora->desarrollos as $desarrollo)
                <a href="{{ route('storefront.desarrollo', $desarrollo) }}" class="desarrollo-card">
                    <div class="nombre">{{ $desarrollo->nombre }}</div>
                    <div class="loc">{{ collect([$desarrollo->ciudad, $desarrollo->provincia])->filter()->implode(', ') ?: '—' }}</div>
                </a>
            @endforeach
        </div>
    @else
        <p style="color:var(--text3);">Todavía no tiene desarrollos publicados.</p>
    @endif
@endsection
