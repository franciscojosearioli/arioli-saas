@extends('layouts.admin')
@section('content')

@push('styles')
<style>
.ua-show-wrap { max-width: 640px; display: flex; flex-direction: column; gap: 20px; }

.ua-back-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; border-radius: 10px;
    border: 1px solid var(--border); background: var(--card);
    font-size: 12px; font-weight: 600; color: var(--t2);
    text-decoration: none; transition: all .12s;
}
.ua-back-btn:hover { border-color: #1a3561; color: #1a3561; text-decoration: none; }
.ua-back-btn svg { width: 13px; height: 13px; }

.ua-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
}
.ua-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--border);
}
.ua-card-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 600; color: var(--t1);
}
.ua-card-title svg { width: 16px; height: 16px; color: #1a3561; }
.ua-card-id { font-size: 11px; color: var(--t3); font-family: monospace; }

.ua-meta-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 0;
}
.ua-meta-cell {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    border-right: 1px solid var(--border);
}
.ua-meta-cell:nth-child(even) { border-right: none; }
.ua-meta-cell:nth-last-child(-n+2) { border-bottom: none; }
.ua-meta-cell.full { grid-column: 1 / -1; border-right: none; }
.ua-meta-cell.full:last-child { border-bottom: none; }
.ua-lbl {
    font-size: 10px; font-weight: 600; color: var(--t3);
    text-transform: uppercase; letter-spacing: .07em; margin-bottom: 5px;
}
.ua-val { font-size: 13px; color: var(--t1); }

.ua-users { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 2px; }
.ua-user-tag {
    display: inline-flex; align-items: center;
    padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 600;
    background: #eff6ff; color: #1a3561; border: 1px solid rgba(29,78,216,.15);
}

.ua-link-val {
    color: var(--accent); text-decoration: none; font-size: 13px;
    word-break: break-all;
}
.ua-link-val:hover { text-decoration: underline; }

.ua-actions-row {
    display: flex; gap: 10px; align-items: center; padding: 16px 20px;
    border-top: 1px solid var(--border); background: #f8fafc;
}
.ua-del-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 9px;
    background: #fef2f2; color: #dc2626;
    font-size: 12px; font-weight: 600; border: 1px solid #fecaca;
    cursor: pointer; font-family: var(--font-sans);
    transition: all .12s;
}
.ua-del-btn:hover { background: #dc2626; color: #fff; }
.ua-del-btn svg { width: 13px; height: 13px; }
</style>
@endpush

<div class="ua-show-wrap">

    {{-- Back --}}
    <div>
        <a href="{{ route('admin.user-alerts.index') }}" class="ua-back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a alertas
        </a>
    </div>

    {{-- Card --}}
    <div class="ua-card">
        <div class="ua-card-head">
            <div class="ua-card-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Detalle de Alerta
            </div>
            <span class="ua-card-id">#{{ $userAlert->id }}</span>
        </div>

        <div class="ua-meta-grid">
            <div class="ua-meta-cell full">
                <div class="ua-lbl">Mensaje</div>
                <div class="ua-val" style="font-size:14px;">{{ $userAlert->alert_text }}</div>
            </div>

            @if($userAlert->alert_link)
            <div class="ua-meta-cell full">
                <div class="ua-lbl">Enlace</div>
                <div class="ua-val">
                    <a href="{{ $userAlert->alert_link }}" class="ua-link-val" target="_blank">{{ $userAlert->alert_link }}</a>
                </div>
            </div>
            @endif

            <div class="ua-meta-cell full">
                <div class="ua-lbl">Destinatarios</div>
                <div class="ua-users">
                    @forelse($userAlert->users as $user)
                    <span class="ua-user-tag">{{ $user->name }}</span>
                    @empty
                    <span style="font-size:12px;color:var(--t3);">Sin destinatarios</span>
                    @endforelse
                </div>
            </div>

            <div class="ua-meta-cell">
                <div class="ua-lbl">Fecha de creación</div>
                <div class="ua-val">{{ $userAlert->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <div class="ua-meta-cell">
                <div class="ua-lbl">Última modificación</div>
                <div class="ua-val">{{ $userAlert->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        @can('user_alert_delete')
        <div class="ua-actions-row">
            <form action="{{ route('admin.user-alerts.destroy', $userAlert->id) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar esta alerta?')">
                @csrf @method('DELETE')
                <button type="submit" class="ua-del-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar alerta
                </button>
            </form>
        </div>
        @endcan
    </div>

</div>

@endsection
