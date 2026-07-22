@extends('layouts.admin')
@section('content')

@push('styles')
<style>
/* ── Layout ── */
.tcf-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    align-items: start;
}
@media(max-width:960px) { .tcf-layout { grid-template-columns: 1fr; } }

/* ── Editor panel ── */
.tcf-editor { display: flex; flex-direction: column; gap: 16px; }

.tcf-back-row { display: flex; align-items: center; gap: 10px; }
.tcf-back-row a {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; border-radius: 8px;
    border: 1.5px solid var(--border); background: var(--card);
    font-size: 12px; font-weight: 600; color: var(--t2); text-decoration: none; transition: all .12s;
}
.tcf-back-row a:hover { border-color: #1a3561; color: #1a3561; }
.tcf-back-row a svg  { width: 13px; height: 13px; }
.tcf-back-row h1 { font-size: 20px; font-weight: 700; color: var(--t1); margin: 0; }

.tcf-card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); overflow: hidden; }
.tcf-card-hdr {
    background: #1a3561; color: #fff;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
    padding: 10px 18px; display: flex; align-items: center; justify-content: space-between; gap: 8px;
}
.tcf-card-hdr-left { display: flex; align-items: center; gap: 7px; }
.tcf-card-hdr svg  { width: 14px; height: 14px; opacity: .85; }
.tcf-card-body { padding: 18px 20px; display: flex; flex-direction: column; gap: 14px; }

