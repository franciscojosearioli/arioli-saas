@extends('layouts.admin')

@section('title', 'Pacientes')

@push('styles')
<style>
    .page-wrap {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* ── Page header ── */
    .page-hdr {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .page-hdr-left h1 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -.02em;
    }
    .page-hdr-left p {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 3px;
    }
    .page-hdr-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* ── Buttons ── */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        font-family: var(--font-sans);
        text-decoration: none;
        cursor: pointer;
        transition: background .15s, transform .15s;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
    }
    .btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); color: #fff; }
    .btn-primary svg   { width: 15px; height: 15px; }

    .btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        background: var(--card-bg);
        color: var(--text-secondary);
        border: 1px solid var(--card-border);
        border-radius: 9px;
        font-size: 13px;
        font-weight: 500;
        font-family: var(--font-sans);
        text-decoration: none;
        cursor: pointer;
        transition: all .15s;
    }
    .btn-ghost:hover {
        background: var(--body-bg);
        color: var(--text-primary);
        border-color: var(--text-muted);
    }
    .btn-ghost svg { width: 15px; height: 15px; }

    /* ── Alerts ── */
    .alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
    }
    .alert svg { width: 16px; height: 16px; flex-shrink: 0; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .alert-error   { background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; }
    html.dark .alert-success { background: #052e16; border-color: #166534; color: #4ade80; }
    html.dark .alert-error   { background: #2d0a0a; border-color: #7f1d1d; color: #f87171; }

    /* ── Stats bar ── */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    @@media (max-width: 600px) { .stats-bar { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--card-shadow);
    }
    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon svg { width: 20px; height: 20px; }
    .stat-icon.blue   { background: #eff6ff; color: #1d4ed8; }
    .stat-icon.green  { background: #f0fdf4; color: #16a34a; }
    .stat-icon.slate  { background: #f1f5f9; color: #475569; }
    html.dark .stat-icon.blue  { background: #1e3a5f; color: #60a5fa; }
    html.dark .stat-icon.green { background: #052e16; color: #4ade80; }
    html.dark .stat-icon.slate { background: #1e293b; color: #94a3b8; }
    .stat-value { font-size: 22px; font-weight: 700; color: var(--text-primary); line-height: 1; }
    .stat-label { font-size: 11px; color: var(--text-muted); margin-top: 3px; text-transform: uppercase; letter-spacing: .05em; font-weight: 500; }

    /* ── Card ── */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 14px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }
    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .card-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .card-body { padding: 20px; }

    /* ── Search grid ── */
    .search-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr) 140px;
        gap: 10px;
        margin-bottom: 16px;
    }
    @@media (max-width: 900px) { .search-grid { grid-template-columns: repeat(2, 1fr); } }
    @@media (max-width: 540px) { .search-grid { grid-template-columns: 1fr; } }

    .search-input-wrap { position: relative; }
    .search-input-wrap svg {
        position: absolute;
        left: 10px; top: 50%;
        transform: translateY(-50%);
        width: 14px; height: 14px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .search-input, .search-select {
        width: 100%;
        padding: 8px 12px 8px 32px;
        border: 1.5px solid var(--card-border);
        border-radius: 8px;
        font-size: 13px;
        font-family: var(--font-sans);
        color: var(--text-primary);
        background: var(--body-bg);
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        appearance: none;
    }
    .search-select { padding-left: 32px; cursor: pointer; }
    .search-input::placeholder { color: var(--text-muted); opacity: 1; }
    .search-input:focus, .search-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(59,130,246,.1);
        background: var(--card-bg);
    }

    /* ── Table ── */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th {
        padding: 10px 16px;
        text-align: left;
        font-size: 10px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .07em;
        background: var(--body-bg);
        border-bottom: 1px solid var(--card-border);
        white-space: nowrap;
    }
    .data-table tbody tr {
        border-bottom: 1px solid var(--card-border);
        transition: background .1s;
    }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: var(--body-bg); }
    .data-table tbody td {
        padding: 13px 16px;
        font-size: 13px;
        color: var(--text-primary);
        white-space: nowrap;
    }

    /* ── Avatar cell ── */
    .paciente-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .paciente-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .paciente-name  { font-weight: 600; color: var(--text-primary); }
    .paciente-id    { font-size: 11px; color: var(--text-muted); margin-top: 1px; }

    /* ── DNI badge ── */
    .dni-badge {
        font-family: var(--font-mono, monospace);
        font-size: 12px;
        background: var(--body-bg);
        border: 1px solid var(--card-border);
        border-radius: 6px;
        padding: 3px 8px;
        color: var(--text-secondary);
        letter-spacing: .02em;
    }

    /* ── Especialidad chip ── */
    .esp-chip {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 500;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid rgba(29,78,216,.15);
    }
    html.dark .esp-chip { background: #1e3a5f; color: #60a5fa; border-color: rgba(96,165,250,.2); }

    /* ── Action buttons ── */
    .action-wrap { display: flex; align-items: center; gap: 6px; }
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 11px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 500;
        font-family: var(--font-sans);
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: background .12s;
    }
    .action-btn svg { width: 13px; height: 13px; }
    .btn-view   { background: #eff6ff; color: #1d4ed8; }
    .btn-view:hover   { background: #dbeafe; }
    .btn-edit   { background: #fffbeb; color: #b45309; }
    .btn-edit:hover   { background: #fef3c7; }
    .btn-delete { background: #fff1f2; color: #dc2626; }
    .btn-delete:hover { background: #fecdd3; }
    html.dark .btn-view   { background: #1e3a5f; color: #60a5fa; }
    html.dark .btn-edit   { background: #1c1302; color: #fbbf24; }
    html.dark .btn-delete { background: #2d0a0a; color: #f87171; }

    /* ── Pagination ── */
    .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-top: 1px solid var(--card-border);
        flex-wrap: wrap;
        gap: 10px;
    }
    .pagination-info { font-size: 12px; color: var(--text-muted); }
    .pagination-btns { display: flex; gap: 4px; flex-wrap: wrap; }
    .page-btn {
        padding: 5px 10px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 500;
        font-family: var(--font-sans);
        border: 1px solid var(--card-border);
        background: var(--card-bg);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all .12s;
        min-width: 34px;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .page-btn:hover { background: var(--body-bg); }
    .page-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }
    .page-btn:disabled { opacity: .4; cursor: not-allowed; }

    /* ── Inactivos tab link ── */
    .tab-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid rgba(194,65,12,.2);
        transition: all .15s;
    }
    .tab-link:hover { background: #ffedd5; }
    .tab-link svg { width: 13px; height: 13px; }
    html.dark .tab-link { background: #1c0a00; color: #fb923c; border-color: rgba(251,146,60,.2); }
    html.dark .tab-link:hover { background: #2d1200; }

    /* ── Empty state ── */
    .empty-state {
        padding: 56px 20px;
        text-align: center;
        color: var(--text-muted);
    }
    .empty-state svg   { width: 44px; height: 44px; margin: 0 auto 14px; opacity: .25; display: block; }
    .empty-state strong { display: block; font-size: 14px; color: var(--text-secondary); margin-bottom: 4px; }
    .empty-state p { font-size: 12px; }

    /* ── Skeleton ── */
    .skeleton-line {
        height: 12px;
        border-radius: 6px;
        background: linear-gradient(90deg, var(--card-border) 25%, var(--body-bg) 50%, var(--card-border) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.2s infinite;
    }
    @@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
</style>
@endpush

@section('content')
<div class="page-wrap">

    {{-- ── Header ── --}}
    <div class="page-hdr">
        <div class="page-hdr-left">
            <h1>Pacientes</h1>
            <p>Gestión de pacientes activos del sistema</p>
        </div>
        <div class="page-hdr-actions">
            {{-- Acceso a inactivos --}}
            <a href="{{ route('panel.paciente_inactivo.index') }}" class="tab-link">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pacientes inactivos
            </a>
            @can('paciente_create')
            <a href="{{ route('panel.paciente.create') }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo paciente
            </a>
            @endcan
        </div>
    </div>

    {{-- ── Alerts ── --}}
    @if(session('success'))
    <div class="alert alert-success">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-error">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Stats ── --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <div>
                <div class="stat-value" id="stat-total">{{ $Pacientes->total() ?? count($Pacientes) }}</div>
                <div class="stat-label">Pacientes activos</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $totalInformes ?? '—' }}</div>
                <div class="stat-label">Informes este mes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon slate">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $citasHoy ?? '—' }}</div>
                <div class="stat-label">Citas hoy</div>
            </div>
        </div>
    </div>

    {{-- ── Table card ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Todos los pacientes</span>
            <span id="count-badge" style="font-size:12px; color:var(--text-muted);"></span>
        </div>

        <div class="card-body" style="padding-bottom:0;">
            <div class="search-grid">
                {{-- Nombre --}}
                <div class="search-input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <input type="text" id="searchNombre" class="search-input" placeholder="Nombre...">
                </div>
                {{-- Apellido --}}
                <div class="search-input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <input type="text" id="searchApellido" class="search-input" placeholder="Apellido...">
                </div>
                {{-- DNI --}}
                <div class="search-input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                    </svg>
                    <input type="text" id="searchDni" class="search-input" placeholder="DNI...">
                </div>
                {{-- Especialidad (select) --}}
                <div class="search-input-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <select id="searchEspecialidad" class="search-select">
                        <option value="">Especialidad...</option>
                        @foreach($especialidades ?? [] as $esp)
                            <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>DNI</th>
                        <th>Especialidad</th>
                        <th>Tutor / Responsable</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="pacientesTableBody">
                    @forelse($Pacientes as $Paciente)
                    <tr>
                        <td>
                            <div class="paciente-cell">
                                <div class="paciente-avatar">
                                    {{ strtoupper(substr($Paciente->nombre ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="paciente-name">{{ $Paciente->apellido }}, {{ $Paciente->nombre }}</div>
                                    <div class="paciente-id">#{{ $Paciente->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="dni-badge">{{ $Paciente->dni ?? '—' }}</span>
                        </td>
                        <td>
                            @if($Paciente->especialidad ?? null)
                                <span class="esp-chip">{{ $Paciente->especialidad->nombre }}</span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">—</span>
                            @endif
                        </td>
                        <td style="color:var(--text-secondary);">
                            {{ $Paciente->tutor?->nombre ?? '—' }}
                        </td>
                        <td>
                            <div class="action-wrap" style="justify-content:flex-end;">
                                @can('paciente_show')
                                <a href="{{ route('panel.paciente.show', $Paciente->id) }}" class="action-btn btn-view">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-6.364 6.364A9 9 0 1121 12a9 9 0 01-12.728 6.364z"/>
                                    </svg>
                                    Ver
                                </a>
                                @endcan
                                @can('paciente_edit')
                                <a href="{{ route('panel.paciente.edit', $Paciente->id) }}" class="action-btn btn-edit">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </a>
                                @endcan
                                @can('paciente_delete')
                                <form action="{{ route('panel.paciente.destroy', $Paciente->id) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar a {{ $Paciente->nombre }} {{ $Paciente->apellido }}?');"
                                      style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete">
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
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                </svg>
                                <strong>Sin pacientes registrados</strong>
                                <p>Agregá el primer paciente usando el botón superior.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrap" id="paginationInfo">
            @if(method_exists($Pacientes, 'total'))
            <div class="pagination-info">
                Mostrando {{ $Pacientes->firstItem() }}–{{ $Pacientes->lastItem() }} de {{ $Pacientes->total() }} pacientes
            </div>
            <div class="pagination-btns">
                @foreach($Pacientes->links()->elements[0] ?? [] as $page => $url)
                    <a href="{{ $url }}" class="page-btn {{ $Pacientes->currentPage() == $page ? 'active' : '' }}">{{ $page }}</a>
                @endforeach
            </div>
            @else
            <div class="pagination-info">{{ count($Pacientes) }} pacientes</div>
            <div></div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    let debounceTimer;
    let currentPage = 1;

    function skeletonRows(cols) {
        return Array(5).fill('').map(() => `
            <tr>
                ${Array(cols).fill('').map(() =>
                    `<td><div class="skeleton-line" style="width:${50 + Math.random()*40}%"></div></td>`
                ).join('')}
            </tr>`).join('');
    }

    function fetchPacientes(page) {
        page = page || currentPage;
        const body        = document.getElementById('pacientesTableBody');
        const pagInfo     = document.getElementById('paginationInfo');
        const countBadge  = document.getElementById('count-badge');
        const statTotal   = document.getElementById('stat-total');

        body.innerHTML = skeletonRows(5);

        const q = {
            nombre:        document.getElementById('searchNombre').value.trim(),
            apellido:      document.getElementById('searchApellido').value.trim(),
            dni:           document.getElementById('searchDni').value.trim(),
            especialidad:  document.getElementById('searchEspecialidad').value,
            page
        };

        const hasFilter = q.nombre || q.apellido || q.dni || q.especialidad;

        fetch('{{ route("panel.paciente.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(q)
        })
        .then(r => r.json())
        .then(data => {
            const pacientes = data.pacientes?.data ?? data.pacientes ?? [];

            if (!pacientes.length) {
                body.innerHTML = `
                    <tr><td colspan="5">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                            </svg>
                            <strong>Sin resultados</strong>
                            <p>No se encontraron pacientes con esos filtros.</p>
                        </div>
                    </td></tr>`;
                pagInfo.innerHTML = '';
                countBadge.textContent = '';
                return;
            }

            const total = data.pacientes?.total ?? pacientes.length;
            if (statTotal) statTotal.textContent = total;
            countBadge.textContent = `${total} pacientes`;

            body.innerHTML = pacientes.map(p => {
                const inicial = (p.nombre ?? 'P').charAt(0).toUpperCase();
                const espChip = p.especialidad
                    ? `<span class="esp-chip">${p.especialidad.nombre}</span>`
                    : `<span style="color:var(--text-muted);font-size:12px;">—</span>`;
                const tutor = p.tutor?.nombre ?? '—';

                return `
                <tr>
                    <td>
                        <div class="paciente-cell">
                            <div class="paciente-avatar">${inicial}</div>
                            <div>
                                <div class="paciente-name">${p.apellido}, ${p.nombre}</div>
                                <div class="paciente-id">#${p.id}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="dni-badge">${p.dni ?? '—'}</span></td>
                    <td>${espChip}</td>
                    <td style="color:var(--text-secondary);">${tutor}</td>
                    <td>
                        <div class="action-wrap" style="justify-content:flex-end;">
                            <a href="{{ url('paciente') }}/${p.id}" class="action-btn btn-view">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-6.364 6.364A9 9 0 1121 12a9 9 0 01-12.728 6.364z"/></svg>
                                Ver
                            </a>
                            <a href="{{ url('paciente') }}/${p.id}/edit" class="action-btn btn-edit">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Editar
                            </a>
                            <form action="{{ url('paciente') }}/${p.id}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('¿Eliminar a ${p.nombre} ${p.apellido}?');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="action-btn btn-delete">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>`;
            }).join('');

            // Pagination
            if (hasFilter) {
                pagInfo.innerHTML = `<div class="pagination-info">Mostrando ${pacientes.length} de ${total} resultados</div><div></div>`;
            } else if (data.pacientes?.links) {
                const btns = data.pacientes.links.map(link => {
                    if (!link.url) {
                        return `<button class="page-btn" disabled>${link.label.replace(/&laquo;/,'‹').replace(/&raquo;/,'›')}</button>`;
                    }
                    const pg = parseInt(new URL(link.url).searchParams.get('page')) || page;
                    return `<button class="page-btn ${link.active ? 'active' : ''}" onclick="window.__fetchPacientes(${pg})">${link.label.replace(/&laquo;/,'‹').replace(/&raquo;/,'›')}</button>`;
                }).join('');
                pagInfo.innerHTML = `
                    <div class="pagination-info">Mostrando ${data.pacientes.from}–${data.pacientes.to} de ${total} pacientes</div>
                    <div class="pagination-btns">${btns}</div>`;
            }
        })
        .catch(err => console.error('Error:', err));
    }

    window.__fetchPacientes = function(page) {
        currentPage = page;
        fetchPacientes(page);
    };

    // Init: only activate live search if the route exists
    const routeExists = @json(Route::has('panel.paciente.search'));

    document.addEventListener('DOMContentLoaded', () => {
        if (!routeExists) return;

        ['searchNombre', 'searchApellido', 'searchDni'].forEach(id => {
            document.getElementById(id)?.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => { currentPage = 1; fetchPacientes(1); }, 380);
            });
        });
        document.getElementById('searchEspecialidad')?.addEventListener('change', () => {
            currentPage = 1; fetchPacientes(1);
        });
    });
})();
</script>
@endpush