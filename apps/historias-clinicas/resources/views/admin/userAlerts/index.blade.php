@extends('layouts.admin')
@section('content')

@push('styles')
<style>
.ua-wrap { display: flex; flex-direction: column; gap: 20px; }

.ua-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
}
.ua-header h1 { font-size: 22px; font-weight: 700; color: var(--t1); letter-spacing: -.02em; margin: 0; }
.ua-header p  { font-size: 13px; color: var(--t2); margin: 3px 0 0; }

.ua-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; border-radius: 10px;
    font-size: 13px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    transition: background .15s, transform .12s;
}
.ua-btn:hover { transform: translateY(-1px); text-decoration: none; }
.ua-btn svg   { width: 15px; height: 15px; }
.ua-btn.primary { background: #1a3561; color: #fff; box-shadow: 0 2px 8px rgba(26,53,97,.2); }
.ua-btn.primary:hover { background: #142a4f; color: #fff; }

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

.ua-table-wrap { overflow-x: auto; }
.ua-table { width: 100%; border-collapse: collapse; min-width: 600px; }
.ua-table th {
    font-size: 10px; font-weight: 700; color: #1a3561;
    text-transform: uppercase; letter-spacing: .06em;
    padding: 10px 16px; text-align: left;
    background: #f8fafc; border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.ua-table td {
    padding: 12px 16px; font-size: 13px; color: var(--t1);
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}
.ua-table tbody tr:last-child td { border-bottom: none; }
.ua-table tbody tr:hover td { background: #f8fafc; }

.ua-id { font-size: 11px; color: var(--t2); font-family: monospace; }
.ua-text { font-size: 13px; color: var(--t1); }
.ua-link { font-size: 12px; color: var(--accent); text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; display: inline-block; }
.ua-link:hover { text-decoration: underline; }
.ua-users { display: flex; flex-wrap: wrap; gap: 4px; }
.ua-user-tag {
    display: inline-flex; align-items: center;
    padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 600;
    background: #eff6ff; color: #1a3561; border: 1px solid rgba(29,78,216,.15);
}
.ua-date { font-size: 11px; color: var(--t2); white-space: nowrap; }

.ua-actions { display: flex; gap: 6px; flex-wrap: nowrap; }
.ua-act-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 11px; border-radius: 7px; font-size: 11px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer; transition: all .12s;
    white-space: nowrap;
}
.ua-act-btn svg { width: 12px; height: 12px; }
.ua-act-btn.view { background: #f0fdf4; color: #166534; }
.ua-act-btn.view:hover { background: #166534; color: #fff; }
.ua-act-btn.del  { background: #fef2f2; color: #dc2626; }
.ua-act-btn.del:hover { background: #dc2626; color: #fff; }

.ua-empty {
    padding: 48px 20px; text-align: center; color: var(--t2); font-size: 13px;
}
.ua-empty svg { width: 36px; height: 36px; margin: 0 auto 10px; display: block; opacity: .3; }

.ua-alert {
    padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 500;
    background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;
    display: flex; align-items: center; gap: 8px;
}
</style>
@endpush

<div class="ua-wrap">

    {{-- Header --}}
    <div class="ua-header">
        <div>
            <h1>Alertas de Usuarios</h1>
            <p>Notificaciones enviadas a los usuarios del sistema.</p>
        </div>
        @can('user_alert_create')
        <a href="{{ route('admin.user-alerts.create') }}" class="ua-btn primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva alerta
        </a>
        @endcan
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="ua-alert">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Table --}}
    <div class="ua-card">
        <div class="ua-card-head">
            <div class="ua-card-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Alertas registradas
            </div>
        </div>

        @php $alerts = \App\Models\UserAlert::with('users')->latest()->get(); @endphp

        @if($alerts->isEmpty())
        <div class="ua-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            No hay alertas registradas. Creá la primera.
        </div>
        @else
        <div class="ua-table-wrap">
            <table class="ua-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Mensaje</th>
                        <th>Enlace</th>
                        <th>Destinatarios</th>
                        <th>Fecha</th>
                        <th style="width:140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts as $alert)
                    <tr>
                        <td><span class="ua-id">{{ $alert->id }}</span></td>
                        <td><span class="ua-text">{{ $alert->alert_text }}</span></td>
                        <td>
                            @if($alert->alert_link)
                            <a href="{{ $alert->alert_link }}" class="ua-link" target="_blank" title="{{ $alert->alert_link }}">{{ $alert->alert_link }}</a>
                            @else
                            <span style="color:var(--t3);font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="ua-users">
                                @forelse($alert->users as $user)
                                <span class="ua-user-tag">{{ $user->name }}</span>
                                @empty
                                <span style="color:var(--t3);font-size:12px;">Sin destinatarios</span>
                                @endforelse
                            </div>
                        </td>
                        <td><span class="ua-date">{{ $alert->created_at->format('d/m/Y H:i') }}</span></td>
                        <td>
                            <div class="ua-actions">
                                @can('user_alert_show')
                                <a href="{{ route('admin.user-alerts.show', $alert->id) }}" class="ua-act-btn view">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Ver
                                </a>
                                @endcan
                                @can('user_alert_delete')
                                <form action="{{ route('admin.user-alerts.destroy', $alert->id) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar esta alerta?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ua-act-btn del">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

@endsection
