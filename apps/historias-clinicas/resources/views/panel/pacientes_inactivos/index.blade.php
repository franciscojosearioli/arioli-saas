@extends('layouts.panel')
@section('title', 'Pacientes Inactivos')
@section('content')

@push('styles')
<style>
    .pinact-wrap {
        display: flex; flex-direction: column; gap: 20px;
        animation: pinactFadeUp .35s ease both;
    }
    @@keyframes pinactFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .pinact-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .pinact-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .pinact-header-left p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

    .pinact-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow); overflow: hidden;
    }
    .pinact-table-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-wrap: wrap; gap: 8px;
    }
    .pinact-table-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .pinact-table-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
    .pinact-count {
        font-size: 12px; color: var(--text-muted, #94a3b8);
        display: flex; align-items: center; gap: 6px;
    }
    .pinact-count strong { color: var(--text-primary, #0f172a); }
    .pinact-table-body { padding: 16px 20px; }

    /* ── egreso badge ── */
    .pinact-egreso {
        display: inline-flex; padding: 2px 8px; border-radius: 99px;
        font-size: 11px; font-weight: 600;
    }
    .pinact-egreso.alta      { background: #f0fdf4; color: #16a34a; }
    .pinact-egreso.abandono  { background: #fffbeb; color: #b45309; }
    .pinact-egreso.expulsion { background: #fff1f2; color: #e11d48; }
    .pinact-egreso.other     { background: var(--body-bg,#f1f5f9); color: var(--text-secondary,#64748b); }

    .pinact-action-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity .15s; font-family: var(--font-sans, inherit);
    }
    .pinact-action-btn:hover { opacity: .82; text-decoration: none; }
    .pinact-action-btn svg  { width: 12px; height: 12px; }
    .pinact-action-btn.view   { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); }
    .pinact-action-btn.edit   { background: #f0fdf4; color: #16a34a; }
    .pinact-action-btn.danger { background: #fff1f2; color: #e11d48; }
</style>
@endpush

<div class="pinact-wrap">

    <div class="pinact-header">
        <div class="pinact-header-left">
            <h1>Pacientes Inactivos</h1>
            <p>Historias clínicas con egreso registrado</p>
        </div>
    </div>

    <div class="pinact-table-card">
        <div class="pinact-table-head">
            <div class="pinact-table-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Listado de historias clínicas inactivas
            </div>
            <div class="pinact-count">Total: <strong>{{ $Pacientes->count() }}</strong></div>
        </div>

        <div class="pinact-table-body">
            <div class="table-responsive">
                <table class="table datatable datatable-Paciente" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.paciente.fields.nombre') }}</th>
                            <th>{{ trans('cruds.paciente.fields.apellido') }}</th>
                            <th>{{ trans('cruds.paciente.fields.dni') }}</th>
                            <th>Motivo de egreso</th>
                            <th>Fecha de egreso</th>
                            <th style="width:160px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Pacientes as $Paciente)
                        <tr data-entry-id="{{ $Paciente->id }}">
                            <td></td>
                            <td>{{ $Paciente->nombre }}</td>
                            <td>{{ $Paciente->apellido }}</td>
                            <td>{{ $Paciente->dni }}</td>
                            <td>
                                @php
                                    $egreso = $Paciente->ficha_admision->tipo_egreso ?? null;
                                    $cls = match(strtolower($egreso ?? '')) {
                                        'alta'      => 'alta',
                                        'abandono'  => 'abandono',
                                        'expulsion','expulsión' => 'expulsion',
                                        default     => 'other',
                                    };
                                @endphp
                                @if($egreso)
                                    <span class="pinact-egreso {{ $cls }}">{{ $egreso }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($Paciente->ficha_admision?->fecha_egreso)
                                    {{ \Carbon\Carbon::parse($Paciente->ficha_admision->fecha_egreso)->format('d/m/Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    @can('paciente_show')
                                    <a href="{{ route('panel.paciente.show', $Paciente->id) }}" class="pinact-action-btn view">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-12 0c0 5.523 4.477 10 10 10S22 17.523 22 12 17.523 2 12 2 2 6.477 2 12z"/>
                                        </svg>
                                        {{ trans('global.view') }}
                                    </a>
                                    @endcan
                                    @can('paciente_edit')
                                    <a href="{{ route('panel.paciente.edit', $Paciente->id) }}" class="pinact-action-btn edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        {{ trans('global.edit') }}
                                    </a>
                                    @endcan
                                    @can('paciente_delete')
                                    <form action="{{ route('panel.paciente.destroy', $Paciente->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}')"
                                          style="display:inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="pinact-action-btn danger">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            {{ trans('global.delete') }}
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

@push('scripts')
<script>
$(function () {
    var dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

    @can('paciente_delete')
    dtButtons.push({
        text: '{{ trans('global.datatables.delete') }}',
        url:  '{{ route('panel.paciente.massDestroy') }}',
        className: 'btn-danger',
        action: function (e, dt, node, config) {
            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id');
            });
            if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return; }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                    headers: { 'x-csrf-token': _token },
                    method: 'POST', url: config.url,
                    data: { ids: ids, _method: 'DELETE' }
                }).done(function () { location.reload(); });
            }
        }
    });
    @endcan

    $.extend(true, $.fn.dataTable.defaults, {
        orderCellsTop: true, order: [[ 1, 'asc' ]], pageLength: 100,
    });
    $('.datatable-Paciente:not(.ajaxTable)').DataTable({ buttons: dtButtons });
});
</script>
@endpush