.tcf-field label { display: block; font-size: 12px; font-weight: 600; color: var(--t2); margin-bottom: 5px; }
.tcf-field input[type=text] {
    width: 100%; padding: 9px 12px; border-radius: 9px;
    border: 1.5px solid var(--border); background: var(--card); color: var(--t1);
    font-size: 13px; font-family: inherit; transition: border-color .12s;
}
.tcf-field input[type=text]:focus { outline: none; border-color: #1a3561; }
.tcf-field textarea {
    width: 100%; padding: 9px 12px; border-radius: 9px;
    border: 1.5px solid var(--border); background: var(--card); color: var(--t1);
    font-size: 13px; font-family: inherit; resize: vertical; transition: border-color .12s;
}
.tcf-field textarea:focus { outline: none; border-color: #1a3561; }
.tcf-field .error { font-size: 12px; color: #be123c; margin-top: 3px; }

.tcf-checks { display: flex; flex-wrap: wrap; gap: 16px; }
.tcf-checks label { display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer; }
.tcf-checks input { accent-color: #1a3561; width: 15px; height: 15px; }

/* ── Rich text toolbar ── */
.fmt-toolbar {
    display: flex; gap: 2px; padding: 6px 8px;
    background: var(--body-bg, #f8fafc);
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}
.fmt-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 28px; border: none; border-radius: 5px;
    background: transparent; cursor: pointer; color: var(--t2);
    font-size: 13px; font-weight: 700; transition: background .12s;
}
.fmt-btn:hover { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); }
.fmt-sep { width: 1px; background: var(--border); margin: 2px 4px; align-self: stretch; }
.fmt-select {
    height: 28px; font-size: 11px; padding: 0 4px; cursor: pointer; font-weight: normal;
    border: none; border-radius: 5px; background: transparent; color: var(--t2);
}
.fmt-select:hover { background: var(--accent-light, #eff6ff); }

/* ── Page editor ── */
.tcf-page-card { border-radius: 12px; border: 1.5px solid var(--border); overflow: hidden; background: var(--card); }
.tcf-page-card + .tcf-page-card { margin-top: 12px; }
.tcf-page-hdr {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 14px; background: #1a3561; color: #fff;
    font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
}
.tcf-page-remove {
    width: 22px; height: 22px; border-radius: 5px; border: none;
    background: rgba(255,255,255,.15); color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 13px;
    transition: background .12s;
}
.tcf-page-remove:hover { background: rgba(255,255,255,.3); }
.page-editor {
    min-height: 220px; padding: 14px 16px;
    font-size: 13px; font-family: inherit; line-height: 1.7;
    color: var(--t1); background: var(--card); outline: none;
    overflow-y: auto;
}
.page-editor:empty::before {
    content: attr(data-placeholder);
    color: var(--t3, #94a3b8); pointer-events: none;
}
.page-editor:focus { outline: none; }

.tcf-add-page-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 11px; border-radius: 10px;
    border: 1.5px dashed var(--border); background: transparent;
    color: var(--t2); font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all .15s;
}
.tcf-add-page-btn:hover { border-color: #1a3561; color: #1a3561; background: #f0f5ff; }

.tcf-actions { display: flex; gap: 10px; }
.tcf-btn {
    padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 600;
    cursor: pointer; border: none; transition: background .15s, transform .12s;
    text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
}
.tcf-btn:hover { transform: translateY(-1px); }
.tcf-btn.primary { background: #1a3561; color: #fff; box-shadow: 0 2px 8px rgba(26,53,97,.2); }
.tcf-btn.primary:hover { background: #142a4f; }
.tcf-btn.ghost { background: var(--card); color: var(--t2); border: 1.5px solid var(--border); }
.tcf-btn.ghost:hover { color: var(--t1); }

/* ── Preview panel ── */
.tcf-preview { position: sticky; top: 80px; }
.tcf-prev-header {
    display: flex; align-items: center; gap: 8px; margin-bottom: 12px;
}
.tcf-prev-header span { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--t2); }
.tcf-prev-badge { padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #eff6ff; color: #1d4ed8; }

.tcf-prev-frame {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    box-shadow: 0 2px 20px rgba(0,0,0,.08); max-height: 82vh; overflow-y: auto;
}
.tcf-prev-body { padding: 24px 28px; font-family: Georgia, serif; font-size: 12.5px; line-height: 1.75; color: #1e293b; }

.tcf-prev-inst { text-align: center; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #64748b; margin-bottom: 2px; }
.tcf-prev-doc-title { text-align: center; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 16px; word-break: break-word; }
.tcf-prev-patient {
    display: grid; grid-template-columns: 1fr 1fr; gap: 3px 12px;
    padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 7px;
    margin-bottom: 16px; font-size: 11px;
}
.tcf-prev-patient .lbl { color: #64748b; font-size: 9px; text-transform: uppercase; letter-spacing: .04em; }
.tcf-prev-patient .val { font-weight: 600; }
.tcf-prev-pg-content p  { margin: 0 0 8px; }
.tcf-prev-pg-content ol,
.tcf-prev-pg-content ul { margin: 0 0 8px; padding-left: 20px; }
.tcf-prev-pg-content li { margin-bottom: 3px; }
.tcf-prev-pg-content strong { font-weight: 700; }

.tcf-prev-sig {
    margin-top: 18px; padding-top: 12px; border-top: 1px solid #cbd5e1;
    display: grid; gap: 12px;
}
.tcf-prev-sig-col { text-align: center; }
.tcf-prev-sig-line { border-top: 1px solid #334155; width: 75%; margin: 0 auto 4px; padding-top: 4px; }
.tcf-prev-sig-label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }

.tcf-prev-pagesep {
    text-align: center; font-size: 9px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; color: #94a3b8; margin: 20px 0 14px; position: relative;
    border-top: 1px dashed #e2e8f0; padding-top: 14px;
}
.tcf-prev-empty { text-align: center; padding: 32px 16px; color: #94a3b8; font-size: 12px; font-style: italic; font-family: sans-serif; }
</style>
@endpush

<form method="POST"
      action="{{ isset($tipo->id) ? route('admin.tipos-consentimiento.update', $tipo) : route('admin.tipos-consentimiento.store') }}"
      id="tcf-form">
@csrf
@if(isset($tipo->id)) @method('PUT') @endif

<div class="tcf-layout">

    {{-- ══ LEFT: Editor ══ --}}
    <div class="tcf-editor">

        <div class="tcf-back-row">
            <a href="{{ route('admin.tipos-consentimiento.index') }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
            <h1>{{ isset($tipo->id) ? 'Editar plantilla' : 'Nueva plantilla' }}</h1>
        </div>

        {{-- Datos generales --}}
        <div class="tcf-card">
            <div class="tcf-card-hdr">
                <div class="tcf-card-hdr-left">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Datos generales
                </div>
            </div>
            <div class="tcf-card-body">
                <div class="tcf-field">
                    <label>Nombre <span style="color:#be123c">*</span></label>
                    <input type="text" id="inp-nombre" name="nombre"
                        value="{{ old('nombre', $tipo->nombre) }}"
                        placeholder="Ej: Consentimiento informado para tratamiento"
                        required>
                    @error('nombre')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="tcf-field">
                    <label>Descripción interna</label>
                    <textarea name="descripcion" rows="2" placeholder="Breve descripción para uso interno">{{ old('descripcion', $tipo->descripcion) }}</textarea>
                </div>
                <div class="tcf-checks">
                    <label>
                        <input type="checkbox" name="requiere_firma_profesional" id="chk-prof" value="1"
                            {{ old('requiere_firma_profesional', $tipo->requiere_firma_profesional ?? false) ? 'checked' : '' }}>
                        Requiere firma del profesional / director
                    </label>
                    <label>
                        <input type="checkbox" name="activo" value="1"
                            {{ old('activo', $tipo->activo ?? true) ? 'checked' : '' }}>
                        Activo
                    </label>
                </div>
            </div>
        </div>

        {{-- Pages --}}
        <div id="pages-container"></div>

        <button type="button" class="tcf-add-page-btn" id="btn-add-page">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Agregar página
        </button>

        <div class="tcf-actions">
            <button type="submit" class="tcf-btn primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ isset($tipo->id) ? 'Guardar cambios' : 'Crear plantilla' }}
            </button>
            <a href="{{ route('admin.tipos-consentimiento.index') }}" class="tcf-btn ghost">Cancelar</a>
        </div>

    </div>

    {{-- ══ RIGHT: Preview ══ --}}
    <div class="tcf-preview">
        <div class="tcf-prev-header">
            <span>Vista previa</span>
            <span class="tcf-prev-badge">En tiempo real</span>
        </div>
        <div class="tcf-prev-frame">
            <div class="tcf-prev-body">
                <div id="prev-pages">
                    <div class="tcf-prev-empty">Las páginas aparecerán aquí...</div>
                </div>
            </div>
        </div>
    </div>

</div>
</form>

@push('scripts')
<script>
(function () {
    var activeEditor = null;
    var pageCounter  = 0;
    var today        = '{{ now()->format("d/m/Y") }}';

    /* ── Toolbar commands ── */
    window.execFmt = function (cmd) {
        if (!activeEditor) return;
        activeEditor.focus();
        document.execCommand(cmd, false, null);
        updatePreview();
    };
    window.execFmtVal = function (cmd, val) {
        if (!activeEditor || !val) return;
        activeEditor.focus();
        document.execCommand(cmd, false, val);
        updatePreview();
    };

    /* ── Build one page card HTML ── */
    function toolbarHTML() {
        return '<div class="fmt-toolbar">' +
            '<button type="button" class="fmt-btn" onmousedown="event.preventDefault()" onclick="execFmt(\'bold\')" title="Negrita"><b>B</b></button>' +
            '<button type="button" class="fmt-btn" onmousedown="event.preventDefault()" onclick="execFmt(\'italic\')" title="Cursiva"><i>I</i></button>' +
            '<button type="button" class="fmt-btn" onmousedown="event.preventDefault()" onclick="execFmt(\'underline\')" title="Subrayado"><u>S</u></button>' +
            '<button type="button" class="fmt-btn" onmousedown="event.preventDefault()" onclick="execFmt(\'strikeThrough\')" title="Tachado" style="text-decoration:line-through;">T</button>' +
            '<div class="fmt-sep"></div>' +
            '<button type="button" class="fmt-btn" onmousedown="event.preventDefault()" onclick="execFmt(\'insertOrderedList\')" title="Lista numerada"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg></button>' +
            '<button type="button" class="fmt-btn" onmousedown="event.preventDefault()" onclick="execFmt(\'insertUnorderedList\')" title="Lista con viñetas"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></button>' +
            '<div class="fmt-sep"></div>' +
            '<button type="button" class="fmt-btn" onmousedown="event.preventDefault()" onclick="execFmt(\'justifyLeft\')" title="Izquierda"><svg fill="currentColor" viewBox="0 0 16 16" style="width:13px;height:13px;"><path d="M2 12.5a.5.5 0 010-1h7a.5.5 0 010 1H2zm0-3a.5.5 0 010-1h11a.5.5 0 010 1H2zm0-3a.5.5 0 010-1h7a.5.5 0 010 1H2zm0-3a.5.5 0 010-1h11a.5.5 0 010 1H2z"/></svg></button>' +
            '<button type="button" class="fmt-btn" onmousedown="event.preventDefault()" onclick="execFmt(\'justifyCenter\')" title="Centrar"><svg fill="currentColor" viewBox="0 0 16 16" style="width:13px;height:13px;"><path d="M4 12.5a.5.5 0 010-1h8a.5.5 0 010 1H4zm-2-3a.5.5 0 010-1h12a.5.5 0 010 1H2zm2-3a.5.5 0 010-1h8a.5.5 0 010 1H4zm-2-3a.5.5 0 010-1h12a.5.5 0 010 1H2z"/></svg></button>' +
            '<div class="fmt-sep"></div>' +
            '<select class="fmt-select" onmousedown="if(document.activeElement!==activeEditor) event.stopPropagation()" onchange="execFmtVal(\'formatBlock\', this.value); this.selectedIndex=0;">' +
                '<option value="">Estilo...</option>' +
                '<option value="h1">Título 1</option>' +
                '<option value="h2">Título 2</option>' +
                '<option value="h3">Título 3</option>' +
                '<option value="p">Párrafo</option>' +
            '</select>' +
            '<div class="fmt-sep"></div>' +
            '<button type="button" class="fmt-btn" onmousedown="event.preventDefault()" onclick="execFmt(\'removeFormat\')" title="Limpiar" style="width:auto;padding:0 7px;font-size:11px;font-weight:normal;">Limpiar</button>' +
        '</div>';
    }

    window.addPage = function (initialContent) {
        pageCounter++;
        var idx = pageCounter;
        var container = document.getElementById('pages-container');

        var wrap = document.createElement('div');
        wrap.className = 'tcf-page-card';
        wrap.dataset.pageId = idx;

        wrap.innerHTML =
            '<div class="tcf-page-hdr">' +
                '<span class="page-label">Página</span>' +
                '<button type="button" class="tcf-page-remove" onmousedown="event.preventDefault()" onclick="removePage(' + idx + ')" title="Eliminar página">✕</button>' +
            '</div>' +
            toolbarHTML() +
            '<div class="page-editor" contenteditable="true" ' +
                'data-placeholder="Ingresá el contenido de esta página..." ' +
                'onfocus="window.onEditorFocus(this)" ' +
                'onblur="window.onEditorBlur()" ' +
                'oninput="updatePreview()" ' +
                'onkeyup="updatePreview()" ' +
                'onmouseup="updatePreview()">' +
            '</div>';

        container.appendChild(wrap);
        relabelPages();

        if (initialContent) {
            wrap.querySelector('.page-editor').innerHTML = initialContent;
        }

        updatePreview();
        return wrap.querySelector('.page-editor');
    };

    window.removePage = function (pageId) {
        var cards = document.querySelectorAll('.tcf-page-card');
        if (cards.length <= 1) { return; }
        var card = document.querySelector('[data-page-id="' + pageId + '"]');
        if (card) {
            if (activeEditor && card.contains(activeEditor)) activeEditor = null;
            card.remove();
        }
        relabelPages();
        updatePreview();
    };

    function relabelPages() {
        var cards = document.querySelectorAll('.tcf-page-card');
        cards.forEach(function (card, i) {
            card.querySelector('.page-label').textContent = 'Página ' + (i + 1);
        });
        // Hide remove button when only one page remains
        var removeBtns = document.querySelectorAll('.tcf-page-remove');
        removeBtns.forEach(function (btn) {
            btn.style.opacity = cards.length <= 1 ? '0.3' : '1';
            btn.style.pointerEvents = cards.length <= 1 ? 'none' : 'auto';
        });
    }

    window.onEditorFocus = function (el) { activeEditor = el; };
    window.onEditorBlur  = function ()   { /* keep activeEditor for toolbar clicks */ };

    /* ── Live preview ── */
    window.updatePreview = function () {
        var editors  = document.querySelectorAll('.page-editor');
        var nombre   = document.getElementById('inp-nombre').value.trim() || 'Nombre de la plantilla';
        var reqProf  = document.getElementById('chk-prof').checked;
        var sigCols  = reqProf ? '1fr 1fr 1fr 1fr' : '1fr 1fr 1fr';

        var html = '';

        editors.forEach(function (ed, idx) {
            var content = ed.innerHTML.trim();
            var isFirst = idx === 0;

            if (!isFirst) {
                html += '<div class="tcf-prev-pagesep">Página ' + (idx + 1) + '</div>';
            }

            html += '<div class="tcf-prev-inst">Centro Médico / Clínica</div>';
            html += '<div class="tcf-prev-doc-title">' + escHtml(nombre) + '</div>';

            if (isFirst) {
                html += '<div class="tcf-prev-patient">' +
                    '<div><div class="lbl">Paciente</div><div class="val">Apellido, Nombre</div></div>' +
                    '<div><div class="lbl">DNI</div><div class="val">12.345.678</div></div>' +
                    '<div><div class="lbl">Fecha</div><div class="val">' + today + '</div></div>' +
                    '<div><div class="lbl">Institución</div><div class="val">Centro médico</div></div>' +
                '</div>';
            } else {
                html += '<div class="tcf-prev-patient" style="grid-template-columns:1fr 1fr;">' +
                    '<div><div class="lbl">Paciente</div><div class="val">Apellido, Nombre</div></div>' +
                    '<div><div class="lbl">DNI</div><div class="val">12.345.678</div></div>' +
                '</div>';
            }

            html += '<div class="tcf-prev-pg-content">' +
                (content || '<span style="color:#94a3b8;font-style:italic;font-family:sans-serif;font-size:12px;">Ingresá el contenido de esta página...</span>') +
            '</div>';

            html += '<div class="tcf-prev-sig" style="grid-template-columns:' + sigCols + ';">' +
                '<div class="tcf-prev-sig-col"><div class="tcf-prev-sig-line"></div><div class="tcf-prev-sig-label">Firma del paciente</div></div>' +
                '<div class="tcf-prev-sig-col"><div class="tcf-prev-sig-line"></div><div class="tcf-prev-sig-label">Aclaración / DNI</div></div>' +
                '<div class="tcf-prev-sig-col"><div class="tcf-prev-sig-line"></div><div class="tcf-prev-sig-label">Fecha y lugar</div></div>';

            if (reqProf) {
                html += '<div class="tcf-prev-sig-col"><div class="tcf-prev-sig-line"></div><div class="tcf-prev-sig-label">Firma del profesional</div></div>';
            }

            html += '</div>';
        });

        document.getElementById('prev-pages').innerHTML = html || '<div class="tcf-prev-empty">Agregá al menos una página.</div>';
    };

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── Checkbox listener ── */
    document.getElementById('chk-prof').addEventListener('change', updatePreview);
    document.getElementById('inp-nombre').addEventListener('input', updatePreview);

    /* ── Add page button ── */
    document.getElementById('btn-add-page').addEventListener('click', function () {
        addPage('');
        // Scroll to new page
        var cards = document.querySelectorAll('.tcf-page-card');
        if (cards.length) cards[cards.length - 1].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    /* ── Form submit: collect editors into hidden inputs ── */
    document.getElementById('tcf-form').addEventListener('submit', function () {
        document.querySelectorAll('input[name="paginas[]"]').forEach(function (el) { el.remove(); });
        document.querySelectorAll('.page-editor').forEach(function (ed) {
            var inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'paginas[]';
            inp.value = ed.innerHTML;
            document.getElementById('tcf-form').appendChild(inp);
        });
    });

    /* ── Initialize pages from existing data / old() ── */
    var initialPages = @json(
        old('paginas',
            isset($tipo->id) && !empty($tipo->contenido_paginas)
                ? $tipo->contenido_paginas
                : ['']
        )
    );

    if (!Array.isArray(initialPages) || initialPages.length === 0) {
        initialPages = [''];
    }

    initialPages.forEach(function (content) {
        addPage(content || '');
    });

    updatePreview();
})();
</script>
@endpush

@endsection
