@extends('layouts.panel')
@section('title', 'Informes')

@push('styles')
<style>
    /* ══════════════════════════════════════
       INFORMES — design tokens
    ══════════════════════════════════════ */
    .inf-wrap {
        display: flex; flex-direction: column; gap: 20px;
        max-width: 1400px; margin: 0 auto; width: 100%;
        animation: infFadeUp .35s ease both;
    }
    @@keyframes infFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── page header ── */
    .inf-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .inf-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .inf-header-left p {
        font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0;
    }
    .inf-btn-new {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px;
        background: var(--accent, #1d4ed8); color: #fff;
        font-size: 13px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: background .15s, transform .15s;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
    }
    .inf-btn-new:hover { background: var(--accent-hover, #1e40af); transform: translateY(-1px); color:#fff; text-decoration:none; }
    .inf-btn-new svg  { width: 15px; height: 15px; }

    /* ── filter bar ── */
    .inf-filters {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow);
        padding: 14px 20px;
        display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
    }
    .inf-filter-group { display: flex; flex-direction: column; gap: 3px; flex: 1; min-width: 160px; }
    .inf-filter-label { font-size: 10px; font-weight: 600; text-transform: uppercase;
                        letter-spacing: .07em; color: var(--text-muted, #94a3b8); }
    .inf-filters input,
    .inf-filters select {
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: 8px; padding: 7px 10px;
        font-size: 13px; color: var(--text-primary, #0f172a);
        background: var(--body-bg, #f8fafc);
        font-family: var(--font-sans, inherit);
        width: 100%; outline: none;
        transition: border-color .15s;
    }
    .inf-filters input:focus,
    .inf-filters select:focus { border-color: var(--accent, #1d4ed8); }
    html.dark .inf-filters input,
    html.dark .inf-filters select { background: #0f172a; color: #f1f5f9; border-color: #1e293b; }
    .inf-btn-clear {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px; border: 1px solid var(--card-border, #e8edf2);
        background: var(--body-bg, #f8fafc); color: var(--text-secondary, #64748b);
        font-size: 13px; font-weight: 500; cursor: pointer;
        transition: background .15s; white-space: nowrap;
        font-family: var(--font-sans, inherit); align-self: flex-end;
    }
    .inf-btn-clear:hover { background: var(--card-border, #e8edf2); }
    .inf-btn-clear svg { width: 14px; height: 14px; }

    /* ── table card ── */
    .inf-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }
    .inf-table-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-wrap: wrap; gap: 8px;
    }
    .inf-table-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .inf-table-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
    .inf-table-body { padding: 16px 20px; }

    /* ── DataTables overrides ── */
    .inf-table-body .dataTables_wrapper .dataTables_length select,
    .inf-table-body .dataTables_wrapper .dataTables_filter input {
        border: 1px solid var(--card-border, #e8edf2) !important;
        border-radius: 8px !important; padding: 5px 10px !important;
        font-size: 12px !important; background: var(--body-bg, #f8fafc) !important;
        color: var(--text-primary, #0f172a) !important;
        font-family: var(--font-sans, inherit) !important;
    }
    .inf-table-body .dataTables_wrapper .dataTables_info { font-size: 12px; color: var(--text-muted, #94a3b8); }
    .inf-table-body table.dataTable { border-collapse: collapse !important; width: 100% !important; }
    .inf-table-body table.dataTable thead th {
        background: var(--body-bg, #f8fafc) !important;
        color: var(--text-muted, #94a3b8) !important;
        font-size: 10px !important; font-weight: 700 !important;
        text-transform: uppercase !important; letter-spacing: .07em !important;
        border-bottom: 2px solid var(--card-border, #e8edf2) !important;
        border-top: none !important; padding: 10px 14px !important;
    }
    .inf-table-body table.dataTable tbody td {
        font-size: 13px; color: var(--text-primary, #0f172a);
        border-bottom: 1px solid var(--card-border, #e8edf2) !important;
        border-top: none !important;
        padding: 11px 14px !important; vertical-align: middle;
    }
    .inf-table-body table.dataTable tbody tr:last-child td { border-bottom: none !important; }
    .inf-table-body table.dataTable tbody tr:hover td { background: var(--body-bg, #f8fafc) !important; }
    html.dark .inf-table-body table.dataTable thead th { background: #0b1120 !important; }
    html.dark .inf-table-body table.dataTable tbody tr:hover td { background: #0b1120 !important; }

    /* ── Pagination ── */
    .inf-table-body .dataTables_wrapper .pagination .page-item .page-link {
        border-radius: 7px !important; font-size: 12px !important;
        border-color: var(--card-border, #e8edf2) !important;
        color: var(--text-secondary, #64748b) !important;
        background: var(--card-bg, #fff) !important;
        margin: 0 2px;
    }
    .inf-table-body .dataTables_wrapper .pagination .page-item.active .page-link {
        background: var(--accent, #1d4ed8) !important;
        border-color: var(--accent, #1d4ed8) !important;
        color: #fff !important;
    }

    /* ── action buttons ── */
    .inf-action-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity .15s; font-family: var(--font-sans, inherit);
    }
    .inf-action-btn:hover { opacity: .82; text-decoration: none; }
    .inf-action-btn svg  { width: 12px; height: 12px; }
    .inf-action-btn.view   { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); }
    .inf-action-btn.edit   { background: #fff7ed; color: #c2410c; }
    .inf-action-btn.danger { background: #fff1f2; color: #e11d48; }

    /* ── download link ── */
    .inf-download {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 12px; color: var(--accent, #1d4ed8);
        text-decoration: none; font-weight: 500;
    }
    .inf-download:hover { text-decoration: underline; }
    .inf-download svg { width: 13px; height: 13px; }
    .inf-no-file { font-size: 12px; color: var(--text-muted, #94a3b8); }

    /* ── tipo badge ── */
    .inf-tipo {
        display: inline-block; padding: 2px 10px; border-radius: 99px;
        font-size: 11px; font-weight: 600;
        background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8);
    }
</style>
@endpush

@section('content')
<div class="inf-wrap">

    {{-- ── Header ── --}}
    <div class="inf-header">
        <div class="inf-header-left">
            <h1>Informes</h1>
            <p>Consultá y gestioná los informes de cada paciente</p>
        </div>
        @can('informe_create')
        <a href="{{ route('panel.informe.create') }}" class="inf-btn-new">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Informe
        </a>
        @endcan
    </div>

    {{-- ── Filtros ── --}}
    <div class="inf-filters">
        <div class="inf-filter-group">
            <span class="inf-filter-label">Fecha</span>
            <input type="date" id="filter-fecha">
        </div>
        <div class="inf-filter-group">
            <span class="inf-filter-label">Tipo de informe</span>
            <select id="filter-tipo">
                <option value="">Todos los tipos</option>
                @foreach($tiposDeInforme as $tipo)
                    <option value="{{ $tipo->name }}">{{ $tipo->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="inf-filter-group" style="flex:2;min-width:200px;">
            <span class="inf-filter-label">Paciente</span>
            <input type="text" id="filter-paciente" placeholder="Buscar por nombre o apellido…">
        </div>
        <button type="button" class="inf-btn-clear" id="reset-filters">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Limpiar
        </button>
    </div>

    {{-- ── Tabla ── --}}
    <div class="inf-table-card">
        <div class="inf-table-head">
            <div class="inf-table-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Listado de informes
            </div>
        </div>
        <div class="inf-table-body">
            <div class="table-responsive">
                <table class="table datatable datatable-Informe" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.informe.fields.fecha') }}</th>
                            <th>{{ trans('cruds.informe.fields.tipo') }}</th>
                            <th>{{ trans('cruds.informe.fields.paciente') }}</th>
                            @can('paciente_edit')
                            <th>Archivos</th>
                            @endcan
                            <th style="width:120px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Informes as $Informe)
                        <tr data-entry-id="{{ $Informe->id }}">
                            <td></td>
                            <td data-fecha="{{ \Carbon\Carbon::parse($Informe->fecha)->format('Y-m-d') }}">
                                {{ \Carbon\Carbon::parse($Informe->fecha)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($Informe->tipo)
                                    <span class="inf-tipo">{{ $Informe->tipo->name }}</span>
                                @endif
                            </td>
                            <td>
                                {{ trim(($Informe->paciente->nombre ?? '') . ' ' . ($Informe->paciente->apellido ?? '')) ?: '—' }}
                            </td>
                            @can('paciente_edit')
                            <td>
                                @php
                                    $files = $Informe->document_files ? json_decode($Informe->document_files) : null;
                                @endphp
                                @if($files && (is_array($files) || is_object($files)) && count((array)$files) > 0)
                                    @foreach($files as $i => $file)
                                    <a href="{{ asset('storage/uploads/' . ($Informe->paciente->id ?? '') . '/' . ($Informe->tipo->id ?? '') . '/' . $file) }}"
                                       target="_blank" class="inf-download" style="{{ $i > 0 ? 'margin-top:4px;display:inline-flex;' : '' }}">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Descargar{{ count((array)$files) > 1 ? ' (' . ($i + 1) . ')' : '' }}
                                    </a><br>
                                    @endforeach
                                @else
                                    <span class="inf-no-file">Sin archivos</span>
                                @endif
                            </td>
                            @endcan
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    @can('informe_show')
                                    <a href="{{ route('panel.informe.show', $Informe->id) }}" class="inf-action-btn view">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-12 0c0 5.523 4.477 10 10 10S22 17.523 22 12 17.523 2 12 2 2 6.477 2 12z"/>
                                        </svg>
                                        Ver
                                    </a>
                                    @endcan
                                    @can('informe_edit')
                                    @if(auth()->user()->is_admin || $Informe->profesional_id == auth()->id())
                                    <a href="{{ route('panel.informe.edit', $Informe->id) }}" class="inf-action-btn edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </a>
                                    @endif
                                    @endcan
                                    @can('informe_delete')
                                    @if(auth()->user()->is_admin || $Informe->profesional_id == auth()->id())
                                    <form action="{{ route('panel.informe.destroy', $Informe->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inf-action-btn danger">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                    @endif
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

    @can('informe_delete')
    dtButtons.push({
        text: '{{ trans('global.datatables.delete') }}',
        url:  '{{ route('panel.informe.massDestroy') }}',
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
        order: [[1, 'desc']],
        pageLength: 100,
    });

    var table = $('.datatable-Informe').DataTable({ buttons: dtButtons });

    // Filtros personalizados
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var fecha    = $('#filter-fecha').val();
        var tipo     = $('#filter-tipo').val().toLowerCase();
        var paciente = $('#filter-paciente').val().toLowerCase();

        var rowFecha    = $(table.row(dataIndex).node()).find('td').eq(1).data('fecha') || '';
        var rowTipo     = data[2].toLowerCase();
        var rowPaciente = data[3].toLowerCase();

        return (fecha    === '' || rowFecha    === fecha) &&
               (tipo     === '' || rowTipo.includes(tipo)) &&
               (paciente === '' || rowPaciente.includes(paciente));
    });

    $('#filter-fecha, #filter-tipo, #filter-paciente').on('keyup change', function () {
        table.draw();
    });

    $('#reset-filters').on('click', function () {
        $('#filter-fecha').val('');
        $('#filter-tipo').val('');
        $('#filter-paciente').val('');
        table.draw();
    });
});
</script>
@endpush
