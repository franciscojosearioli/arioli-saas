@extends('layouts.admin')
@section('content')

@push('styles')
<style>
    .esp-wrap {
        display: flex; flex-direction: column; gap: 20px;
        animation: espFadeUp .35s ease both;
    }
    @@keyframes espFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── header ── */
    .esp-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .esp-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .esp-header-left p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

    .esp-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px;
        font-size: 13px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: background .15s, transform .15s;
    }
    .esp-btn:hover { transform: translateY(-1px); text-decoration: none; }
    .esp-btn svg  { width: 15px; height: 15px; }
    .esp-btn.primary {
        background: var(--accent, #1d4ed8); color: #fff;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
    }
    .esp-btn.primary:hover { background: var(--accent-hover, #1e40af); color: #fff; }

    /* ── flash ── */
    .esp-alert {
        padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 500;
        background: #f0fdf4; color: #16a34a;
        border: 1px solid #bbf7d0;
        display: flex; align-items: center; gap: 8px;
    }
    .esp-alert svg { width: 15px; height: 15px; flex-shrink: 0; }

    /* ── table card ── */
    .esp-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow); overflow: hidden;
    }
    .esp-table-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-wrap: wrap; gap: 8px;
    }
    .esp-table-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .esp-table-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
    .esp-table-body { padding: 16px 20px; }

    /* ── count badge ── */
    .esp-count {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 9px; border-radius: 99px; font-size: 11px; font-weight: 600;
        background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8);
    }

    /* ── action btn ── */
    .esp-action-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity .15s; font-family: var(--font-sans, inherit);
    }
    .esp-action-btn:hover { opacity: .82; text-decoration: none; }
    .esp-action-btn svg  { width: 12px; height: 12px; }
    .esp-action-btn.edit   { background: #f0fdf4; color: #16a34a; }
    .esp-action-btn.danger { background: #fff1f2; color: #e11d48; }
</style>
@endpush

<div class="esp-wrap">

    {{-- ── Header ── --}}
    <div class="esp-header">
        <div class="esp-header-left">
            <h1>Especialidades</h1>
            <p>Especialidades médicas disponibles en el sistema</p>
        </div>
        @can('especialidad_create')
        <a href="{{ route('admin.especialidades.create') }}" class="esp-btn primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Especialidad
        </a>
        @endcan
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
    <div class="esp-alert">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Tabla ── --}}
    <div class="esp-table-card">
        <div class="esp-table-head">
            <div class="esp-table-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                Listado de especialidades
            </div>
        </div>

        <div class="esp-table-body">
            <div class="table-responsive">
                <table class="table datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Profesionales</th>
                            <th style="width:140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($especialidades as $especialidad)
                        <tr>
                            <td></td>
                            <td>{{ $especialidad->nombre }}</td>
                            <td>{{ $especialidad->descripcion ?? '—' }}</td>
                            <td>
                                <span class="esp-count">{{ $especialidad->profesionales_count }}</span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    @can('especialidad_edit')
                                    <a href="{{ route('admin.especialidades.edit', $especialidad->id) }}" class="esp-action-btn edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </a>
                                    @endcan
                                    @can('especialidad_delete')
                                    <form action="{{ route('admin.especialidades.destroy', $especialidad->id) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar especialidad?')"
                                          style="display:inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="esp-action-btn danger">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
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
        </div>
    </div>

</div>

@endsection
