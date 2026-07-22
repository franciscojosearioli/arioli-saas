@extends('layouts.panel')
@section('title', 'Panel Operativo')

@push('styles')
<style>
    /* ── Dashboard vars — usa las mismas que el layout ── */
    :root {
        --green:        #16a34a;
        --green-bg:     #f0fdf4;
        --green-light:  #dcfce7;
        --yellow:       #d97706;
        --yellow-bg:    #fffbeb;
        --red:          #dc2626;
        --red-bg:       #fef2f2;
        --blue:         #1d4ed8;
        --blue-bg:      #eff6ff;
        --purple:       #7c3aed;
        --purple-bg:    #f3f4f6;
        --orange:       #ea580c;
        --orange-bg:    #fff7ed;
        --teal:         #0891b2;
        --teal-bg:      #f0fdfa;
    }

    /* Aliases que apuntan a las vars del layout y se adaptan al tema */
    .dash {
        --border: var(--card-border, #e5e7eb);
        --text:   var(--text-primary, #0f172a);
        --text-2: var(--text-secondary, #64748b);
        --text-3: var(--text-muted, #94a3b8);
        --card:   var(--card-bg, #ffffff);
        --radius: 14px;
        --shadow: var(--card-shadow, 0 1px 3px rgba(0,0,0,.05));
    }

    /* Dark overrides para colores estáticos */
    html.dark {
        --green-bg: #052e16;
        --green-light: #14532d;
        --yellow-bg: #1c1000;
        --red-bg: #1c0000;
        --blue-bg: #0c1a3a;
        --blue: #3b82f6;
        --purple-bg: #1c1917;
        --orange-bg: #1c0a00;
        --teal-bg: #042f2e;
    }

    /* ── Page wrapper ── */
    .dash {
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* ── Welcome banner ── */
    .dash-welcome {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .dash-welcome h1 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -.02em;
    }
    .dash-welcome p {
        font-size: 13px;
        color: var(--text-secondary);
        margin-top: 2px;
    }
    .dash-date {
        font-size: 12px;
        color: var(--text-muted);
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 6px 14px;
    }

    /* ── KPI grid ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
    }
    @@media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } }
    @@media (max-width: 768px)  { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @@media (max-width: 600px)  { .kpi-grid { grid-template-columns: 1fr; } }

    .kpi-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
        overflow: hidden;
        animation: fadeUp .4s ease both;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,.08), 0 8px 32px rgba(0,0,0,.06);
    }

    .kpi-card:nth-child(1) { animation-delay: .05s; }
    .kpi-card:nth-child(2) { animation-delay: .10s; }
    .kpi-card:nth-child(3) { animation-delay: .15s; }
    .kpi-card:nth-child(4) { animation-delay: .20s; }
    .kpi-card:nth-child(5) { animation-delay: .25s; }

    @@keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: var(--radius) var(--radius) 0 0;
    }
    .kpi-blue::before     { background: var(--blue); }
    .kpi-green::before    { background: var(--green); }
    .kpi-yellow::before   { background: var(--yellow); }
    .kpi-red::before      { background: var(--red); }
    .kpi-purple::before   { background: var(--purple); }
    .kpi-teal::before     { background: var(--teal); }

    .kpi-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }
    .kpi-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .kpi-icon svg { width: 20px; height: 20px; }
    .kpi-blue   .kpi-icon { background: var(--blue-bg);   color: var(--blue);   }
    .kpi-green  .kpi-icon { background: var(--green-bg);  color: var(--green);  }
    .kpi-yellow .kpi-icon { background: var(--yellow-bg); color: var(--yellow); }
    .kpi-red    .kpi-icon { background: var(--red-bg);    color: var(--red);    }
    .kpi-purple .kpi-icon { background: var(--purple-bg); color: var(--purple); }
    .kpi-teal   .kpi-icon { background: var(--teal-bg);   color: var(--teal);   }

    .kpi-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .07em;
    }
    .kpi-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
        letter-spacing: -.03em;
    }
    .kpi-sub {
        font-size: 12px;
        color: var(--text-secondary);
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .kpi-sub span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 500;
    }
    .kpi-sub .tag-green  { background: var(--green-light); color: var(--green); }
    .kpi-sub .tag-yellow { background: #fef3c7; color: var(--yellow); }
    .kpi-sub .tag-red    { background: #fee2e2; color: var(--red); }
    .kpi-sub .tag-blue   { background: var(--blue-bg); color: var(--blue); }
    .kpi-sub .tag-teal   { background: var(--teal-bg); color: var(--teal); }
    .kpi-sub .tag-gray   { background: #f3f4f6; color: #374151; }
    .kpi-link {
        font-size: 11px;
        color: var(--blue);
        text-decoration: none;
        font-weight: 500;
        margin-top: 4px;
    }
    .kpi-link:hover { text-decoration: underline; }

    /* ── Charts row ── */
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
        animation: fadeUp .4s .25s ease both;
    }
    @@media (max-width: 1000px) { .charts-row { grid-template-columns: 1fr 1fr; } }
    @@media (max-width: 640px)  { .charts-row { grid-template-columns: 1fr; } }

    .chart-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px 22px;
    }
    .chart-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    .chart-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-bottom: 16px;
    }
    .chart-wrap {
        position: relative;
        height: 180px;
    }

    /* ── Tables row ── */
    .tables-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        animation: fadeUp .4s .35s ease both;
    }
    @@media (max-width: 800px) { .tables-row { grid-template-columns: 1fr; } }

    .table-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .table-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .table-card-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .table-card-link {
        font-size: 11px;
        color: var(--blue);
        text-decoration: none;
        font-weight: 500;
    }
    .table-card-link:hover { text-decoration: underline; }

    .dash-table {
        width: 100%;
        border-collapse: collapse;
    }
    .dash-table thead th {
        padding: 9px 16px;
        text-align: left;
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .07em;
        background: var(--body-bg);
        border-bottom: 1px solid var(--border);
    }
    .dash-table tbody tr {
        border-bottom: 1px solid var(--card-border);
        transition: background .1s;
    }
    .dash-table tbody tr:last-child { border-bottom: none; }
    .dash-table tbody tr:hover { background: var(--body-bg); }
    .dash-table tbody td {
        padding: 11px 16px;
        font-size: 12.5px;
        color: var(--text-primary);
    }
    .dash-table .td-muted { font-size: 11px; color: var(--text-muted); }

    .badge-red    { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 500; background: #fee2e2; color: var(--red); }
    .badge-green  { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 500; background: var(--green-light); color: var(--green); }
    .badge-blue   { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 500; background: var(--blue-bg); color: var(--blue); }
    .badge-yellow { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 500; background: var(--yellow-bg); color: var(--yellow); }
    .badge-teal   { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 500; background: var(--teal-bg); color: var(--teal); }

    .empty-msg {
        padding: 32px 16px;
        text-align: center;
        font-size: 13px;
        color: var(--text-3);
    }

    /* ── Quick actions ── */
    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .quick-action {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 8px;
        background: var(--body-bg);
        text-decoration: none;
        transition: background .15s;
        border: 1px solid transparent;
    }
    .quick-action:hover {
        background: var(--accent-light);
        text-decoration: none;
        border-color: var(--accent-border);
    }
    .quick-action-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .quick-action-icon svg { width: 14px; height: 14px; }
    .quick-action-text {
        font-size: 12.5px;
        font-weight: 500;
        color: var(--text-primary);
    }
</style>
@endpush

@section('content')

<div class="dash">

    {{-- Alerts section --}}
    @if($alerts->isNotEmpty())
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
            @foreach($alerts as $alert)
                <div style="padding: 12px 16px; background: var(--blue-bg); border: 1px solid var(--blue); border-radius: 8px; font-size: 13px; color: var(--blue);">
                    {{ $alert->alert_text }}
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Welcome ── --}}
    <div class="dash-welcome">
        <div>
            <h1>Panel Operativo - Sistema de Historias Clínicas 👩‍⚕️</h1>
            <p>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        </div>
        <div class="dash-date">{{ now()->format('H:i') }} hs</div>
    </div>

    {{-- ── KPIs ── --}}
    <div class="kpi-grid">

        {{-- Pacientes Activos --}}
        <div class="kpi-card kpi-green">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Pacientes Activos</div>
                    <div class="kpi-value">{{ $pacientesActivosCount }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-sub">
                <span class="tag-green">En tratamiento</span>
            </div>
            <a href="{{ route('panel.paciente.index') }}" class="kpi-link">Ver pacientes →</a>
        </div>

        {{-- Pacientes Inactivos --}}
        <div class="kpi-card kpi-yellow">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Pacientes Dados de Alta</div>
                    <div class="kpi-value">{{ $pacientesInactivosCount }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-sub">
                <span class="tag-yellow">Con alta médica</span>
            </div>
            <a href="{{ route('panel.paciente_inactivo.index') }}" class="kpi-link">Ver inactivos →</a>
        </div>

        {{-- Total Historias --}}
        <div class="kpi-card kpi-blue">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Total Historias</div>
                    <div class="kpi-value">{{ $historiasCount }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-sub">
                <span class="tag-blue">Registros totales</span>
            </div>
            <a href="{{ route('panel.paciente.index') }}" class="kpi-link">Ver historias →</a>
        </div>

        {{-- Informes --}}
        <div class="kpi-card kpi-purple">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Informes</div>
                    <div class="kpi-value">{{ $informesCount }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-sub">
                <span class="tag-teal">{{ $informesHoy }} hoy</span>
                <span class="tag-gray">{{ $informesSemana }} esta semana</span>
            </div>
            <a href="{{ route('panel.informe.index') }}" class="kpi-link">Ver informes →</a>
        </div>

        {{-- Medicaciones --}}
        <div class="kpi-card kpi-teal">
            <div class="kpi-top">
                <div>
                    <div class="kpi-label">Prescripciones</div>
                    <div class="kpi-value">{{ $medicacionesCount }}</div>
                </div>
                <div class="kpi-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
            </div>
            <div class="kpi-sub">
                <span class="tag-teal">{{ $medicacionesHoy }} programadas hoy</span>
            </div>
            <a href="{{ route('panel.medicacion.index') }}" class="kpi-link">Ver medicaciones →</a>
        </div>

    </div>

    {{-- ── Charts ── --}}
    <div class="charts-row">

        {{-- Accesos Rápidos --}}
        <div class="chart-card">
            <div class="chart-title">Accesos rápidos médicos</div>
            <div class="chart-sub">Acciones frecuentes del personal</div>
            <div class="quick-actions">
                <a href="{{ route('panel.paciente.create') }}" class="quick-action">
                    <div class="quick-action-icon" style="background:var(--green-bg); color:var(--green);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="quick-action-text">Nuevo paciente</span>
                </a>
                <a href="{{ route('panel.informe.create') }}" class="quick-action">
                    <div class="quick-action-icon" style="background:var(--blue-bg); color:var(--blue);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="quick-action-text">Nuevo informe</span>
                </a>
                <a href="{{ route('panel.medicacion.create') }}" class="quick-action">
                    <div class="quick-action-icon" style="background:var(--purple-bg); color:var(--purple);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="quick-action-text">Nueva medicación</span>
                </a>
                <a href="{{ route('panel.medicacion.esquema') }}" class="quick-action" target="_blank">
                    <div class="quick-action-icon" style="background:var(--teal-bg); color:var(--teal);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="quick-action-text">Esquema medicación</span>
                </a>
                @if(Route::has('panel.agenda.create'))
                <a href="{{ route('panel.agenda.create') }}" class="quick-action">
                    <div class="quick-action-icon" style="background:var(--yellow-bg); color:var(--yellow);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="quick-action-text">Nueva cita</span>
                </a>
                @endif
            </div>
        </div>

        {{-- Estadísticas de Actividad --}}
        <div class="chart-card">
            <div class="chart-title">Actividad de hoy</div>
            <div class="chart-sub">Resumen de actividades médicas</div>
            <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 12px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--blue);"></div>
                        Informes del día
                    </span>
                    <span style="font-size: 14px; font-weight: 700; color: var(--text-primary);">{{ $informesHoy }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--teal);"></div>
                        Prescripciones programadas
                    </span>
                    <span style="font-size: 14px; font-weight: 700; color: var(--text-primary);">{{ $medicacionesHoy }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--purple);"></div>
                        Informes esta semana
                    </span>
                    <span style="font-size: 14px; font-weight: 700; color: var(--text-primary);">{{ $informesSemana }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--green);"></div>
                        Pacientes en tratamiento
                    </span>
                    <span style="font-size: 14px; font-weight: 700; color: var(--text-primary);">{{ $pacientesActivosCount }}</span>
                </div>
            </div>
        </div>

        {{-- Estado del Sistema --}}
        <div class="chart-card">
            <div class="chart-title">Estado del sistema</div>
            <div class="chart-sub">Resumen general</div>
            <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 12px;">
                @php
                    $totalPacientes = $pacientesActivosCount + $pacientesInactivosCount;
                    $pctActivos = $totalPacientes > 0 ? ($pacientesActivosCount / $totalPacientes * 100) : 0;
                @endphp
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-size: 12px; color: var(--text-secondary);">Pacientes activos</span>
                        <span style="font-size: 12px; font-weight: 600; color: var(--text-primary);">{{ number_format($pctActivos, 1) }}%</span>
                    </div>
                    <div style="height: 6px; background: #f3f4f6; border-radius: 99px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $pctActivos }}%; background: var(--green); border-radius: 99px; transition: width 1s ease;"></div>
                    </div>
                </div>

                <div style="padding-top: 8px; border-top: 1px solid var(--card-border);">
                    <div style="font-size: 11px; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Sistema operativo</div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <div style="width: 6px; height: 6px; border-radius: 50%; background: var(--green);"></div>
                        <span style="font-size: 12px; color: var(--text-secondary);">Funcionando correctamente</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Tables ── --}}
    <div class="tables-row">

        {{-- Últimos Pacientes --}}
        <div class="table-card">
            <div class="table-card-header">
                <span class="table-card-title">👥 Últimos pacientes activos</span>
                <a href="{{ route('panel.paciente.index') }}" class="table-card-link">Ver todos →</a>
            </div>
            @if($ultimosPacientes->isEmpty())
                <div class="empty-msg">No hay pacientes registrados.</div>
            @else
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Fecha de ingreso</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ultimosPacientes as $paciente)
                        <tr>
                            <td>
                                <div>{{ $paciente->nombre }} {{ $paciente->apellido }}</div>
                                <div class="td-muted">{{ $paciente->dni ?? 'Sin DNI' }}</div>
                            </td>
                            <td>{{ $paciente->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($paciente->ficha_admision && !$paciente->ficha_admision->fecha_egreso)
                                    <span class="badge-green">Activo</span>
                                @else
                                    <span class="badge-yellow">Dado de Alta</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Últimos Informes --}}
        <div class="table-card">
            <div class="table-card-header">
                <span class="table-card-title">📋 Últimos informes médicos</span>
                <a href="{{ route('panel.informe.index') }}" class="table-card-link">Ver todos →</a>
            </div>
            @if($ultimosInformes->isEmpty())
                <div class="empty-msg">No hay informes registrados.</div>
            @else
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ultimosInformes as $informe)
                        <tr>
                            <td>
                                <div>{{ $informe->paciente->nombre ?? 'N/A' }} {{ $informe->paciente->apellido ?? '' }}</div>
                            </td>
                            <td>{{ $informe->created_at->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge-blue">Informe</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- ── Próximas citas ── --}}
    @if(Route::has('panel.agenda.index'))
    <div class="table-card" style="animation: fadeUp .4s .45s ease both;">
        <div class="table-card-header">
            <span class="table-card-title">📅 Mis próximas citas</span>
            <a href="{{ route('panel.agenda.index') }}" class="table-card-link">Ver agenda →</a>
        </div>
        @if($proximasCitas->isEmpty())
            <div class="empty-msg">No tenés citas próximas pendientes o confirmadas.</div>
        @else
            <div style="overflow-x: auto;">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proximasCitas as $cita)
                    @php
                        $badgeClass = match($cita->estado) {
                            'confirmado' => 'badge-green',
                            'cancelado'  => 'badge-red',
                            default      => 'badge-yellow',
                        };
                        $estadoLabel = \App\Models\Agenda::estadosLabels()[$cita->estado] ?? $cita->estado;
                    @endphp
                    <tr>
                        <td>{{ $cita->fecha_hora_inicio->format('d/m/Y') }}</td>
                        <td class="td-muted">{{ $cita->fecha_hora_inicio->format('H:i') }} hs</td>
                        <td>{{ ($cita->paciente->apellido ?? '') . ', ' . ($cita->paciente->nombre ?? 'N/A') }}</td>
                        <td style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ \Illuminate\Support\Str::limit($cita->motivo, 55) }}
                        </td>
                        <td><span class="{{ $badgeClass }}">{{ $estadoLabel }}</span></td>
                        <td style="text-align: right;">
                            <a href="{{ route('panel.agenda.show', $cita->id) }}" class="kpi-link">Ver →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animate progress bars on load
    document.querySelectorAll('[style*="transition: width"]').forEach(el => {
        const match = el.style.width.match(/(\d+(?:\.\d+)?)%/);
        if (match) {
            const targetWidth = match[0];
            el.style.width = '0%';
            requestAnimationFrame(() => {
                el.style.width = targetWidth;
            });
        }
    });
});
</script>
@endpush