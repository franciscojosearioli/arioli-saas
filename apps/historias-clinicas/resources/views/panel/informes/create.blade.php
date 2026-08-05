@extends('layouts.panel')

@section('title', 'Nuevo Informe')

@push('styles')
<style>
/* ── Form cards ── */
.inf-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: var(--card-radius, 14px);
    box-shadow: var(--card-shadow);
    margin-bottom: 20px;
    overflow: hidden;
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

/* ── Fields ── */
.inf-label {
    display: block;
    font-size: 10px;
    font-weight: 600;
    color: var(--text-muted, #94a3b8);
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: 5px;
}
.inf-label .req { color: #dc2626; margin-left: 2px; }
.inf-input, .inf-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: 8px;
    font-size: 13px;
    font-family: var(--font-sans, inherit);
    color: var(--text-primary, #0f172a);
    background: var(--body-bg, #f8fafc);
    outline: none;
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

/* ── Tipo selector (buttons) ── */
.tipo-btns { display: flex; gap: 10px; margin-bottom: 0; }
.tipo-btn {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 14px 10px;
    border: 2px solid var(--card-border, #e8edf2);
    border-radius: 10px;
    background: var(--body-bg, #f8fafc);
    cursor: pointer;
    transition: all .15s;
    color: var(--text-secondary, #64748b);
    font-size: 12px;
    font-weight: 600;
    text-align: center;
}
.tipo-btn svg { width: 22px; height: 22px; }
.tipo-btn:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); background: var(--accent-light, #eff6ff); }
.tipo-btn.active { border-color: var(--accent, #1d4ed8); background: var(--accent, #1d4ed8); color: #fff; }

/* ── Voz ── */
.voz-wrap { margin-bottom: 10px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
#mic-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 8px;
    border: 1.5px solid var(--accent, #1d4ed8);
    background: var(--card-bg, #fff);
    color: var(--accent, #1d4ed8);
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s;
}
#mic-btn svg { width: 17px; height: 17px; }
#mic-btn.recording {
    background: #dc2626;
    border-color: #dc2626;
    color: #fff;
    animation: pulse-rec .9s infinite;
}
@@keyframes pulse-rec {
    0%,100% { box-shadow: 0 0 0 0 rgba(220,38,38,.4); }
    50%      { box-shadow: 0 0 0 8px rgba(220,38,38,0); }
}
#speech-preview {
    flex: 1;
    font-size: 12px;
    color: var(--text-secondary, #64748b);
    font-style: italic;
    min-height: 20px;
    padding: 6px 10px;
    background: var(--body-bg, #f8fafc);
    border-radius: 6px;
    border: 1px solid var(--card-border, #e8edf2);
    max-width: 400px;
}
#speech-status {
    font-size: 11px;
    font-weight: 600;
    color: #dc2626;
    margin-left: 4px;
}
#mic-not-supported { display: none; font-size: 11px; color: #b45309; margin-top: 4px; }

/* ── Toolbar de formato ── */
.fmt-toolbar {
    display: flex;
    gap: 2px;
    padding: 6px 8px;
    background: var(--body-bg, #f8fafc);
    border: 1px solid var(--card-border, #e8edf2);
    border-bottom: none;
    border-radius: 8px 8px 0 0;
    flex-wrap: wrap;
}
.fmt-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px; height: 28px;
    border: none;
    border-radius: 5px;
    background: transparent;
    cursor: pointer;
    color: var(--text-secondary, #64748b);
    font-size: 13px;
    font-weight: 700;
    transition: background .12s;
}
.fmt-btn:hover { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); }
.fmt-btn.active { background: var(--accent, #1d4ed8); color: #fff; }
.fmt-sep { width: 1px; background: var(--card-border, #e8edf2); margin: 2px 4px; align-self: stretch; }
.fmt-editor-wrap {
    border: 1px solid var(--card-border, #e8edf2);
    border-top: none;
    border-radius: 0 0 8px 8px;
    overflow: hidden;
}
#editor-content {
    min-height: 280px;
    padding: 14px 16px;
    font-size: 13.5px;
    font-family: var(--font-sans, inherit);
    line-height: 1.7;
    color: var(--text-primary, #0f172a);
    background: var(--card-bg, #fff);
    outline: none;
    overflow-y: auto;
}
#editor-content:focus { outline: none; }
#redaccion { display: none; }

/* ── File upload ── */
.file-drop {
    border: 2px dashed var(--card-border, #e8edf2);
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    background: var(--body-bg, #f8fafc);
}
.file-drop:hover, .file-drop.drag-over {
    border-color: var(--accent, #1d4ed8);
    background: var(--accent-light, #eff6ff);
}
.file-drop svg { width: 32px; height: 32px; color: #94a3b8; margin-bottom: 8px; }
.file-drop p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 0; }
.file-drop strong { color: var(--accent, #1d4ed8); }
#file-input { display: none; }
#file-list { list-style: none; padding: 0; margin: 10px 0 0; }
#file-list li {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 10px;
    background: var(--body-bg, #f8fafc);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: 6px;
    font-size: 12px;
    color: var(--text-primary, #0f172a);
    margin-bottom: 4px;
}
#file-list li svg { width: 14px; height: 14px; color: var(--accent, #1d4ed8); flex-shrink: 0; }

/* ── Actions ── */
.inf-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 0 0;
}
.btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; border-radius: 10px;
    border: 1px solid var(--card-border, #e8edf2);
    background: var(--card-bg, #fff); color: var(--text-secondary, #64748b);
    font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all .15s;
}
.btn-back:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); }
.btn-save {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 20px; border-radius: 10px;
    background: var(--accent, #1d4ed8); color: #fff;
    border: none; font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: var(--font-sans, inherit);
    box-shadow: 0 2px 8px rgba(29,78,216,.25);
    transition: background .15s, transform .15s;
}
.btn-save:hover { background: var(--accent-hover, #1e40af); transform: translateY(-1px); }
.btn-save svg { width: 15px; height: 15px; }
</style>
@endpush

@section('content')
<div style="max-width:860px; margin:0 auto; padding:20px 0;">

    <div style="margin-bottom:20px; display:flex; align-items:center; gap:12px;">
        <a href="{{ url()->previous() }}" class="btn-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--text-primary,#0f172a); letter-spacing:-.02em; margin:0;">Nuevo Informe</h1>
            <p style="font-size:13px; color:var(--text-secondary,#64748b); margin:3px 0 0;">Completá los campos y redactá o adjuntá el informe clínico.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('panel.informe.store') }}" enctype="multipart/form-data" id="informe-form">
        @csrf
        @if(request('from_paciente'))
        <input type="hidden" name="from_paciente" value="{{ request('from_paciente') }}">
        @endif

        {{-- Errores globales --}}
        @if($errors->any())
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#dc2626;">
            <strong>Corregí los siguientes errores:</strong>
            <ul style="margin:6px 0 0 18px; padding:0;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
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
                            <option value="{{ $pid }}" {{ old('paciente_id', request('paciente_id')) == $pid ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @error('paciente') <div style="color:#dc2626; font-size:11px; margin-top:3px;">{{ $message }}</div> @enderror
                    </div>
                    <div class="inf-field">
                        <label class="inf-label" for="profesional_id">Profesional</label>
                        <select class="inf-select select2" name="profesional_id" id="profesional_id">
                            @foreach($profesionales as $pid => $nombre)
                            <option value="{{ $pid }}" {{ old('profesional_id', auth()->id()) == $pid ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── DATOS DEL INFORME ── --}}
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
                                <option value="{{ $tid }}" {{ old('tipo_id') == $tid ? 'selected' : '' }}>{{ $entry }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="inf-field">
                        <label class="inf-label" for="fecha">Fecha <span class="req">*</span></label>
                        <input class="inf-input" type="date" name="fecha" id="fecha"
                               value="{{ old('fecha', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="inf-row">
                    <div class="inf-field">
                        <label class="inf-label" for="diagnostico">Diagnóstico / Motivo</label>
                        <input class="inf-input" type="text" name="diagnostico" id="diagnostico"
                               value="{{ old('diagnostico') }}"
                               placeholder="Diagnóstico principal o motivo de consulta">
                    </div>
                    <div class="inf-field" style="max-width:200px;">
                        <label class="inf-label" for="codigo_cie10">Código CIE-10 / DSM-5</label>
                        <input class="inf-input" type="text" name="codigo_cie10" id="codigo_cie10"
                               value="{{ old('codigo_cie10') }}"
                               placeholder="Ej: F32.1" maxlength="10">
                    </div>
                </div>

                <div class="inf-field">
                    <label class="inf-label" for="agenda_id">
                        Cita asociada
                        <span style="font-weight:400; text-transform:none; letter-spacing:0; color:var(--t2);">(opcional)</span>
                    </label>
                    <select class="inf-select select2" name="agenda_id" id="agenda_id">
                        <option value="">— Sin cita asociada —</option>
                    </select>
                    <div class="inf-hint">Seleccioná primero el paciente para ver sus citas.</div>
                </div>
            </div>
        </div>

        {{-- ── CONTENIDO ── --}}
        <div class="inf-card">
            <div class="inf-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Contenido del Informe
            </div>
            <div class="inf-card-body">

                {{-- Selector tipo --}}
                <div class="inf-field">
                    <label class="inf-label">Formato <span class="req">*</span></label>
                    <div class="tipo-btns">
                        <button type="button" class="tipo-btn {{ old('tipo_seleccion') == 'redaccion' ? 'active' : '' }}"
                                onclick="setTipo('redaccion')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Redactar
                        </button>
                        <button type="button" class="tipo-btn {{ old('tipo_seleccion') == 'documento' ? 'active' : '' }}"
                                onclick="setTipo('documento')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            Subir PDF
                        </button>
                    </div>
                    <input type="hidden" name="tipo_seleccion" id="tipo_seleccion_hidden" value="{{ old('tipo_seleccion', '') }}">
                </div>

                {{-- Redacción --}}
                <div id="bloque-redaccion" style="display:none;">

                    {{-- Plantilla (solo si el tipo elegido tiene alguna) --}}
                    <div class="inf-field" id="plantilla-wrap" style="display:none;">
                        <label class="inf-label" for="plantilla_id">Usar plantilla</label>
                        <select class="inf-select select2" id="plantilla_id">
                            <option value="">— Sin plantilla (redacción libre) —</option>
                        </select>
                        <div class="inf-hint">Al elegir una plantilla se precarga el texto; podés editarlo antes de guardar.</div>
                        <input type="hidden" name="plantilla_documento_version_id" id="plantilla_documento_version_id" value="">
                    </div>

                    {{-- Barra de voz --}}
                    <div class="voz-wrap">
                        <button type="button" id="mic-btn" onclick="toggleMic()">
                            <svg id="mic-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 016 0v6a3 3 0 01-3 3z"/>
                            </svg>
                            <span id="mic-label">Dictar con micrófono</span>
                        </button>
                        <div id="speech-preview" style="display:none;"></div>
                        <span id="speech-status"></span>
                    </div>
                    <div id="mic-not-supported">
                        ⚠ Tu navegador no soporta dictado por voz. Usá Chrome o Edge.
                    </div>

                    {{-- Toolbar de formato --}}
                    <div class="fmt-toolbar" id="fmt-toolbar">
                        <button type="button" class="fmt-btn" onclick="execFmt('bold')" title="Negrita (Ctrl+B)"><b>B</b></button>
                        <button type="button" class="fmt-btn" onclick="execFmt('italic')" title="Cursiva (Ctrl+I)"><i>I</i></button>
                        <button type="button" class="fmt-btn" onclick="execFmt('underline')" title="Subrayado (Ctrl+U)"><u>S</u></button>
                        <button type="button" class="fmt-btn" onclick="execFmt('strikeThrough')" title="Tachado" style="text-decoration:line-through;">T</button>
                        <div class="fmt-sep"></div>
                        <button type="button" class="fmt-btn" onclick="execFmt('insertUnorderedList')" title="Lista">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </button>
                        <button type="button" class="fmt-btn" onclick="execFmt('insertOrderedList')" title="Lista numerada">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </button>
                        <div class="fmt-sep"></div>
                        <button type="button" class="fmt-btn" onclick="execFmt('justifyLeft')" title="Alinear izquierda">
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
                        <button type="button" class="fmt-btn" onclick="execFmt('removeFormat')" title="Limpiar formato" style="font-size:11px; width:auto; padding:0 6px; font-weight:normal;">Limpiar</button>
                    </div>

                    <div class="fmt-editor-wrap">
                        <div id="editor-content" contenteditable="true"
                             onkeyup="syncEditor()" onmouseup="syncEditor()"
                             oninput="syncEditor()"
                             placeholder="Redactá el informe aquí o usá el micrófono para dictar..."></div>
                    </div>
                    {{-- Campo oculto que se envía --}}
                    <textarea name="redaccion_informe" id="redaccion" style="display:none;"></textarea>
                    <div class="inf-hint" style="margin-top:6px;">El texto admite formato: negrita, listas, títulos y más.</div>
                </div>

                {{-- Documento PDF --}}
                <div id="bloque-documento" style="display:none;">
                    <div class="file-drop" id="file-drop" onclick="document.getElementById('file-input').click()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p>Arrastrá o <strong>hacé clic</strong> para seleccionar archivos PDF</p>
                        <p style="font-size:11px; margin-top:4px;">Máximo 10 MB por archivo</p>
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
                Receta médica
                <span style="font-size:11px;font-weight:400;color:var(--text-muted,#94a3b8);margin-left:6px;">(opcional)</span>
            </div>
            <div class="inf-card-body">
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
                            <div style="font-size:11px;color:var(--text-muted);">El informe quedará pendiente de firma y podrás firmarlo después.</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px;border-radius:8px;border:1.5px solid var(--card-border);background:var(--body-bg);">
                        <input type="radio" name="firmar_ahora" value="1" style="width:16px;height:16px;">
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--text-primary);">Firmar ahora</div>
                            <div style="font-size:11px;color:var(--text-muted);">Se generará el PDF con tu firma digital. El documento quedará firmado y no podrá modificarse.</div>
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
                Guardar Informe
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
/* ════════════════════════════════════════════════
   TIPO DE CONTENIDO
════════════════════════════════════════════════ */
function setTipo(tipo) {
    document.getElementById('tipo_seleccion_hidden').value = tipo;
    document.querySelectorAll('.tipo-btn').forEach(b => b.classList.remove('active'));
    event.currentTarget.classList.add('active');
    document.getElementById('bloque-redaccion').style.display = tipo === 'redaccion' ? 'block' : 'none';
    document.getElementById('bloque-documento').style.display = tipo === 'documento' ? 'block' : 'none';
    const fc = document.getElementById('firma-card');
    if (fc) fc.style.display = tipo === 'redaccion' ? '' : 'none';
    if (tipo === 'documento') {
        document.getElementById('plantilla-wrap').style.display = 'none';
    } else if (typeof jQuery !== 'undefined' && $('#tipo_id').val()) {
        $('#tipo_id').trigger('change');
    }
}

// Restaurar si hay old value
(function() {
    const v = '{{ old("tipo_seleccion", "") }}';
    if (v === 'redaccion' || v === 'documento') {
        document.getElementById('bloque-' + v).style.display = 'block';
        const fc = document.getElementById('firma-card');
        if (fc) fc.style.display = v === 'redaccion' ? '' : 'none';
    }
})();

/* ════════════════════════════════════════════════
   EDITOR CON FORMATO (contenteditable)
════════════════════════════════════════════════ */
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

// Placeholder CSS fallback
const editor = document.getElementById('editor-content');
editor.addEventListener('focus', () => {
    if (editor.innerHTML === '') editor.innerHTML = '';
});

// Restaurar contenido si hay old value
(function() {
    const old = `{!! old('redaccion_informe', '') !!}`;
    if (old) {
        document.getElementById('editor-content').innerHTML = old;
        syncEditor();
    }
})();

/* ════════════════════════════════════════════════
   VOZ — Web Speech API
════════════════════════════════════════════════ */
let recognition = null;
let isRecording  = false;
let finalTranscript = '';

const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

if (SpeechRecognition) {
    recognition = new SpeechRecognition();
    recognition.lang = 'es-AR';
    recognition.continuous = true;
    recognition.interimResults = true;

    recognition.onstart = () => {
        isRecording = true;
        const btn = document.getElementById('mic-btn');
        btn.classList.add('recording');
        document.getElementById('mic-label').textContent = 'Detener dictado';
        document.getElementById('speech-preview').style.display = 'block';
        document.getElementById('speech-status').textContent = '● Grabando...';
    };

    recognition.onend = () => {
        isRecording = false;
        const btn = document.getElementById('mic-btn');
        btn.classList.remove('recording');
        document.getElementById('mic-label').textContent = 'Dictar con micrófono';
        document.getElementById('speech-preview').textContent = '';
        document.getElementById('speech-preview').style.display = 'none';
        document.getElementById('speech-status').textContent = '';
        finalTranscript = '';
    };

    recognition.onresult = (event) => {
        let interim = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
            const t = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
                finalTranscript += t;
                // Insertar en el editor
                const edEl = document.getElementById('editor-content');
                edEl.focus();
                // Insertar en posición del cursor si la hay, sino al final
                const sel = window.getSelection();
                if (sel.rangeCount > 0 && edEl.contains(sel.anchorNode)) {
                    const range = sel.getRangeAt(0);
                    range.deleteContents();
                    range.insertNode(document.createTextNode(t + ' '));
                    range.collapse(false);
                } else {
                    edEl.innerHTML += (edEl.innerHTML ? ' ' : '') + t + ' ';
                }
                syncEditor();
                finalTranscript = '';
            } else {
                interim += t;
            }
        }
        document.getElementById('speech-preview').textContent = interim || '…';
    };

    recognition.onerror = (e) => {
        if (e.error !== 'aborted') {
            document.getElementById('speech-status').textContent = '⚠ Error: ' + e.error;
        }
        isRecording = false;
        recognition.stop();
    };

} else {
    document.getElementById('mic-not-supported').style.display = 'block';
    document.getElementById('mic-btn').style.display = 'none';
}

function toggleMic() {
    if (!recognition) return;
    if (isRecording) {
        recognition.stop();
    } else {
        document.getElementById('editor-content').focus();
        recognition.start();
    }
}

/* ════════════════════════════════════════════════
   FILE UPLOAD
════════════════════════════════════════════════ */
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

// Drag & drop
const dropZone = document.getElementById('file-drop');
['dragenter','dragover'].forEach(e => dropZone.addEventListener(e, ev => {
    ev.preventDefault(); dropZone.classList.add('drag-over');
}));
['dragleave','drop'].forEach(e => dropZone.addEventListener(e, ev => {
    ev.preventDefault(); dropZone.classList.remove('drag-over');
    if (e === 'drop') {
        document.getElementById('file-input').files = ev.dataTransfer.files;
        document.getElementById('file-input').dispatchEvent(new Event('change'));
    }
}));

/* ════════════════════════════════════════════════
   SELECT2 — citas por paciente (AJAX)
════════════════════════════════════════════════ */
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    function cargarCitas(pacienteId) {
        const $s = $('#agenda_id');
        $s.empty().append('<option value="">— Sin cita asociada —</option>');
        if (!pacienteId) return;
        $.getJSON('{{ url("/agenda-citas-paciente") }}/' + pacienteId, function(data) {
            $.each(data, function(i, c) {
                $s.append('<option value="' + c.id + '">' + c.label + '</option>');
            });
            $s.trigger('change.select2');
        });
    }

    $('#paciente_id').on('change', function() { cargarCitas($(this).val()); });
    if ($('#paciente_id').val()) cargarCitas($('#paciente_id').val());

    /* ── Plantillas del tipo de informe (motor de documentos) ── */
    function cargarPlantillas(tipoId) {
        const $wrap = $('#plantilla-wrap');
        const $sel  = $('#plantilla_id');
        $sel.empty().append('<option value="">— Sin plantilla (redacción libre) —</option>').trigger('change.select2');
        document.getElementById('plantilla_documento_version_id').value = '';
        if (!tipoId) { $wrap.hide(); return; }
        $.getJSON('{{ url("/informe/tipos") }}/' + tipoId + '/plantillas', function(data) {
            if (!data.length) { $wrap.hide(); return; }
            data.forEach(p => $sel.append('<option value="' + p.id + '">' + p.nombre + '</option>'));
            $sel.trigger('change.select2');
            $wrap.show();
        });
    }

    $('#tipo_id').on('change', function() { cargarPlantillas($(this).val()); });
    if ($('#tipo_id').val()) cargarPlantillas($('#tipo_id').val());

    $('#plantilla_id').on('change', function() {
        const plantillaId = $(this).val();
        document.getElementById('plantilla_documento_version_id').value = '';
        if (!plantillaId) return;

        const params = {
            paciente_id:    $('#paciente_id').val(),
            diagnostico:    $('#diagnostico').val(),
            fecha:          $('#fecha').val(),
            profesional_id: $('#profesional_id').val()
        };
        $.getJSON('{{ url("/informe/plantillas") }}/' + plantillaId + '/preview', params, function(data) {
            document.getElementById('editor-content').innerHTML = data.contenido;
            syncEditor();
            document.getElementById('plantilla_documento_version_id').value = data.version_id;
        });
    });
});

/* ════════════════════════════════════════════════
   RECETA UPLOAD
════════════════════════════════════════════════ */
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
['dragenter','dragover'].forEach(e => recetaDrop.addEventListener(e, ev => {
    ev.preventDefault(); recetaDrop.classList.add('drag-over');
}));
['dragleave','drop'].forEach(e => recetaDrop.addEventListener(e, ev => {
    ev.preventDefault(); recetaDrop.classList.remove('drag-over');
    if (e === 'drop') {
        document.getElementById('receta-input').files = ev.dataTransfer.files;
        document.getElementById('receta-input').dispatchEvent(new Event('change'));
    }
}));

/* Sync antes de enviar */
document.getElementById('informe-form').addEventListener('submit', function() {
    syncEditor();
});
</script>
@endpush
