@extends('layouts.admin')
@section('content')

@push('styles')
<style>
    .cfg-wrap {
        display: flex; flex-direction: column; gap: 24px;
        animation: cfgFadeUp .35s ease both;
    }
    @@keyframes cfgFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── header ── */
    .cfg-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .cfg-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .cfg-header-left p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

    .cfg-btn-save {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 20px; border-radius: 10px;
        background: var(--accent, #1d4ed8); color: #fff;
        font-size: 13px; font-weight: 600; border: none; cursor: pointer;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
        transition: background .15s, transform .15s;
    }
    .cfg-btn-save:hover { background: var(--accent-hover, #1e40af); transform: translateY(-1px); }
    .cfg-btn-save svg { width: 15px; height: 15px; }

    /* ── two-column grid ── */
    .cfg-grid {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 24px;
        align-items: start;
    }
    @@media (max-width: 900px) { .cfg-grid { grid-template-columns: 1fr; } }

    /* ── section card ── */
    .cfg-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow);
        overflow: hidden; margin-bottom: 20px;
    }
    .cfg-card:last-child { margin-bottom: 0; }
    .cfg-card-head {
        display: flex; align-items: center; gap: 9px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--card-border, #e8edf2);
        font-size: 13px; font-weight: 600;
        color: var(--text-primary, #0f172a);
    }
    .cfg-card-head svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); flex-shrink: 0; }
    .cfg-card-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; }

    /* ── row inside card ── */
    .cfg-row { display: grid; gap: 16px; }
    .cfg-row.cols-2 { grid-template-columns: 1fr 1fr; }
    .cfg-row.cols-8-4 { grid-template-columns: 2fr 1fr; }
    @@media (max-width: 600px) {
        .cfg-row.cols-2, .cfg-row.cols-8-4 { grid-template-columns: 1fr; }
    }

    /* ── field ── */
    .cfg-field { display: flex; flex-direction: column; gap: 5px; }
    .cfg-label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .06em; color: var(--text-muted, #94a3b8);
    }
    .cfg-label .req { color: #e11d48; }
    .cfg-hint { font-size: 11px; color: var(--text-muted, #94a3b8); margin: 0; }

    .cfg-input, .cfg-textarea, .cfg-select {
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: 8px; padding: 9px 12px;
        font-size: 13px; color: var(--text-primary, #0f172a);
        background: var(--body-bg, #f8fafc);
        font-family: var(--font-sans, inherit);
        width: 100%; outline: none; transition: border-color .15s, box-shadow .15s;
    }
    .cfg-input:focus, .cfg-textarea:focus, .cfg-select:focus {
        border-color: var(--accent, #1d4ed8);
        box-shadow: 0 0 0 3px rgba(29,78,216,.08);
    }
    .cfg-input.err, .cfg-textarea.err, .cfg-select.err { border-color: #e11d48; }
    html.dark .cfg-input, html.dark .cfg-textarea, html.dark .cfg-select {
        background: #0f172a; color: #f1f5f9; border-color: #1e293b;
    }
    .cfg-textarea { resize: vertical; line-height: 1.6; }
    .cfg-select { appearance: none; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 12px center;
        padding-right: 32px;
    }
    .cfg-err { font-size: 11px; color: #e11d48; margin: 2px 0 0; }

    /* ── file upload ── */
    .cfg-file-area {
        border: 2px dashed var(--card-border, #e8edf2);
        border-radius: 10px; padding: 14px 16px;
        display: flex; flex-direction: column; gap: 8px;
        align-items: center; transition: border-color .15s;
    }
    .cfg-file-area:hover { border-color: var(--accent, #1d4ed8); }
    .cfg-file-area input[type="file"] { display: none; }
    .cfg-file-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 8px;
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        color: var(--text-secondary, #64748b);
        font-size: 12px; font-weight: 600; cursor: pointer;
        transition: background .12s;
    }
    .cfg-file-btn:hover { background: var(--body-bg, #f8fafc); }
    .cfg-file-btn svg { width: 13px; height: 13px; }
    .cfg-file-name { font-size: 11px; color: var(--text-muted, #94a3b8); text-align: center; }

    .cfg-preview-wrap { text-align: center; }
    .cfg-preview-img {
        max-height: 64px; max-width: 100%; object-fit: contain;
        border-radius: 8px; padding: 8px;
        background: #1e293b;
    }
    .cfg-preview-img.favicon { background: transparent; max-width: 64px; }
    .cfg-preview-label { font-size: 10px; color: var(--text-muted, #94a3b8); margin-top: 4px; }
</style>
@endpush

<form action="{{ route('admin.configuracion.update') }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="cfg-wrap">

    @if(app(\App\Services\License\LicenseClientInterface::class)->isDemo())
    <div style="background:#fef3c7; border:1px solid #f59e0b; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:13px; color:#92400e; display:flex; align-items:center; gap:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        <span><strong>Modo Demo:</strong> Esta sección es de solo lectura. Los cambios no se guardarán.</span>
    </div>
    @endif

    {{-- ── Header ── --}}
    <div class="cfg-header">
        <div class="cfg-header-left">
            <h1>Configuración del Sistema</h1>
            <p>Identidad, datos institucionales y preferencias</p>
        </div>
        <button type="submit" class="cfg-btn-save">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 21H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-8H7v8M7 3v5h8"/>
            </svg>
            Guardar cambios
        </button>
    </div>

    {{-- ── Grid ── --}}
    <div class="cfg-grid">

        {{-- ════ COLUMNA IZQUIERDA ════ --}}
        <div>

            {{-- Identidad del sistema --}}
            <div class="cfg-card">
                <div class="cfg-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                    </svg>
                    Identidad del Sistema
                </div>
                <div class="cfg-card-body">

                    <div class="cfg-field">
                        <label class="cfg-label" for="nombre_sistema">
                            Nombre del sistema <span class="req">*</span>
                        </label>
                        <input type="text" name="nombre_sistema" id="nombre_sistema"
                               class="cfg-input {{ $errors->has('nombre_sistema') ? 'err' : '' }}"
                               value="{{ old('nombre_sistema', $config->nombre_sistema) }}"
                               placeholder="Ej: Historias Clínicas — Centro Médico" required>
                        <span class="cfg-hint">Aparece en el título del navegador y en el encabezado.</span>
                        @error('nombre_sistema') <span class="cfg-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="cfg-row cols-2">
                        {{-- Logo --}}
                        <div class="cfg-field">
                            <label class="cfg-label">Logo del sistema</label>
                            <div class="cfg-file-area" onclick="document.getElementById('logo').click()">
                                <input type="file" id="logo" name="logo" accept="image/*"
                                       onchange="previewImagen(this,'preview-logo');actualizarNombre(this,'nombre-logo')">
                                <button type="button" class="cfg-file-btn">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Elegir imagen
                                </button>
                                <span class="cfg-file-name" id="nombre-logo">PNG, JPG o SVG · máx. 2 MB</span>
                            </div>
                            @error('logo') <span class="cfg-err">{{ $message }}</span> @enderror
                            <div class="cfg-preview-wrap">
                                <img id="preview-logo" src="{{ $config->logo_url }}" alt="Logo" class="cfg-preview-img">
                                <p class="cfg-preview-label">Vista previa (fondo oscuro)</p>
                            </div>
                        </div>

                        {{-- Favicon --}}
                        <div class="cfg-field">
                            <label class="cfg-label">Favicon</label>
                            <div class="cfg-file-area" onclick="document.getElementById('favicon').click()">
                                <input type="file" id="favicon" name="favicon" accept="image/*"
                                       onchange="previewImagen(this,'preview-favicon');actualizarNombre(this,'nombre-favicon')">
                                <button type="button" class="cfg-file-btn">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Elegir imagen
                                </button>
                                <span class="cfg-file-name" id="nombre-favicon">PNG o ICO · máx. 512 KB</span>
                            </div>
                            @error('favicon') <span class="cfg-err">{{ $message }}</span> @enderror
                            <div class="cfg-preview-wrap">
                                <img id="preview-favicon" src="{{ $config->favicon_url }}" alt="Favicon" class="cfg-preview-img favicon">
                                <p class="cfg-preview-label">Vista previa</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Información institucional --}}
            <div class="cfg-card">
                <div class="cfg-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Información de la Institución
                </div>
                <div class="cfg-card-body">

                    <div class="cfg-row cols-8-4">
                        <div class="cfg-field">
                            <label class="cfg-label" for="nombre_institucion">Nombre de la institución</label>
                            <input type="text" name="nombre_institucion" id="nombre_institucion"
                                   class="cfg-input {{ $errors->has('nombre_institucion') ? 'err' : '' }}"
                                   value="{{ old('nombre_institucion', $config->nombre_institucion) }}"
                                   placeholder="Ej: Centro de Salud San Martín">
                            @error('nombre_institucion') <span class="cfg-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="cfg-field">
                            <label class="cfg-label" for="tipo_institucion">Tipo</label>
                            <select name="tipo_institucion" id="tipo_institucion"
                                    class="cfg-select {{ $errors->has('tipo_institucion') ? 'err' : '' }}">
                                <option value="">— Seleccionar —</option>
                                @foreach(['Centro Terapéutico','Centro de Rehabilitación','Clínica','Consultorio','Hospital','Fundación','Otro'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo_institucion', $config->tipo_institucion) === $tipo ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_institucion') <span class="cfg-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="cfg-field">
                        <label class="cfg-label" for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="2"
                                  class="cfg-textarea {{ $errors->has('descripcion') ? 'err' : '' }}"
                                  placeholder="Breve descripción de la institución...">{{ old('descripcion', $config->descripcion) }}</textarea>
                        @error('descripcion') <span class="cfg-err">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

        </div>

        {{-- ════ COLUMNA DERECHA ════ --}}
        <div>

            {{-- Datos de contacto --}}
            <div class="cfg-card">
                <div class="cfg-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                    Datos de Contacto
                </div>
                <div class="cfg-card-body">

                    <div class="cfg-field">
                        <label class="cfg-label" for="direccion">Dirección</label>
                        <input type="text" name="direccion" id="direccion"
                               class="cfg-input {{ $errors->has('direccion') ? 'err' : '' }}"
                               value="{{ old('direccion', $config->direccion) }}"
                               placeholder="Calle, número, piso...">
                        @error('direccion') <span class="cfg-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="cfg-row cols-2">
                        <div class="cfg-field">
                            <label class="cfg-label" for="localidad">Localidad</label>
                            <input type="text" name="localidad" id="localidad"
                                   class="cfg-input {{ $errors->has('localidad') ? 'err' : '' }}"
                                   value="{{ old('localidad', $config->localidad) }}"
                                   placeholder="Localidad">
                            @error('localidad') <span class="cfg-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="cfg-field">
                            <label class="cfg-label" for="provincia">Provincia</label>
                            <input type="text" name="provincia" id="provincia"
                                   class="cfg-input {{ $errors->has('provincia') ? 'err' : '' }}"
                                   value="{{ old('provincia', $config->provincia) }}"
                                   placeholder="Provincia">
                            @error('provincia') <span class="cfg-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="cfg-row cols-2">
                        <div class="cfg-field">
                            <label class="cfg-label" for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono"
                                   class="cfg-input {{ $errors->has('telefono') ? 'err' : '' }}"
                                   value="{{ old('telefono', $config->telefono) }}"
                                   placeholder="(011) 4444-5555">
                            @error('telefono') <span class="cfg-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="cfg-field">
                            <label class="cfg-label" for="cuit">CUIT</label>
                            <input type="text" name="cuit" id="cuit"
                                   class="cfg-input {{ $errors->has('cuit') ? 'err' : '' }}"
                                   value="{{ old('cuit', $config->cuit) }}"
                                   placeholder="30-12345678-9">
                            @error('cuit') <span class="cfg-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="cfg-field">
                        <label class="cfg-label" for="email_contacto">Email de contacto</label>
                        <input type="email" name="email_contacto" id="email_contacto"
                               class="cfg-input {{ $errors->has('email_contacto') ? 'err' : '' }}"
                               value="{{ old('email_contacto', $config->email_contacto) }}"
                               placeholder="info@institucion.org">
                        @error('email_contacto') <span class="cfg-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="cfg-field">
                        <label class="cfg-label" for="website">Sitio web</label>
                        <input type="url" name="website" id="website"
                               class="cfg-input {{ $errors->has('website') ? 'err' : '' }}"
                               value="{{ old('website', $config->website) }}"
                               placeholder="https://www.institucion.org">
                        @error('website') <span class="cfg-err">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            {{-- PDFs --}}
            <div class="cfg-card">
                <div class="cfg-card-head">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Configuración de PDFs
                </div>
                <div class="cfg-card-body">
                    <div class="cfg-field">
                        <label class="cfg-label" for="pie_pdf">Pie de página en PDFs</label>
                        <textarea name="pie_pdf" id="pie_pdf" rows="3"
                                  class="cfg-textarea {{ $errors->has('pie_pdf') ? 'err' : '' }}"
                                  placeholder="Ej: Centro Médico San Martín | Tel: (011) 4444-5555 | info@clinica.com">{{ old('pie_pdf', $config->pie_pdf) }}</textarea>
                        <span class="cfg-hint">Aparece al pie de cada informe y ficha generada en PDF.</span>
                        @error('pie_pdf') <span class="cfg-err">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
function previewImagen(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function actualizarNombre(input, labelId) {
    const el = document.getElementById(labelId);
    if (el && input.files && input.files[0]) {
        el.textContent = input.files[0].name;
    }
}
</script>
@endpush
