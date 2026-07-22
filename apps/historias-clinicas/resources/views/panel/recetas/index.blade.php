@extends('layouts.panel')

@section('title', 'Recetas')

@push('styles')
<style>
.rec-page {
    max-width: 1100px; margin: 0 auto;
    animation: recFadeUp .3s ease both;
}
@@keyframes recFadeUp {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Page header ── */
.rec-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px; margin-bottom: 20px;
}
.rec-header-title {
    display: flex; align-items: center; gap: 10px;
}
.rec-header-title h1 {
    font-size: 20px; font-weight: 700;
    color: var(--text-primary, #0f172a);
    letter-spacing: -.02em; margin: 0;
}
.rec-header-title .rec-count {
    display: inline-flex; align-items: center;
    padding: 2px 10px; border-radius: 20px;
    background: var(--accent-light, #eff6ff);
    color: var(--accent, #1d4ed8);
    font-size: 12px; font-weight: 700;
    border: 1px solid rgba(29,78,216,.15);
}

/* ── Filter card ── */
.rec-filters {
    background: var(--card-bg, #fff);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: var(--card-radius, 14px);
    padding: 16px 20px;
    margin-bottom: 18px;
    display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;
}
.rec-filter-field {
    display: flex; flex-direction: column; gap: 4px;
    flex: 1; min-width: 160px;
}
.rec-filter-field label {
    font-size: 10px; font-weight: 600;
    color: var(--text-muted, #94a3b8);
    text-transform: uppercase; letter-spacing: .07em;
}
.rec-filter-field input, .rec-filter-field select {
    padding: 8px 12px;
    border: 1px solid var(--card-border, #e8edf2); border-radius: 8px;
    font-size: 13px; font-family: var(--font-sans, inherit);
    color: var(--text-primary, #0f172a);
    background: var(--body-bg, #f8fafc); outline: none;
    transition: border-color .15s;
}
.rec-filter-field input:focus, .rec-filter-field select:focus {
    border-color: var(--accent, #1d4ed8);
    background: var(--card-bg, #fff);
}
.btn-filter {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; border-radius: 8px;
    background: var(--accent, #1d4ed8); color: #fff;
    font-size: 12px; font-weight: 600; border: none; cursor: pointer;
    font-family: var(--font-sans, inherit); transition: background .12s;
    height: 37px; align-self: flex-end;
}
.btn-filter:hover { background: var(--accent-hover, #1e40af); }
.btn-filter svg { width: 13px; height: 13px; }
.btn-filter-clear {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; border-radius: 8px;
    border: 1px solid var(--card-border, #e8edf2);
    background: var(--card-bg, #fff);
    color: var(--text-secondary, #64748b);
    font-size: 12px; font-weight: 600; text-decoration: none;
    height: 37px; align-self: flex-end; transition: border-color .12s;
}
.btn-filter-clear:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); }

/* ── Table card ── */
.rec-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: var(--card-radius, 14px);
    overflow: hidden;
}
.rec-table {
    width: 100%; border-collapse: collapse;
}
.rec-table th {
    padding: 10px 14px;
    background: var(--body-bg, #f8fafc);
    border-bottom: 1px solid var(--card-border, #e8edf2);
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: var(--text-muted, #94a3b8);
    white-space: nowrap; text-align: left;
}
.rec-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--card-border, #e8edf2);
    font-size: 13px; color: var(--text-primary, #0f172a);
    vertical-align: middle;
}
.rec-table tr:last-child td { border-bottom: none; }
.rec-table tr:hover td { background: var(--body-bg, #f8fafc); }

/* ── Thumbnail ── */
.rec-thumb {
    width: 44px; height: 44px; border-radius: 7px;
    object-fit: cover;
    border: 1px solid var(--card-border, #e8edf2);
    flex-shrink: 0; display: block;
}
.rec-pdf-thumb {
    width: 44px; height: 44px; border-radius: 7px;
    background: #fee2e2; display: flex;
    align-items: center; justify-content: center; flex-shrink: 0;
}
.rec-pdf-thumb svg { width: 20px; height: 20px; color: #dc2626; }

/* ── Patient + file cell ── */
.rec-patient-name { font-weight: 600; color: var(--text-primary, #0f172a); }
.rec-patient-sub { font-size: 11px; color: var(--text-muted, #94a3b8); margin-top: 1px; }
.rec-file-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px; }
.rec-file-ext { font-size: 11px; color: var(--text-muted, #94a3b8); }

/* ── Tipo badge ── */
.rec-tipo-badge {
    display: inline-block; padding: 2px 8px; border-radius: 20px;
    background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8);
    font-size: 11px; font-weight: 600;
    border: 1px solid rgba(29,78,216,.12);
    white-space: nowrap;
}

/* ── Actions ── */
.rec-actions { display: flex; gap: 6px; align-items: center; }
.btn-ver-informe {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 600;
    background: var(--accent, #1d4ed8); color: #fff; text-decoration: none;
    transition: background .12s;
}
.btn-ver-informe:hover { background: var(--accent-hover, #1e40af); color: #fff; }
.btn-ver-informe svg { width: 12px; height: 12px; }
.btn-dl-receta {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 600;
    border: 1px solid var(--card-border, #e8edf2);
    background: var(--card-bg, #fff); color: var(--text-secondary, #64748b);
    text-decoration: none; transition: border-color .12s;
}
.btn-dl-receta:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); }
.btn-dl-receta svg { width: 12px; height: 12px; }

/* ── Empty state ── */
.rec-empty {
    padding: 60px 30px; text-align: center;
}
.rec-empty svg { width: 48px; height: 48px; color: var(--text-muted, #94a3b8); margin: 0 auto 12px; display: block; opacity: .4; }
.rec-empty p { font-size: 14px; color: var(--text-muted, #94a3b8); margin: 0; }
.rec-empty small { font-size: 12px; color: var(--text-muted, #94a3b8); }

/* ── Pagination ── */
.rec-pagination { padding: 14px 18px; border-top: 1px solid var(--card-border, #e8edf2); }
</style>
@endpush

@section('content')

<div class="rec-page">

    {{-- Header --}}
    <div class="rec-header">
        <div class="rec-header-title">
            <h1>Recetas</h1>
            <span class="rec-count">{{ $recetas->total() }}</span>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('panel.recetas.index') }}" class="rec-filters">
        <div class="rec-filter-field">
            <label>Paciente</label>
            <input type="text" name="paciente" value="{{ request('paciente') }}"
                   placeholder="Nombre o apellido…">
        </div>
        <div class="rec-filter-field" style="max-width:160px;">
            <label>Fecha desde</label>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}">
        </div>
        <div class="rec-filter-field" style="max-width:160px;">
            <label>Fecha hasta</label>
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
        </div>
        <button type="submit" class="btn-filter">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filtrar
        </button>
        @if(request()->hasAny(['paciente','fecha_desde','fecha_hasta']))
        <a href="{{ route('panel.recetas.index') }}" class="btn-filter-clear">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Limpiar
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="rec-card">
        @if($recetas->count())
        <table class="rec-table">
            <thead>
                <tr>
                    <th style="width:52px;"></th>
                    <th>Archivo</th>
                    <th>Paciente</th>
                    <th>Tipo de informe</th>
                    <th>Fecha</th>
                    <th>Profesional</th>
                    <th style="width:1%;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recetas as $receta)
                @php $informe = $receta->informe; @endphp
                <tr>
                    {{-- Miniatura --}}
                    <td style="padding:10px 10px 10px 14px;">
                        @if($receta->isImage())
                        <img src="{{ $receta->url() }}" alt="{{ $receta->nombre_original }}" class="rec-thumb">
                        @else
                        <div class="rec-pdf-thumb">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                    </td>

                    {{-- Nombre archivo --}}
                    <td>
                        <div class="rec-file-name" title="{{ $receta->nombre_original }}">{{ $receta->nombre_original }}</div>
                        <div class="rec-file-ext">{{ strtoupper(pathinfo($receta->nombre_original, PATHINFO_EXTENSION)) }}</div>
                    </td>

                    {{-- Paciente --}}
                    <td>
                        @if($informe && $informe->paciente)
                        <div class="rec-patient-name">
                            {{ $informe->paciente->apellido }}, {{ $informe->paciente->nombre }}
                        </div>
                        @if($informe->paciente->dni)
                        <div class="rec-patient-sub">DNI {{ $informe->paciente->dni }}</div>
                        @endif
                        @else
                        <span style="color:var(--text-muted,#94a3b8);">—</span>
                        @endif
                    </td>

                    {{-- Tipo de informe --}}
                    <td>
                        @if($informe && $informe->tipo)
                        <span class="rec-tipo-badge">{{ $informe->tipo->name }}</span>
                        @else
                        <span style="color:var(--text-muted,#94a3b8);">—</span>
                        @endif
                    </td>

                    {{-- Fecha --}}
                    <td style="white-space:nowrap;">
                        @if($informe && $informe->fecha)
                        {{ \Carbon\Carbon::parse($informe->fecha)->format('d/m/Y') }}
                        @else
                        —
                        @endif
                    </td>

                    {{-- Profesional --}}
                    <td style="white-space:nowrap;">
                        {{ optional($informe)->profesional->name ?? '—' }}
                    </td>

                    {{-- Acciones --}}
                    <td>
                        <div class="rec-actions">
                            <a href="{{ $receta->url() }}" download="{{ $receta->nombre_original }}"
                               class="btn-dl-receta" title="Descargar">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Descargar
                            </a>
                            @if($informe)
                            <a href="{{ route('panel.informe.show', $informe->id) }}" class="btn-ver-informe">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver informe
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($recetas->hasPages())
        <div class="rec-pagination">
            {{ $recetas->links() }}
        </div>
        @endif

        @else
        <div class="rec-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>No se encontraron recetas</p>
            @if(request()->hasAny(['paciente','fecha_desde','fecha_hasta']))
            <small>Probá ajustando los filtros de búsqueda.</small>
            @else
            <small>Las recetas adjuntas a los informes aparecerán aquí.</small>
            @endif
        </div>
        @endif
    </div>

</div>

@endsection
