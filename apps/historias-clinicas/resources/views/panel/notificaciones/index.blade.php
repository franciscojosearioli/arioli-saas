@extends('layouts.panel')

@section('title', 'Mis Notificaciones')

@section('content')

@push('styles')
<style>
.notif-page { display: flex; flex-direction: column; gap: 20px; max-width: 720px; }

.notif-topbar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
}
.notif-topbar h1 { font-size: 22px; font-weight: 700; color: var(--text-primary, #0f172a); letter-spacing: -.02em; margin: 0; }
.notif-topbar p  { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

.notif-read-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 10px;
    background: var(--accent, #1d4ed8); color: #fff;
    font-size: 12px; font-weight: 600; border: none; cursor: pointer;
    font-family: var(--font-sans); transition: background .12s, transform .12s;
    text-decoration: none;
}
.notif-read-btn:hover { background: var(--accent-hover, #1e40af); transform: translateY(-1px); color: #fff; }
.notif-read-btn svg { width: 14px; height: 14px; }

.notif-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: var(--card-radius, 14px);
    box-shadow: var(--card-shadow);
    overflow: hidden;
}
.notif-card-hdr {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid var(--card-border, #e8edf2);
    font-size: 14px; font-weight: 600;
    color: var(--text-primary, #0f172a);
}
.notif-card-hdr-l {
    display: flex; align-items: center; gap: 8px;
}
.notif-card-hdr-l svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
.notif-badge {
    display: inline-flex; padding: 2px 9px; border-radius: 99px;
    font-size: 10px; font-weight: 700;
    background: #fef2f2; color: #dc2626;
}

.notif-list-item {
    display: flex; gap: 14px; align-items: flex-start;
    padding: 16px 20px;
    border-bottom: 1px solid var(--card-border, #e8edf2);
    transition: background .1s;
}
.notif-list-item:last-child { border-bottom: none; }
.notif-list-item.is-unread { background: var(--accent-light, #eff6ff); }
.notif-list-item:hover { background: #f8fafc; }
.notif-list-item.is-unread:hover { background: #dbeafe; }

.notif-indicator {
    width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; margin-top: 4px;
}
.notif-indicator.unread { background: var(--accent, #1d4ed8); }
.notif-indicator.read   { background: var(--card-border, #e8edf2); }

.notif-content { flex: 1; min-width: 0; }
.notif-msg {
    font-size: 14px; color: var(--text-primary, #0f172a);
    line-height: 1.5; margin-bottom: 6px;
    font-weight: 500;
}
.notif-list-item.is-unread .notif-msg { font-weight: 600; }
.notif-link-row {
    display: inline-flex; align-items: center; gap: 4px;
    margin-bottom: 6px;
}
.notif-ext-link {
    font-size: 12px; color: var(--accent, #1d4ed8); text-decoration: none; font-weight: 500;
}
.notif-ext-link:hover { text-decoration: underline; }
.notif-ext-link svg { width: 11px; height: 11px; }
.notif-footer {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.notif-date-str { font-size: 11px; color: var(--text-muted, #94a3b8); }
.notif-mark-one {
    font-size: 11px; color: var(--text-secondary, #64748b);
    background: none; border: none; cursor: pointer; font-family: var(--font-sans);
    padding: 3px 8px; border-radius: 6px; transition: all .1s;
}
.notif-mark-one:hover { background: var(--card-border, #e8edf2); color: var(--text-primary, #0f172a); }

.notif-empty {
    padding: 52px 20px; text-align: center;
    color: var(--text-muted, #94a3b8); font-size: 13px;
}
.notif-empty svg { width: 38px; height: 38px; margin: 0 auto 10px; display: block; opacity: .3; }

.notif-flash {
    padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 500;
    background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;
    display: flex; align-items: center; gap: 8px;
}
</style>
@endpush

<div class="notif-page">

    @php
        $unreadCount = $userAlerts->filter(fn($a) => !$a->pivot->read)->count();
    @endphp

    {{-- Header --}}
    <div class="notif-topbar">
        <div>
            <h1>Mis Notificaciones</h1>
            <p>
                {{ $userAlerts->count() }} notificación{{ $userAlerts->count() != 1 ? 'es' : '' }}
                @if($unreadCount > 0)
                — <span style="color:#dc2626;font-weight:600;">{{ $unreadCount }} sin leer</span>
                @endif
            </p>
        </div>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('panel.notificaciones.readAll') }}" style="margin:0;">
            @csrf
            <button type="submit" class="notif-read-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Marcar todas como leídas
            </button>
        </form>
        @endif
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="notif-flash">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- List --}}
    <div class="notif-card">
        <div class="notif-card-hdr">
            <div class="notif-card-hdr-l">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Historial
            </div>
            @if($unreadCount > 0)
            <span class="notif-badge">{{ $unreadCount }} nuevas</span>
            @endif
        </div>

        @forelse($userAlerts as $alert)
        @php $read = (bool)($alert->pivot->read ?? true); @endphp
        <div class="notif-list-item {{ !$read ? 'is-unread' : '' }}">
            <div class="notif-indicator {{ !$read ? 'unread' : 'read' }}"></div>
            <div class="notif-content">
                <div class="notif-msg">{{ $alert->alert_text }}</div>
                @if($alert->alert_link)
                <div class="notif-link-row">
                    <a href="{{ $alert->alert_link }}" class="notif-ext-link" target="_blank">
                        Ver enlace
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
                @endif
                <div class="notif-footer">
                    <span class="notif-date-str">
                        {{ $alert->created_at->format('d/m/Y H:i') }}
                        <span style="margin-left:4px;">· {{ $alert->created_at->diffForHumans() }}</span>
                    </span>
                    @if(!$read)
                    <form method="POST" action="{{ route('panel.notificaciones.readOne', $alert->id) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="notif-mark-one">Marcar como leída</button>
                    </form>
                    @else
                    <span style="font-size:11px;color:var(--text-muted,#94a3b8);">Leída</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="notif-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            No tenés notificaciones todavía.
        </div>
        @endforelse
    </div>

</div>

@endsection
