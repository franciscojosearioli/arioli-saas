@extends('layouts.panel')
@section('title', 'Prescripción')

@push('styles')
<style>
    .med-wrap {
        display: flex; flex-direction: column; gap: 20px;
        max-width: 1400px; margin: 0 auto; width: 100%;
        animation: medFadeUp .35s ease both;
    }
    @@keyframes medFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── header ── */
    .med-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .med-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .med-header-left p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }
    .med-header-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

    .med-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px;
        font-size: 13px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: background .15s, transform .15s;
    }
    .med-btn:hover { transform: translateY(-1px); text-decoration: none; }
    .med-btn svg  { width: 15px; height: 15px; }
    .med-btn.primary {
        background: var(--accent, #1d4ed8); color: #fff;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
    }
    .med-btn.primary:hover { background: var(--accent-hover, #1e40af); color: #fff; }
    .med-btn.ghost {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        color: var(--text-secondary, #64748b);
    }
    .med-btn.ghost:hover { background: var(--body-bg, #f8fafc); color: var(--text-primary, #0f172a); }

    /* ── filter bar ── */
    .med-filters {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow);
        padding: 14px 20px;
        display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
    }
    .med-filter-group { display: flex; flex-direction: column; gap: 3px; flex: 1; min-width: 200px; }
    .med-filter-label {
        font-size: 10px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .07em; color: var(--text-muted, #94a3b8);
    }
    .med-filters input {
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: 8px; padding: 7px 10px;
        font-size: 13px; color: var(--text-primary, #0f172a);
        background: var(--body-bg, #f8fafc);
        font-family: var(--font-sans, inherit);
        width: 100%; outline: none; transition: border-color .15s;
    }
    .med-filters input:focus { border-color: var(--accent, #1d4ed8); }
    html.dark .med-filters input { background: #0f172a; color: #f1f5f9; border-color: #1e293b; }
    .med-btn-clear {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px; border: 1px solid var(--card-border, #e8edf2);
        background: var(--body-bg, #f8fafc); color: var(--text-secondary, #64748b);
        font-size: 13px; font-weight: 500; cursor: pointer;
        transition: background .15s; font-family: var(--font-sans, inherit); white-space: nowrap;
    }
    .med-btn-clear:hover { background: var(--card-border, #e8edf2); }
    .med-btn-clear svg { width: 14px; height: 14px; }

    /* ── table card ── */
    .med-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow); overflow: hidden;
    }
    .med-table-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-wrap: wrap; gap: 8px;
    }
    .med-table-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .med-table-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
    .med-table-body { padding: 16px 20px; }

    /* ── action btn ── */
    .med-action-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity .15s; font-family: var(--font-sans, inherit);
    }
    .med-action-btn:hover { opacity: .82; text-decoration: none; }
    .med-action-btn svg  { width: 12px; height: 12px; }
    .med-action-btn.view   { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); }
    .med-action-btn.danger { background: #fff1f2; color: #e11d48; }
</style>
@endpush

@section('content')
<div class="med-wrap">

    {{-- ── Header ── --}}
    <div class="med-header">
        <div class="med-header-left">
            <h1>Prescripción</h1>
            <p>Esquemas de prescripción por paciente</p>
        </div>
        <div class="med-header-actions">
            <a href="{{ route('panel.medicacion.esquema') }}" target="_blank" class="med-btn ghost">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Esquema completo
            </a>
            @can('medicacion_create')
            <a href="{{ route('panel.medicacion.create') }}" class="med-btn primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo esquema
            </a>
            @endcan
        </div>
    </div>

    {{-- ── Filtros ── --}}
    <div class="med-filters">
        <div class="med-filter-group">
            <span class="med-filter-label">Paciente</span>
            <input type="text" id="filter-paciente" placeholder="Buscar por nombre o apellido…">
        </div>
        <button type="button" class="med-btn-clear" id="reset-filters">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Limpiar
        </button>
    </div>

    {{-- ── Tabla ── --}}
    <div class="med-table-card">
        <div class="med-table-head">
            <div class="med-table-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                Listado de esquemas
            </div>
        </div>
        <div class="med-table-body">
            <div class="table-responsive">
                <table class="table datatable datatable-Medicacion" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.medicacion.fields.paciente') }}</th>
                            <th>Fecha del esquema</th>
                            <th style="width:140px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicaciones as $medicacion)
                        <tr data-entry-id="{{ $medicacion['id'] }}">
                            <td></td>
                            <td>
                                {{ trim(($medicacion['paciente']->nombre ?? '') . ' ' . ($medicacion['paciente']->apellido ?? '')) ?: '—' }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($medicacion['fecha'])->format('d/m/Y') }}
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    @can('medicacion_show')
                                    <a href="{{ route('panel.medicacion.show', $medicacion['id']) }}" class="med-action-btn view">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-12 0c0 5.523 4.477 10 10 10S22 17.523 22 12 17.523 2 12 2 2 6.477 2 12z"/>
                                        </svg>
                                        Ver esquema
                                    </a>
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

    @can('medicacion_delete')
    dtButtons.push({
        text: '{{ trans('global.datatables.delete') }}',
        url:  '{{ route('panel.medicacion.massDestroy') }}',
        className: 'btn-danger',
        action: function (e, dt, node, config) {
            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id');
            });
            if (ids.length === 0) {
                alert('{{ trans('global.datatables.zero_selected') }}');
                return;
            }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                    headers: { 'x-csrf-token': _token },
                    method: 'POST',
                    url: config.url,
                    data: { ids: ids, _method: 'DELETE' }
                }).done(function () { location.reload(); });
            }
        }
    });
    @endcan

    $.extend(true, $.fn.dataTable.defaults, {
        orderCellsTop: true,
        order: [[2, 'desc']],
        pageLength: 100,
    });

    var table = $('.datatable-Medicacion').DataTable({ buttons: dtButtons });

    $.fn.dataTable.ext.search.push(function (settings, data) {
        var paciente = $('#filter-paciente').val().toLowerCase();
        return paciente === '' || data[0].toLowerCase().includes(paciente);
    });

    $('#filter-paciente').on('keyup change', function () { table.draw(); });

    $('#reset-filters').on('click', function () {
        $('#filter-paciente').val('');
        table.draw();
    });
});
</script>
@endpush
