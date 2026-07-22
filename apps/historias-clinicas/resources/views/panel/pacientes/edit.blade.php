@extends('layouts.panel')
@section('title', 'Editar — ' . $Paciente->apellido . ', ' . $Paciente->nombre)

@push('styles')
<style>
.pe-wrap { display:flex; flex-direction:column; gap:20px; animation:rxFade .3s ease both; }
@@keyframes rxFade { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }

/* Hero */
.pe-hero {
    background:var(--card-bg); border:1px solid var(--card-border);
    border-radius:14px; box-shadow:var(--card-shadow);
    padding:18px 24px;
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
}
.pe-avatar {
    width:46px; height:46px; border-radius:50%;
    background:var(--accent); color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:16px; font-weight:700; flex-shrink:0;
}
.pe-hero-info { flex:1; min-width:0; padding-left:12px; }
.pe-hero-title { font-size:17px; font-weight:700; color:var(--text-primary); margin:0; }
.pe-hero-sub { font-size:12px; color:var(--text-muted); margin-top:2px; }
.pe-hero-actions { display:flex; gap:7px; }

/* Buttons */
.rx-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:7px 14px; border-radius:9px; font-size:13px; font-weight:600;
    text-decoration:none !important; border:none; cursor:pointer;
    transition:background .15s, transform .1s; font-family:var(--font-sans); white-space:nowrap;
}
.rx-btn:hover { transform:translateY(-1px); }
.rx-btn svg { width:13px; height:13px; flex-shrink:0; }
.rx-btn.ghost { background:var(--card-bg); border:1px solid var(--card-border); color:var(--text-secondary); }
.rx-btn.ghost:hover { background:var(--body-bg); color:var(--text-primary); }
.rx-btn.success { background:#16a34a; color:#fff; box-shadow:0 2px 8px rgba(22,163,74,.2); }
.rx-btn.success:hover { background:#15803d; color:#fff; }
.rx-btn.primary { background:var(--accent); color:#fff; }
.rx-btn.primary:hover { background:var(--accent-hover); color:#fff; }
.rx-btn.lg { padding:10px 22px; font-size:14px; }

/* Tab container */
.pe-tabs-wrap {
    background:var(--card-bg); border:1px solid var(--card-border);
    border-radius:14px; box-shadow:var(--card-shadow); overflow:hidden;
}

/* Override Bootstrap nav-tabs */
.pe-tabs-wrap .nav-tabs {
    border-bottom:1px solid var(--card-border) !important;
    background:var(--body-bg); padding:0 6px; gap:2px; flex-wrap:nowrap; overflow-x:auto;
    scrollbar-width:none;
}
.pe-tabs-wrap .nav-tabs::-webkit-scrollbar { display:none; }
.pe-tabs-wrap .nav-tabs .nav-item { flex-shrink:0; }
.pe-tabs-wrap .nav-tabs .nav-link {
    border:none !important; border-radius:0 !important;
    border-bottom:2px solid transparent !important; margin-bottom:-1px;
    padding:13px 16px; font-size:13px; font-weight:500;
    color:var(--text-muted) !important; background:none !important;
    display:inline-flex; align-items:center; gap:6px;
    transition:color .15s, border-color .15s; white-space:nowrap;
}
.pe-tabs-wrap .nav-tabs .nav-link:hover { color:var(--text-primary) !important; }
.pe-tabs-wrap .nav-tabs .nav-link.active {
    color:var(--accent) !important; border-bottom-color:var(--accent) !important;
    font-weight:600 !important;
}
.pe-tabs-wrap .nav-tabs .nav-link svg { width:14px; height:14px; }

/* Tab content */
.pe-tabs-wrap .tab-content {
    border:none !important; border-radius:0 !important;
    background:transparent !important; padding:24px !important;
}

/* Error alert */
.pe-alert {
    background:#fee2e2; border:1px solid #fca5a5; border-radius:10px;
    padding:12px 16px; font-size:13px; color:#b91c1c; margin-bottom:0;
    display:flex; align-items:flex-start; gap:8px;
}
.pe-alert svg { width:16px; height:16px; flex-shrink:0; margin-top:1px; }
html.dark .pe-alert { background:#450a0a; border-color:#7f1d1d; color:#fca5a5; }

/* Section header inside tab */
.pe-section-title {
    font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
    color:var(--text-muted); margin:0 0 16px; display:flex; align-items:center; gap:6px;
}
.pe-section-title svg { width:14px; height:14px; }
.pe-divider { border:none; border-top:1px solid var(--card-border); margin:20px 0; }

/* Form fields */
.rx-field { margin-bottom:16px; }
.rx-field:last-child { margin-bottom:0; }
.rx-label {
    display:block; font-size:11px; font-weight:600;
    text-transform:uppercase; letter-spacing:.07em;
    color:var(--text-muted); margin-bottom:6px;
}
.rx-label.required::after { content:' *'; color:#dc2626; }
.rx-input {
    width:100%; padding:8px 11px;
    border:1px solid var(--card-border); border-radius:8px;
    font-size:13px; color:var(--text-primary);
    background:var(--body-bg); font-family:var(--font-sans);
    outline:none; transition:border-color .15s, box-shadow .15s;
    appearance:none; box-sizing:border-box;
}
.rx-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(29,78,216,.1); }
.rx-input.is-invalid { border-color:#dc2626; }
.rx-input[readonly] { opacity:.55; cursor:not-allowed; }
html.dark .rx-input { background:#0b1120; color:#f1f5f9; border-color:#1e293b; }
.rx-error { font-size:11px; color:#dc2626; margin-top:4px; }

/* Grid helpers */
.rx-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.rx-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
.rx-grid-4 { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; }
@@media (max-width:700px) { .rx-grid-2,.rx-grid-3,.rx-grid-4 { grid-template-columns:1fr; } }

/* Dynamic tables inside form */
.pe-dyn-table { width:100%; border-collapse:collapse; margin-bottom:8px; font-size:13px; }
.pe-dyn-table th {
    padding:8px 10px; background:var(--body-bg); color:var(--text-muted);
    font-size:11px; text-transform:uppercase; letter-spacing:.04em; font-weight:600;
    border:1px solid var(--card-border); text-align:center;
}
.pe-dyn-table td { padding:6px 8px; border:1px solid var(--card-border); vertical-align:middle; }
.pe-dyn-table .form-control,
.pe-dyn-table select.form-control { font-size:12px; }

/* Remove row button */
.rx-rm-btn {
    width:30px; height:30px; border-radius:6px;
    background:#fff1f2; border:1px solid #fecdd3; color:#e11d48;
    cursor:pointer; display:flex; align-items:center; justify-content:center;
    transition:background .15s; padding:0; margin:0 auto;
}
.rx-rm-btn:hover { background:#fecdd3; }
.rx-rm-btn svg { width:12px; height:12px; }

/* Tab nav arrows */
.pe-tab-nav-btns {
    display:flex; align-items:center; justify-content:space-between;
    margin-top:22px; padding-top:18px; border-top:1px solid var(--card-border);
}
</style>
@endpush

@section('content')
@php
$fa  = $Paciente->ficha_admision;
$edu = $Paciente->educacion;
$lab = $Paciente->laboral;
$pad = $Paciente->padres_tutores;
$con = $Paciente->conyugue;
$pro = $Paciente->problematica;
$dad = $Paciente->datos_adicionales;
@endphp

<div class="pe-wrap">

    {{-- Hero --}}
    <div class="pe-hero">
        <div style="display:flex;align-items:center;flex:1;min-width:0;">
            <div class="pe-avatar">{{ strtoupper(substr($Paciente->nombre,0,1)) }}{{ strtoupper(substr($Paciente->apellido,0,1)) }}</div>
            <div class="pe-hero-info">
                <h1 class="pe-hero-title">Editar paciente</h1>
                <div class="pe-hero-sub">{{ $Paciente->apellido }}, {{ $Paciente->nombre }}</div>
            </div>
        </div>
        <div class="pe-hero-actions">
            <a href="{{ route('panel.paciente.show', $Paciente->id) }}" class="rx-btn ghost">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Ver ficha
            </a>
            <a href="{{ url()->previous() }}" class="rx-btn ghost">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
    <div class="pe-alert">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <strong>Hay errores en el formulario.</strong> Por favor revisá los campos marcados.<br>
            @foreach($errors->all() as $e)<span style="display:block;margin-top:2px;">{{ $e }}</span>@endforeach
        </div>
    </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('panel.paciente.update', [$Paciente->id]) }}" enctype="multipart/form-data" id="form-paciente">
        @method('PUT')
        @csrf
        <input type="hidden" name="lock_version" value="{{ $Paciente->lock_version }}">

        <div class="pe-tabs-wrap">
            <ul class="nav nav-tabs" id="pacienteTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-personal" role="tab">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="d-none d-md-inline">1. Datos personales</span><span class="d-md-none">1</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-domicilio" role="tab">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="d-none d-md-inline">2. Domicilio</span><span class="d-md-none">2</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-admision" role="tab">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="d-none d-md-inline">3. Admisión</span><span class="d-md-none">3</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-familia" role="tab">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="d-none d-md-inline">4. Familia</span><span class="d-md-none">4</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-antecedentes" role="tab">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="d-none d-md-inline">5. Antecedentes</span><span class="d-md-none">5</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-edlaboral" role="tab">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        <span class="d-none d-md-inline">6. Educación y laboral</span><span class="d-md-none">6</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="pacienteTabContent">

                {{-- ═══ TAB 1: DATOS PERSONALES ═══ --}}
                <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                    <p class="pe-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Identificación
                    </p>
                    <div class="rx-grid-2">
                        <div class="rx-field">
                            <label class="rx-label required" for="nombre">Nombre</label>
                            <input class="rx-input @error('nombre') is-invalid @enderror" type="text" name="nombre" id="nombre" value="{{ old('nombre', $Paciente->nombre) }}" required>
                            @error('nombre')<div class="rx-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="rx-field">
                            <label class="rx-label required" for="apellido">Apellido</label>
                            <input class="rx-input @error('apellido') is-invalid @enderror" type="text" name="apellido" id="apellido" value="{{ old('apellido', $Paciente->apellido) }}" required>
                            @error('apellido')<div class="rx-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="rx-grid-3">
                        <div class="rx-field">
                            <label class="rx-label required" for="dni">DNI / Documento</label>
                            <input class="rx-input @error('dni') is-invalid @enderror" type="text" name="dni" id="dni" value="{{ old('dni', $Paciente->dni) }}" required>
                            @error('dni')<div class="rx-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="fecha_nac">Fecha de nacimiento</label>
                            <input class="rx-input" type="date" name="fecha_nac" id="fecha_nac" value="{{ old('fecha_nac', $Paciente->fecha_nac) }}">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="edad">Edad</label>
                            <input class="rx-input" type="number" name="edad" id="edad" value="{{ old('edad', $Paciente->edad) }}" readonly>
                        </div>
                    </div>
                    <div class="rx-grid-2">
                        <div class="rx-field">
                            <label class="rx-label" for="telefono">Teléfono <small style="font-weight:400;color:#94a3b8;">(opcional)</small></label>
                            <input class="rx-input" type="tel" name="telefono" id="telefono" value="{{ old('telefono', $Paciente->telefono) }}" placeholder="Ej: +54 9 11 1234-5678">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="email">Email del paciente <small style="font-weight:400;color:#94a3b8;">(para consentimientos)</small></label>
                            <input class="rx-input" type="email" name="email" id="email" value="{{ old('email', $Paciente->email) }}" placeholder="paciente@email.com">
                        </div>
                    </div>
                    <div class="rx-grid-2">
                        <div class="rx-field">
                            <label class="rx-label" for="sexo">Sexo</label>
                            <select class="form-control rx-input select2 @error('sexo') is-invalid @enderror" name="sexo" id="sexo">
                                <option value="">— Seleccionar —</option>
                                <option value="M" {{ old('sexo', $Paciente->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sexo', $Paciente->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @error('sexo')<div class="rx-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="estado_civil">Estado civil</label>
                            <select class="form-control rx-input select2 @error('estado_civil') is-invalid @enderror" name="estado_civil" id="estado_civil">
                                <option value="">— Seleccionar —</option>
                                @foreach(['Soltero'=>'Soltero/a','Casado'=>'Casado/a','Divorciado'=>'Divorciado/a','Viudo'=>'Viudo/a','Union libre'=>'Unión libre'] as $val=>$lbl)
                                <option value="{{ $val }}" {{ old('estado_civil', $Paciente->estado_civil) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="pe-divider">
                    <p class="pe-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Cobertura médica
                    </p>
                    <div class="rx-grid-2">
                        <div class="rx-field">
                            <label class="rx-label" for="obra_social">Obra social / Prepaga</label>
                            <input class="rx-input" type="text" name="obra_social" id="obra_social" value="{{ old('obra_social', $Paciente->obra_social) }}" placeholder="Ej: OSDE, IOMA, PAMI, Particular...">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="n_afiliado">N° de afiliado</label>
                            <input class="rx-input" type="text" name="n_afiliado" id="n_afiliado" value="{{ old('n_afiliado', $Paciente->n_afiliado) }}">
                        </div>
                    </div>

                    <div class="pe-tab-nav-btns">
                        <span style="font-size:12px;color:var(--text-muted);"><span style="color:#dc2626">*</span> Campos obligatorios</span>
                        <button type="button" class="rx-btn primary tab-next" data-target="tab-domicilio">
                            Siguiente
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ═══ TAB 2: DOMICILIO ═══ --}}
                <div class="tab-pane fade" id="tab-domicilio" role="tabpanel">
                    <p class="pe-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Domicilio del paciente
                    </p>
                    <div class="rx-grid-2">
                        <div class="rx-field">
                            <label class="rx-label" for="provincia">Provincia</label>
                            <input class="rx-input" type="text" name="provincia" id="provincia" value="{{ old('provincia', $Paciente->provincia) }}">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="localidad">Localidad / Ciudad</label>
                            <input class="rx-input" type="text" name="localidad" id="localidad" value="{{ old('localidad', $Paciente->localidad) }}">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:12px;">
                        <div class="rx-field">
                            <label class="rx-label" for="calle">Calle</label>
                            <input class="rx-input" type="text" name="calle" id="calle" value="{{ old('calle', $Paciente->calle) }}">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="calle_numero">Número</label>
                            <input class="rx-input" type="text" name="calle_numero" id="calle_numero" value="{{ old('calle_numero', $Paciente->calle_numero) }}">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="calle_piso">Piso</label>
                            <input class="rx-input" type="text" name="calle_piso" id="calle_piso" value="{{ old('calle_piso', $Paciente->calle_piso) }}">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="calle_dpto">Dpto.</label>
                            <input class="rx-input" type="text" name="calle_dpto" id="calle_dpto" value="{{ old('calle_dpto', $Paciente->calle_dpto) }}">
                        </div>
                    </div>
                    <div class="pe-tab-nav-btns">
                        <button type="button" class="rx-btn ghost tab-prev" data-target="tab-personal">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </button>
                        <button type="button" class="rx-btn primary tab-next" data-target="tab-admision">
                            Siguiente
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ═══ TAB 3: ADMISIÓN ═══ --}}
                <div class="tab-pane fade" id="tab-admision" role="tabpanel">
                    <p class="pe-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Datos de ingreso
                    </p>
                    <div class="rx-grid-2">
                        <div class="rx-field">
                            <label class="rx-label" for="fecha_ingreso">Fecha de ingreso</label>
                            <input class="rx-input" type="date" name="fecha_ingreso" id="fecha_ingreso" value="{{ old('fecha_ingreso', $fa->fecha_ingreso ?? '') }}">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="modalidad">Modalidad</label>
                            <select class="form-control rx-input select2 @error('modalidad') is-invalid @enderror" name="modalidad" id="modalidad">
                                <option value="">— Seleccionar —</option>
                                @foreach(['Internacion'=>'Internación','Ambulatorio'=>'Ambulatorio','Hospital de Dia'=>'Hospital de Día','Consultorio'=>'Consultorio'] as $val=>$lbl)
                                <option value="{{ $val }}" {{ old('modalidad', $fa->modalidad ?? '') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="rx-grid-2">
                        <div class="rx-field">
                            <label class="rx-label" for="fecha_egreso">Fecha de egreso</label>
                            <input class="rx-input" type="date" name="fecha_egreso" id="fecha_egreso" value="{{ old('fecha_egreso', $fa->fecha_egreso ?? '') }}">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="tipo_egreso">Tipo de egreso</label>
                            <select class="form-control rx-input select2 @error('tipo_egreso') is-invalid @enderror" name="tipo_egreso" id="tipo_egreso">
                                <option value="">— Sin egreso —</option>
                                <option value="Alta" {{ old('tipo_egreso', $fa->tipo_egreso ?? '') == 'Alta' ? 'selected' : '' }}>Alta</option>
                                <option value="Abandono" {{ old('tipo_egreso', $fa->tipo_egreso ?? '') == 'Abandono' ? 'selected' : '' }}>Abandono</option>
                                <option value="Expulsion" {{ old('tipo_egreso', $fa->tipo_egreso ?? '') == 'Expulsion' ? 'selected' : '' }}>Expulsión</option>
                            </select>
                        </div>
                    </div>

                    <hr class="pe-divider">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                        <p class="pe-section-title" style="margin:0;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Reingresos
                        </p>
                        <select class="form-control select2" name="hubo_reingreso" id="hubo_reingreso" style="width:auto;min-width:160px;font-size:13px;">
                            <option value="0" {{ $Paciente->reingreso->isEmpty() ? 'selected' : '' }}>Sin reingresos</option>
                            <option value="1" {{ $Paciente->reingreso->isNotEmpty() ? 'selected' : '' }}>Con reingresos</option>
                        </select>
                    </div>
                    <div id="reingresos-container" style="display:{{ $Paciente->reingreso->isNotEmpty() ? 'block' : 'none' }};">
                        <div style="overflow-x:auto;">
                            <table class="pe-dyn-table">
                                <thead><tr><th>Fecha reingreso</th><th>Modalidad</th><th>Fecha egreso</th><th>Tipo egreso</th><th style="width:40px"></th></tr></thead>
                                <tbody id="table-body-reingresos">
                                    @foreach($Paciente->reingreso as $r)
                                    <tr>
                                        <td><input class="form-control form-control-sm" type="date" name="fecha_reingreso[]" value="{{ $r->fecha_reingreso }}"></td>
                                        <td><select class="form-control form-control-sm select2" name="modalidad_reingreso[]"><option value="Internacion" {{ $r->modalidad=='Internacion'?'selected':'' }}>Internación</option><option value="Ambulatorio" {{ $r->modalidad=='Ambulatorio'?'selected':'' }}>Ambulatorio</option><option value="Consultorio" {{ $r->modalidad=='Consultorio'?'selected':'' }}>Consultorio</option></select></td>
                                        <td><input class="form-control form-control-sm" type="date" name="fecha_egreso_reingreso[]" value="{{ $r->fecha_egreso }}"></td>
                                        <td><select class="form-control form-control-sm select2" name="tipo_egreso_reingreso[]"><option value="">Sin egreso</option><option value="Alta" {{ $r->tipo_egreso=='Alta'?'selected':'' }}>Alta</option><option value="Abandono" {{ $r->tipo_egreso=='Abandono'?'selected':'' }}>Abandono</option><option value="Expulsion" {{ $r->tipo_egreso=='Expulsion'?'selected':'' }}>Expulsión</option></select></td>
                                        <td><button type="button" class="rx-rm-btn remove-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="rx-btn ghost" id="add-row-reingresos" style="font-size:12px;padding:6px 12px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Agregar reingreso
                        </button>
                    </div>

                    <div class="pe-tab-nav-btns">
                        <button type="button" class="rx-btn ghost tab-prev" data-target="tab-domicilio">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </button>
                        <button type="button" class="rx-btn primary tab-next" data-target="tab-familia">
                            Siguiente
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ═══ TAB 4: FAMILIA ═══ --}}
                <div class="tab-pane fade" id="tab-familia" role="tabpanel">
                    <p class="pe-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Responsables / Contactos
                    </p>
                    <div style="overflow-x:auto;margin-bottom:16px;">
                        <table class="pe-dyn-table">
                            <thead><tr><th style="width:12%">Vínculo</th><th>Nombre completo</th><th style="width:24%">Teléfono</th><th style="width:14%">Responsable</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight:700;text-align:center;color:var(--text-primary);">Padre</td>
                                    <td><input class="form-control form-control-sm" type="text" name="padre_nombre" value="{{ old('padre_nombre', $pad->padre_nombre ?? '') }}"></td>
                                    <td><input class="form-control form-control-sm" type="text" name="padre_telefono" value="{{ old('padre_telefono', $pad->padre_telefono ?? '') }}"></td>
                                    <td><select class="form-control form-control-sm select2" name="padre_responsable"><option value="No" {{ old('padre_responsable',$pad->padre_responsable??'No')=='No'?'selected':'' }}>No</option><option value="Si" {{ old('padre_responsable',$pad->padre_responsable??'')=='Si'?'selected':'' }}>Sí</option></select></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700;text-align:center;color:var(--text-primary);">Madre</td>
                                    <td><input class="form-control form-control-sm" type="text" name="madre_nombre" value="{{ old('madre_nombre', $pad->madre_nombre ?? '') }}"></td>
                                    <td><input class="form-control form-control-sm" type="text" name="madre_telefono" value="{{ old('madre_telefono', $pad->madre_telefono ?? '') }}"></td>
                                    <td><select class="form-control form-control-sm select2" name="madre_responsable"><option value="No" {{ old('madre_responsable',$pad->madre_responsable??'No')=='No'?'selected':'' }}>No</option><option value="Si" {{ old('madre_responsable',$pad->madre_responsable??'')=='Si'?'selected':'' }}>Sí</option></select></td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700;text-align:center;color:var(--text-primary);">Tutor</td>
                                    <td><input class="form-control form-control-sm" type="text" name="tutor_nombre" value="{{ old('tutor_nombre', $pad->tutor_nombre ?? '') }}"></td>
                                    <td><input class="form-control form-control-sm" type="text" name="tutor_telefono" value="{{ old('tutor_telefono', $pad->tutor_telefono ?? '') }}"></td>
                                    <td><select class="form-control form-control-sm select2" name="tutor_responsable"><option value="No" {{ old('tutor_responsable',$pad->tutor_responsable??'No')=='No'?'selected':'' }}>No</option><option value="Si" {{ old('tutor_responsable',$pad->tutor_responsable??'')=='Si'?'selected':'' }}>Sí</option></select></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="conyuge-form" style="display:{{ old('estado_civil', $Paciente->estado_civil) == 'Casado' ? 'block' : 'none' }};">
                        <hr class="pe-divider">
                        <p class="pe-section-title">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Datos del cónyuge / pareja
                        </p>
                        <div class="rx-grid-2">
                            <div class="rx-field"><label class="rx-label" for="conyugue_nombre">Nombre</label><input class="rx-input" type="text" name="conyugue_nombre" id="conyugue_nombre" value="{{ old('conyugue_nombre', $con->conyugue_nombre ?? '') }}"></div>
                            <div class="rx-field"><label class="rx-label" for="conyugue_apellido">Apellido</label><input class="rx-input" type="text" name="conyugue_apellido" id="conyugue_apellido" value="{{ old('conyugue_apellido', $con->conyugue_apellido ?? '') }}"></div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 3fr;gap:16px;">
                            <div class="rx-field"><label class="rx-label" for="conyugue_edad">Edad</label><input class="rx-input" type="number" name="conyugue_edad" id="conyugue_edad" value="{{ old('conyugue_edad', $con->conyugue_edad ?? '') }}"></div>
                            <div class="rx-field"><label class="rx-label" for="conyugue_domicilio">Domicilio</label><input class="rx-input" type="text" name="conyugue_domicilio" id="conyugue_domicilio" value="{{ old('conyugue_domicilio', $con->conyugue_domicilio ?? '') }}"></div>
                        </div>
                    </div>

                    <hr class="pe-divider">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                        <p class="pe-section-title" style="margin:0;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Hijos
                        </p>
                        <select class="form-control select2" name="familiar_hijos" id="familiar_hijos" style="width:auto;min-width:150px;font-size:13px;">
                            <option value="No" {{ old('familiar_hijos', $Paciente->familiar_hijos) == 'No' ? 'selected' : '' }}>No tiene</option>
                            <option value="Si" {{ old('familiar_hijos', $Paciente->familiar_hijos) == 'Si' ? 'selected' : '' }}>Tiene hijos</option>
                        </select>
                    </div>
                    <div id="table-container-hijos" style="display:{{ old('familiar_hijos', $Paciente->familiar_hijos) == 'Si' ? 'block' : 'none' }};">
                        <table class="pe-dyn-table">
                            <thead><tr><th>Nombre</th><th style="width:20%">Edad</th><th style="width:14%">Tutor</th><th style="width:40px"></th></tr></thead>
                            <tbody id="table-body-hijos">
                                @foreach($Paciente->hijos as $hijo)
                                <tr>
                                    <td><input class="form-control form-control-sm" type="text" name="hijos_nombre[]" value="{{ old('hijos_nombre.'.$loop->index, $hijo->hijos_nombre) }}"></td>
                                    <td><input class="form-control form-control-sm" type="number" name="hijos_edad[]" value="{{ old('hijos_edad.'.$loop->index, $hijo->hijos_edad) }}"></td>
                                    <td><select class="form-control form-control-sm select2" name="hijos_tutor[]"><option value="No" {{ old('hijos_tutor.'.$loop->index, $hijo->hijos_tutor)=='No'?'selected':'' }}>No</option><option value="Si" {{ old('hijos_tutor.'.$loop->index, $hijo->hijos_tutor)=='Si'?'selected':'' }}>Sí</option></select></td>
                                    <td><button type="button" class="rx-rm-btn remove-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="rx-btn ghost" id="add-row-hijos" style="font-size:12px;padding:6px 12px;margin-bottom:14px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Agregar hijo
                        </button>
                    </div>

                    <hr class="pe-divider">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                        <p class="pe-section-title" style="margin:0;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Hermanos
                        </p>
                        <select class="form-control select2" name="familiar_hermanos" id="familiar_hermanos" style="width:auto;min-width:160px;font-size:13px;">
                            <option value="No" {{ old('familiar_hermanos', $Paciente->familiar_hermanos) == 'No' ? 'selected' : '' }}>No tiene</option>
                            <option value="Si" {{ old('familiar_hermanos', $Paciente->familiar_hermanos) == 'Si' ? 'selected' : '' }}>Tiene hermanos</option>
                        </select>
                    </div>
                    <div id="table-container-hermanos" style="display:{{ old('familiar_hermanos', $Paciente->familiar_hermanos) == 'Si' ? 'block' : 'none' }};">
                        <table class="pe-dyn-table">
                            <thead><tr><th>Nombre</th><th style="width:20%">Edad</th><th style="width:14%">Convive</th><th style="width:40px"></th></tr></thead>
                            <tbody id="table-body-hermanos">
                                @foreach($Paciente->hermanos as $h)
                                <tr>
                                    <td><input class="form-control form-control-sm" type="text" name="hermanos_nombre[]" value="{{ old('hermanos_nombre.'.$loop->index, $h->hermanos_nombre) }}"></td>
                                    <td><input class="form-control form-control-sm" type="number" name="hermanos_edad[]" value="{{ old('hermanos_edad.'.$loop->index, $h->hermanos_edad) }}"></td>
                                    <td><select class="form-control form-control-sm select2" name="hermanos_convive[]"><option value="No" {{ old('hermanos_convive.'.$loop->index, $h->hermanos_convive)=='No'?'selected':'' }}>No</option><option value="Si" {{ old('hermanos_convive.'.$loop->index, $h->hermanos_convive)=='Si'?'selected':'' }}>Sí</option></select></td>
                                    <td><button type="button" class="rx-rm-btn remove-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="rx-btn ghost" id="add-row-hermanos" style="font-size:12px;padding:6px 12px;margin-bottom:14px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Agregar hermano
                        </button>
                    </div>

                    <div class="pe-tab-nav-btns">
                        <button type="button" class="rx-btn ghost tab-prev" data-target="tab-admision">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </button>
                        <button type="button" class="rx-btn primary tab-next" data-target="tab-antecedentes">
                            Siguiente
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ═══ TAB 5: ANTECEDENTES ═══ --}}
                <div class="tab-pane fade" id="tab-antecedentes" role="tabpanel">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                        <p class="pe-section-title" style="margin:0;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tratamientos anteriores
                        </p>
                        <select class="form-control select2" name="historial_tratamiento" id="historial_tratamiento" style="width:auto;min-width:160px;font-size:13px;">
                            <option value="No" {{ old('historial_tratamiento', $Paciente->historial_tratamiento) == 'No' ? 'selected' : '' }}>No tuvo</option>
                            <option value="Si" {{ old('historial_tratamiento', $Paciente->historial_tratamiento) == 'Si' ? 'selected' : '' }}>Tuvo tratamientos</option>
                        </select>
                    </div>
                    <div id="table-container-historial_tratamiento" style="display:{{ old('historial_tratamiento', $Paciente->historial_tratamiento) == 'Si' ? 'block' : 'none' }};">
                        <table class="pe-dyn-table">
                            <thead><tr><th>Lugar / Institución</th><th style="width:30%">Duración</th><th style="width:40px"></th></tr></thead>
                            <tbody id="table-body-historial_tratamiento">
                                @foreach($Paciente->historial_tratamientos as $t)
                                <tr>
                                    <td><input class="form-control form-control-sm" type="text" name="lugar[]" value="{{ $t->lugar }}"></td>
                                    <td><input class="form-control form-control-sm" type="text" name="duracion[]" value="{{ $t->duracion }}"></td>
                                    <td><button type="button" class="rx-rm-btn remove-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="rx-btn ghost" id="add-row-historial_tratamiento" style="font-size:12px;padding:6px 12px;margin-bottom:14px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Agregar
                        </button>
                    </div>

                    <hr class="pe-divider">
                    <p class="pe-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Problemática / Motivo de consulta
                    </p>
                    <div class="rx-field">
                        <label class="rx-label" for="problematica">Problemática principal</label>
                        <input class="rx-input" type="text" name="problematica" id="problematica" value="{{ old('problematica', $pro->problematica ?? '') }}">
                    </div>
                    <div class="rx-field">
                        <label class="rx-label" for="problematica_detalles">Detalles</label>
                        <textarea class="rx-input" name="problematica_detalles" id="problematica_detalles" rows="3" style="resize:vertical;">{{ old('problematica_detalles', $pro->problematica_detalles ?? '') }}</textarea>
                    </div>

                    <hr class="pe-divider">
                    <p class="pe-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Antecedentes relevantes
                    </p>
                    <div class="rx-grid-4">
                        @foreach([['name'=>'abuso_sexual','label'=>'Abuso sexual'],['name'=>'sobredosis','label'=>'Sobredosis'],['name'=>'antecedentes_legales','label'=>'Antec. legales'],['name'=>'analfabeto','label'=>'Analfabetismo']] as $field)
                        <div class="rx-field">
                            <label class="rx-label" for="{{ $field['name'] }}">{{ $field['label'] }}</label>
                            <select class="form-control rx-input select2" name="{{ $field['name'] }}" id="{{ $field['name'] }}">
                                <option value="">—</option>
                                <option value="No" {{ old($field['name'], $dad->{$field['name']} ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Si" {{ old($field['name'], $dad->{$field['name']} ?? '') == 'Si' ? 'selected' : '' }}>Sí</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                    <div class="rx-grid-3">
                        <div class="rx-field">
                            <label class="rx-label" for="padres_separados">Padres separados</label>
                            <select class="form-control rx-input select2" name="padres_separados" id="padres_separados">
                                <option value="">—</option>
                                <option value="No" {{ old('padres_separados', $dad->padres_separados ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Si" {{ old('padres_separados', $dad->padres_separados ?? '') == 'Si' ? 'selected' : '' }}>Sí</option>
                            </select>
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="privado_libertad">Privado de libertad</label>
                            <select class="form-control rx-input select2" name="privado_libertad" id="privado_libertad">
                                <option value="">—</option>
                                <option value="No" {{ old('privado_libertad', $dad->privado_libertad ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Si" {{ old('privado_libertad', $dad->privado_libertad ?? '') == 'Si' ? 'selected' : '' }}>Sí</option>
                            </select>
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="tiempo_privado_libertad">Tiempo privado</label>
                            <input class="rx-input" type="text" name="tiempo_privado_libertad" id="tiempo_privado_libertad" value="{{ old('tiempo_privado_libertad', $dad->tiempo_privado_libertad ?? '') }}">
                        </div>
                    </div>
                    <div class="rx-grid-2">
                        <div>
                            <div class="rx-field">
                                <label class="rx-label" for="enfermedad_cronica">Enfermedad crónica</label>
                                <select class="form-control rx-input select2" name="enfermedad_cronica" id="enfermedad_cronica">
                                    <option value="">—</option>
                                    <option value="No" {{ old('enfermedad_cronica', $dad->enfermedad_cronica ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                    <option value="Si" {{ old('enfermedad_cronica', $dad->enfermedad_cronica ?? '') == 'Si' ? 'selected' : '' }}>Sí</option>
                                </select>
                            </div>
                            <div class="rx-field" id="form-enfermedad_cronica_detalles" style="display:{{ old('enfermedad_cronica', $dad->enfermedad_cronica ?? '') == 'Si' ? 'block' : 'none' }};">
                                <label class="rx-label" for="enfermedad_cronica_detalle">Detalle</label>
                                <textarea class="rx-input" name="enfermedad_cronica_detalle" id="enfermedad_cronica_detalle" rows="2" style="resize:vertical;">{{ old('enfermedad_cronica_detalle', $dad->enfermedad_cronica_detalle ?? '') }}</textarea>
                            </div>
                        </div>
                        <div>
                            <div class="rx-field">
                                <label class="rx-label" for="alergia">Alergias</label>
                                <select class="form-control rx-input select2" name="alergia" id="alergia">
                                    <option value="">—</option>
                                    <option value="No" {{ old('alergia', $dad->alergia ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                    <option value="Si" {{ old('alergia', $dad->alergia ?? '') == 'Si' ? 'selected' : '' }}>Sí</option>
                                </select>
                            </div>
                            <div class="rx-field" id="form-alergia_detalles" style="display:{{ old('alergia', $dad->alergia ?? '') == 'Si' ? 'block' : 'none' }};">
                                <label class="rx-label" for="alergia_detalle">Detalle alergias</label>
                                <textarea class="rx-input" name="alergia_detalle" id="alergia_detalle" rows="2" style="resize:vertical;">{{ old('alergia_detalle', $dad->alergia_detalle ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pe-tab-nav-btns">
                        <button type="button" class="rx-btn ghost tab-prev" data-target="tab-familia">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </button>
                        <button type="button" class="rx-btn primary tab-next" data-target="tab-edlaboral">
                            Siguiente
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ═══ TAB 6: EDUCACIÓN Y LABORAL ═══ --}}
                <div class="tab-pane fade" id="tab-edlaboral" role="tabpanel">
                    <p class="pe-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        Nivel educativo
                    </p>
                    <div style="overflow-x:auto;margin-bottom:16px;">
                        <table class="pe-dyn-table">
                            <thead>
                                <tr><th style="width:15%">Nivel</th><th>Completo</th><th>Incompleto</th><th>Expulsado</th><th>Interrumpido</th><th>Cambios</th><th>Último año</th></tr>
                            </thead>
                            <tbody>
                                @foreach([['label'=>'Primaria','key'=>'primaria'],['label'=>'Secundaria','key'=>'secundaria'],['label'=>'Terciaria','key'=>'terciaria'],['label'=>'Universitaria','key'=>'facultad']] as $nv)
                                <tr>
                                    <td style="font-weight:700;color:var(--text-primary);">{{ $nv['label'] }}</td>
                                    @foreach(['completa','incompleta','expulsado','interrumpido','cambios'] as $c)
                                    @php $f=$nv['key'].'_'.$c; $dv=$edu?($edu->$f??'No'):'No'; @endphp
                                    <td style="text-align:center;">
                                        <select class="form-control form-control-sm select2" name="{{ $f }}">
                                            <option value="No" {{ old($f,$dv)!='Si'?'selected':'' }}>No</option>
                                            <option value="Si" {{ old($f,$dv)=='Si'?'selected':'' }}>Sí</option>
                                        </select>
                                    </td>
                                    @endforeach
                                    @php $af=$nv['key'].'_ultimo_anio'; $av=$edu?($edu->$af??''):''; @endphp
                                    <td><input class="form-control form-control-sm" type="number" name="{{ $af }}" value="{{ old($af,$av) }}" min="1" max="6" style="width:70px;margin:0 auto;display:block;"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="rx-field">
                        <label class="rx-label" for="observaciones">Observaciones educativas</label>
                        <textarea class="rx-input" name="observaciones" id="observaciones" rows="2" style="resize:vertical;">{{ old('observaciones', $edu->observaciones ?? '') }}</textarea>
                    </div>

                    <hr class="pe-divider">
                    <p class="pe-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Situación laboral
                    </p>
                    <div class="rx-grid-3">
                        <div class="rx-field">
                            <label class="rx-label" for="actividad_laboral">Actividad laboral</label>
                            <select class="form-control rx-input select2" name="actividad_laboral" id="actividad_laboral">
                                <option value="">—</option>
                                <option value="No" {{ old('actividad_laboral', $lab->actividad_laboral ?? '') == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Si" {{ old('actividad_laboral', $lab->actividad_laboral ?? '') == 'Si' ? 'selected' : '' }}>Sí</option>
                            </select>
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="empresa_laboral">Empresa / Empleador</label>
                            <input class="rx-input" type="text" name="empresa_laboral" id="empresa_laboral" value="{{ old('empresa_laboral', $lab->empresa_laboral ?? '') }}">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="cargo_laboral">Cargo / Ocupación</label>
                            <input class="rx-input" type="text" name="cargo_laboral" id="cargo_laboral" value="{{ old('cargo_laboral', $lab->cargo_laboral ?? '') }}">
                        </div>
                    </div>
                    <div class="rx-grid-2">
                        <div class="rx-field">
                            <label class="rx-label" for="antiguedad_laboral">Antigüedad</label>
                            <input class="rx-input" type="text" name="antiguedad_laboral" id="antiguedad_laboral" value="{{ old('antiguedad_laboral', $lab->antiguedad_laboral ?? '') }}">
                        </div>
                        <div class="rx-field">
                            <label class="rx-label" for="antecedente_laboral">Antecedente laboral</label>
                            <textarea class="rx-input" name="antecedente_laboral" id="antecedente_laboral" rows="2" style="resize:vertical;">{{ old('antecedente_laboral', $lab->antecedente_laboral ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="pe-tab-nav-btns">
                        <button type="button" class="rx-btn ghost tab-prev" data-target="tab-antecedentes">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </button>
                        <button type="submit" class="rx-btn success lg">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Guardar cambios
                        </button>
                    </div>
                </div>

            </div>{{-- /tab-content --}}
        </div>{{-- /pe-tabs-wrap --}}

        <div style="text-align:right;margin-top:12px;">
            <button type="submit" class="rx-btn success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Guardar cambios
            </button>
        </div>

    </form>
</div>{{-- /pe-wrap --}}
@endsection

@section('scripts')
@parent
<script>
$(document).ready(function() {
    // Tab navigation buttons
    $('.tab-next').on('click', function() {
        $('#pacienteTabs a[href="#' + $(this).data('target') + '"]').tab('show');
        window.scrollTo(0, 0);
    });
    $('.tab-prev').on('click', function() {
        $('#pacienteTabs a[href="#' + $(this).data('target') + '"]').tab('show');
        window.scrollTo(0, 0);
    });

    // Hash restore
    var hash = window.location.hash;
    if (hash && $(hash).length) {
        $('#pacienteTabs a[href="' + hash + '"]').tab('show');
    }
    $('#pacienteTabs a').on('shown.bs.tab', function(e) {
        history.replaceState(null, null, e.target.hash);
    });

    // Select2
    $('.select2').select2({ width: '100%', dropdownParent: $('body') });

    // Auto-calc edad
    $('#fecha_nac').on('change', function() {
        var d = new Date($(this).val());
        if (!isNaN(d)) {
            var hoy = new Date(), edad = hoy.getFullYear() - d.getFullYear();
            var m = hoy.getMonth() - d.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < d.getDate())) edad--;
            if (edad >= 0) $('#edad').val(edad);
        }
    });

    // Estado civil → cónyuge
    $('#estado_civil').on('change', function() {
        $('#conyuge-form').toggle($(this).val() === 'Casado');
    });

    // Hijos
    $('#familiar_hijos').on('change', function() {
        $('#table-container-hijos').toggle($(this).val() === 'Si');
    });

    // Hermanos
    $('#familiar_hermanos').on('change', function() {
        $('#table-container-hermanos').toggle($(this).val() === 'Si');
    });

    // Historial tratamiento
    $('#historial_tratamiento').on('change', function() {
        $('#table-container-historial_tratamiento').toggle($(this).val() === 'Si');
    });

    // Alergia
    $('#alergia').on('change', function() {
        $('#form-alergia_detalles').toggle($(this).val() === 'Si');
    });

    // Enfermedad crónica
    $('#enfermedad_cronica').on('change', function() {
        $('#form-enfermedad_cronica_detalles').toggle($(this).val() === 'Si');
    });

    // Reingresos
    $('#hubo_reingreso').on('change', function() {
        if ($(this).val() === '1') {
            $('#reingresos-container').show();
        } else {
            $('#reingresos-container').hide();
            $('#table-body-reingresos').empty();
        }
    });

    $('#add-row-reingresos').on('click', function() {
        var $row = $('<tr>' +
            '<td><input class="form-control form-control-sm" type="date" name="fecha_reingreso[]"></td>' +
            '<td><select class="form-control form-control-sm select2" name="modalidad_reingreso[]">' +
            '<option value="Internacion">Internación</option>' +
            '<option value="Ambulatorio">Ambulatorio</option>' +
            '<option value="Consultorio">Consultorio</option>' +
            '</select></td>' +
            '<td><input class="form-control form-control-sm" type="date" name="fecha_egreso_reingreso[]"></td>' +
            '<td><select class="form-control form-control-sm select2" name="tipo_egreso_reingreso[]">' +
            '<option value="">Sin egreso</option><option value="Alta">Alta</option>' +
            '<option value="Abandono">Abandono</option><option value="Expulsion">Expulsión</option>' +
            '</select></td>' +
            '<td><button type="button" class="rx-rm-btn remove-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>' +
            '</tr>');
        $('#table-body-reingresos').append($row);
        $row.find('.select2').select2({ width: '100%', dropdownParent: $('body') });
    });

    $('#add-row-hijos').on('click', function() {
        $('#table-body-hijos').append('<tr>' +
            '<td><input class="form-control form-control-sm" type="text" name="hijos_nombre[]"></td>' +
            '<td><input class="form-control form-control-sm" type="number" name="hijos_edad[]"></td>' +
            '<td><select class="form-control form-control-sm" name="hijos_tutor[]"><option value="No" selected>No</option><option value="Si">Sí</option></select></td>' +
            '<td><button type="button" class="rx-rm-btn remove-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>' +
            '</tr>');
    });

    $('#add-row-hermanos').on('click', function() {
        $('#table-body-hermanos').append('<tr>' +
            '<td><input class="form-control form-control-sm" type="text" name="hermanos_nombre[]"></td>' +
            '<td><input class="form-control form-control-sm" type="number" name="hermanos_edad[]"></td>' +
            '<td><select class="form-control form-control-sm" name="hermanos_convive[]"><option value="No" selected>No</option><option value="Si">Sí</option></select></td>' +
            '<td><button type="button" class="rx-rm-btn remove-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>' +
            '</tr>');
    });

    $('#add-row-historial_tratamiento').on('click', function() {
        $('#table-body-historial_tratamiento').append('<tr>' +
            '<td><input class="form-control form-control-sm" type="text" name="lugar[]"></td>' +
            '<td><input class="form-control form-control-sm" type="text" name="duracion[]"></td>' +
            '<td><button type="button" class="rx-rm-btn remove-row"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></td>' +
            '</tr>');
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endsection
