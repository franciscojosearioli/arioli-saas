@extends('layouts.panel')
@section('title', 'Agenda')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    /* ══════════════════════════════════════
       AGENDA — design tokens
    ══════════════════════════════════════ */
    :root {
        --ag-pending:    #f59e0b;
        --ag-pending-bg: #fffbeb;
        --ag-ok:         #16a34a;
        --ag-ok-bg:      #f0fdf4;
        --ag-cancel:     #dc2626;
        --ag-cancel-bg:  #fef2f2;
        --ag-done:       #64748b;
        --ag-done-bg:    #f8fafc;
        --ag-virtual:    #0891b2;
        --ag-virtual-bg: #f0fdfa;
    }
    html.dark {
        --ag-pending-bg: #1c1000;
        --ag-ok-bg:      #052e16;
        --ag-cancel-bg:  #1c0000;
        --ag-done-bg:    #0f172a;
        --ag-virtual-bg: #042f2e;
    }

    /* ── wrapper ── */
    .ag-wrap {
        display: flex; flex-direction: column; gap: 20px;
        max-width: 1400px; margin: 0 auto; width: 100%;
        animation: agFadeUp .35s ease both;
    }
    @@keyframes agFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── page header ── */
    .ag-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .ag-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .ag-header-left p {
        font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0;
    }
    .ag-btn-new {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px;
        background: var(--accent, #1d4ed8); color: #fff;
        font-size: 13px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: background .15s, transform .15s;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
    }
    .ag-btn-new:hover { background: var(--accent-hover, #1e40af); transform: translateY(-1px); color:#fff; text-decoration:none; }
    .ag-btn-new svg  { width: 15px; height: 15px; }

    /* ── filter bar ── */
    .ag-filters {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow);
        padding: 14px 20px;
        display: flex; flex-wrap: wrap; gap: 12px; align-items: center;
    }
    .ag-filter-group { display: flex; flex-direction: column; gap: 3px; flex: 1; min-width: 160px; }
    .ag-filter-label { font-size: 10px; font-weight: 600; text-transform: uppercase;
                       letter-spacing: .07em; color: var(--text-muted, #94a3b8); }
    .ag-filters select {
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: 8px; padding: 7px 10px;
        font-size: 13px; color: var(--text-primary, #0f172a);
        background: var(--body-bg, #f8fafc);
        font-family: var(--font-sans, inherit);
        width: 100%; outline: none;
        transition: border-color .15s;
    }
    .ag-filters select:focus { border-color: var(--accent, #1d4ed8); }
    html.dark .ag-filters select { background: #0f172a; color: #f1f5f9; border-color: #1e293b; }
    .ag-btn-clear {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px; border: 1px solid var(--card-border, #e8edf2);
        background: var(--body-bg, #f8fafc); color: var(--text-secondary, #64748b);
        font-size: 13px; font-weight: 500; cursor: pointer;
        transition: background .15s; white-space: nowrap;
        font-family: var(--font-sans, inherit); align-self: flex-end;
    }
    .ag-btn-clear:hover { background: var(--card-border, #e8edf2); }
    .ag-btn-clear svg { width: 14px; height: 14px; }

    /* ── calendar card ── */
    .ag-calendar-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }
    .ag-calendar-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-wrap: wrap; gap: 10px;
    }
    .ag-calendar-head-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .ag-calendar-head-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
    .ag-legend { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    .ag-legend-item {
        display: flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 500; color: var(--text-secondary, #64748b);
    }
    .ag-dot {
        width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
    }
    .ag-calendar-body { padding: 20px; }

    /* ── FullCalendar overrides ── */
    .fc { font-family: var(--font-sans, inherit) !important; font-size: 13px; }
    .fc .fc-button {
        background: var(--body-bg, #f8fafc) !important;
        border: 1px solid var(--card-border, #e8edf2) !important;
        color: var(--text-secondary, #64748b) !important;
        border-radius: 8px !important;
        font-size: 12px !important; font-weight: 600 !important;
        padding: 5px 12px !important; transition: all .15s !important;
        box-shadow: none !important;
    }
    .fc .fc-button:hover {
        background: var(--card-border, #e8edf2) !important;
        color: var(--text-primary, #0f172a) !important;
    }
    .fc .fc-button-primary:not(.fc-button-active):focus { box-shadow: none !important; }
    .fc .fc-button-active, .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: var(--accent, #1d4ed8) !important;
        border-color: var(--accent, #1d4ed8) !important;
        color: #fff !important;
    }
    .fc .fc-toolbar-title {
        font-size: 16px !important; font-weight: 700 !important;
        color: var(--text-primary, #0f172a) !important;
    }
    .fc .fc-col-header-cell-cushion,
    .fc .fc-daygrid-day-number {
        color: var(--text-secondary, #64748b) !important;
        text-decoration: none !important;
    }
    .fc .fc-daygrid-day.fc-day-today { background: var(--accent-light, #eff6ff) !important; }
    .fc .fc-daygrid-day-top { padding: 4px 6px; }
    .fc-theme-standard td, .fc-theme-standard th { border-color: var(--card-border, #e8edf2) !important; }
    .fc-theme-standard .fc-scrollgrid { border-color: var(--card-border, #e8edf2) !important; }
    .fc .fc-event {
        border-radius: 6px !important; border: none !important;
        font-size: 11px !important; padding: 2px 6px !important;
        font-weight: 600 !important; cursor: pointer;
    }
    .fc .fc-daygrid-more-link { font-size: 11px; color: var(--accent, #1d4ed8); font-weight: 600; }
    html.dark .fc .fc-col-header-cell-cushion,
    html.dark .fc .fc-daygrid-day-number { color: #94a3b8 !important; }
    html.dark .fc-theme-standard td,
    html.dark .fc-theme-standard th,
    html.dark .fc-theme-standard .fc-scrollgrid { border-color: #1e293b !important; }
    html.dark .fc .fc-toolbar-title { color: #f1f5f9 !important; }

    /* ── badges ── */
    .ag-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 10px; border-radius: 99px;
        font-size: 11px; font-weight: 700;
    }
    .ag-badge.pending  { background: var(--ag-pending-bg); color: var(--ag-pending); }
    .ag-badge.ok       { background: var(--ag-ok-bg);      color: var(--ag-ok); }
    .ag-badge.cancel   { background: var(--ag-cancel-bg);  color: var(--ag-cancel); }
    .ag-badge.done     { background: var(--ag-done-bg);    color: var(--ag-done); }
    .ag-badge.virtual  { background: var(--ag-virtual-bg); color: var(--ag-virtual); }
    .ag-badge.presencial { background: #f1f5f9; color: #475569; }
</style>
@endpush

@section('content')
<div class="ag-wrap">

    {{-- ── Header ── --}}
    <div class="ag-header">
        <div class="ag-header-left">
            <h1>Agenda de Citas</h1>
            <p>Visualizá y gestioná las citas del sistema</p>
        </div>
        @can('agenda_create')
        <a href="{{ route('panel.agenda.create') }}" class="ag-btn-new">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Cita
        </a>
        @endcan
    </div>

    {{-- ── Filtros ── --}}
    <div class="ag-filters">
        <div class="ag-filter-group">
            <span class="ag-filter-label">Paciente</span>
            <select id="filter-paciente" class="select2-filter">
                <option value="">Todos los pacientes</option>
                @foreach($pacientes as $p)
                <option value="{{ $p->id }}">{{ $p->apellido }}, {{ $p->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="ag-filter-group">
            <span class="ag-filter-label">Profesional</span>
            <select id="filter-profesional" class="select2-filter">
                <option value="">Todos los profesionales</option>
                @foreach($profesionales as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="ag-filter-group">
            <span class="ag-filter-label">Estado</span>
            <select id="filter-estado">
                <option value="">Todos los estados</option>
                @foreach($estados as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="ag-filter-group">
            <span class="ag-filter-label">Modalidad</span>
            <select id="filter-modalidad">
                <option value="">Presencial / Virtual</option>
                @foreach($modalidades as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button id="btn-limpiar-filtros" class="ag-btn-clear">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Limpiar
        </button>
    </div>

    {{-- ── Calendario ── --}}
    <div class="ag-calendar-card">
        <div class="ag-calendar-head">
            <div class="ag-calendar-head-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Calendario de citas
            </div>
            <div class="ag-legend">
                <div class="ag-legend-item">
                    <span class="ag-dot" style="background:var(--ag-pending)"></span> Pendiente
                </div>
                <div class="ag-legend-item">
                    <span class="ag-dot" style="background:var(--ag-ok)"></span> Confirmado
                </div>
                <div class="ag-legend-item">
                    <span class="ag-dot" style="background:var(--ag-cancel)"></span> Cancelado
                </div>
                <div class="ag-legend-item">
                    <span class="ag-dot" style="background:var(--ag-done)"></span> Realizado
                </div>
            </div>
        </div>
        <div class="ag-calendar-body">
            <div id="calendar"></div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', list: 'Lista' },
        height: 'auto',
        nowIndicator: true,
        navLinks: false,
        selectable: false,
        editable: false,
        dayMaxEvents: 4,
        events: function (info, successCallback, failureCallback) {
            $.ajax({
                url: '{{ route("panel.agenda.eventos") }}',
                method: 'GET',
                data: {
                    start:          info.startStr,
                    end:            info.endStr,
                    paciente_id:    $('#filter-paciente').val(),
                    profesional_id: $('#filter-profesional').val(),
                    estado:         $('#filter-estado').val(),
                    modalidad:      $('#filter-modalidad').val(),
                },
                success: function (data) { successCallback(data); },
                error:   function ()     { failureCallback(); }
            });
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            var props = info.event.extendedProps;
            @canany(['agenda_show', 'agenda_edit'])
            window.location.href = props.url_show;
            @endcanany
        },
    });

    calendar.render();

    $('#filter-paciente, #filter-profesional, #filter-estado, #filter-modalidad').on('change', function () {
        calendar.refetchEvents();
    });

    $('#btn-limpiar-filtros').on('click', function () {
        $('#filter-paciente, #filter-profesional').val(null).trigger('change');
        $('#filter-estado, #filter-modalidad').val('');
        calendar.refetchEvents();
    });

    $('.select2-filter').select2({
        width: '100%',
        placeholder: function() { return $(this).find('option:first').text(); },
        allowClear: true
    });
});
</script>
@endpush
