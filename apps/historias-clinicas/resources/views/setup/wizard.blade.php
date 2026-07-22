<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuración inicial — {{ $config->nombre_sistema ?? 'Historias Clínicas' }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { background: #0f172a; font-family: 'Segoe UI', system-ui, sans-serif; min-height: 100vh; display: flex; align-items: flex-start; justify-content: center; padding: 32px 16px 48px; margin: 0; }
        .wizard-card { background: #fff; border-radius: 20px; width: 100%; max-width: 640px; overflow: hidden; box-shadow: 0 25px 80px rgba(0,0,0,.4); }
        .wizard-header { background: linear-gradient(135deg, #0f766e, #0d9488); padding: 32px 40px 24px; color: #fff; }
        .wizard-header h1 { font-size: 22px; font-weight: 700; margin: 0 0 6px; }
        .wizard-header p { font-size: 14px; opacity: .85; margin: 0; }
        .wizard-body { padding: 32px 40px 40px; }
        .error-banner { background: #fef2f2; border-bottom: 1px solid #fecaca; padding: 14px 40px; }
        .error-banner p { color: #dc2626; font-size: 13px; margin: 2px 0; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin: 28px 0 14px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9; }
        .section-title:first-child { margin-top: 0; }
        .form-group { margin-bottom: 18px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .required-mark { color: #ef4444; margin-left: 2px; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 9px 13px; border: 1.5px solid #e5e7eb; border-radius: 8px;
            font-size: 14px; font-family: inherit; outline: none; transition: border-color .15s;
            background: #fff; color: #111827;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13,148,136,.1); }
        .form-textarea { resize: vertical; min-height: 70px; }
        .hint { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        .field-err { font-size: 12px; color: #dc2626; margin-top: 4px; display: block; }

        /* File upload */
        .file-upload-area {
            border: 1.5px dashed #d1d5db; border-radius: 8px; padding: 14px 16px;
            display: flex; align-items: center; gap: 12px; background: #f9fafb; cursor: pointer;
            transition: border-color .15s;
        }
        .file-upload-area:hover { border-color: #0d9488; }
        .file-upload-area input[type=file] { display: none; }
        .file-upload-btn {
            background: #fff; border: 1.5px solid #d1d5db; border-radius: 6px;
            padding: 5px 12px; font-size: 13px; font-weight: 600; color: #374151;
            cursor: pointer; white-space: nowrap; flex-shrink: 0; transition: border-color .15s;
        }
        .file-upload-btn:hover { border-color: #0d9488; color: #0f766e; }
        .file-upload-label { font-size: 12px; color: #6b7280; line-height: 1.4; }
        .preview-box { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
        .preview-box img { max-height: 40px; max-width: 80px; border-radius: 4px; object-fit: contain; border: 1px solid #e5e7eb; }
        .preview-box span { font-size: 12px; color: #6b7280; }

        .btn-submit {
            background: #0f766e; color: #fff; border: none; padding: 13px 32px;
            border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer;
            width: 100%; margin-top: 28px; font-family: inherit; transition: background .2s;
        }
        .btn-submit:hover { background: #0d6158; }

        @@media (max-width: 520px) {
            .form-row { grid-template-columns: 1fr; }
            .wizard-body { padding: 24px 20px 32px; }
            .wizard-header { padding: 24px 20px 20px; }
        }
    </style>
</head>
<body>
<div class="wizard-card">

    <div class="wizard-header">
        <h1>Configuración inicial del sistema</h1>
        <p>Completá los datos de tu institución para comenzar. Podés modificarlos después desde Configuración.</p>
    </div>

    @if($errors->any())
    <div class="error-banner">
        @foreach($errors->all() as $error)
            <p>• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <div class="wizard-body">
        <form action="{{ route('setup.save') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- ── IDENTIDAD DEL SISTEMA ── --}}
            <p class="section-title">Identidad del Sistema</p>

            <div class="form-group">
                <label class="form-label" for="nombre_sistema">Nombre del sistema<span class="required-mark">*</span></label>
                <input type="text" name="nombre_sistema" id="nombre_sistema" class="form-input"
                       value="{{ old('nombre_sistema', $config->nombre_sistema ?? 'Historias Clínicas') }}" required>
                <span class="hint">Aparece en el título del navegador y en el encabezado.</span>
                @error('nombre_sistema') <span class="field-err">{{ $message }}</span> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Logo del sistema</label>
                    <div class="file-upload-area" onclick="document.getElementById('logo').click()">
                        <input type="file" name="logo" id="logo" accept="image/png,image/jpg,image/jpeg,image/svg+xml,image/webp"
                               onchange="previewImg(this,'logo-preview','logo-preview-wrap')">
                        <button type="button" class="file-upload-btn">Elegir imagen</button>
                        <span class="file-upload-label">PNG, JPG o SVG<br>máx. 2 MB</span>
                    </div>
                    @if($config->logo)
                    <div class="preview-box">
                        <img src="{{ Storage::url($config->logo) }}" id="logo-preview" alt="Logo">
                        <span>Logo actual</span>
                    </div>
                    @else
                    <div class="preview-box" id="logo-preview-wrap" style="display:none">
                        <img src="" id="logo-preview" alt="Logo">
                        <span>Seleccionado</span>
                    </div>
                    @endif
                    @error('logo') <span class="field-err">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Favicon</label>
                    <div class="file-upload-area" onclick="document.getElementById('favicon').click()">
                        <input type="file" name="favicon" id="favicon" accept="image/png,image/x-icon,image/jpeg"
                               onchange="previewImg(this,'favicon-preview','favicon-preview-wrap')">
                        <button type="button" class="file-upload-btn">Elegir imagen</button>
                        <span class="file-upload-label">PNG o ICO<br>máx. 512 KB</span>
                    </div>
                    @if($config->favicon)
                    <div class="preview-box">
                        <img src="{{ Storage::url($config->favicon) }}" id="favicon-preview" alt="Favicon">
                        <span>Favicon actual</span>
                    </div>
                    @else
                    <div class="preview-box" id="favicon-preview-wrap" style="display:none">
                        <img src="" id="favicon-preview" alt="Favicon">
                        <span>Seleccionado</span>
                    </div>
                    @endif
                    @error('favicon') <span class="field-err">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ── INFORMACIÓN DE LA INSTITUCIÓN ── --}}
            <p class="section-title">Información de la Institución</p>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="nombre_institucion">Nombre de la institución<span class="required-mark">*</span></label>
                    <input type="text" name="nombre_institucion" id="nombre_institucion" class="form-input"
                           value="{{ old('nombre_institucion', $config->nombre_institucion) }}" required>
                    @error('nombre_institucion') <span class="field-err">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="tipo_institucion">Tipo</label>
                    <select name="tipo_institucion" id="tipo_institucion" class="form-select">
                        <option value="">— Seleccionar —</option>
                        @foreach(['Centro Terapéutico','Centro de Rehabilitación','Clínica','Consultorio','Hospital','Fundación','Otro'] as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipo_institucion', $config->tipo_institucion) === $tipo ? 'selected' : '' }}>
                                {{ $tipo }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_institucion') <span class="field-err">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-textarea"
                          placeholder="Breve descripción de la institución...">{{ old('descripcion', $config->descripcion) }}</textarea>
                @error('descripcion') <span class="field-err">{{ $message }}</span> @enderror
            </div>

            {{-- ── DATOS DE CONTACTO ── --}}
            <p class="section-title">Datos de Contacto</p>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-input"
                           value="{{ old('direccion', $config->direccion) }}">
                    @error('direccion') <span class="field-err">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="localidad">Localidad</label>
                    <input type="text" name="localidad" id="localidad" class="form-input"
                           value="{{ old('localidad', $config->localidad) }}">
                    @error('localidad') <span class="field-err">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="provincia">Provincia</label>
                    <input type="text" name="provincia" id="provincia" class="form-input"
                           value="{{ old('provincia', $config->provincia ?? 'Buenos Aires') }}">
                    @error('provincia') <span class="field-err">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-input"
                           value="{{ old('telefono', $config->telefono) }}">
                    @error('telefono') <span class="field-err">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="cuit">CUIT</label>
                    <input type="text" name="cuit" id="cuit" class="form-input"
                           placeholder="30-12345678-9"
                           value="{{ old('cuit', $config->cuit) }}">
                    @error('cuit') <span class="field-err">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email_contacto">Email de contacto</label>
                    <input type="email" name="email_contacto" id="email_contacto" class="form-input"
                           value="{{ old('email_contacto', $config->email_contacto) }}">
                    @error('email_contacto') <span class="field-err">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="website">Sitio web</label>
                <input type="url" name="website" id="website" class="form-input"
                       placeholder="https://www.institucion.org"
                       value="{{ old('website', $config->website) }}">
                @error('website') <span class="field-err">{{ $message }}</span> @enderror
            </div>

            {{-- ── CONFIGURACIÓN DE PDFs ── --}}
            <p class="section-title">Configuración de PDFs</p>

            <div class="form-group">
                <label class="form-label" for="pie_pdf">Pie de página en PDFs</label>
                <textarea name="pie_pdf" id="pie_pdf" class="form-textarea" rows="2"
                          placeholder="Ej: Centro Médico San Martín | Tel: (011) 4444-5555 | info@clinica.com">{{ old('pie_pdf', $config->pie_pdf) }}</textarea>
                <span class="hint">Aparece al pie de cada informe y ficha generada en PDF.</span>
                @error('pie_pdf') <span class="field-err">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-submit">Guardar y comenzar →</button>
        </form>
    </div>
</div>

<script>
function previewImg(input, imgId, wrapId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById(imgId);
        if (img) img.src = e.target.result;
        const wrap = document.getElementById(wrapId);
        if (wrap) wrap.style.display = 'flex';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
</body>
</html>
