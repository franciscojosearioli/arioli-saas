@extends('layouts.panel')
@section('title', 'Prescripción — ' . ($medicacion->paciente->nombre ?? '') . ' ' . ($medicacion->paciente->apellido ?? ''))

@push('styles')
<style>
    .rx-wrap {
        display: flex; flex-direction: column; gap: 24px;
        max-width: 1100px; margin: 0 auto;
        animation: rxFade .3s ease both;
    }
    @@keyframes rxFade { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

    .rx-header {
        display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    }
    .rx-header-left h1 { font-size: 22px; font-weight: 700; color: var(--text-primary); letter-spacing:-.02em; margin:0; }
    .rx-header-left p  { font-size: 13px; color: var(--text-secondary); margin: 4px 0 0; }

    .rx-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;
        text-decoration: none !important; border: none; cursor: pointer;
        transition: background .15s, transform .1s; font-family: var(--font-sans);
    }
    .rx-btn:hover { transform: translateY(-1px); }
    .rx-btn svg   { width: 15px; height: 15px; flex-shrink: 0; }
    .rx-btn.primary { background: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(29,78,216,.22); }
    .rx-btn.primary:hover { background: var(--accent-hover); color: #fff; }
    .rx-btn.ghost   { background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-secondary); }
    .rx-btn.ghost:hover { background: var(--body-bg); color: var(--text-primary); }
    .rx-btn.sm { padding: 7px 14px; font-size: 12px; }

    .rx-card {
        background: var(--card-bg); border: 1px solid var(--card-border);
        border-radius: 14px; box-shadow: var(--card-shadow); overflow: hidden;
    }
    .rx-card-header {
        padding: 16px 22px; border-bottom: 1px solid var(--card-border);
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    }
    .rx-card-header-left { display: flex; align-items: center; gap: 10px; }
    .rx-card-header svg  { width: 17px; height: 17px; color: var(--accent); flex-shrink: 0; }
    .rx-card-title { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .rx-card-body  { padding: 22px; }

    .rx-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
    @@media (max-width: 768px) { .rx-grid { grid-template-columns: 1fr; } }

    /* Horario blocks */
    .rx-horario-block { margin-bottom: 20px; }
    .rx-horario-block:last-child { margin-bottom: 0; }
    .rx-horario-label {
        display: flex; align-items: center; gap: 8px;
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
        margin-bottom: 10px;
    }
    .rx-horario-dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    }
    .dot-Mañana   { background: #f59e0b; }
    .dot-Mediodia { background: #f97316; }
    .dot-Tarde    { background: #3b82f6; }
    .dot-Noche    { background: #8b5cf6; }
    .lbl-Mañana   { color: #d97706; }
    .lbl-Mediodia { color: #ea580c; }
    .lbl-Tarde    { color: #2563eb; }
    .lbl-Noche    { color: #7c3aed; }

    .rx-med-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; border-radius: 9px;
        background: var(--body-bg); border: 1px solid var(--card-border);
        margin-bottom: 6px;
    }
    .rx-med-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .rx-med-dose {
        font-size: 12px; color: var(--text-secondary);
        background: var(--card-bg); border: 1px solid var(--card-border);
        padding: 2px 10px; border-radius: 99px;
    }

    /* History sidebar */
    .rx-history-item {
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
        padding: 11px 14px; border-radius: 9px;
        border: 1px solid var(--card-border); margin-bottom: 8px;
        cursor: pointer; transition: background .12s;
        background: var(--body-bg);
    }
    .rx-history-item:hover { background: var(--accent-light); border-color: var(--accent); }
    .rx-history-date { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .rx-history-see {
        font-size: 11px; color: var(--accent); font-weight: 600;
        display: flex; align-items: center; gap: 4px; white-space: nowrap;
    }
    .rx-history-see svg { width: 12px; height: 12px; }

    /* Panel histórico (reemplazo de modal) */
    .rx-hist-panel {
        display: none; margin-top: 12px;
        background: var(--card-bg); border: 1px solid var(--card-border);
        border-radius: 10px; padding: 16px;
    }
    .rx-hist-panel.open { display: block; animation: rxFade .2s ease; }
    .rx-hist-panel-head {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid var(--card-border);
    }
    .rx-hist-close {
        background: var(--body-bg); border: 1px solid var(--card-border);
        border-radius: 6px; cursor: pointer; padding: 4px 6px; color: var(--text-muted);
    }
    .rx-hist-close:hover { background: var(--card-border); }
    .rx-hist-close svg { width: 14px; height: 14px; display: block; }

    .rx-empty {
        text-align: center; padding: 40px 20px;
        color: var(--text-muted); font-size: 13px;
    }
    .rx-empty svg { width: 40px; height: 40px; margin: 0 auto 10px; opacity: .3; display: block; }

    /* ── Recetas section ── */
    .rec-filters-inline {
        display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;
        padding: 14px 22px; background: var(--body-bg); border-bottom: 1px solid var(--card-border);
    }
    .rec-filter-f { display: flex; flex-direction: column; gap: 3px; flex: 1; min-width: 140px; }
    .rec-filter-f label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); }
    .rec-filter-f input, .rec-filter-f select {
        padding: 7px 10px; border: 1px solid var(--card-border); border-radius: 7px;
        font-size: 12px; font-family: var(--font-sans); color: var(--text-primary);
        background: var(--card-bg); outline: none; transition: border-color .15s;
    }
    .rec-filter-f input:focus, .rec-filter-f select:focus { border-color: var(--accent); }
    .btn-rec-filter {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 7px 16px; border-radius: 7px;
        background: var(--accent); color: #fff; font-size: 11px; font-weight: 700;
        border: none; cursor: pointer; font-family: var(--font-sans);
        height: 34px; align-self: flex-end; transition: background .12s;
    }
    .btn-rec-filter:hover { background: var(--accent-hover); }
    .btn-rec-clear {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 7px 12px; border-radius: 7px; font-size: 11px; font-weight: 600;
        border: 1px solid var(--card-border); background: var(--card-bg);
        color: var(--text-secondary); text-decoration: none;
        height: 34px; align-self: flex-end; transition: border-color .12s;
    }
    .btn-rec-clear:hover { border-color: var(--accent); color: var(--accent); }

    .rec-item {
        display: flex; align-items: center; gap: 14px;
        padding: 12px 22px; border-bottom: 1px solid var(--card-border);
        transition: background .1s;
    }
    .rec-item:last-child { border-bottom: none; }
    .rec-item:hover { background: var(--body-bg); }
    .rec-thumb-sm {
        width: 48px; height: 48px; border-radius: 8px; object-fit: cover;
        border: 1px solid var(--card-border); flex-shrink: 0; display: block;
    }
    .rec-pdf-sm {
        width: 48px; height: 48px; border-radius: 8px; flex-shrink: 0;
        background: #fee2e2; display: flex; align-items: center; justify-content: center;
    }
    .rec-pdf-sm svg { width: 22px; height: 22px; color: #dc2626; }
    .rec-item-body { flex: 1; min-width: 0; }
    .rec-item-name { font-size: 13px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .rec-item-meta { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
    .rec-item-actions { display: flex; gap: 6px; flex-shrink: 0; }
    .btn-rec-dl {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 10px; border-radius: 7px; font-size: 11px; font-weight: 600;
        border: 1px solid var(--card-border); background: var(--card-bg);
        color: var(--text-secondary); text-decoration: none; transition: all .12s;
    }
    .btn-rec-dl:hover { border-color: var(--accent); color: var(--accent); }
    .btn-rec-dl svg { width: 11px; height: 11px; }
    .btn-rec-inf {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 11px; border-radius: 7px; font-size: 11px; font-weight: 600;
        background: var(--accent); color: #fff; text-decoration: none; transition: background .12s;
    }
    .btn-rec-inf:hover { background: var(--accent-hover); color: #fff; }
    .btn-rec-inf svg { width: 11px; height: 11px; }
</style>
@endpush

@section('content')
<div class="rx-wrap">

    {{-- Header --}}
    <div class="rx-header">
        <div class="rx-header-left">
            <h1>{{ $medicacion->paciente->nombre ?? '' }} {{ $medicacion->paciente->apellido ?? '' }}</h1>
            <p>Esquema de prescripción — último actualizado el {{ \Carbon\Carbon::parse($ultimaFecha)->format('d/m/Y') }}</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('panel.medicacion.index') }}" class="rx-btn ghost sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
            <form action="{{ route('panel.paciente.medicacionPaciente', $medicacion->paciente_id) }}" method="GET" target="_blank" style="margin:0;">
                @csrf
                <input type="hidden" name="fecha" value="{{ $medicacion->fecha }}">
                <button type="submit" class="rx-btn ghost sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimir
                </button>
            </form>
            @can('paciente_edit')
            @if(auth()->user()->is_admin || $medicacion->user_id == auth()->id())
            <a href="{{ route('panel.medicacion.edit', [$medicacion->paciente_id, $ultimaFecha]) }}" class="rx-btn primary sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar esquema
            </a>
            @endif
            @endcan
        </div>
    </div>

    {{-- Main grid --}}
    <div class="rx-grid">

        {{-- Esquema actual --}}
        <div class="rx-card">
            <div class="rx-card-header">
                <div class="rx-card-header-left">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <span class="rx-card-title">Esquema actual</span>
                </div>
                <span style="font-size:12px;color:var(--text-muted);">
                    {{ \Carbon\Carbon::parse($ultimaFecha)->format('d \d\e F \d\e Y') }}
                </span>
            </div>
            <div class="rx-card-body">
                @forelse($medicacionesUltimaFecha as $horario => $medsPorHorario)
                <div class="rx-horario-block">
                    <div class="rx-horario-label">
                        <span class="rx-horario-dot dot-{{ $horario }}"></span>
                        <span class="lbl-{{ $horario }}">{{ $horario }}</span>
                    </div>
                    @foreach($medsPorHorario as $m)
                    <div class="rx-med-row">
                        <span class="rx-med-name">{{ $m->medicamento ?? '—' }}</span>
                        <span class="rx-med-dose">{{ $m->unidad ?? '—' }}</span>
                    </div>
                    @endforeach
                </div>
                @empty
                <div class="rx-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Sin medicamentos registrados para este esquema.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Historial --}}
        <div class="rx-card">
            <div class="rx-card-header">
                <div class="rx-card-header-left">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="rx-card-title">Esquemas anteriores</span>
                </div>
            </div>
            <div class="rx-card-body">
                @forelse($medicacionesAnteriores as $fecha => $medsPorFecha)
                @php $fkey = Str::slug($fecha); @endphp
                <div class="rx-history-item" onclick="toggleHistPanel('{{ $fkey }}')">
                    <span class="rx-history-date">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                    <span class="rx-history-see">
                        <span id="hist-arrow-{{ $fkey }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                        Ver
                    </span>
                </div>
                <div class="rx-hist-panel" id="hist-panel-{{ $fkey }}">
                    <div class="rx-hist-panel-head">
                        <span style="font-size:13px;font-weight:600;color:var(--text-primary);">
                            {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                        </span>
                        <div style="display:flex;align-items:center;gap:8px;">
                            @can('paciente_edit')
                            @if(auth()->user()->is_admin || $medsPorFecha->first()?->user_id == auth()->id())
                            <a href="{{ route('panel.medicacion.edit', [$medicacion->paciente_id, $fecha]) }}"
                               class="rx-btn ghost sm">Editar</a>
                            @endif
                            @endcan
                            <button class="rx-hist-close" onclick="toggleHistPanel('{{ $fkey }}')">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    @foreach($medsPorFecha->groupBy('horario') as $horario => $meds)
                    <div class="rx-horario-block">
                        <div class="rx-horario-label">
                            <span class="rx-horario-dot dot-{{ $horario }}"></span>
                            <span class="lbl-{{ $horario }}">{{ $horario }}</span>
                        </div>
                        @foreach($meds as $m)
                        <div class="rx-med-row">
                            <span class="rx-med-name">{{ $m->medicamento ?? '—' }}</span>
                            <span class="rx-med-dose">{{ $m->unidad ?? '—' }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @empty
                <div class="rx-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    No hay esquemas anteriores.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Recetas del paciente --}}
    <div class="rx-card">
        <div class="rx-card-header">
            <div class="rx-card-header-left">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="rx-card-title">Recetas adjuntas</span>
            </div>
            @if($recetas->count())
            <span style="font-size:12px;color:var(--text-muted);">{{ $recetas->count() }} {{ $recetas->count() === 1 ? 'receta' : 'recetas' }}</span>
            @endif
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('panel.medicacion.show', $medicacion->id) }}" class="rec-filters-inline">
            @if($informesConRecetas->count())
            <div class="rec-filter-f" style="max-width:280px;">
                <label>Informe</label>
                <select name="rec_informe">
                    <option value="">Todos los informes</option>
                    @foreach($informesConRecetas as $inf)
                    <option value="{{ $inf->id }}" {{ request('rec_informe') == $inf->id ? 'selected' : '' }}>
                        {{ $inf->tipo->name ?? 'Informe' }} — {{ \Carbon\Carbon::parse($inf->fecha)->format('d/m/Y') }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="rec-filter-f" style="max-width:150px;">
                <label>Desde</label>
                <input type="date" name="rec_desde" value="{{ request('rec_desde') }}">
            </div>
            <div class="rec-filter-f" style="max-width:150px;">
                <label>Hasta</label>
                <input type="date" name="rec_hasta" value="{{ request('rec_hasta') }}">
            </div>
            <button type="submit" class="btn-rec-filter">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filtrar
            </button>
            @if(request()->hasAny(['rec_informe','rec_desde','rec_hasta']))
            <a href="{{ route('panel.medicacion.show', $medicacion->id) }}" class="btn-rec-clear">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </a>
            @endif
        </form>

        {{-- Lista de recetas --}}
        @forelse($recetas as $receta)
        <div class="rec-item">
            @if($receta->isImage())
            <img src="{{ $receta->url() }}" alt="{{ $receta->nombre_original }}" class="rec-thumb-sm">
            @else
            <div class="rec-pdf-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            @endif
            <div class="rec-item-body">
                <div class="rec-item-name" title="{{ $receta->nombre_original }}">{{ $receta->nombre_original }}</div>
                <div class="rec-item-meta">
                    {{ strtoupper(pathinfo($receta->nombre_original, PATHINFO_EXTENSION)) }}
                    @if($receta->informe)
                    &nbsp;·&nbsp; {{ $receta->informe->tipo->name ?? 'Informe' }}
                    &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($receta->informe->fecha)->format('d/m/Y') }}
                    @endif
                </div>
            </div>
            <div class="rec-item-actions">
                <a href="{{ $receta->url() }}" download="{{ $receta->nombre_original }}" class="btn-rec-dl" title="Descargar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar
                </a>
                @if($receta->informe)
                <a href="{{ route('panel.informe.show', $receta->informe->id) }}" class="btn-rec-inf">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver informe
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="rx-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            @if(request()->hasAny(['rec_informe','rec_desde','rec_hasta']))
            No se encontraron recetas con los filtros aplicados.
            @else
            Este paciente no tiene recetas adjuntas a sus informes.
            @endif
        </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleHistPanel(key) {
    const panel = document.getElementById('hist-panel-' + key);
    const arrow = document.getElementById('hist-arrow-' + key);
    if (!panel) return;

    const isOpen = panel.classList.contains('open');

    // Close all
    document.querySelectorAll('.rx-hist-panel.open').forEach(el => el.classList.remove('open'));
    document.querySelectorAll('[id^="hist-arrow-"]').forEach(el => {
        el.style.transform = 'rotate(0deg)';
    });

    if (!isOpen) {
        panel.classList.add('open');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        if (arrow) arrow.style.transform = 'rotate(180deg)';
    }
}
</script>
@endpush
