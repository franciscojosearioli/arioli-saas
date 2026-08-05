@extends('layouts.panel')

@section('title', 'Editar Informe')

@section('content')

@push('styles')
<style>
.inf-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: var(--card-radius, 14px);
    box-shadow: var(--card-shadow);
    margin-bottom: 20px; overflow: hidden;
    animation: infFadeUp .3s ease both;
}
@@keyframes infFadeUp {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.inf-card-hdr {
    display: flex; align-items: center; gap: 8px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--card-border, #e8edf2);
    font-size: 14px; font-weight: 600;
    color: var(--text-primary, #0f172a);
}
.inf-card-hdr svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); flex-shrink: 0; }
.inf-card-body { padding: 20px; }

.inf-label {
    display: block; font-size: 10px; font-weight: 600;
    color: var(--text-muted, #94a3b8);
    text-transform: uppercase; letter-spacing: .07em; margin-bottom: 5px;
}
.inf-label .req { color: #dc2626; margin-left: 2px; }
.inf-input, .inf-select {
    width: 100%; padding: 8px 12px;
    border: 1px solid var(--card-border, #e8edf2); border-radius: 8px;
    font-size: 13px; font-family: var(--font-sans, inherit);
    color: var(--text-primary, #0f172a);
    background: var(--body-bg, #f8fafc); outline: none;
    transition: border-color .15s;
}
.inf-input:focus, .inf-select:focus {
    border-color: var(--accent, #1d4ed8);
    background: var(--card-bg, #fff);
}
.inf-hint { font-size: 11px; color: var(--text-muted, #94a3b8); margin-top: 4px; }
.inf-row { display: flex; gap: 14px; }
.inf-row > * { flex: 1; min-width: 0; }
.inf-field { margin-bottom: 14px; }

/* ── tipo botones ── */
.tipo-btns { display: flex; gap: 10px; }
.tipo-btn {
    flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px;
    padding: 14px 10px; border: 2px solid var(--card-border, #e8edf2); border-radius: 10px;
    background: var(--body-bg, #f8fafc); cursor: pointer; transition: all .15s;
    color: var(--text-secondary, #64748b); font-size: 12px; font-weight: 600; text-align: center;
}
.tipo-btn svg { width: 22px; height: 22px; }
.tipo-btn:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); background: var(--accent-light, #eff6ff); }
.tipo-btn.active { border-color: var(--accent, #1d4ed8); background: var(--accent, #1d4ed8); color: #fff; }

/* ── PDF actual ── */
.current-pdf-wrap {
    border: 1px solid var(--card-border, #e8edf2); border-radius: 8px; overflow: hidden; margin-bottom: 14px;
}
.current-pdf-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 12px; background: var(--body-bg, #f8fafc);
    border-bottom: 1px solid var(--card-border, #e8edf2);
    font-size: 12px; color: var(--text-secondary, #64748b);
}
.current-pdf-bar strong { color: var(--text-primary, #0f172a); }
.current-pdf-bar a {
    font-size: 11px; color: var(--accent, #1d4ed8); font-weight: 600; text-decoration: none;
}
.current-pdf-bar a:hover { text-decoration: underline; }
.current-pdf-iframe { width: 100%; height: 400px; border: none; display: block; }

/* ── Voz ── */
.voz-wrap { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; }
#mic-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px; border-radius: 8px;
    border: 1.5px solid var(--accent, #1d4ed8);
    background: var(--card-bg, #fff); color: var(--accent, #1d4ed8);
    font-size: 13px; font-weight: 700; cursor: pointer; transition: all .15s;
}
#mic-btn svg { width: 17px; height: 17px; }
#mic-btn.recording {
    background: #dc2626; border-color: #dc2626; color: #fff;
    animation: pulse-rec .9s infinite;
}
@@keyframes pulse-rec {
    0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.4); }
    50%      { box-shadow: 0 0 0 8px rgba(220,38,38,0); }
}
#speech-preview {
    flex: 1; font-size: 12px; color: var(--text-secondary, #64748b); font-style: italic;
    padding: 6px 10px; background: var(--body-bg, #f8fafc); border-radius: 6px;
    border: 1px solid var(--card-border, #e8edf2); max-width: 400px;
}
#speech-status { font-size: 11px; font-weight: 600; color: #dc2626; }
#mic-not-supported { display: none; font-size: 11px; color: #b45309; margin-top: 4px; }

/* ── Editor ── */
.fmt-toolbar {
    display: flex; gap: 2px; padding: 6px 8px;
    background: var(--body-bg, #f8fafc);
    border: 1px solid var(--card-border, #e8edf2);
    border-bottom: none; border-radius: 8px 8px 0 0; flex-wrap: wrap;
}
.fmt-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 28px; border: none; border-radius: 5px;
    background: transparent; cursor: pointer;
    color: var(--text-secondary, #64748b);
    font-size: 13px; font-weight: 700; transition: background .12s;
}
.fmt-btn:hover { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); }
.fmt-sep { width: 1px; background: var(--card-border, #e8edf2); margin: 2px 4px; align-self: stretch; }
.fmt-editor-wrap {
    border: 1px solid var(--card-border, #e8edf2);
    border-top: none; border-radius: 0 0 8px 8px; overflow: hidden;
}
#editor-content {
    min-height: 280px; padding: 14px 16px;
    font-size: 13.5px; font-family: var(--font-sans, inherit); line-height: 1.7;
    color: var(--text-primary, #0f172a); background: var(--card-bg, #fff); outline: none;
}
#editor-content:empty:before { content: attr(placeholder); color: #aaa; pointer-events: none; }
#redaccion { display: none; }

/* ── File upload ── */
.file-drop {
    border: 2px dashed var(--card-border, #e8edf2); border-radius: 10px; padding: 28px;
    text-align: center; cursor: pointer; background: var(--body-bg, #f8fafc);
    transition: border-color .15s, background .15s;
}
.file-drop:hover, .file-drop.drag-over { border-color: var(--accent, #1d4ed8); background: var(--accent-light, #eff6ff); }
.file-drop svg { width: 28px; height: 28px; color: #94a3b8; margin-bottom: 6px; }
.file-drop p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 0; }
.file-drop strong { color: var(--accent, #1d4ed8); }
#file-input { display: none; }
#file-list { list-style: none; padding: 0; margin: 10px 0 0; }
#file-list li {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 10px; background: var(--body-bg, #f8fafc);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: 6px; font-size: 12px;
    color: var(--text-primary, #0f172a); margin-bottom: 4px;
}
#file-list li svg { width: 14px; height: 14px; color: var(--accent, #1d4ed8); flex-shrink: 0; }

/* ── Receta existente ── */
.receta-existing-item {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 12px; border-radius: 8px;
    border: 1px solid var(--card-border, #e8edf2);
    background: var(--body-bg, #f8fafc); margin-bottom: 6px;
}
.receta-existing-item img {
    width: 40px; height: 40px; object-fit: cover;
    border-radius: 5px; flex-shrink: 0;
}
.receta-existing-item .rec-pdf-icon {
    width: 40px; height: 40px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: #fee2e2; border-radius: 5px;
}
.receta-existing-item .rec-pdf-icon svg { width: 18px; height: 18px; color: #dc2626; }
.receta-existing-item .rec-info { flex: 1; min-width: 0; }
.receta-existing-item .rec-info span { display: block; font-size: 12px; font-weight: 600; color: var(--text-primary,#0f172a); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.receta-existing-item .rec-info small { font-size: 11px; color: var(--text-muted,#94a3b8); }
.btn-del-receta {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
    background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; cursor: pointer; transition: background .12s;
}
.btn-del-receta:hover { background: #fecaca; }

/* ── Warning replace box ── */
.replace-notice {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 10px 14px; border-radius: 8px;
    background: #fffbeb; border: 1px solid #fde68a;
    font-size: 12px; color: #92400e; margin-bottom: 14px;
}
.replace-notice svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; color: #f59e0b; }

/* ── Actions ── */
.inf-actions { display: flex; align-items: center; justify-content: space-between; padding-top: 14px; }
.btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; border-radius: 10px;
    border: 1px solid var(--card-border, #e8edf2);
    background: var(--card-bg, #fff); color: var(--text-secondary, #64748b);
    font-size: 13px; font-weight: 600; text-decoration: none; transition: all .15s;
}
.btn-back:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); }
.btn-save {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 20px; border-radius: 10px;
    background: var(--accent, #1d4ed8); color: #fff; border: none;
    font-size: 13px; font-weight: 600; cursor: pointer;
    font-family: var(--font-sans, inherit);
    box-shadow: 0 2px 8px rgba(29,78,216,.25);
    transition: background .15s, transform .15s;
}
.btn-save:hover { background: var(--accent-hover, #1e40af); transform: translateY(-1px); }
.btn-save svg { width: 15px; height: 15px; }
</style>
@endpush

<div style="max-width:860px; margin:0 auto; padding:20px 0;">

    <div style="margin-bottom:20px; display:flex; align-items:center; gap:12px;">
        <a href="{{ url()->previous() }}" class="btn-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--text-primary,#0f172a); letter-spacing:-.02em; margin:0;">Editar Informe</h1>
            <p style="font-size:13px; color:var(--text-secondary,#64748b); margin:3px 0 0;">
                {{ $Informe->tipo->name ?? '' }}
                @if($Informe->fecha) · {{ \Carbon\Carbon::parse($Informe->fecha)->format('d/m/Y') }} @endif
                @if($Informe->paciente) · {{ $Informe->paciente->apellido }}, {{ $Informe->paciente->nombre }} @endif
            </p>
        </div>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#dc2626;">
        <strong>Corregí los siguientes errores:</strong>
        <ul style="margin:6px 0 0 18px; padding:0;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('panel.informe.update', $Informe->id) }}" enctype="multipart/form-data" id="edit-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="lock_version" value="{{ $Informe->lock_version }}">
        @if(request('from_paciente'))
        <input type="hidden" name="from_paciente" value="{{ request('from_paciente') }}">
        @endif

        {{-- ── IDENTIFICACIÓN ── --}}
        <div class="inf-card">
            <div class="inf-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Identificación
            </div>
            <div class="inf-card-body">
                <div class="inf-row">
                    <div class="inf-field">
                        <label class="inf-label" for="paciente_id">Paciente <span class="req">*</span></label>
                        <select class="inf-select select2" name="paciente_id" id="paciente_id" required>
                            @foreach($pacientes as $pid => $entry)
                            <option value="{{ $pid }}" {{ old('paciente_id', $Informe->paciente_id) == $pid ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="inf-field">
                        <label class="inf-label" for="profesional_id">Profesional</label>
                        <select class="inf-select select2" name="profesional_id" id="profesional_id">
                            @foreach($profesionales as $pid => $nombre)
                            <option value="{{ $pid }}" {{ old('profesional_id', $Informe->profesional_id) == $pid ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── DATOS ── --}}
        <div class="inf-card">
            <div class="inf-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Datos del Informe
            </div>
            <div class="inf-card-body">
                <div class="inf-row">
                    <div class="inf-field">
                        <label class="inf-label" for="tipo_id">Tipo de Informe <span class="req">*</span></label>
                        <select class="inf-select select2" name="tipo_id" id="tipo_id" required>
                            @foreach($tipos as $tid => $entry)
                                @if($tid !== '')
                                <option value="{{ $tid }}" {{ old('tipo_id', $Informe->tipo_id) == $tid ? 'selected' : '' }}>{{ $entry }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="inf-field">
                        <label class="inf-label" for="fecha">Fecha <span class="req">*</span></label>
                        <input class="inf-input" type="date" name="fecha" id="fecha"
                               value="{{ old('fecha', $Informe->fecha) }}" required>
                    </div>
                </div>
                <div class="inf-row">
                    <div class="inf-field">
                        <label class="inf-label" for="diagnostico">Diagnóstico / Motivo</label>
                        <input class="inf-input" type="text" name="diagnostico" id="diagnostico"
                               value="{{ old('diagnostico', $Informe->diagnostico) }}"
                               placeholder="Diagnóstico o motivo de consulta">
                    </div>
                    <div class="inf-field" style="max-width:200px;">
                        <label class="inf-label" for="codigo_cie10">Código CIE-10 / DSM-5</label>
                        <input class="inf-input" type="text" name="codigo_cie10" id="codigo_cie10"
                               value="{{ old('codigo_cie10', $Informe->codigo_cie10) }}"
                               placeholder="Ej: F32.1" maxlength="10">
                    </div>
                </div>
                <div class="inf-field">
                    <label class="inf-label" for="agenda_id">Cita asociada <span style="font-weight:400; text-transform:none; color:var(--t2);">(opcional)</span></label>
                    <select class="inf-select select2" name="agenda_id" id="agenda_id">
                        <option value="">— Sin cita asociada —</option>
                        @foreach($agendas as $a)
                        <option value="{{ $a->id }}" {{ old('agenda_id', $Informe->agenda_id) == $a->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($a->fecha_hora_inicio)->format('d/m/Y H:i') }} — {{ $a->motivo }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── CONTENIDO ── --}}
        <div class="inf-card">
            <div class="inf-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Contenido
            </div>
            <div class="inf-card-body">

                {{-- PDF actual --}}
                @if(!empty($attachedFiles))
                @php $firstFile = $attachedFiles[0]; $fileUrl = asset('storage/uploads/' . $Informe->paciente_id . '/' . $Informe->tipo_id . '/' . $firstFile); @endphp
                <div class="current-pdf-wrap">
                    <div class="current-pdf-bar">
                        <strong>Documento actual</strong>
                        <a href="{{ $fileUrl }}" target="_blank">Abrir en nueva pestaña ↗</a>
                    </div>
                    <iframe class="current-pdf-iframe" src="{{ $fileUrl }}#toolbar=0&navpanes=0&scrollbar=1"></iframe>
                </div>
                @endif

                {{-- Selector de modo ── --}}
                <div class="inf-field">
                    <label class="inf-label">¿Qué querés hacer? <span class="req">*</span></label>
                    <div class="tipo-btns">
                        <button type="button" class="tipo-btn {{ old('tipo_seleccion') == 'redaccion' ? 'active' : '' }}"
                                data-tipo="redaccion" onclick="setTipoEdit('redaccion', this)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Redactar nuevo contenido
                        </button>
                        <button type="button" class="tipo-btn {{ old('tipo_seleccion', 'documento') == 'documento' ? 'active' : '' }}"
                                data-tipo="documento" onclick="setTipoEdit('documento', this)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            Reemplazar PDF
                        </button>
                    </div>
                    <input type="hidden" name="tipo_seleccion" id="tipo_seleccion_hidden" value="{{ old('tipo_seleccion', 'documento') }}">
                </div>

                {{-- ── BLOQUE REDACCIÓN ── --}}
                <div id="bloque-redaccion" style="display:none;">

                    <div class="replace-notice">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        El texto que redactés reemplazará el PDF actual. El contenido anterior no se puede recuperar.
                    </div>

                    {{-- Barra de voz --}}
                    <div class="voz-wrap">
                        <button type="button" id="mic-btn" onclick="toggleMic()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 016 0v6a3 3 0 01-3 3z"/>
                            </svg>
                            <span id="mic-label">Dictar con micrófono</span>
                        </button>
                        <div id="speech-preview" style="display:none;"></div>
                        <span id="speech-status"></span>
                    </div>
                    <div id="mic-not-supported"></div>

                    {{-- Toolbar de formato --}}
                    <div class="fmt-toolbar">
                        <button type="button" class="fmt-btn" onclick="execFmt('bold')" title="Negrita"><b>B</b></button>
                        <button type="button" class="fmt-btn" onclick="execFmt('italic')" title="Cursiva"><i>I</i></button>
                        <button type="button" class="fmt-btn" onclick="execFmt('underline')" title="Subrayado"><u>S</u></button>
                        <button type="button" class="fmt-btn" onclick="execFmt('strikeThrough')" title="Tachado" style="text-decoration:line-through;">T</button>
                        <div class="fmt-sep"></div>
                        <button type="button" class="fmt-btn" onclick="execFmt('insertUnorderedList')" title="Lista">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </button>
                        <button type="button" class="fmt-btn" onclick="execFmt('insertOrderedList')" title="Lista numerada">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </button>
                        <div class="fmt-sep"></div>
                        <button type="button" class="fmt-btn" onclick="execFmt('justifyLeft')" title="Izquierda">
                            <svg fill="currentColor" viewBox="0 0 16 16" style="width:13px;height:13px;"><path d="M2 12.5a.5.5 0 010-1h7a.5.5 0 010 1H2zm0-3a.5.5 0 010-1h11a.5.5 0 010 1H2zm0-3a.5.5 0 010-1h7a.5.5 0 010 1H2zm0-3a.5.5 0 010-1h11a.5.5 0 010 1H2z"/></svg>
                        </button>
                        <button type="button" class="fmt-btn" onclick="execFmt('justifyCenter')" title="Centrar">
                            <svg fill="currentColor" viewBox="0 0 16 16" style="width:13px;height:13px;"><path d="M4 12.5a.5.5 0 010-1h8a.5.5 0 010 1H4zm-2-3a.5.5 0 010-1h12a.5.5 0 010 1H2zm2-3a.5.5 0 010-1h8a.5.5 0 010 1H4zm-2-3a.5.5 0 010-1h12a.5.5 0 010 1H2z"/></svg>
                        </button>
                        <div class="fmt-sep"></div>
                        <select class="fmt-btn" style="width:auto; font-size:11px; padding:0 4px; cursor:pointer; font-weight:normal;"
                                onchange="execFmtVal('formatBlock', this.value); this.value='';">
                            <option value="">Párrafo</option>
                            <option value="h1">Título 1</option>
                            <option value="h2">Título 2</option>
                            <option value="h3">Título 3</option>
                            <option value="p">Normal</option>
                        </select>
                        <div class="fmt-sep"></div>
                        <button type="button" class="fmt-btn" onclick="execFmt('removeFormat')" title="Limpiar" style="font-size:11px; width:auto; padding:0 6px; font-weight:normal;">Limpiar</button>
                    </div>
                    <div class="fmt-editor-wrap">
                        <div id="editor-content" contenteditable="true"
                             oninput="syncEditor()" onkeyup="syncEditor()" onmouseup="syncEditor()"
                             placeholder="Redactá el nuevo contenido aquí…"></div>
                    </div>
                    <textarea name="redaccion_informe" id="redaccion" style="display:none;"></textarea>
                    <div class="inf-hint" style="margin-top:6px;">El texto admite formato: negrita, listas, títulos y más.</div>
                </div>

                {{-- ── BLOQUE DOCUMENTO ── --}}
                <div id="bloque-documento">
                    @if(!empty($attachedFiles))
                    <div class="replace-notice">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Si subís un nuevo PDF reemplazará el documento actual. Si no seleccionás ningún archivo, el PDF actual se mantiene.
                    </div>
                    @endif
                    <div class="file-drop" id="file-drop" onclick="document.getElementById('file-input').click()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p>Arrastrá o <strong>hacé clic</strong> para seleccionar un nuevo PDF</p>
                        <p style="font-size:11px; margin-top:4px;">Máximo 10 MB</p>
                    </div>
                    <input type="file" id="file-input" name="document_file[]" multiple accept=".pdf">
                    <ul id="file-list"></ul>
                </div>

            </div>
        </div>

        {{-- ── RECETA ── --}}
        <div class="inf-card">
            <div class="inf-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Recetas médicas
                <span style="font-size:11px;font-weight:400;color:var(--text-muted,#94a3b8);margin-left:6px;">(opcional)</span>
            </div>
            <div class="inf-card-body">
                @if($Informe->recetas->count() > 0)
                <div style="margin-bottom:12px;">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted,#94a3b8);margin-bottom:6px;">Recetas actuales</div>
                    @foreach($Informe->recetas as $receta)
                    <div class="receta-existing-item">
                        @if($receta->isImage())
                        <img src="{{ $receta->url() }}" alt="{{ $receta->nombre_original }}">
                        @else
                        <div class="rec-pdf-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        @endif
                        <div class="rec-info">
                            <span>{{ $receta->nombre_original }}</span>
                            <small>{{ strtoupper(pathinfo($receta->nombre_original, PATHINFO_EXTENSION)) }}</small>
                        </div>
                        <a href="{{ $receta->url() }}" download="{{ $receta->nombre_original }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;font-size:11px;font-weight:600;background:var(--accent,#1d4ed8);color:#fff;text-decoration:none;margin-right:4px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                        <form method="POST" action="{{ route('panel.receta.destroy', $receta->id) }}"
                              onsubmit="return confirm('¿Eliminar esta receta?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-del-receta">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Eliminar
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif

                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted,#94a3b8);margin-bottom:6px;">Agregar nuevas recetas</div>
                <div class="file-drop" id="receta-drop" onclick="document.getElementById('receta-input').click()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p>Arrastrá o <strong>hacé clic</strong> para adjuntar recetas (imagen o PDF)</p>
                    <p style="font-size:11px;margin-top:4px;">JPG, PNG, WEBP, PDF — Máx 10 MB</p>
                </div>
                <input type="file" id="receta-input" name="receta_file[]" multiple
                       accept="image/jpeg,image/png,image/webp,application/pdf"
                       style="display:none;">
                <ul id="receta-list" style="list-style:none;padding:0;margin:8px 0 0;"></ul>
            </div>
        </div>

        {{-- ── FIRMA ── --}}
        @if(auth()->user()->firma_nombre)
        <div class="inf-card" id="firma-card" style="display:none;">
            <div class="inf-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Firma digital
            </div>
            <div class="inf-card-body">
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px;border-radius:8px;border:1.5px solid var(--card-border);background:var(--body-bg);">
                        <input type="radio" name="firmar_ahora" value="0" checked style="width:16px;height:16px;">
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--text-primary);">Guardar sin firmar</div>
                            <div style="font-size:11px;color:var(--text-muted);">El informe quedará pendiente de firma.</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px;border-radius:8px;border:1.5px solid var(--card-border);background:var(--body-bg);">
                        <input type="radio" name="firmar_ahora" value="1" style="width:16px;height:16px;">
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--text-primary);">Firmar ahora</div>
                            <div style="font-size:11px;color:var(--text-muted);">Se generará el PDF con tu firma. El documento quedará bloqueado.</div>
                        </div>
                    </label>
                </div>
                <div style="margin-top:10px;padding:10px 14px;background:var(--body-bg);border-radius:8px;border:1px solid var(--card-border);font-size:12px;color:var(--text-secondary);">
                    <strong>Firmará como:</strong> {{ auth()->user()->firma_nombre }}
                    @if(auth()->user()->firma_dni) — DNI {{ auth()->user()->firma_dni }}@endif
                    @if(auth()->user()->firma_matricula) — M.P. {{ auth()->user()->firma_matricula }}@endif
                </div>
            </div>
        </div>
        @else
        <input type="hidden" name="firmar_ahora" value="0">
        @endif

        {{-- ── ACCIONES ── --}}
        <div class="inf-actions">
            <a href="{{ url()->previous() }}" class="btn-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Cancelar
            </a>
            <button type="submit" class="btn-save">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Guardar cambios
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
/* ════ MODO CONTENIDO ════ */
function setTipoEdit(tipo, btn) {
    document.getElementById('tipo_seleccion_hidden').value = tipo;
    document.querySelectorAll('.tipo-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('bloque-redaccion').style.display = tipo === 'redaccion' ? 'block' : 'none';
    document.getElementById('bloque-documento').style.display = tipo === 'documento' ? 'block' : 'none';
    const fc = document.getElementById('firma-card');
    if (fc) fc.style.display = tipo === 'redaccion' ? '' : 'none';
}

// Inicializar según old value
(function() {
    const v = '{{ old("tipo_seleccion", "documento") }}';
    document.getElementById('bloque-redaccion').style.display = v === 'redaccion' ? 'block' : 'none';
    document.getElementById('bloque-documento').style.display = v === 'documento' ? 'block' : 'none';
    const fc = document.getElementById('firma-card');
    if (fc) fc.style.display = v === 'redaccion' ? '' : 'none';
})();

/* ════ EDITOR ════ */
function execFmt(cmd) {
    document.getElementById('editor-content').focus();
    document.execCommand(cmd, false, null);
    syncEditor();
}
function execFmtVal(cmd, val) {
    if (!val) return;
    document.getElementById('editor-content').focus();
    document.execCommand(cmd, false, val);
    syncEditor();
}
function syncEditor() {
    document.getElementById('redaccion').value =
        document.getElementById('editor-content').innerHTML;
}

/* ════ VOZ ════ */
let recognition = null, isRecording = false;
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

if (SpeechRecognition) {
    recognition = new SpeechRecognition();
    recognition.lang = 'es-AR';
    recognition.continuous = true;
    recognition.interimResults = true;

    recognition.onstart = () => {
        isRecording = true;
        document.getElementById('mic-btn').classList.add('recording');
        document.getElementById('mic-label').textContent = 'Detener dictado';
        document.getElementById('speech-preview').style.display = 'block';
        document.getElementById('speech-status').textContent = '● Grabando...';
    };
    recognition.onend = () => {
        isRecording = false;
        document.getElementById('mic-btn').classList.remove('recording');
        document.getElementById('mic-label').textContent = 'Dictar con micrófono';
        document.getElementById('speech-preview').style.display = 'none';
        document.getElementById('speech-preview').textContent = '';
        document.getElementById('speech-status').textContent = '';
    };
    recognition.onresult = (event) => {
        let interim = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
            const t = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
                const ed = document.getElementById('editor-content');
                ed.focus();
                const sel = window.getSelection();
                if (sel.rangeCount && ed.contains(sel.anchorNode)) {
                    const r = sel.getRangeAt(0);
                    r.deleteContents();
                    r.insertNode(document.createTextNode(t + ' '));
                    r.collapse(false);
                } else {
                    ed.innerHTML += t + ' ';
                }
                syncEditor();
            } else {
                interim += t;
            }
        }
        document.getElementById('speech-preview').textContent = interim || '…';
    };
    recognition.onerror = (e) => {
        if (e.error !== 'aborted') document.getElementById('speech-status').textContent = '⚠ ' + e.error;
        recognition.stop();
    };
} else {
    document.getElementById('mic-not-supported').textContent = '⚠ Tu navegador no soporta dictado. Usá Chrome o Edge.';
    document.getElementById('mic-not-supported').style.display = 'block';
    document.getElementById('mic-btn').style.display = 'none';
}
function toggleMic() {
    if (!recognition) return;
    isRecording ? recognition.stop() : recognition.start();
}

/* ════ FILE UPLOAD ════ */
document.getElementById('file-input').addEventListener('change', function() {
    const list = document.getElementById('file-list');
    list.innerHTML = '';
    Array.from(this.files).forEach(f => {
        const li = document.createElement('li');
        li.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        ${f.name} <span style="color:#64748b; font-size:11px;">(${(f.size/1024/1024).toFixed(2)} MB)</span>`;
        list.appendChild(li);
    });
});
const dropZone = document.getElementById('file-drop');
['dragenter','dragover'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('drag-over'); }));
['dragleave','drop'].forEach(e => dropZone.addEventListener(e, ev => {
    ev.preventDefault(); dropZone.classList.remove('drag-over');
    if (ev.type === 'drop') {
        document.getElementById('file-input').files = ev.dataTransfer.files;
        document.getElementById('file-input').dispatchEvent(new Event('change'));
    }
}));

/* ════ RECETA UPLOAD ════ */
document.getElementById('receta-input').addEventListener('change', function() {
    const list = document.getElementById('receta-list');
    list.innerHTML = '';
    Array.from(this.files).forEach(f => {
        const li = document.createElement('li');
        li.style.cssText = 'display:flex;align-items:center;gap:8px;padding:6px 0;font-size:12px;border-bottom:1px solid var(--card-border,#e2e8f0);';
        li.innerHTML = `<svg style="width:14px;height:14px;flex-shrink:0;color:#64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${f.name}</span>
        <span style="color:#64748b;font-size:11px;flex-shrink:0;">${(f.size/1024/1024).toFixed(2)} MB</span>`;
        list.appendChild(li);
    });
});
const recetaDrop = document.getElementById('receta-drop');
['dragenter','dragover'].forEach(e => recetaDrop.addEventListener(e, ev => { ev.preventDefault(); recetaDrop.classList.add('drag-over'); }));
['dragleave','drop'].forEach(e => recetaDrop.addEventListener(e, ev => {
    ev.preventDefault(); recetaDrop.classList.remove('drag-over');
    if (ev.type === 'drop') {
        document.getElementById('receta-input').files = ev.dataTransfer.files;
        document.getElementById('receta-input').dispatchEvent(new Event('change'));
    }
}));

/* ════ SELECT2 / CITAS ════ */
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });
    // Agendas ya están cargadas por el controller — no necesitan AJAX
});

/* Sync antes de enviar */
document.getElementById('edit-form').addEventListener('submit', function() {
    syncEditor();
});
</script>
@endpush
