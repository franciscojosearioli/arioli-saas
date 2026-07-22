@extends('layouts.panel')
@section('content')

@push('styles')
<style>
/* ══════════════════════════════════════════
   PACIENTE CREATE — scoped design overrides
══════════════════════════════════════════ */

.pac-wrap {
    display: flex; flex-direction: column; gap: 20px;
    animation: pacFadeUp .35s ease both;
}
@@keyframes pacFadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── header ── */
.pac-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
}
.pac-header h1 {
    font-size: 22px; font-weight: 700;
    color: var(--text-primary, #0f172a);
    letter-spacing: -.02em; margin: 0;
}
.pac-header p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

.pac-btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 8px;
    border: 1px solid var(--card-border, #e8edf2);
    background: var(--card-bg, #fff); color: var(--text-secondary, #64748b);
    font-size: 13px; font-weight: 500; text-decoration: none; white-space: nowrap;
    transition: background .12s;
}
.pac-btn-back:hover { background: var(--body-bg,#f8fafc); color: var(--text-primary,#0f172a); text-decoration: none; }
.pac-btn-back svg { width: 14px; height: 14px; }

/* ── error alert ── */
.pac-alert-err {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; border-radius: 10px;
    background: #fff1f2; color: #e11d48;
    border: 1px solid #fecaca; font-size: 13px; font-weight: 500;
}
.pac-alert-err svg { width: 16px; height: 16px; flex-shrink: 0; }

/* ══ TABS ══ */
#pacienteTabs {
    display: flex; flex-wrap: wrap; gap: 2px;
    background: var(--body-bg, #f1f5f9);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: 12px 12px 0 0; padding: 4px;
    border-bottom: none; margin-bottom: 0;
}
#pacienteTabs .nav-item { flex: 1; min-width: 0; }
#pacienteTabs .nav-link {
    display: flex; align-items: center; justify-content: center; gap: 5px;
    padding: 8px 10px; border-radius: 8px !important;
    font-size: 12px; font-weight: 600; text-align: center;
    color: var(--text-secondary, #64748b) !important;
    border: none !important; background: transparent !important;
    white-space: nowrap; transition: all .12s;
}
#pacienteTabs .nav-link:hover {
    color: var(--text-primary, #0f172a) !important;
    background: var(--card-bg, #fff) !important;
}
#pacienteTabs .nav-link.active {
    background: var(--card-bg, #fff) !important;
    color: var(--accent, #1d4ed8) !important;
    box-shadow: 0 1px 4px rgba(0,0,0,.08);
}
.pac-tab-badge {
    width: 16px; height: 16px; border-radius: 50%;
    background: #e11d48; color: #fff;
    font-size: 9px; font-weight: 800; line-height: 16px; text-align: center;
    flex-shrink: 0;
}

/* ══ TAB CONTENT PANEL ══ */
.pac-wrap .tab-content {
    background: var(--card-bg, #fff);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: 0 0 14px 14px;
    border-top: none;
    box-shadow: var(--card-shadow);
    padding: 24px !important;
}

/* ══ FORM ELEMENTS (scoped) ══ */
.pac-wrap .form-group { margin-bottom: 0; }
.pac-wrap .form-group > label,
.pac-wrap label.font-weight-bold,
.pac-wrap label {
    font-size: 11px !important; font-weight: 600 !important;
    text-transform: uppercase; letter-spacing: .06em;
    color: var(--text-muted, #94a3b8) !important;
    margin-bottom: 5px; display: block;
}
.pac-wrap .form-control {
    border: 1px solid var(--card-border, #e8edf2) !important;
    border-radius: 8px !important; padding: 8px 11px !important;
    font-size: 13px !important; color: var(--text-primary, #0f172a) !important;
    background: var(--body-bg, #f8fafc) !important;
    font-family: var(--font-sans, inherit) !important;
    height: auto !important; box-shadow: none !important;
    transition: border-color .15s !important;
}
.pac-wrap .form-control:focus {
    border-color: var(--accent, #1d4ed8) !important;
    box-shadow: 0 0 0 3px rgba(29,78,216,.08) !important;
}
.pac-wrap .form-control.is-invalid { border-color: #e11d48 !important; }
.pac-wrap .invalid-feedback { font-size: 11px; color: #e11d48; display: block; margin-top: 3px; }
.pac-wrap .form-control-sm { padding: 5px 9px !important; font-size: 12px !important; }
.pac-wrap .form-control[readonly] { opacity: .6; cursor: not-allowed; }
html.dark .pac-wrap .form-control {
    background: #0f172a !important; color: #f1f5f9 !important; border-color: #1e293b !important;
}

/* ── rows → spacing ── */
.pac-wrap .row { margin-left: -8px; margin-right: -8px; }
.pac-wrap .row > [class*="col-"] { padding-left: 8px; padding-right: 8px; margin-bottom: 14px; }

/* ── section headings ── */
.pac-wrap h6.text-muted {
    font-size: 12px !important; font-weight: 700 !important; text-transform: uppercase;
    letter-spacing: .06em; color: var(--text-secondary, #64748b) !important;
    display: flex; align-items: center; gap: 6px;
}
.pac-wrap hr { border-color: var(--card-border, #e8edf2); margin: 18px 0; }
.pac-wrap p.text-muted.small { font-size: 12px; color: var(--text-muted, #94a3b8); }

/* ── sub-tables (familia, educación, reingresos) ── */
.pac-wrap .table {
    font-size: 12px;
}
.pac-wrap .table thead th {
    font-size: 10px !important; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: var(--text-muted, #94a3b8);
    background: var(--body-bg, #f8fafc); border-bottom: 1px solid var(--card-border, #e8edf2);
    border-top: none; padding: 7px 10px;
}
.pac-wrap .table td {
    border-color: var(--card-border, #e8edf2); padding: 6px 8px;
    vertical-align: middle; color: var(--text-primary, #0f172a);
}
.pac-wrap .table-bordered { border: 1px solid var(--card-border, #e8edf2); border-radius: 8px; overflow: hidden; }
.pac-wrap .table-bordered td, .pac-wrap .table-bordered th { border-color: var(--card-border, #e8edf2); }
.pac-wrap .thead-light th { background: var(--body-bg, #f8fafc) !important; }
html.dark .pac-wrap .table thead th { background: #0f172a; }
html.dark .pac-wrap .table td { background: transparent; color: #f1f5f9; border-color: #1e293b; }

/* ── nav buttons (Siguiente / Anterior) ── */
.pac-wrap .tab-nav-footer {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 20px; padding-top: 16px;
    border-top: 1px solid var(--card-border, #e8edf2);
    flex-wrap: wrap; gap: 10px;
}
.pac-wrap .tab-nav-footer .req-hint { font-size: 12px; color: var(--text-muted, #94a3b8); }
.pac-wrap .tab-nav-footer .req-hint span { color: #e11d48; }

/* Override Bootstrap btn inside pac-wrap */
.pac-wrap .btn { border-radius: 9px !important; font-size: 13px !important; font-weight: 600 !important; }
.pac-wrap .btn-primary, .pac-wrap .btn-primary:hover { background: var(--accent, #1d4ed8) !important; border-color: var(--accent, #1d4ed8) !important; }
.pac-wrap .btn-outline-secondary {
    color: var(--text-secondary, #64748b) !important;
    border-color: var(--card-border, #e8edf2) !important;
    background: var(--card-bg, #fff) !important;
}
.pac-wrap .btn-outline-secondary:hover {
    background: var(--body-bg, #f8fafc) !important;
    color: var(--text-primary, #0f172a) !important;
}
.pac-wrap .btn-success { background: #16a34a !important; border-color: #16a34a !important; }
.pac-wrap .btn-success:hover { background: #15803d !important; }
.pac-wrap .btn-outline-primary {
    color: var(--accent, #1d4ed8) !important;
    border-color: var(--accent, #1d4ed8) !important;
    background: transparent !important;
}
.pac-wrap .btn-outline-primary:hover { background: var(--accent-light, #eff6ff) !important; }
.pac-wrap .btn-danger { background: #e11d48 !important; border-color: #e11d48 !important; }
.pac-wrap .btn-sm { padding: 5px 12px !important; font-size: 12px !important; }
.pac-wrap .btn-lg { padding: 11px 24px !important; font-size: 14px !important; }
.pac-wrap .badge-danger { background: #e11d48 !important; border-radius: 99px !important; padding: 2px 6px !important; font-size: 10px !important; }
</style>
@endpush

<div class="pac-wrap">

    {{-- ── Header ── --}}
    <div class="pac-header">
        <div>
            <h1>Registrar nuevo paciente</h1>
            <p>Los campos con <span style="color:#e11d48;font-weight:700;">*</span> son obligatorios. El resto puede completarse luego.</p>
        </div>
        <a href="{{ url()->previous() }}" class="pac-btn-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>
    </div>

    {{-- ── Error alert ── --}}
    @if($errors->any())
    <div class="pac-alert-err">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <strong>Hay errores en el formulario.</strong>&nbsp;Por favor revisá los campos marcados.
    </div>
    @endif

    <form method="POST" action="{{ route('panel.paciente.store') }}" enctype="multipart/form-data" id="form-paciente">
        @csrf

        {{-- ══ TAB NAV ══ --}}
        <ul class="nav" id="pacienteTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tab-personal" role="tab">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="d-none d-md-inline">1. Datos personales</span>
                    <span class="d-md-none">1</span>
                    <span class="pac-tab-badge">*</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-domicilio" role="tab">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="d-none d-md-inline">2. Domicilio</span>
                    <span class="d-md-none">2</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-admision" role="tab">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="d-none d-md-inline">3. Admisión</span>
                    <span class="d-md-none">3</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-familia" role="tab">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="d-none d-md-inline">4. Familia</span>
                    <span class="d-md-none">4</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-antecedentes" role="tab">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="d-none d-md-inline">5. Antecedentes</span>
                    <span class="d-md-none">5</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-edlaboral" role="tab">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                    <span class="d-none d-md-inline">6. Educación y laboral</span>
                    <span class="d-md-none">6</span>
                </a>
            </li>
        </ul>

        {{-- ══ TAB CONTENT (kept exactly, only footer buttons changed) ══ --}}
        <div class="tab-content border border-top-0 bg-white p-4" id="pacienteTabContent">

            {{-- ===== TAB 1: DATOS PERSONALES ===== --}}
            <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                <p class="text-muted small mb-4">Completá al menos el nombre, apellido y documento para guardar el paciente.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre <span style="color:#e11d48">*</span></label>
                            <input class="form-control @error('nombre') is-invalid @enderror" type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required placeholder="Nombre(s) del paciente">
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido">Apellido <span style="color:#e11d48">*</span></label>
                            <input class="form-control @error('apellido') is-invalid @enderror" type="text" name="apellido" id="apellido" value="{{ old('apellido') }}" required placeholder="Apellido del paciente">
                            @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dni">DNI / Documento <span style="color:#e11d48">*</span></label>
                            <input class="form-control @error('dni') is-invalid @enderror" type="text" name="dni" id="dni" value="{{ old('dni') }}" required placeholder="Número de documento">
                            @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_nac">Fecha de nacimiento</label>
                            <input class="form-control" type="date" name="fecha_nac" id="fecha_nac" value="{{ old('fecha_nac') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="edad">Edad <small style="font-size:10px;color:var(--text-muted,#94a3b8);text-transform:none;letter-spacing:0;">(calculada)</small></label>
                            <input class="form-control" type="number" name="edad" id="edad" value="{{ old('edad') }}" readonly placeholder="Se calcula al ingresar fecha">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono <small style="font-weight:400;color:#94a3b8;">(opcional)</small></label>
                            <input class="form-control" type="tel" name="telefono" id="telefono" value="{{ old('telefono') }}" placeholder="Ej: +54 9 11 1234-5678">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email del paciente <small style="font-weight:400;color:#94a3b8;">(opcional — para enviar consentimientos)</small></label>
                            <input class="form-control" type="email" name="email" id="email" value="{{ old('email') }}" placeholder="paciente@email.com">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select class="form-control select2 @error('sexo') is-invalid @enderror" name="sexo" id="sexo">
                                <option value="">— Seleccionar —</option>
                                <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @error('sexo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="estado_civil">Estado civil</label>
                            <select class="form-control select2 @error('estado_civil') is-invalid @enderror" name="estado_civil" id="estado_civil">
                                <option value="">— Seleccionar —</option>
                                <option value="Soltero" {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>Soltero/a</option>
                                <option value="Casado" {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>Casado/a</option>
                                <option value="Divorciado" {{ old('estado_civil') == 'Divorciado' ? 'selected' : '' }}>Divorciado/a</option>
                                <option value="Viudo" {{ old('estado_civil') == 'Viudo' ? 'selected' : '' }}>Viudo/a</option>
                                <option value="Union libre" {{ old('estado_civil') == 'Union libre' ? 'selected' : '' }}>Unión libre</option>
                            </select>
                            @error('estado_civil')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <hr>
                <h6 class="text-muted mb-3">Cobertura médica</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="obra_social">Obra social / Prepaga</label>
                            <input class="form-control" type="text" name="obra_social" id="obra_social" value="{{ old('obra_social') }}" placeholder="Ej: OSDE, IOMA, PAMI, Particular...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="n_afiliado">N° de afiliado</label>
                            <input class="form-control" type="text" name="n_afiliado" id="n_afiliado" value="{{ old('n_afiliado') }}" placeholder="Si aplica">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <span class="text-muted small"><span style="color:#e11d48">*</span> Campos obligatorios</span>
                    <button type="button" class="btn btn-primary tab-next" data-target="tab-domicilio">
                        Siguiente →
                    </button>
                </div>
            </div>

            {{-- ===== TAB 2: DOMICILIO ===== --}}
            <div class="tab-pane fade" id="tab-domicilio" role="tabpanel">
                <h6 class="text-muted mb-3">Domicilio del paciente</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="provincia">Provincia</label>
                            <input class="form-control" type="text" name="provincia" id="provincia" value="{{ old('provincia') }}" placeholder="Ej: Buenos Aires, Córdoba...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="localidad">Localidad / Ciudad</label>
                            <input class="form-control" type="text" name="localidad" id="localidad" value="{{ old('localidad') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="calle">Calle</label>
                            <input class="form-control" type="text" name="calle" id="calle" value="{{ old('calle') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="calle_numero">Número</label>
                            <input class="form-control" type="text" name="calle_numero" id="calle_numero" value="{{ old('calle_numero') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="calle_piso">Piso</label>
                            <input class="form-control" type="text" name="calle_piso" id="calle_piso" value="{{ old('calle_piso') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="calle_dpto">Dpto.</label>
                            <input class="form-control" type="text" name="calle_dpto" id="calle_dpto" value="{{ old('calle_dpto') }}">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary tab-prev" data-target="tab-personal">← Anterior</button>
                    <button type="button" class="btn btn-primary tab-next" data-target="tab-admision">Siguiente →</button>
                </div>
            </div>

            {{-- ===== TAB 3: ADMISIÓN ===== --}}
            <div class="tab-pane fade" id="tab-admision" role="tabpanel">
                <h6 class="text-muted mb-3">Datos de ingreso</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_ingreso">Fecha de ingreso</label>
                            <input class="form-control" type="date" name="fecha_ingreso" id="fecha_ingreso" value="{{ old('fecha_ingreso') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="modalidad">Modalidad</label>
                            <select class="form-control select2 @error('modalidad') is-invalid @enderror" name="modalidad" id="modalidad">
                                <option value="">— Seleccionar —</option>
                                <option value="Internacion" {{ old('modalidad') == 'Internacion' ? 'selected' : '' }}>Internación</option>
                                <option value="Ambulatorio" {{ old('modalidad') == 'Ambulatorio' ? 'selected' : '' }}>Ambulatorio</option>
                                <option value="Hospital de Dia" {{ old('modalidad') == 'Hospital de Dia' ? 'selected' : '' }}>Hospital de Día</option>
                                <option value="Consultorio" {{ old('modalidad') == 'Consultorio' ? 'selected' : '' }}>Consultorio</option>
                            </select>
                            @error('modalidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_egreso">Fecha de egreso</label>
                            <input class="form-control" type="date" name="fecha_egreso" id="fecha_egreso" value="{{ old('fecha_egreso') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_egreso">Tipo de egreso</label>
                            <select class="form-control select2 @error('tipo_egreso') is-invalid @enderror" name="tipo_egreso" id="tipo_egreso">
                                <option value="">— Sin egreso —</option>
                                <option value="Alta" {{ old('tipo_egreso') == 'Alta' ? 'selected' : '' }}>Alta</option>
                                <option value="Abandono" {{ old('tipo_egreso') == 'Abandono' ? 'selected' : '' }}>Abandono</option>
                                <option value="Expulsion" {{ old('tipo_egreso') == 'Expulsion' ? 'selected' : '' }}>Expulsión</option>
                            </select>
                            @error('tipo_egreso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex align-items-center mb-3" style="gap:12px;">
                    <h6 class="text-muted mb-0">Reingresos</h6>
                    <select class="form-control form-control-sm select2" name="hubo_reingreso" id="hubo_reingreso" style="width:auto;min-width:150px;">
                        <option value="0" selected>Sin reingresos</option>
                        <option value="1">Con reingresos</option>
                    </select>
                </div>
                <div id="reingresos-container" style="display:none;">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Fecha reingreso</th><th>Modalidad</th>
                                <th>Fecha egreso</th><th>Tipo egreso</th>
                                <th style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody id="table-body-reingresos"></tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-row-reingresos">+ Agregar reingreso</button>
                </div>
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary tab-prev" data-target="tab-domicilio">← Anterior</button>
                    <button type="button" class="btn btn-primary tab-next" data-target="tab-familia">Siguiente →</button>
                </div>
            </div>

            {{-- ===== TAB 4: FAMILIA ===== --}}
            <div class="tab-pane fade" id="tab-familia" role="tabpanel">
                <h6 class="text-muted mb-3">Responsables / Contactos</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:12%">Vínculo</th>
                                <th>Nombre completo</th>
                                <th style="width:22%">Teléfono</th>
                                <th style="width:15%">Responsable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="align-middle" style="font-weight:700;text-align:center;">Padre</td>
                                <td><input class="form-control form-control-sm" type="text" name="padre_nombre" placeholder="Nombre y apellido" value="{{ old('padre_nombre') }}"></td>
                                <td><input class="form-control form-control-sm" type="text" name="padre_telefono" placeholder="Teléfono" value="{{ old('padre_telefono') }}"></td>
                                <td><select class="form-control form-control-sm select2" name="padre_responsable"><option value="No" selected>No</option><option value="Si">Sí</option></select></td>
                            </tr>
                            <tr>
                                <td class="align-middle" style="font-weight:700;text-align:center;">Madre</td>
                                <td><input class="form-control form-control-sm" type="text" name="madre_nombre" placeholder="Nombre y apellido" value="{{ old('madre_nombre') }}"></td>
                                <td><input class="form-control form-control-sm" type="text" name="madre_telefono" placeholder="Teléfono" value="{{ old('madre_telefono') }}"></td>
                                <td><select class="form-control form-control-sm select2" name="madre_responsable"><option value="No" selected>No</option><option value="Si">Sí</option></select></td>
                            </tr>
                            <tr>
                                <td class="align-middle" style="font-weight:700;text-align:center;">Tutor</td>
                                <td><input class="form-control form-control-sm" type="text" name="tutor_nombre" placeholder="Nombre y apellido" value="{{ old('tutor_nombre') }}"></td>
                                <td><input class="form-control form-control-sm" type="text" name="tutor_telefono" placeholder="Teléfono" value="{{ old('tutor_telefono') }}"></td>
                                <td><select class="form-control form-control-sm select2" name="tutor_responsable"><option value="No" selected>No</option><option value="Si">Sí</option></select></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="conyuge-form" style="display:none;">
                    <hr>
                    <h6 class="text-muted mb-3">Datos del cónyuge / pareja</h6>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label for="conyugue_nombre">Nombre</label><input class="form-control" type="text" name="conyugue_nombre" id="conyugue_nombre" value="{{ old('conyugue_nombre') }}"></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="conyugue_apellido">Apellido</label><input class="form-control" type="text" name="conyugue_apellido" id="conyugue_apellido" value="{{ old('conyugue_apellido') }}"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label for="conyugue_edad">Edad</label><input class="form-control" type="number" name="conyugue_edad" id="conyugue_edad" value="{{ old('conyugue_edad') }}"></div></div>
                        <div class="col-md-9"><div class="form-group"><label for="conyugue_domicilio">Domicilio</label><input class="form-control" type="text" name="conyugue_domicilio" id="conyugue_domicilio" value="{{ old('conyugue_domicilio') }}"></div></div>
                    </div>
                </div>

                <hr>
                <div class="d-flex align-items-center mb-2" style="gap:12px;">
                    <h6 class="text-muted mb-0">Hijos</h6>
                    <select class="form-control form-control-sm select2" name="familiar_hijos" id="familiar_hijos" style="width:auto;min-width:150px;">
                        <option value="No" {{ old('familiar_hijos') == 'No' ? 'selected' : '' }}>No tiene</option>
                        <option value="Si" {{ old('familiar_hijos') == 'Si' ? 'selected' : '' }}>Tiene hijos</option>
                    </select>
                </div>
                <div id="table-container-hijos" style="display:none;">
                    <table class="table table-bordered table-sm mb-2">
                        <thead class="thead-light"><tr><th>Nombre</th><th style="width:18%">Edad</th><th style="width:15%">Tutor</th><th style="width:8%"></th></tr></thead>
                        <tbody id="table-body-hijos">
                            <tr>
                                <td><input class="form-control form-control-sm" type="text" name="hijos_nombre[]"></td>
                                <td><input class="form-control form-control-sm" type="number" name="hijos_edad[]"></td>
                                <td><select class="form-control form-control-sm select2" name="hijos_tutor[]"><option value="No" selected>No</option><option value="Si">Sí</option></select></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-row-hijos">+ Agregar hijo</button>
                </div>

                <hr>
                <div class="d-flex align-items-center mb-2" style="gap:12px;">
                    <h6 class="text-muted mb-0">Hermanos</h6>
                    <select class="form-control form-control-sm select2" name="familiar_hermanos" id="familiar_hermanos" style="width:auto;min-width:165px;">
                        <option value="No" {{ old('familiar_hermanos') == 'No' ? 'selected' : '' }}>No tiene</option>
                        <option value="Si" {{ old('familiar_hermanos') == 'Si' ? 'selected' : '' }}>Tiene hermanos</option>
                    </select>
                </div>
                <div id="table-container-hermanos" style="display:none;">
                    <table class="table table-bordered table-sm mb-2">
                        <thead class="thead-light"><tr><th>Nombre</th><th style="width:18%">Edad</th><th style="width:15%">Convive</th><th style="width:8%"></th></tr></thead>
                        <tbody id="table-body-hermanos">
                            <tr>
                                <td><input class="form-control form-control-sm" type="text" name="hermanos_nombre[]"></td>
                                <td><input class="form-control form-control-sm" type="number" name="hermanos_edad[]"></td>
                                <td><select class="form-control form-control-sm select2" name="hermanos_convive[]"><option value="No" selected>No</option><option value="Si">Sí</option></select></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-row-hermanos">+ Agregar hermano</button>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary tab-prev" data-target="tab-admision">← Anterior</button>
                    <button type="button" class="btn btn-primary tab-next" data-target="tab-antecedentes">Siguiente →</button>
                </div>
            </div>

            {{-- ===== TAB 5: ANTECEDENTES ===== --}}
            <div class="tab-pane fade" id="tab-antecedentes" role="tabpanel">
                <div class="d-flex align-items-center mb-3" style="gap:12px;">
                    <h6 class="text-muted mb-0">Tratamientos anteriores</h6>
                    <select class="form-control form-control-sm select2" name="historial_tratamiento" id="historial_tratamiento" style="width:auto;min-width:160px;">
                        <option value="No">No tuvo</option>
                        <option value="Si" {{ old('historial_tratamiento') == 'Si' ? 'selected' : '' }}>Tuvo tratamientos</option>
                    </select>
                </div>
                <div id="table-container-historial_tratamiento" style="display:none;">
                    <table class="table table-bordered table-sm mb-2">
                        <thead class="thead-light"><tr><th>Lugar / Institución</th><th style="width:28%">Duración</th><th style="width:8%"></th></tr></thead>
                        <tbody id="table-body-historial_tratamiento">
                            <tr>
                                <td><input class="form-control form-control-sm" type="text" name="lugar[]"></td>
                                <td><input class="form-control form-control-sm" type="text" name="duracion[]"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-row-historial_tratamiento">+ Agregar</button>
                </div>

                <hr>
                <h6 class="text-muted mb-3">Problemática / Motivo de consulta</h6>
                <div class="row">
                    <div class="col-md-12"><div class="form-group"><label for="problematica">Problemática principal</label><input class="form-control" type="text" name="problematica" id="problematica" value="{{ old('problematica') }}" placeholder="Diagnóstico o motivo de consulta general"></div></div>
                    <div class="col-md-12"><div class="form-group"><label for="problematica_detalles">Detalles</label><textarea class="form-control" name="problematica_detalles" id="problematica_detalles" rows="3" placeholder="Descripción ampliada...">{{ old('problematica_detalles') }}</textarea></div></div>
                </div>

                <hr>
                <h6 class="text-muted mb-3">Antecedentes relevantes</h6>
                <div class="row">
                    <div class="col-6 col-md-3"><div class="form-group"><label for="abuso_sexual">Abuso sexual</label><select class="form-control select2" name="abuso_sexual" id="abuso_sexual"><option value="">—</option><option value="No" {{ old('abuso_sexual') == 'No' ? 'selected' : '' }}>No</option><option value="Si" {{ old('abuso_sexual') == 'Si' ? 'selected' : '' }}>Sí</option></select></div></div>
                    <div class="col-6 col-md-3"><div class="form-group"><label for="sobredosis">Sobredosis</label><select class="form-control select2" name="sobredosis" id="sobredosis"><option value="">—</option><option value="No" {{ old('sobredosis') == 'No' ? 'selected' : '' }}>No</option><option value="Si" {{ old('sobredosis') == 'Si' ? 'selected' : '' }}>Sí</option></select></div></div>
                    <div class="col-6 col-md-3"><div class="form-group"><label for="antecedentes_legales">Antec. legales</label><select class="form-control select2" name="antecedentes_legales" id="antecedentes_legales"><option value="">—</option><option value="No" {{ old('antecedentes_legales') == 'No' ? 'selected' : '' }}>No</option><option value="Si" {{ old('antecedentes_legales') == 'Si' ? 'selected' : '' }}>Sí</option></select></div></div>
                    <div class="col-6 col-md-3"><div class="form-group"><label for="analfabeto">Analfabetismo</label><select class="form-control select2" name="analfabeto" id="analfabeto"><option value="">—</option><option value="No" {{ old('analfabeto') == 'No' ? 'selected' : '' }}>No</option><option value="Si" {{ old('analfabeto') == 'Si' ? 'selected' : '' }}>Sí</option></select></div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label for="padres_separados">Padres separados</label><select class="form-control select2" name="padres_separados" id="padres_separados"><option value="">—</option><option value="No" {{ old('padres_separados') == 'No' ? 'selected' : '' }}>No</option><option value="Si" {{ old('padres_separados') == 'Si' ? 'selected' : '' }}>Sí</option></select></div></div>
                    <div class="col-md-4"><div class="form-group"><label for="privado_libertad">Privado de libertad</label><select class="form-control select2" name="privado_libertad" id="privado_libertad"><option value="">—</option><option value="No" {{ old('privado_libertad') == 'No' ? 'selected' : '' }}>No</option><option value="Si" {{ old('privado_libertad') == 'Si' ? 'selected' : '' }}>Sí</option></select></div></div>
                    <div class="col-md-4"><div class="form-group"><label for="tiempo_privado_libertad">Tiempo privado de libertad</label><input class="form-control" type="text" name="tiempo_privado_libertad" id="tiempo_privado_libertad" value="{{ old('tiempo_privado_libertad') }}" placeholder="Ej: 2 años"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group"><label for="enfermedad_cronica">Enfermedad crónica</label><select class="form-control select2" name="enfermedad_cronica" id="enfermedad_cronica"><option value="">—</option><option value="No" {{ old('enfermedad_cronica') == 'No' ? 'selected' : '' }}>No</option><option value="Si" {{ old('enfermedad_cronica') == 'Si' ? 'selected' : '' }}>Sí</option></select></div>
                        <div class="form-group" id="form-enfermedad_cronica_detalles" style="display:none;"><label for="enfermedad_cronica_detalles">Detalle enfermedad</label><textarea class="form-control" name="enfermedad_cronica_detalles" id="enfermedad_cronica_detalles" rows="2">{{ old('enfermedad_cronica_detalles') }}</textarea></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"><label for="alergia">Alergias</label><select class="form-control select2" name="alergia" id="alergia"><option value="">—</option><option value="No" {{ old('alergia') == 'No' ? 'selected' : '' }}>No</option><option value="Si" {{ old('alergia') == 'Si' ? 'selected' : '' }}>Sí</option></select></div>
                        <div class="form-group" id="form-alergia_detalles" style="display:none;"><label for="alergia_detalles">Detalle alergias</label><textarea class="form-control" name="alergia_detalles" id="alergia_detalles" rows="2">{{ old('alergia_detalles') }}</textarea></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary tab-prev" data-target="tab-familia">← Anterior</button>
                    <button type="button" class="btn btn-primary tab-next" data-target="tab-edlaboral">Siguiente →</button>
                </div>
            </div>

            {{-- ===== TAB 6: EDUCACIÓN Y LABORAL ===== --}}
            <div class="tab-pane fade" id="tab-edlaboral" role="tabpanel">
                <h6 class="text-muted mb-3">Nivel educativo</h6>
                @php
                $niveles = [
                    ['label' => 'Primaria',      'key' => 'primaria'],
                    ['label' => 'Secundaria',    'key' => 'secundaria'],
                    ['label' => 'Terciaria',     'key' => 'terciaria'],
                    ['label' => 'Universitaria', 'key' => 'facultad'],
                ];
                @endphp
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:15%">Nivel</th>
                                <th class="text-center">Completo</th>
                                <th class="text-center">Incompleto</th>
                                <th class="text-center">Último año</th>
                                <th class="text-center">Expulsado</th>
                                <th class="text-center">Interrumpido</th>
                                <th class="text-center">Cambios</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($niveles as $nivel)
                            <tr>
                                <td class="align-middle" style="font-weight:700;">{{ $nivel['label'] }}</td>
                                <td class="text-center"><select class="form-control form-control-sm select2" name="{{ $nivel['key'] }}_completa"><option value="No" {{ old($nivel['key'].'_completa','No') != 'Si' ? 'selected':'' }}>No</option><option value="Si" {{ old($nivel['key'].'_completa')=='Si'?'selected':'' }}>Sí</option></select></td>
                                <td class="text-center"><select class="form-control form-control-sm select2" name="{{ $nivel['key'] }}_incompleta"><option value="No" {{ old($nivel['key'].'_incompleta','No') != 'Si' ? 'selected':'' }}>No</option><option value="Si" {{ old($nivel['key'].'_incompleta')=='Si'?'selected':'' }}>Sí</option></select></td>
                                <td class="text-center"><input class="form-control form-control-sm" type="number" name="{{ $nivel['key'] }}_ultimo_anio" value="{{ old($nivel['key'].'_ultimo_anio') }}" min="1" max="6"></td>
                                <td class="text-center"><select class="form-control form-control-sm select2" name="{{ $nivel['key'] }}_expulsado"><option value="No" {{ old($nivel['key'].'_expulsado','No') != 'Si' ? 'selected':'' }}>No</option><option value="Si" {{ old($nivel['key'].'_expulsado')=='Si'?'selected':'' }}>Sí</option></select></td>
                                <td class="text-center"><select class="form-control form-control-sm select2" name="{{ $nivel['key'] }}_interrumpido"><option value="No" {{ old($nivel['key'].'_interrumpido','No') != 'Si' ? 'selected':'' }}>No</option><option value="Si" {{ old($nivel['key'].'_interrumpido')=='Si'?'selected':'' }}>Sí</option></select></td>
                                <td class="text-center"><select class="form-control form-control-sm select2" name="{{ $nivel['key'] }}_cambios"><option value="No" {{ old($nivel['key'].'_cambios','No') != 'Si' ? 'selected':'' }}>No</option><option value="Si" {{ old($nivel['key'].'_cambios')=='Si'?'selected':'' }}>Sí</option></select></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label for="observaciones">Observaciones educativas</label>
                    <textarea class="form-control" name="observaciones" id="observaciones" rows="2">{{ old('observaciones') }}</textarea>
                </div>

                <hr>
                <h6 class="text-muted mb-3">Situación laboral</h6>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label for="actividad_laboral">Actividad laboral</label><select class="form-control select2" name="actividad_laboral" id="actividad_laboral"><option value="">—</option><option value="No" {{ old('actividad_laboral')=='No'?'selected':'' }}>No</option><option value="Si" {{ old('actividad_laboral')=='Si'?'selected':'' }}>Sí</option></select></div></div>
                    <div class="col-md-5"><div class="form-group"><label for="empresa_laboral">Empresa / Empleador</label><input class="form-control" type="text" name="empresa_laboral" id="empresa_laboral" value="{{ old('empresa_laboral') }}"></div></div>
                    <div class="col-md-4"><div class="form-group"><label for="cargo_laboral">Cargo / Ocupación</label><input class="form-control" type="text" name="cargo_laboral" id="cargo_laboral" value="{{ old('cargo_laboral') }}"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label for="antiguedad_laboral">Antigüedad</label><input class="form-control" type="text" name="antiguedad_laboral" id="antiguedad_laboral" value="{{ old('antiguedad_laboral') }}" placeholder="Ej: 3 años"></div></div>
                    <div class="col-md-8"><div class="form-group"><label for="antecedente_laboral">Antecedente laboral</label><textarea class="form-control" name="antecedente_laboral" id="antecedente_laboral" rows="2">{{ old('antecedente_laboral') }}</textarea></div></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary tab-prev" data-target="tab-antecedentes">← Anterior</button>
                    <button type="submit" class="btn btn-success btn-lg">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:6px;vertical-align:middle;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-8H7v8M7 3v5h8"/>
                        </svg>
                        Guardar paciente
                    </button>
                </div>
            </div>

        </div>{{-- /tab-content --}}

        <div class="mt-3 text-right">
            <button type="submit" class="btn btn-success">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:5px;vertical-align:middle;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar paciente
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Tab navigation
    $('.tab-next').on('click', function() {
        $('#pacienteTabs a[href="#' + $(this).data('target') + '"]').tab('show');
        window.scrollTo(0, 0);
    });
    $('.tab-prev').on('click', function() {
        $('#pacienteTabs a[href="#' + $(this).data('target') + '"]').tab('show');
        window.scrollTo(0, 0);
    });

    // Hash-based tab restore on page load
    var hash = window.location.hash;
    if (hash && $(hash).length) {
        $('#pacienteTabs a[href="' + hash + '"]').tab('show');
    }
    $('#pacienteTabs a').on('shown.bs.tab', function(e) {
        history.replaceState(null, null, e.target.hash);
    });

    // Auto-calculate edad from fecha_nac
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
    function toggleConyuge() {
        $('#conyuge-form').toggle($('#estado_civil').val() === 'Casado');
    }
    $('#estado_civil').on('change', toggleConyuge);
    toggleConyuge();

    // Hijos
    $('#familiar_hijos').on('change', function() {
        $('#table-container-hijos').toggle($(this).val() === 'Si');
    }).trigger('change');

    // Hermanos
    $('#familiar_hermanos').on('change', function() {
        $('#table-container-hermanos').toggle($(this).val() === 'Si');
    }).trigger('change');

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
        $('#table-body-reingresos').append(`<tr>
            <td><input class="form-control form-control-sm" type="date" name="fecha_reingreso[]"></td>
            <td><select class="form-control form-control-sm select2" name="modalidad_reingreso[]">
                <option value="Internacion">Internación</option>
                <option value="Ambulatorio">Ambulatorio</option>
                <option value="Consultorio">Consultorio</option>
            </select></td>
            <td><input class="form-control form-control-sm" type="date" name="fecha_egreso_reingreso[]"></td>
            <td><select class="form-control form-control-sm select2" name="tipo_egreso_reingreso[]">
                <option value="">Sin egreso</option><option value="Alta">Alta</option>
                <option value="Abandono">Abandono</option><option value="Expulsion">Expulsión</option>
            </select></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">×</button></td>
        </tr>`);
        $('.select2').select2();
    });

    $('#add-row-hijos').on('click', function() {
        $('#table-body-hijos').append(`<tr>
            <td><input class="form-control form-control-sm" type="text" name="hijos_nombre[]"></td>
            <td><input class="form-control form-control-sm" type="number" name="hijos_edad[]"></td>
            <td><select class="form-control form-control-sm select2" name="hijos_tutor[]"><option value="No" selected>No</option><option value="Si">Sí</option></select></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">×</button></td>
        </tr>`);
    });

    $('#add-row-hermanos').on('click', function() {
        $('#table-body-hermanos').append(`<tr>
            <td><input class="form-control form-control-sm" type="text" name="hermanos_nombre[]"></td>
            <td><input class="form-control form-control-sm" type="number" name="hermanos_edad[]"></td>
            <td><select class="form-control form-control-sm select2" name="hermanos_convive[]"><option value="No" selected>No</option><option value="Si">Sí</option></select></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">×</button></td>
        </tr>`);
    });

    $('#add-row-historial_tratamiento').on('click', function() {
        $('#table-body-historial_tratamiento').append(`<tr>
            <td><input class="form-control form-control-sm" type="text" name="lugar[]"></td>
            <td><input class="form-control form-control-sm" type="text" name="duracion[]"></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">×</button></td>
        </tr>`);
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endpush
