@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    #dash-calendar .fc-toolbar-title { font-size: 15px; }
    #dash-calendar .fc-button { padding: 4px 8px; font-size: 12.5px; }
    #dash-calendar .fc-daygrid-day-number,
    #dash-calendar .fc-col-header-cell-cushion { font-size: 12px; }
    #dash-calendar .fc-event { font-size: 11px; cursor: pointer; }

    :root {
        --green:       #16a34a; --green-bg:  #f0fdf4; --green-light: #dcfce7;
        --yellow:      #d97706; --yellow-bg: #fffbeb;
        --red:         #dc2626; --red-bg:    #fef2f2;
        --blue:        #1d4ed8; --blue-bg:   #eff6ff;
        --purple:      #7c3aed; --purple-bg: #f5f3ff;
    }
    html.dark {
        --green-bg: #052e16; --green-light: #14532d;
        --yellow-bg: #1c1000; --red-bg: #1c0000;
        --blue-bg: #0c1a3a; --blue: #3b82f6;
        --purple-bg: #1c1917;
    }

    .dash {
        display: flex; flex-direction: column; gap: 24px;
        max-width: 1400px; margin: 0 auto; width: 100%;
    }

    /* ── Welcome ── */
    .dash-welcome {
        display: flex; align-items: center;
        justify-content: space-between; flex-wrap: wrap; gap: 12px;
    }
    .dash-welcome h1 {
        font-size: 21px; font-weight: 700;
        color: var(--t1); letter-spacing: -.02em; margin: 0;
    }
    .dash-welcome p { font-size: 13px; color: var(--t2); margin: 3px 0 0; }
    .dash-date {
        font-size: 12px; color: var(--t3);
        background: var(--card); border: 1px solid var(--border);
        border-radius: 8px; padding: 6px 14px;
    }

    /* ── KPI grid ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
    }
    @@media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(3,1fr); } }
    @@media (max-width: 768px)  { .kpi-grid { grid-template-columns: repeat(2,1fr); } }
    @@media (max-width: 480px)  { .kpi-grid { grid-template-columns: 1fr; } }

    .kpi-card {
        background: var(--card); border: 1px solid var(--border);
        border-radius: 14px; box-shadow: var(--shadow);
        padding: 18px 20px; display: flex; flex-direction: column; gap: 10px;
        position: relative; overflow: hidden;
        transition: transform .2s, box-shadow .2s;
        animation: fadeUp .4s ease both;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,0,0,.09); }
    .kpi-card:nth-child(1){animation-delay:.05s} .kpi-card:nth-child(2){animation-delay:.10s}
    .kpi-card:nth-child(3){animation-delay:.15s} .kpi-card:nth-child(4){animation-delay:.20s}
    .kpi-card:nth-child(5){animation-delay:.25s}
    @@keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

    .kpi-card::before {
        content:''; position:absolute; top:0; left:0; right:0; height:3px;
        border-radius:14px 14px 0 0;
    }
    .kpi-blue::before   { background: var(--blue); }
    .kpi-green::before  { background: var(--green); }
    .kpi-yellow::before { background: var(--yellow); }
    .kpi-red::before    { background: var(--red); }
    .kpi-purple::before { background: var(--purple); }

    .kpi-top { display:flex; align-items:flex-start; justify-content:space-between; }
    .kpi-icon {
        width:38px; height:38px; border-radius:10px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .kpi-icon svg { width:18px; height:18px; }
    .kpi-blue   .kpi-icon { background:var(--blue-bg);   color:var(--blue);   }
    .kpi-green  .kpi-icon { background:var(--green-bg);  color:var(--green);  }
    .kpi-yellow .kpi-icon { background:var(--yellow-bg); color:var(--yellow); }
    .kpi-red    .kpi-icon { background:var(--red-bg);    color:var(--red);    }
    .kpi-purple .kpi-icon { background:var(--purple-bg); color:var(--purple); }

    .kpi-label { font-size:11px; font-weight:600; color:var(--t3); text-transform:uppercase; letter-spacing:.07em; }
    .kpi-value { font-size:30px; font-weight:700; color:var(--t1); line-height:1; letter-spacing:-.03em; }
    .kpi-sub   { font-size:12px; color:var(--t2); display:flex; flex-wrap:wrap; gap:5px; }
    .kpi-sub span {
        display:inline-flex; align-items:center; gap:4px;
        padding:2px 8px; border-radius:99px; font-size:11px; font-weight:500;
    }
    .tag-green  { background:var(--green-light); color:var(--green); }
    .tag-yellow { background:#fef3c7; color:var(--yellow); }
    .tag-red    { background:#fee2e2; color:var(--red); }
    .tag-blue   { background:var(--blue-bg); color:var(--blue); }
    .tag-gray   { background:#f3f4f6; color:#374151; }
    html.dark .tag-gray { background:#1e293b; color:#94a3b8; }
    .kpi-link { font-size:11px; color:var(--accent); text-decoration:none; font-weight:500; }
    .kpi-link:hover { text-decoration:underline; }

    /* ── Charts row ── */
    .charts-row {
        display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;
        animation: fadeUp .4s .25s ease both;
    }
    @@media (max-width:1000px) { .charts-row { grid-template-columns:1fr 1fr; } }
    @@media (max-width:640px)  { .charts-row { grid-template-columns:1fr; } }

    .chart-card {
        background:var(--card); border:1px solid var(--border);
        border-radius:14px; box-shadow:var(--shadow); padding:18px 20px;
    }
    .chart-title { font-size:13px; font-weight:700; color:var(--t1); margin-bottom:3px; }
    .chart-sub   { font-size:11px; color:var(--t3); margin-bottom:14px; }
    .chart-wrap  { position:relative; height:175px; }

    .donut-legend { display:flex; flex-direction:column; gap:6px; margin-top:12px; }
    .donut-legend-item { display:flex; align-items:center; justify-content:space-between; font-size:12px; }
    .donut-legend-left { display:flex; align-items:center; gap:7px; color:var(--t2); }
    .donut-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .donut-val { font-weight:600; color:var(--t1); font-size:12px; }

    /* Activity bars */
    .activity-stat { display:flex; flex-direction:column; gap:12px; }
    .act-row { display:flex; flex-direction:column; gap:4px; }
    .act-top { display:flex; justify-content:space-between; align-items:center; }
    .act-label { font-size:12px; color:var(--t2); font-weight:500; }
    .act-val   { font-size:12px; font-weight:700; color:var(--t1); }
    .prog-bar { height:6px; background:var(--border); border-radius:99px; overflow:hidden; }
    .prog-fill { height:100%; border-radius:99px; transition:width 1s ease; }

    /* Quick actions */
    .quick-actions { display:flex; flex-direction:column; gap:7px; }
    .qa {
        display:flex; align-items:center; gap:11px;
        padding:9px 13px; border-radius:8px; background:var(--bg);
        text-decoration:none; transition:background .15s;
        border:1px solid transparent;
    }
    .qa:hover { background:var(--accent-lt); border-color:rgba(29,78,216,.12); text-decoration:none; }
    .qa-ico { width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .qa-ico svg { width:14px; height:14px; }
    .qa-txt { font-size:12.5px; font-weight:500; color:var(--t1); }

    /* ── Tables row ── */
    .tables-row {
        display:grid; grid-template-columns:1fr 1fr; gap:14px;
        animation: fadeUp .4s .35s ease both;
    }
    @@media (max-width:800px) { .tables-row { grid-template-columns:1fr; } }

    .tbl-card { background:var(--card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; }
    .tbl-head {
        padding:14px 18px; border-bottom:1px solid var(--border);
        display:flex; align-items:center; justify-content:space-between;
    }
    .tbl-head-title { font-size:13px; font-weight:700; color:var(--t1); }
    .tbl-head-link  { font-size:11px; color:var(--accent); text-decoration:none; font-weight:500; }
    .tbl-head-link:hover { text-decoration:underline; }

    .dash-tbl { width:100%; border-collapse:collapse; }
    .dash-tbl thead th {
        padding:9px 16px; text-align:left; font-size:10px; font-weight:600;
        color:var(--t3); text-transform:uppercase; letter-spacing:.07em;
        background:var(--bg); border-bottom:1px solid var(--border);
    }
    .dash-tbl tbody tr { border-bottom:1px solid var(--border); transition:background .1s; }
    .dash-tbl tbody tr:last-child { border-bottom:none; }
    .dash-tbl tbody tr:hover { background:var(--bg); }
    .dash-tbl tbody td { padding:10px 16px; font-size:12.5px; color:var(--t1); }
    .td-sub { font-size:11px; color:var(--t3); margin-top:1px; }

    .bdg { display:inline-block; padding:2px 8px; border-radius:99px; font-size:11px; font-weight:500; }
    .bdg-green  { background:var(--green-light); color:var(--green); }
    .bdg-red    { background:#fee2e2; color:var(--red); }
    .bdg-blue   { background:var(--blue-bg); color:var(--blue); }
    .bdg-yellow { background:var(--yellow-bg); color:var(--yellow); }
    html.dark .bdg-green  { background:#052e16; color:#4ade80; }
    html.dark .bdg-red    { background:#2d0a0a; color:#f87171; }
    html.dark .bdg-blue   { background:#0c1a3a; color:#60a5fa; }
    html.dark .bdg-yellow { background:#1c1000; color:#fbbf24; }

    .empty-msg { padding:30px 16px; text-align:center; font-size:13px; color:var(--t3); }
</style>
@endpush

@section('content')
<div class="dash">

    {{-- ── Alertas del sistema ── --}}
    @if(isset($alerts) && $alerts->isNotEmpty())
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($alerts as $alert)
        <div style="padding:12px 16px;background:var(--blue-bg);border:1px solid var(--blue);border-radius:10px;font-size:13px;color:var(--blue);">
            {{ $alert->alert_text }}
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Welcome ── --}}
    <div class="dash-welcome">
        <div>
            <h1>Bienvenido a {{ $sistemaConfig->nombre_sistema ?? 'Sistema de Salud' }}</h1>
            <p>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        </div>
        <div class="dash-date">{{ now()->format('H:i') }} hs</div>
    </div>

    {{-- ── KPIs ── --}}
    <div class="kpi-grid">

        <div class="kpi-card kpi-green">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Pacientes Activos</div>
                    <div class="kpi-value">{{ $pacientesActivosCount }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                </div>
            </div>
            <div class="kpi-sub"><span class="tag-green">En tratamiento</span></div>
            <a href="{{ route('panel.paciente.index') }}" class="kpi-link">Ver pacientes →</a>
        </div>

        <div class="kpi-card kpi-blue">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Historias Clínicas</div>
                    <div class="kpi-value">{{ $historiasCount }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="kpi-sub"><span class="tag-blue">Registros médicos</span></div>
            <a href="{{ route('panel.paciente.index') }}" class="kpi-link">Ver historias →</a>
        </div>

        @if($capabilityMedicacion)
        <div class="kpi-card kpi-purple">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Prescripciones</div>
                    <div class="kpi-value">{{ $medicaciones->count() }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
            </div>
            <div class="kpi-sub"><span class="tag-gray">Tratamientos activos</span></div>
            <a href="{{ route('panel.medicacion.index') }}" class="kpi-link">Ver medicaciones →</a>
        </div>
        @endif

        <div class="kpi-card kpi-yellow">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Informes del mes</div>
                    <div class="kpi-value">{{ $informesDelMes }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="kpi-sub"><span class="tag-yellow">Este mes</span></div>
            <a href="{{ route('panel.informe.index') }}" class="kpi-link">Ver informes →</a>
        </div>

        <div class="kpi-card kpi-red">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Usuarios</div>
                    <div class="kpi-value">{{ $totalUsuarios }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="kpi-sub"><span class="tag-gray">Personal médico</span></div>
            <a href="{{ route('admin.users.index') }}" class="kpi-link">Ver usuarios →</a>
        </div>

    </div>

    {{-- ── Charts ── --}}
    <div class="charts-row">

        {{-- Donut medicaciones --}}
        @if($capabilityMedicacion)
        <div class="chart-card">
            <div class="chart-title">Prescripciones por horario</div>
            <div class="chart-sub">Distribución de tratamientos activos</div>
            <div class="chart-wrap">
                <canvas id="chartMedicaciones"></canvas>
            </div>
            @php $medPorHorario = $medicaciones->groupBy('horario')->map->count(); @endphp
            <div class="donut-legend">
                @foreach(['mañana'=>'#16a34a','mediodía'=>'#d97706','tarde'=>'#3b82f6','noche'=>'#7c3aed'] as $h => $c)
                <div class="donut-legend-item">
                    <div class="donut-legend-left">
                        <div class="donut-dot" style="background:{{ $c }}"></div>
                        {{ ucfirst($h) }}
                    </div>
                    <div class="donut-val">{{ $medPorHorario[$h] ?? 0 }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Actividad --}}
        <div class="chart-card">
            <div class="chart-title">Actividad del sistema</div>
            <div class="chart-sub">Registros de los últimos 30 días</div>
            @php $maxAct = max($informesRecientes + $medicacionesRecientes + $pacientesRecientes, 1); @endphp
            <div class="activity-stat">
                <div class="act-row">
                    <div class="act-top">
                        <span class="act-label">📋 Informes creados</span>
                        <span class="act-val" style="color:#3b82f6">{{ $informesRecientes }}</span>
                    </div>
                    <div class="prog-bar"><div class="prog-fill" style="width:{{ $informesRecientes/$maxAct*100 }}%;background:#3b82f6"></div></div>
                </div>
                @if($capabilityMedicacion)
                <div class="act-row">
                    <div class="act-top">
                        <span class="act-label">💊 Prescripciones</span>
                        <span class="act-val" style="color:#7c3aed">{{ $medicacionesRecientes }}</span>
                    </div>
                    <div class="prog-bar"><div class="prog-fill" style="width:{{ $medicacionesRecientes/$maxAct*100 }}%;background:#7c3aed"></div></div>
                </div>
                @endif
                <div class="act-row">
                    <div class="act-top">
                        <span class="act-label">👥 Pacientes nuevos</span>
                        <span class="act-val" style="color:#16a34a">{{ $pacientesRecientes }}</span>
                    </div>
                    <div class="prog-bar"><div class="prog-fill" style="width:{{ $pacientesRecientes/$maxAct*100 }}%;background:#16a34a"></div></div>
                </div>
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <div class="chart-card">
            <div class="chart-title">Accesos rápidos</div>
            <div class="chart-sub">Acciones frecuentes</div>
            <div class="quick-actions">
                <a href="{{ route('panel.paciente.create') }}" class="qa">
                    <div class="qa-ico" style="background:var(--green-bg);color:var(--green)"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
                    <span class="qa-txt">Nuevo paciente</span>
                </a>
                @if($capabilityMedicacion)
                <a href="{{ route('panel.medicacion.create') }}" class="qa">
                    <div class="qa-ico" style="background:var(--purple-bg);color:var(--purple)"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
                    <span class="qa-txt">Nueva medicación</span>
                </a>
                @endif
                <a href="{{ route('panel.informe.create') }}" class="qa">
                    <div class="qa-ico" style="background:var(--blue-bg);color:var(--blue)"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
                    <span class="qa-txt">Nuevo informe</span>
                </a>
                @if($capabilityMedicacion)
                <a href="{{ route('panel.medicacion.esquema') }}" class="qa">
                    <div class="qa-ico" style="background:var(--yellow-bg);color:var(--yellow)"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                    <span class="qa-txt">Esquema de medicación</span>
                </a>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Tables ── --}}
    <div class="tables-row">

        {{-- Últimos pacientes --}}
        <div class="tbl-card">
            <div class="tbl-head">
                <span class="tbl-head-title">👥 Últimos pacientes registrados</span>
                <a href="{{ route('panel.paciente.index') }}" class="tbl-head-link">Ver todos →</a>
            </div>
            @if(isset($ultimosPacientes) && $ultimosPacientes->isNotEmpty())
            <table class="dash-tbl">
                <thead><tr><th>Paciente</th><th>Registro</th><th>Estado</th></tr></thead>
                <tbody>
                    @foreach($ultimosPacientes as $p)
                    <tr>
                        <td>
                            <div>{{ $p->nombre }} {{ $p->apellido }}</div>
                            <div class="td-sub">{{ $p->dni ?? 'Sin DNI' }}</div>
                        </td>
                        <td>{{ $p->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if(optional($p->ficha_admision)->fecha_egreso)
                                <span class="bdg bdg-red">Inactivo</span>
                            @else
                                <span class="bdg bdg-green">Activo</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-msg">No hay pacientes registrados aún.</div>
            @endif
        </div>

        {{-- Medicaciones hoy --}}
        @if($capabilityMedicacion)
        <div class="tbl-card">
            <div class="tbl-head">
                <span class="tbl-head-title">💊 Prescripciones programadas hoy</span>
                <a href="{{ route('panel.medicacion.esquema') }}" class="tbl-head-link">Ver esquema →</a>
            </div>
            @if($medicaciones->isNotEmpty())
            <table class="dash-tbl">
                <thead><tr><th>Paciente</th><th>Horario</th><th>Medicamento</th><th>Dosis</th></tr></thead>
                <tbody>
                    @foreach($medicaciones->take(8) as $m)
                    @php $bdgMap = ['mañana'=>'bdg-green','mediodía'=>'bdg-yellow','tarde'=>'bdg-blue','noche'=>'bdg-red']; @endphp
                    <tr>
                        <td>{{ optional($m->paciente)->nombre }} {{ optional($m->paciente)->apellido }}</td>
                        <td><span class="bdg {{ $bdgMap[$m->horario] ?? '' }}">{{ ucfirst($m->horario) }}</span></td>
                        <td>{{ $m->medicamento }}</td>
                        <td class="td-sub">{{ $m->cantidad }} {{ $m->unidad }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-msg">No hay prescripciones programadas para hoy.</div>
            @endif
        </div>
        @endif

    </div>

    {{-- ── Calendario ── --}}
    @if(Route::has('panel.agenda.eventos'))
    <div class="tbl-card" style="animation: fadeUp .4s .4s ease both;">
        <div class="tbl-head">
            <span class="tbl-head-title">🗓️ Calendario</span>
            <a href="{{ route('panel.agenda.index') }}" class="tbl-head-link">Ver agenda →</a>
        </div>
        <div style="padding:16px;">
            <div id="dash-calendar"></div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const medCanvas = document.getElementById('chartMedicaciones');
    if (medCanvas) {
        const medData = @json($medicaciones->groupBy('horario')->map->count());
        const horarios = ['mañana','mediodía','tarde','noche'];
        const colores  = ['#16a34a','#d97706','#3b82f6','#7c3aed'];

        new Chart(medCanvas, {
            type: 'doughnut',
            data: {
                labels: horarios.map(h => h.charAt(0).toUpperCase() + h.slice(1)),
                datasets: [{
                    data: horarios.map(h => medData[h] || 0),
                    backgroundColor: colores,
                    borderWidth: 0, hoverOffset: 6
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                cutout: '70%',
                animation: { animateRotate: true, duration: 800 }
            }
        });
    }

    // Animar barras de progreso
    document.querySelectorAll('.prog-fill').forEach(el => {
        const w = el.style.width;
        el.style.width = '0';
        requestAnimationFrame(() => { el.style.width = w; });
    });
});
</script>
@endpush

@if(Route::has('panel.agenda.eventos'))
@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('dash-calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,listWeek'
        },
        buttonText: { today: 'Hoy', month: 'Mes', list: 'Lista' },
        height: 480,
        nowIndicator: true,
        navLinks: false,
        selectable: false,
        editable: false,
        dayMaxEvents: 3,
        events: '{{ route("panel.agenda.eventos") }}',
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            var props = info.event.extendedProps;
            @canany(['agenda_show', 'agenda_edit'])
            if (props.url_show) window.location.href = props.url_show;
            @endcanany
        },
    });

    calendar.render();
});
</script>
@endpush
@endif