<x-admin-layout title="Configuración">

    {{-- Header --}}
    <div style="display:flex; align-items:center; gap:14px; margin-bottom:28px;">
        <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Configuración</h1>
            <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">Configuración global del sistema</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">
            <ul style="list-style:none; padding:0; margin:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabs --}}
    <div style="display:flex; gap:4px; border-bottom:1px solid var(--border-color, #e5e7eb); margin-bottom:24px; overflow-x:auto;">
        @foreach($tabs as $key => $tab)
            <a href="{{ route('configuracion.index', ['tab' => $key]) }}"
               style="padding:10px 16px; font-size:13.5px; font-weight:600; white-space:nowrap; text-decoration:none;
                      color:{{ $active === $key ? 'var(--accent)' : 'var(--text-muted)' }};
                      border-bottom:2px solid {{ $active === $key ? 'var(--accent)' : 'transparent' }};">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    <div class="card" style="padding:28px; max-width:720px;">
        @include($tabs[$active]['view'])
    </div>

</x-admin-layout>
