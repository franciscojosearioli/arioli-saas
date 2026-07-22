@extends('layouts.panel')
@section('title', $Paciente->apellido . ', ' . $Paciente->nombre . ' — Historia Clínica')

@push('styles')
<style>
.pt-wrap { display:flex; flex-direction:column; gap:20px; animation:rxFade .3s ease both; }
@@keyframes rxFade { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }

.pt-hero {
    background:var(--card-bg); border:1px solid var(--card-border);
    border-radius:14px; box-shadow:var(--card-shadow);
    padding:20px 24px;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px;
}
.pt-avatar {
    width:54px; height:54px; border-radius:50%;
    background:var(--accent); color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:19px; font-weight:700; flex-shrink:0; letter-spacing:-.02em;
}
.pt-hero-info { flex:1; min-width:0; padding-left:14px; }
.pt-hero-name { font-size:19px; font-weight:700; color:var(--text-primary); margin:0; }
.pt-hero-meta { font-size:13px; color:var(--text-muted); margin-top:3px; }
.pt-hero-actions { display:flex; flex-wrap:wrap; gap:7px; }

.rx-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:7px 14px; border-radius:9px; font-size:13px; font-weight:600;
    text-decoration:none !important; border:none; cursor:pointer;
    transition:background .15s, transform .1s; font-family:var(--font-sans); white-space:nowrap;
}
.rx-btn:hover { transform:translateY(-1px); }
.rx-btn svg { width:13px; height:13px; flex-shrink:0; }
.rx-btn.primary { background:var(--accent); color:#fff; box-shadow:0 2px 8px rgba(29,78,216,.2); }
.rx-btn.primary:hover { background:var(--accent-hover); color:#fff; }
.rx-btn.ghost { background:var(--card-bg); border:1px solid var(--card-border); color:var(--text-secondary); }
.rx-btn.ghost:hover { background:var(--body-bg); color:var(--text-primary); }
.rx-btn.success { background:#16a34a; color:#fff; }
.rx-btn.success:hover { background:#15803d; color:#fff; }
.rx-btn.info { background:#0284c7; color:#fff; }
.rx-btn.info:hover { background:#0369a1; color:#fff; }
.rx-btn.warn { background:#d97706; color:#fff; }
.rx-btn.warn:hover { background:#b45309; color:#fff; }
.rx-btn.icon-only { padding:7px; }
.rx-btn.icon-only svg { margin:0; width:14px; height:14px; }

.pt-tabs {
    background:var(--card-bg); border:1px solid var(--card-border);
    border-radius:14px; box-shadow:var(--card-shadow); overflow:hidden;
}
.pt-tab-nav {
    display:flex; overflow-x:auto; border-bottom:1px solid var(--card-border);
    padding:0 6px; gap:2px; scrollbar-width:none;
}
.pt-tab-nav::-webkit-scrollbar { display:none; }
.pt-tab-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:13px 16px; font-size:13px; font-weight:500;
    color:var(--text-muted); border:none; background:none; cursor:pointer;
    border-bottom:2px solid transparent; margin-bottom:-1px; white-space:nowrap;
    transition:color .15s, border-color .15s; font-family:var(--font-sans);
}
.pt-tab-btn:hover { color:var(--text-secondary); background:var(--body-bg); border-radius:6px 6px 0 0; }
.pt-tab-btn.active { color:var(--accent); border-bottom-color:var(--accent); font-weight:600; }
.pt-tab-btn svg { width:14px; height:14px; }
.pt-tab-body { padding:22px; }
.pt-tab-pane { display:none; }
.pt-tab-pane.active { display:block; }

.rx-card {
    background:var(--card-bg); border:1px solid var(--card-border);
    border-radius:12px; box-shadow:var(--card-shadow); overflow:hidden;
}
.rx-card-header {
    padding:13px 18px; border-bottom:1px solid var(--card-border);
    display:flex; align-items:center; justify-content:space-between; gap:8px;
}
.rx-card-header-left { display:flex; align-items:center; gap:8px; }
.rx-card-header svg { width:15px; height:15px; color:var(--accent); flex-shrink:0; }
.rx-card-title { font-size:13px; font-weight:600; color:var(--text-primary); }
.rx-card-body { padding:0; }

.pt-info-table { width:100%; border-collapse:collapse; }
.pt-info-table th, .pt-info-table td {
    padding:9px 16px; border-bottom:1px solid var(--card-border); font-size:13px;
}
.pt-info-table tr:last-child th,
.pt-info-table tr:last-child td { border-bottom:none; }
.pt-info-table th {
    font-weight:600; color:var(--text-muted); font-size:11px;
    text-transform:uppercase; letter-spacing:.04em; width:36%;
}
.pt-info-table td { color:var(--text-primary); }
.pt-info-table thead th {
    background:var(--body-bg); font-size:11px;
}

.pt-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@@media (max-width:720px) { .pt-grid-2 { grid-template-columns:1fr; } }

.pt-badge {
    display:inline-block; padding:2px 10px; border-radius:99px;
    font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.04em;
}
.pt-badge.primary  { background:#dbeafe; color:#1d4ed8; }
.pt-badge.info     { background:#e0f2fe; color:#0284c7; }
.pt-badge.success  { background:#dcfce7; color:#16a34a; }
.pt-badge.warning  { background:#fef3c7; color:#d97706; }
.pt-badge.danger   { background:#fee2e2; color:#dc2626; }
.pt-badge.muted    { background:var(--body-bg); color:var(--text-muted); border:1px solid var(--card-border); }
html.dark .pt-badge.primary { background:#1e3a5f; color:#93c5fd; }
html.dark .pt-badge.info    { background:#0c4a6e; color:#7dd3fc; }
html.dark .pt-badge.success { background:#14532d; color:#86efac; }
html.dark .pt-badge.warning { background:#451a03; color:#fcd34d; }
html.dark .pt-badge.danger  { background:#450a0a; color:#fca5a5; }

.pt-tab-body .dataTables_wrapper .dataTables_filter input,
.pt-tab-body .dataTables_wrapper .dataTables_length select {
    background:var(--body-bg); border:1px solid var(--card-border);
    border-radius:7px; color:var(--text-primary); padding:4px 8px; font-size:13px;
}
.pt-tab-body table.dataTable thead th {
    background:var(--body-bg); color:var(--text-muted);
    border-bottom:2px solid var(--card-border);
    font-size:11px; text-transform:uppercase; letter-spacing:.04em; font-weight:600;
}
.pt-tab-body table.dataTable tbody tr { border-bottom:1px solid var(--card-border); }
.pt-tab-body table.dataTable tbody td { font-size:13px; color:var(--text-primary); }
.pt-tab-body table.dataTable tbody tr:hover td { background:rgba(0,0,0,.03); }
.dataTables_wrapper .dataTables_paginate .paginate_button {
    background:var(--card-bg) !important; color:var(--text-primary) !important;
    border:1px solid var(--card-border) !important; border-radius:6px !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background:var(--accent) !important; color:#fff !important;
    border-color:var(--accent) !important;
}

.pt-empty {
    text-align:center; padding:36px 20px; color:var(--text-muted); font-size:13px;
}
.pt-empty svg { width:36px; height:36px; margin:0 auto 10px; display:block; opacity:.35; }

.dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.dot-Mañana   { background:#f59e0b; }
.dot-Mediodia { background:#ea580c; }
.dot-Tarde    { background:#3b82f6; }
.dot-Noche    { background:#8b5cf6; }

.rx-scheme {
    border:1px solid var(--card-border); border-radius:10px; margin-bottom:10px; overflow:hidden;
}
.rx-scheme-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:11px 15px; background:var(--body-bg); gap:8px; cursor:pointer;
}
.rx-scheme-header:hover { background:var(--card-border); }
.rx-scheme-date { font-size:13px; font-weight:600; color:var(--text-primary); }
.rx-scheme-actions { display:flex; align-items:center; gap:6px; }
.rx-scheme-body { display:none; padding:14px; border-top:1px solid var(--card-border); }
.rx-scheme-body.open { display:block; }

.rx-horario-block { margin-bottom:12px; }
.rx-horario-block:last-child { margin-bottom:0; }
.rx-horario-title {
    display:flex; align-items:center; gap:6px;
    font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
    color:var(--text-muted); margin-bottom:7px;
}
.rx-med-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:6px 10px; border-radius:7px; background:var(--body-bg);
    margin-bottom:3px; font-size:13px;
}
.rx-med-name { font-weight:600; color:var(--text-primary); }
.rx-med-dose {
    display:inline-block; padding:2px 9px; border-radius:99px;
    background:var(--card-border); color:var(--text-secondary);
    font-size:11px; font-weight:600;
}

.pt-section { border:1px solid var(--card-border); border-radius:10px; margin-bottom:10px; overflow:hidden; }
.pt-section-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 16px; background:var(--body-bg); cursor:pointer;
    font-size:13px; font-weight:600; color:var(--text-primary);
}
.pt-section-header:hover { background:var(--card-border); }
.pt-section-header svg { width:14px; height:14px; color:var(--text-muted); transition:transform .2s; flex-shrink:0; }
.pt-section-header.open svg { transform:rotate(180deg); }
.pt-section-body { display:none; }
.pt-section-body.open { display:block; }
.pt-section-subhead {
    font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
    color:var(--text-muted); margin:0 0 8px;
}

.pt-pdf-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(170px,1fr)); gap:12px; }
.pt-pdf-btn {
    display:flex; flex-direction:column; align-items:center;
    padding:20px 14px; border-radius:12px; gap:10px;
    border:1px solid var(--card-border); background:var(--card-bg);
    color:var(--text-secondary); font-size:13px; font-weight:500;
    cursor:pointer; text-decoration:none !important; width:100%;
    transition:border-color .15s, color .15s, background .15s;
}
.pt-pdf-btn:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-light); }
.pt-pdf-btn svg { width:28px; height:28px; opacity:.65; }
.pt-pdf-btn:hover svg { opacity:1; }
</style>
@endpush

@section('content')
<div class="pt-wrap">

    {{-- Hero --}}
    <div class="pt-hero">
        <div style="display:flex;align-items:center;flex:1;min-width:0;">
            <div class="pt-avatar">
                {{ strtoupper(substr($Paciente->nombre,0,1)) }}{{ strtoupper(substr($Paciente->apellido,0,1)) }}
            </div>
            <div class="pt-hero-info">
                <h1 class="pt-hero-name">{{ $Paciente->apellido }}, {{ $Paciente->nombre }}</h1>
                <div class="pt-hero-meta">
                    @if($Paciente->dni)DNI {{ $Paciente->dni }}@endif
                    @if($Paciente->fecha_nac) · {{ \Carbon\Carbon::parse($Paciente->fecha_nac)->age }} años @endif
                    @if($Paciente->obra_social) · {{ $Paciente->obra_social }}@endif
                    @if($Paciente->sexo) · {{ $Paciente->sexo == 'F' ? 'Femenino' : 'Masculino' }}@endif
                    @if($Paciente->telefono)
                    <span style="display:inline-flex;align-items:center;gap:5px;margin-left:8px;padding:3px 10px;background:var(--body-bg);border:1px solid var(--card-border);border-radius:99px;font-size:12px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;opacity:.6;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $Paciente->telefono }}
                    </span>
                    @endif
                    @if($Paciente->email)
                    <span style="display:inline-flex;align-items:center;gap:5px;margin-left:4px;padding:3px 10px;background:var(--body-bg);border:1px solid var(--card-border);border-radius:99px;font-size:12px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;opacity:.6;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $Paciente->email }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="pt-hero-actions">
            @can('agenda_create')
            <a href="{{ route('panel.agenda.create', ['paciente_id'=>$Paciente->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Nueva cita
            </a>
            @endcan
            @can('informe_create')
            <a href="{{ route('panel.informe.create', ['paciente_id'=>$Paciente->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn info">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Nuevo informe
            </a>
            @endcan
            @can('medicacion_create')
            <a href="{{ route('panel.medicacion.create', ['paciente_id'=>$Paciente->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn warn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Prescripción
            </a>
            @endcan
            {{-- Odontología (Componente opcional) — ver docs/ARQUITECTURA_MODULAR.md Etapa 4.3, fricción de acoplamiento --}}
            @if(app(\App\Platform\PlatformRegistry::class)->isCapabilityEnabled('odontologia'))
            @can('odontologia_access')
            <a href="{{ route('panel.odontologia.paciente', $Paciente->id) }}" class="rx-btn ghost">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Odontología
            </a>
            @endcan
            @endif
            {{-- Medicina Laboral (Componente opcional) — segunda repetición EXACTA del mismo punto de fricción, Etapa 5 --}}
            @if(app(\App\Platform\PlatformRegistry::class)->isCapabilityEnabled('medicina_laboral'))
            @can('medicina_laboral_access')
            <a href="{{ route('panel.medicina-laboral.paciente', $Paciente->id) }}" class="rx-btn ghost">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Medicina Laboral
            </a>
            @endcan
            @endif
            @can('paciente_edit')
            <a href="{{ route('panel.paciente.edit', $Paciente->id) }}" class="rx-btn ghost">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </a>
            @endcan
            <a href="{{ route('panel.paciente.index') }}" class="rx-btn ghost">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="pt-tabs">
        <div class="pt-tab-nav" role="tablist">
            <button class="pt-tab-btn active" data-tab="resumen">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Datos
            </button>
            <button class="pt-tab-btn" data-tab="citas">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Citas @if($Agendas->count())<span class="pt-badge muted" style="margin-left:3px;">{{ $Agendas->count() }}</span>@endif
            </button>
            <button class="pt-tab-btn" data-tab="informes">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Informes @if($Informes->count())<span class="pt-badge muted" style="margin-left:3px;">{{ $Informes->count() }}</span>@endif
            </button>
            <button class="pt-tab-btn" data-tab="medicacion">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Prescripción
            </button>
            <button class="pt-tab-btn" data-tab="historia">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Historia completa
            </button>
            <button class="pt-tab-btn" data-tab="recetas">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Recetas @if($Recetas->count())<span class="pt-badge muted" style="margin-left:3px;">{{ $Recetas->count() }}</span>@endif
            </button>
            <button class="pt-tab-btn" data-tab="consentimientos">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Consentimientos @if($Consentimientos->count())<span class="pt-badge muted" style="margin-left:3px;">{{ $Consentimientos->count() }}</span>@endif
            </button>
            <button class="pt-tab-btn" data-tab="pdfs">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                PDFs
            </button>
        </div>

        <div class="pt-tab-body">

            {{-- ══ DATOS ══ --}}
            <div class="pt-tab-pane active" id="pt-tab-resumen">
                <div class="pt-grid-2" style="margin-bottom:14px;">
                    <div class="rx-card">
                        <div class="rx-card-header">
                            <div class="rx-card-header-left">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1"/></svg>
                                <span class="rx-card-title">Datos personales</span>
                            </div>
                        </div>
                        <div class="rx-card-body">
                            <table class="pt-info-table">
                                <tr><th>Nombre</th><td>{{ $Paciente->apellido }}, {{ $Paciente->nombre }}</td></tr>
                                <tr><th>DNI</th><td>{{ $Paciente->dni ?? '—' }}</td></tr>
                                <tr><th>Fecha nac.</th><td>{{ $Paciente->fecha_nac ? \Carbon\Carbon::parse($Paciente->fecha_nac)->format('d/m/Y') : '—' }}</td></tr>
                                <tr><th>Edad</th><td>{{ $Paciente->edad ?? '—' }}</td></tr>
                                <tr><th>Sexo</th><td>{{ $Paciente->sexo == 'F' ? 'Femenino' : ($Paciente->sexo == 'M' ? 'Masculino' : '—') }}</td></tr>
                                <tr><th>Estado civil</th><td>{{ $Paciente->estado_civil ?? '—' }}</td></tr>
                                <tr><th>Obra social</th><td>{{ $Paciente->obra_social ?? '—' }}</td></tr>
                                <tr><th>N° afiliado</th><td>{{ $Paciente->n_afiliado ?? '—' }}</td></tr>
                                <tr><th>Teléfono</th><td>
                                    @if($Paciente->telefono)
                                        <a href="tel:{{ $Paciente->telefono }}" style="color:var(--accent);text-decoration:none;">{{ $Paciente->telefono }}</a>
                                    @else —
                                    @endif
                                </td></tr>
                                <tr><th>Email</th><td>
                                    @if($Paciente->email)
                                        <a href="mailto:{{ $Paciente->email }}" style="color:var(--accent);text-decoration:none;">{{ $Paciente->email }}</a>
                                    @else —
                                    @endif
                                </td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="rx-card">
                        <div class="rx-card-header">
                            <div class="rx-card-header-left">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="rx-card-title">Domicilio y contacto</span>
                            </div>
                        </div>
                        <div class="rx-card-body">
                            <table class="pt-info-table">
                                <tr>
                                    <th>Domicilio</th>
                                    <td>
                                        {{ $Paciente->calle }} {{ $Paciente->calle_numero }}
                                        @if($Paciente->calle_piso) — Piso {{ $Paciente->calle_piso }}@endif
                                        @if($Paciente->calle_dpto) Dpto {{ $Paciente->calle_dpto }}@endif
                                    </td>
                                </tr>
                                <tr><th>Localidad</th><td>{{ $Paciente->localidad ?? '—' }}</td></tr>
                                <tr><th>Provincia</th><td>{{ $Paciente->provincia ?? '—' }}</td></tr>
                                @if($Paciente->padres_tutores)
                                    @if($Paciente->padres_tutores->padre_nombre)
                                    <tr><th>Padre</th><td>{{ $Paciente->padres_tutores->padre_nombre }}<br><small style="color:var(--text-muted)">{{ $Paciente->padres_tutores->padre_telefono }}</small></td></tr>
                                    @endif
                                    @if($Paciente->padres_tutores->madre_nombre)
                                    <tr><th>Madre</th><td>{{ $Paciente->padres_tutores->madre_nombre }}<br><small style="color:var(--text-muted)">{{ $Paciente->padres_tutores->madre_telefono }}</small></td></tr>
                                    @endif
                                    @if($Paciente->padres_tutores->tutor_nombre)
                                    <tr><th>Tutor</th><td>{{ $Paciente->padres_tutores->tutor_nombre }}<br><small style="color:var(--text-muted)">{{ $Paciente->padres_tutores->tutor_telefono }}</small></td></tr>
                                    @endif
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                <div class="rx-card">
                    <div class="rx-card-header">
                        <div class="rx-card-header-left">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span class="rx-card-title">Admisión y egresos</span>
                        </div>
                    </div>
                    <div class="rx-card-body" style="overflow-x:auto;">
                        <table class="pt-info-table">
                            <thead><tr><th>Tipo</th><th>Fecha ingreso</th><th>Modalidad</th><th>Fecha egreso</th><th>Tipo egreso</th></tr></thead>
                            <tbody>
                                @if($Paciente->ficha_admision)
                                <tr>
                                    <td><span class="pt-badge primary">Ingreso</span></td>
                                    <td>{{ $Paciente->ficha_admision->fecha_ingreso ? \Carbon\Carbon::parse($Paciente->ficha_admision->fecha_ingreso)->format('d/m/Y') : '—' }}</td>
                                    <td>{{ $Paciente->ficha_admision->modalidad ?? '—' }}</td>
                                    <td>{{ $Paciente->ficha_admision->fecha_egreso ? \Carbon\Carbon::parse($Paciente->ficha_admision->fecha_egreso)->format('d/m/Y') : '—' }}</td>
                                    <td>{{ $Paciente->ficha_admision->tipo_egreso ?? '—' }}</td>
                                </tr>
                                @endif
                                @foreach($Paciente->reingreso ?? [] as $r)
                                <tr>
                                    <td><span class="pt-badge info">Reingreso</span></td>
                                    <td>{{ $r->fecha_reingreso ? \Carbon\Carbon::parse($r->fecha_reingreso)->format('d/m/Y') : '—' }}</td>
                                    <td>{{ $r->modalidad ?? '—' }}</td>
                                    <td>{{ $r->fecha_egreso ? \Carbon\Carbon::parse($r->fecha_egreso)->format('d/m/Y') : '—' }}</td>
                                    <td>{{ $r->tipo_egreso ?? '—' }}</td>
                                </tr>
                                @endforeach
                                @if(!$Paciente->ficha_admision && collect($Paciente->reingreso ?? [])->isEmpty())
                                <tr><td colspan="5" style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">Sin datos de admisión</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ══ CITAS ══ --}}
            <div class="pt-tab-pane" id="pt-tab-citas">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <span style="font-size:14px;font-weight:600;color:var(--text-primary);">Citas del paciente</span>
                    @can('agenda_create')
                    <a href="{{ route('panel.agenda.create', ['paciente_id'=>$Paciente->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva cita
                    </a>
                    @endcan
                </div>
                @if($Agendas->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable-citas" style="width:100%">
                        <thead><tr><th width="10"></th><th>Fecha</th><th>Profesional</th><th>Especialidad</th><th>Modalidad</th><th>Motivo</th><th>Estado</th><th></th></tr></thead>
                        <tbody>
                            @foreach($Agendas->sortByDesc('fecha_hora_inicio') as $cita)
                            <tr>
                                <td></td>
                                <td>{{ \Carbon\Carbon::parse($cita->fecha_hora_inicio)->format('d/m/Y H:i') }}</td>
                                <td>{{ $cita->getNombreProfesional() }}</td>
                                <td>{{ $cita->especialidad->nombre ?? '—' }}</td>
                                <td>@if($cita->modalidad==='virtual')<span class="pt-badge info">Virtual</span>@else<span class="pt-badge muted">Presencial</span>@endif</td>
                                <td>{{ \Illuminate\Support\Str::limit($cita->motivo, 40) }}</td>
                                <td>
                                    @php $bc=['pendiente'=>'warning','confirmado'=>'success','cancelado'=>'danger','realizado'=>'muted']; @endphp
                                    <span class="pt-badge {{ $bc[$cita->estado] ?? 'muted' }}">{{ \App\Models\Agenda::estadosLabels()[$cita->estado] ?? $cita->estado }}</span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:4px;">
                                    @can('agenda_show')<a href="{{ route('panel.agenda.show', $cita->id) }}" class="rx-btn ghost icon-only" title="Ver"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>@endcan
                                    @can('agenda_edit')<a href="{{ route('panel.agenda.edit', [$cita->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn ghost icon-only" title="Editar"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>@endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="pt-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Sin citas registradas
                    @can('agenda_create')<div style="margin-top:10px;"><a href="{{ route('panel.agenda.create', ['paciente_id'=>$Paciente->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn success">Agendar primera cita</a></div>@endcan
                </div>
                @endif
            </div>

            {{-- ══ INFORMES ══ --}}
            <div class="pt-tab-pane" id="pt-tab-informes">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <span style="font-size:14px;font-weight:600;color:var(--text-primary);">Informes clínicos</span>
                    @can('informe_create')
                    <a href="{{ route('panel.informe.create', ['paciente_id'=>$Paciente->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn info">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nuevo informe
                    </a>
                    @endcan
                </div>
                @if($Informes->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable-informes" style="width:100%">
                        <thead><tr><th width="10"></th><th>Fecha</th><th>Tipo</th><th>Profesional</th><th>Diagnóstico</th><th>CIE-10</th><th></th></tr></thead>
                        <tbody>
                            @foreach($Informes->sortByDesc('fecha') as $inf)
                            <tr>
                                <td></td>
                                <td>{{ \Carbon\Carbon::parse($inf->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $inf->tipo->name ?? '—' }}</td>
                                <td>{{ $inf->profesional->name ?? '—' }}</td>
                                <td>{{ $inf->diagnostico ?? '—' }}</td>
                                <td>@if($inf->codigo_cie10)<span class="pt-badge info">{{ $inf->codigo_cie10 }}</span>@else —@endif</td>
                                <td>
                                    <div style="display:flex;gap:4px;">
                                    @can('informe_show')<a href="{{ route('panel.informe.show', $inf->id) }}" class="rx-btn ghost icon-only" title="Ver" target="_blank"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>@endcan
                                    @can('informe_edit')<a href="{{ route('panel.informe.edit', [$inf->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn ghost icon-only" title="Editar"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>@endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="pt-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Sin informes registrados
                    @can('informe_create')<div style="margin-top:10px;"><a href="{{ route('panel.informe.create', ['paciente_id'=>$Paciente->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn info">Cargar primer informe</a></div>@endcan
                </div>
                @endif
            </div>

            {{-- ══ PRESCRIPCIÓN ══ --}}
            <div class="pt-tab-pane" id="pt-tab-medicacion">
                @php
                    $medicacionesPorFecha = $Medicaciones->sortByDesc('fecha')->groupBy('fecha');
                    $ordenHorarios = ['Mañana'=>1,'Mediodía'=>2,'Mediodia'=>2,'Tarde'=>3,'Noche'=>4];
                @endphp
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <span style="font-size:14px;font-weight:600;color:var(--text-primary);">Esquemas de prescripción</span>
                    @can('medicacion_create')
                    <a href="{{ route('panel.medicacion.create', ['paciente_id'=>$Paciente->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn warn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva prescripción
                    </a>
                    @endcan
                </div>
                @forelse($medicacionesPorFecha as $fecha => $medsPorFecha)
                <div class="rx-scheme">
                    <div class="rx-scheme-header" onclick="toggleScheme('{{ Str::slug($fecha) }}')">
                        <span class="rx-scheme-date">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                        <div class="rx-scheme-actions" onclick="event.stopPropagation()">
                            <form action="{{ route('panel.paciente.medicacionPaciente', $Paciente->id) }}" method="GET" target="_blank" style="display:inline;">
                                <input type="hidden" name="fecha" value="{{ $fecha }}">
                                <button class="rx-btn ghost" type="submit" style="padding:5px 10px;font-size:12px;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Imprimir
                                </button>
                            </form>
                            @can('medicacion_edit')
                            <a href="{{ route('panel.medicacion.edit', [$Paciente->id, $fecha]) }}?from_paciente={{ $Paciente->id }}" class="rx-btn ghost" style="padding:5px 10px;font-size:12px;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Editar
                            </a>
                            @endcan
                            <svg id="chv-{{ Str::slug($fecha) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;color:var(--text-muted);transition:transform .2s;flex-shrink:0;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="rx-scheme-body" id="scheme-{{ Str::slug($fecha) }}">
                        @php $medsPorHorario = $medsPorFecha->sortBy(fn($m)=>$ordenHorarios[$m->horario]??99)->groupBy('horario'); @endphp
                        @foreach($medsPorHorario as $horario => $meds)
                        <div class="rx-horario-block">
                            <div class="rx-horario-title">
                                <span class="dot dot-{{ $horario }}"></span>
                                {{ $horario }}
                            </div>
                            @foreach($meds as $m)
                            <div class="rx-med-row">
                                <span class="rx-med-name">{{ $m->medicamento }}</span>
                                <span class="rx-med-dose">{{ $m->unidad }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="pt-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Sin prescripciones registradas
                    @can('medicacion_create')<div style="margin-top:10px;"><a href="{{ route('panel.medicacion.create', ['paciente_id'=>$Paciente->id,'from_paciente'=>$Paciente->id]) }}" class="rx-btn warn">Cargar primera prescripción</a></div>@endcan
                </div>
                @endforelse
            </div>

            {{-- ══ HISTORIA COMPLETA ══ --}}
            <div class="pt-tab-pane" id="pt-tab-historia">

                <x-field-if entidad="paciente" campo="problematica">
                @if($Paciente->problematica)
                <div class="pt-section">
                    <div class="pt-section-header" onclick="toggleSec(this,'sec-problematica')">
                        <span>Problemática</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="pt-section-body open" id="sec-problematica">
                        <table class="pt-info-table">
                            <tr><th style="width:28%">Problemática</th><td>{{ $Paciente->problematica->problematica ?? '—' }}</td></tr>
                            <tr><th>Detalles</th><td>{{ $Paciente->problematica->problematica_detalles ?? '—' }}</td></tr>
                        </table>
                    </div>
                </div>
                @endif
                </x-field-if>

                <x-field-if entidad="paciente" campo="datos_adicionales">
                @if($Paciente->datos_adicionales)
                @php $da = $Paciente->datos_adicionales; @endphp
                <div class="pt-section">
                    <div class="pt-section-header" onclick="toggleSec(this,'sec-adicionales')">
                        <span>Antecedentes médicos y adicionales</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="pt-section-body" id="sec-adicionales">
                        <table class="pt-info-table">
                            <tr><th style="width:28%">Alergias</th><td>{{ $da->alergia ?? '—' }}{{ $da->alergia_detalles ? ': '.$da->alergia_detalles : '' }}</td></tr>
                            <tr><th>Enf. crónica</th><td>{{ $da->enfermedad_cronica ?? '—' }}{{ $da->enfermedad_cronica_detalles ? ': '.$da->enfermedad_cronica_detalles : '' }}</td></tr>
                            <tr><th>Sobredosis</th><td>{{ $da->sobredosis ?? '—' }}</td></tr>
                            <tr><th>Abuso sexual</th><td>{{ $da->abuso_sexual ?? '—' }}</td></tr>
                            <tr><th>Antec. legales</th><td>{{ $da->antecedentes_legales ?? '—' }}</td></tr>
                            <tr><th>Privado libertad</th><td>{{ $da->privado_libertad ?? '—' }}{{ $da->tiempo_privado_libertad ? ' ('.$da->tiempo_privado_libertad.')' : '' }}</td></tr>
                            <tr><th>Padres separados</th><td>{{ $da->padres_separados ?? '—' }}</td></tr>
                            <tr><th>Analfabeto</th><td>{{ $da->analfabeto ?? '—' }}</td></tr>
                        </table>
                    </div>
                </div>
                @endif
                </x-field-if>

                <x-field-if entidad="paciente" campo="familia">
                <div class="pt-section">
                    <div class="pt-section-header" onclick="toggleSec(this,'sec-familia')">
                        <span>Grupo familiar</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="pt-section-body" id="sec-familia">
                        <div style="padding:14px 16px;">
                            @if($Paciente->estado_civil == 'Casado' && $Paciente->conyugue)
                            <p class="pt-section-subhead">Cónyuge</p>
                            <table class="pt-info-table" style="margin-bottom:14px;">
                                <tr><th>Nombre</th><td>{{ $Paciente->conyugue->conyugue_nombre }} {{ $Paciente->conyugue->conyugue_apellido }}</td></tr>
                                <tr><th>Edad</th><td>{{ $Paciente->conyugue->conyugue_edad }}</td></tr>
                                <tr><th>Domicilio</th><td>{{ $Paciente->conyugue->conyugue_domicilio }}</td></tr>
                            </table>
                            @endif
                            @if($Paciente->hijos && $Paciente->hijos->count())
                            <p class="pt-section-subhead">Hijos</p>
                            <table class="pt-info-table" style="margin-bottom:14px;">
                                <thead><tr><th>Nombre</th><th>Edad</th><th>Tutor</th></tr></thead>
                                <tbody>@foreach($Paciente->hijos as $hijo)<tr><td>{{ $hijo->hijos_nombre }}</td><td>{{ $hijo->hijos_edad }}</td><td>{{ $hijo->hijos_tutor }}</td></tr>@endforeach</tbody>
                            </table>
                            @endif
                            @if($Paciente->hermanos && $Paciente->hermanos->count())
                            <p class="pt-section-subhead">Hermanos</p>
                            <table class="pt-info-table">
                                <thead><tr><th>Nombre</th><th>Edad</th><th>Convive</th></tr></thead>
                                <tbody>@foreach($Paciente->hermanos as $hermano)<tr><td>{{ $hermano->hermanos_nombre }}</td><td>{{ $hermano->hermanos_edad }}</td><td>{{ $hermano->hermanos_convive }}</td></tr>@endforeach</tbody>
                            </table>
                            @endif
                            @if(!$Paciente->conyugue && !($Paciente->hijos && $Paciente->hijos->count()) && !($Paciente->hermanos && $Paciente->hermanos->count()))
                            <p style="color:var(--text-muted);font-size:13px;margin:0;">Sin datos familiares registrados.</p>
                            @endif
                        </div>
                    </div>
                </div>
                </x-field-if>

                <x-field-if entidad="paciente" campo="educacion">
                @if($Paciente->educacion)
                <div class="pt-section">
                    <div class="pt-section-header" onclick="toggleSec(this,'sec-educacion')">
                        <span>Educación</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="pt-section-body" id="sec-educacion">
                        <div style="overflow-x:auto;">
                            <table class="pt-info-table">
                                <thead><tr><th></th><th>Completa</th><th>Incompleta</th><th>Último año</th><th>Expulsado</th><th>Interrumpido</th><th>Cambios</th></tr></thead>
                                <tbody>
                                    @foreach(['primaria','secundaria','terciaria','facultad'] as $nivel)
                                    <tr>
                                        <th style="text-transform:capitalize;">{{ $nivel }}</th>
                                        <td>{{ $Paciente->educacion->{$nivel.'_completa'} }}</td>
                                        <td>{{ $Paciente->educacion->{$nivel.'_incompleta'} }}</td>
                                        <td>{{ $Paciente->educacion->{$nivel.'_ultimo_anio'} }}</td>
                                        <td>{{ $Paciente->educacion->{$nivel.'_expulsado'} }}</td>
                                        <td>{{ $Paciente->educacion->{$nivel.'_interrumpido'} }}</td>
                                        <td>{{ $Paciente->educacion->{$nivel.'_cambios'} }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($Paciente->educacion->observaciones)
                        <div style="padding:10px 16px;border-top:1px solid var(--card-border);font-size:13px;color:var(--text-secondary);">{{ $Paciente->educacion->observaciones }}</div>
                        @endif
                    </div>
                </div>
                @endif
                </x-field-if>

                <x-field-if entidad="paciente" campo="laboral">
                @if($Paciente->laboral)
                <div class="pt-section">
                    <div class="pt-section-header" onclick="toggleSec(this,'sec-laboral')">
                        <span>Situación laboral</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="pt-section-body" id="sec-laboral">
                        <table class="pt-info-table">
                            <tr><th style="width:28%">Actividad</th><td>{{ $Paciente->laboral->actividad_laboral ?? '—' }}</td></tr>
                            @if($Paciente->laboral->actividad_laboral && $Paciente->laboral->actividad_laboral != 'No')
                            <tr><th>Empresa</th><td>{{ $Paciente->laboral->empresa_laboral ?? '—' }}</td></tr>
                            <tr><th>Cargo</th><td>{{ $Paciente->laboral->cargo_laboral ?? '—' }}</td></tr>
                            <tr><th>Antigüedad</th><td>{{ $Paciente->laboral->antiguedad_laboral ?? '—' }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
                @endif
                </x-field-if>

                <x-field-if entidad="paciente" campo="historial_tratamientos">
                @if($Paciente->historial_tratamientos && $Paciente->historial_tratamientos->count())
                <div class="pt-section">
                    <div class="pt-section-header" onclick="toggleSec(this,'sec-historial')">
                        <span>Historial de tratamientos anteriores</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="pt-section-body" id="sec-historial">
                        <table class="pt-info-table">
                            <thead><tr><th>Lugar</th><th>Duración</th></tr></thead>
                            <tbody>@foreach($Paciente->historial_tratamientos as $ht)<tr><td>{{ $ht->lugar }}</td><td>{{ $ht->duracion }}</td></tr>@endforeach</tbody>
                        </table>
                    </div>
                </div>
                @endif
                </x-field-if>
            </div>

            {{-- ══ RECETAS ══ --}}
            <div class="pt-tab-pane" id="pt-tab-recetas">
                <style>
                .rx-receta-item{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid var(--card-border);}
                .rx-receta-item:last-child{border-bottom:none;}
                .rx-receta-thumb{width:44px;height:44px;border-radius:8px;border:1px solid var(--card-border);object-fit:cover;flex-shrink:0;}
                .rx-receta-icon{width:44px;height:44px;border-radius:8px;border:1px solid var(--card-border);display:flex;align-items:center;justify-content:center;background:#f8fafc;flex-shrink:0;}
                .rx-receta-icon svg{width:20px;height:20px;color:#94a3b8;}
                .rx-receta-info{flex:1;min-width:0;}
                .rx-receta-name{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                .rx-receta-meta{font-size:11px;color:#94a3b8;margin-top:2px;}
                .rx-receta-actions{display:flex;gap:6px;flex-shrink:0;}
                </style>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <h3 style="font-size:15px;font-weight:700;">Recetas médicas</h3>
                    <span style="font-size:12px;color:#94a3b8;">Adjuntadas en informes</span>
                </div>

                @if($Recetas->count())
                <div>
                    @foreach($Recetas as $receta)
                    @php $informe = $receta->informe; @endphp
                    <div class="rx-receta-item">
                        @if($receta->isImage())
                        <img src="{{ $receta->url() }}" class="rx-receta-thumb" alt="">
                        @else
                        <div class="rx-receta-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        @endif
                        <div class="rx-receta-info">
                            <div class="rx-receta-name">{{ $receta->nombre_original }}</div>
                            <div class="rx-receta-meta">
                                @if($informe)
                                    {{ $informe->tipo?->name ?? 'Informe' }} ·
                                    {{ $informe->fecha ? \Carbon\Carbon::parse($informe->fecha)->format('d/m/Y') : '' }}
                                @endif
                            </div>
                        </div>
                        <div class="rx-receta-actions">
                            <a href="{{ $receta->url() }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border:1px solid var(--card-border);border-radius:7px;font-size:12px;font-weight:600;color:#64748b;text-decoration:none;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Ver
                            </a>
                            @if($informe)
                            <a href="{{ route('panel.informe.show', $informe->id) }}"
                               style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border:1px solid var(--card-border);border-radius:7px;font-size:12px;font-weight:600;color:#64748b;text-decoration:none;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Informe
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="text-align:center;padding:40px 0;color:#94a3b8;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:36px;height:36px;margin:0 auto 10px;opacity:.4;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p style="font-size:13px;">No hay recetas adjuntas para este paciente.</p>
                    <p style="font-size:12px;margin-top:4px;">Las recetas se adjuntan al crear o editar un informe.</p>
                </div>
                @endif
            </div>

            {{-- ══ CONSENTIMIENTOS ══ --}}
            <div class="pt-tab-pane" id="pt-tab-consentimientos">
                <style>
                .cons-row{display:flex;align-items:center;gap:12px;padding:13px 0;border-bottom:1px solid var(--card-border);}
                .cons-row:last-child{border-bottom:none;}
                .cons-info{flex:1;min-width:0;}
                .cons-nombre{font-size:14px;font-weight:600;}
                .cons-meta{font-size:11px;color:#94a3b8;margin-top:2px;}
                .cons-sigs{display:flex;gap:6px;align-items:center;flex-shrink:0;}
                .sig-chip{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:700;}
                .sig-ok{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;}
                .sig-pending{background:#fef9c3;color:#854d0e;border:1px solid #fde68a;}
                .cons-actions{display:flex;gap:5px;flex-shrink:0;}
                .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;}
                .modal-overlay.open{display:flex;}
                .modal-box{background:#fff;border-radius:14px;padding:24px;width:100%;max-width:420px;box-shadow:0 8px 40px rgba(0,0,0,.18);}
                .modal-box h3{font-size:16px;font-weight:700;margin-bottom:14px;}
                </style>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <h3 style="font-size:15px;font-weight:700;">Consentimientos</h3>
                    @can('paciente_edit')
                    <a href="{{ route('panel.consentimiento.create', $Paciente->id) }}"
                       style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#1d4ed8;color:#fff;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar
                    </a>
                    @endcan
                </div>

                @if(session('message'))
                <div style="padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;font-size:13px;color:#16a34a;margin-bottom:14px;">
                    {{ session('message') }}
                </div>
                @endif
                @if(session('error'))
                <div style="padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:13px;color:#dc2626;margin-bottom:14px;">
                    {{ session('error') }}
                </div>
                @endif

                @if($Consentimientos->count())
                <div>
                @foreach($Consentimientos as $cons)
                <div class="cons-row">
                    <div class="cons-info">
                        <div class="cons-nombre">{{ $cons->tipo->nombre }}</div>
                        <div class="cons-meta">
                            Creado {{ $cons->created_at->format('d/m/Y') }}
                            @if($cons->firmado_profesional && $cons->firmadoPor)
                                · Firmado por {{ $cons->firmadoPor->name }}
                            @endif
                        </div>
                    </div>
                    <div class="cons-sigs">
                        @if($cons->firmado_paciente)
                            <span class="sig-chip sig-ok">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:10px;height:10px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                Paciente
                            </span>
                        @else
                            <span class="sig-chip sig-pending">Paciente pendiente</span>
                        @endif
                        @if($cons->tipo->requiere_firma_profesional)
                            @if($cons->firmado_profesional)
                                <span class="sig-chip sig-ok">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:10px;height:10px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    Profesional
                                </span>
                            @else
                                <span class="sig-chip sig-pending">Prof. pendiente</span>
                            @endif
                        @endif
                    </div>
                    <div class="cons-actions">
                        {{-- PDF --}}
                        <a href="{{ route('panel.consentimiento.pdf', $cons->id) }}" target="_blank"
                           style="display:inline-flex;align-items:center;padding:6px 10px;border:1px solid var(--card-border);border-radius:7px;font-size:11px;font-weight:600;color:#64748b;text-decoration:none;"
                           title="Ver PDF">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </a>

                        @can('paciente_edit')
                        {{-- Actions dropdown --}}
                        <div style="position:relative;" class="cons-menu-wrap" data-id="{{ $cons->id }}">
                            <button type="button" onclick="toggleConsMenu({{ $cons->id }})"
                                style="display:inline-flex;align-items:center;padding:6px 10px;border:1px solid var(--card-border);border-radius:7px;font-size:11px;background:#fff;cursor:pointer;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                            </button>
                            <div id="cons-menu-{{ $cons->id }}"
                                 style="display:none;position:absolute;right:0;top:100%;margin-top:4px;background:#fff;border:1px solid var(--card-border);border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.1);min-width:200px;z-index:50;overflow:hidden;">
                                @if(!$cons->firmado_paciente)
                                <a href="{{ route('panel.consentimiento.firmarPresencial', $cons->id) }}"
                                   style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;color:#0f172a;text-decoration:none;border-bottom:1px solid var(--card-border);">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Firmar presencialmente
                                </a>
                                <button type="button" onclick="openEmailModal({{ $cons->id }})"
                                   style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;color:#0f172a;width:100%;background:none;border:none;border-bottom:1px solid var(--card-border);cursor:pointer;text-align:left;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Enviar por email
                                </button>
                                @endif
                                @if($cons->tipo->requiere_firma_profesional && !$cons->firmado_profesional)
                                <form method="POST" action="{{ route('panel.consentimiento.firmarProfesional', $cons->id) }}"
                                      onsubmit="return confirm('Firmar este consentimiento con tu firma de perfil?')">
                                    @csrf
                                    <button type="submit"
                                        style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;color:#1d4ed8;width:100%;background:none;border:none;border-bottom:1px solid var(--card-border);cursor:pointer;text-align:left;font-weight:600;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Firmar como profesional
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('panel.consentimiento.destroy', $cons->id) }}"
                                      onsubmit="return confirm('¿Eliminar este consentimiento?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;color:#dc2626;width:100%;background:none;border:none;cursor:pointer;text-align:left;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
                @endforeach
                </div>
                @else
                <div style="text-align:center;padding:40px 0;color:#94a3b8;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:36px;height:36px;margin:0 auto 10px;opacity:.4;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <p style="font-size:13px;">No hay consentimientos registrados para este paciente.</p>
                </div>
                @endif

                {{-- Email Modal --}}
                <div class="modal-overlay" id="email-modal">
                    <div class="modal-box">
                        <h3>Enviar por email al paciente</h3>
                        <form method="POST" id="email-modal-form" action="">
                            @csrf
                            <div style="margin-bottom:14px;">
                                <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">Correo electrónico *</label>
                                <input type="email" name="email" id="email-modal-input" class="form-control"
                                       placeholder="paciente@email.com" required
                                       value="{{ $Paciente->email ?? '' }}"
                                       style="width:100%;padding:9px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                                @if(!$Paciente->email)
                                <small style="color:#94a3b8;font-size:11px;margin-top:4px;display:block;">El paciente no tiene email cargado. Podés ingresarlo manualmente o actualizarlo en su ficha.</small>
                                @endif
                            </div>
                            <div style="margin-bottom:18px;">
                                <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:5px;">El enlace expira en (horas)</label>
                                <input type="number" name="expira_horas" value="72" min="1" max="720"
                                       style="width:120px;padding:9px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                            </div>
                            <div style="display:flex;gap:10px;justify-content:flex-end;">
                                <button type="button" onclick="closeEmailModal()"
                                        style="padding:9px 18px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:13px;cursor:pointer;">Cancelar</button>
                                <button type="submit"
                                        style="padding:9px 20px;background:#1d4ed8;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">Enviar enlace</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ══ PDFs ══ --}}
            <div class="pt-tab-pane" id="pt-tab-pdfs">
                @can('paciente_edit')
                <div class="pt-pdf-grid">
                    <form action="{{ route('panel.paciente.historiaClinica', $Paciente->id) }}" method="GET" target="_blank">
                        <button class="pt-pdf-btn" type="submit"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>Historia Clínica</button>
                    </form>

                    <form action="{{ route('panel.paciente.medicacionPaciente', $Paciente->id) }}" method="GET" target="_blank">
                        <button class="pt-pdf-btn" type="submit"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>Prescripción</button>
                    </form>
                </div>
                @endcan
            </div>

        </div>{{-- /pt-tab-body --}}
    </div>{{-- /pt-tabs --}}

</div>{{-- /pt-wrap --}}
@endsection

@push('scripts')
<script>
var dtCitas = null, dtInformes = null;
$(function() {
    function showTab(name) {
        $('.pt-tab-btn').removeClass('active');
        $('.pt-tab-pane').removeClass('active');
        $('[data-tab="'+name+'"]').addClass('active');
        $('#pt-tab-'+name).addClass('active');
        history.replaceState(null, null, '#'+name);
        if (name==='citas'    && dtCitas)    dtCitas.columns.adjust();
        if (name==='informes' && dtInformes) dtInformes.columns.adjust();
    }
    $('.pt-tab-btn').on('click', function() { showTab($(this).data('tab')); });
    var hash = window.location.hash.replace('#','');
    if (hash && $('[data-tab="'+hash+'"]').length) showTab(hash);

    if ($('.datatable-citas').length) {
        dtCitas = $('.datatable-citas').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            pageLength: 15, order: [[1,'desc']], responsive: true
        });
    }
    if ($('.datatable-informes').length) {
        dtInformes = $('.datatable-informes').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            pageLength: 15, order: [[1,'desc']], responsive: true
        });
    }
});
function toggleScheme(key) {
    var $body=$('#scheme-'+key), $chv=$('#chv-'+key), isOpen=$body.hasClass('open');
    $('.rx-scheme-body').removeClass('open');
    $('[id^="chv-"]').css('transform','');
    if (!isOpen) { $body.addClass('open'); $chv.css('transform','rotate(180deg)'); }
}
function toggleSec(header, id) {
    var $body=$('#'+id), isOpen=$body.hasClass('open');
    $body.toggleClass('open',!isOpen);
    $(header).toggleClass('open',!isOpen);
}

// Consent action dropdown
function toggleConsMenu(id) {
    document.querySelectorAll('[id^="cons-menu-"]').forEach(function(m) {
        if (m.id !== 'cons-menu-' + id) m.style.display = 'none';
    });
    var m = document.getElementById('cons-menu-' + id);
    if (m) m.style.display = m.style.display === 'none' ? '' : 'none';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.cons-menu-wrap')) {
        document.querySelectorAll('[id^="cons-menu-"]').forEach(function(m){ m.style.display='none'; });
    }
});

// Email modal
var emailRoutes = {
    @foreach($Consentimientos as $cons)
    {{ $cons->id }}: '{{ route('panel.consentimiento.enviarEmail', $cons->id) }}',
    @endforeach
};
function openEmailModal(id) {
    document.getElementById('email-modal-form').action = emailRoutes[id] || '';
    document.getElementById('email-modal').classList.add('open');
    document.querySelectorAll('[id^="cons-menu-"]').forEach(function(m){ m.style.display='none'; });
}
function closeEmailModal() {
    document.getElementById('email-modal').classList.remove('open');
}
document.getElementById('email-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEmailModal();
});

// Auto-open tab from hash
(function(){
    var hash = window.location.hash.replace('#','');
    if (hash === 'consentimientos' || hash === 'recetas') {
        setTimeout(function(){ $('[data-tab="'+hash+'"]').trigger('click'); }, 50);
    }
})();
</script>
@endpush
