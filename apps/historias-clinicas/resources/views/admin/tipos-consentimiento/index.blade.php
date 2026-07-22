@extends('layouts.admin')
@section('content')

@push('styles')
<style>
.tc-wrap { display:flex; flex-direction:column; gap:20px; }

.tc-header { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; }
.tc-header h1 { font-size:22px; font-weight:700; color:var(--t1); letter-spacing:-.02em; margin:0; }
.tc-header p  { font-size:13px; color:var(--t2); margin:4px 0 0; }

.tc-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:9px 18px; border-radius:10px;
    font-size:13px; font-weight:600; text-decoration:none;
    border:none; cursor:pointer; transition:background .15s, transform .12s;
    white-space:nowrap;
}
.tc-btn:hover { transform:translateY(-1px); text-decoration:none; }
.tc-btn svg   { width:15px; height:15px; }
.tc-btn.primary { background:#1a3561; color:#fff; box-shadow:0 2px 8px rgba(26,53,97,.2); }
.tc-btn.primary:hover { background:#142a4f; color:#fff; }
.tc-btn.ghost   { background:var(--card); color:var(--t2); border:1.5px solid var(--border); }
.tc-btn.ghost:hover { color:var(--t1); border-color:var(--t2); }
.tc-btn.danger  { background:#fff1f2; color:#be123c; border:1.5px solid #fecdd3; }
.tc-btn.danger:hover { background:#ffe4e6; }

.tc-alert {
    padding:10px 16px; border-radius:10px; font-size:13px; font-weight:500;
    background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;
    display:flex; align-items:center; gap:8px;
}

.tc-card { background:var(--card); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:var(--shadow); }

.tc-table { width:100%; border-collapse:collapse; }
.tc-table th {
    padding:10px 16px; font-size:11px; font-weight:700; text-transform:uppercase;
    letter-spacing:.06em; color:var(--t2); border-bottom:1px solid var(--border);
    text-align:left; white-space:nowrap;
}
.tc-table td { padding:14px 16px; border-bottom:1px solid var(--border); vertical-align:middle; }
.tc-table tr:last-child td { border-bottom:none; }
.tc-table tr:hover td { background:rgba(0,0,0,.02); }
.tc-name { font-weight:600; color:var(--t1); font-size:14px; }
.tc-desc { font-size:12px; color:var(--t2); margin-top:2px; }

.tc-badge {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 9px; border-radius:20px; font-size:11px; font-weight:600;
}
.tc-badge.success { background:#f0fdf4; color:#16a34a; }
.tc-badge.muted   { background:var(--body,#f8fafc); color:var(--t3,#94a3b8); border:1px solid var(--border); }
.tc-badge.info    { background:#eff6ff; color:#1d4ed8; }

.tc-actions { display:flex; align-items:center; gap:6px; justify-content:flex-end; }
.tc-act-btn {
    padding:5px 12px; border-radius:8px; font-size:12px; font-weight:600;
    cursor:pointer; border:1.5px solid var(--border); background:var(--card);
    color:var(--t2); text-decoration:none; transition:all .12s;
    display:inline-flex; align-items:center; gap:4px;
}
.tc-act-btn:hover { color:var(--t1); border-color:var(--t2); text-decoration:none; }
.tc-act-btn.danger { color:#be123c; border-color:#fecdd3; background:#fff1f2; }
.tc-act-btn.danger:hover { background:#ffe4e6; }

.tc-empty { text-align:center; padding:48px 24px; color:var(--t3,#94a3b8); }
.tc-empty svg { width:40px; height:40px; margin:0 auto 12px; display:block; opacity:.4; }
.tc-empty p { margin:4px 0 0; font-size:13px; }

/* Modal */
.tc-modal-overlay {
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,.5); backdrop-filter:blur(4px);
    align-items:center; justify-content:center; padding:20px;
}
.tc-modal-overlay.open { display:flex; }
.tc-modal-box {
    background:#fff; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.25);
    width:100%; max-width:720px; max-height:90vh;
    display:flex; flex-direction:column; overflow:hidden;
}
.tc-modal-head {
    padding:18px 22px; border-bottom:1px solid #e2e8f0;
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    flex-shrink:0;
}
.tc-modal-head h2 { font-size:16px; font-weight:700; color:#0f172a; margin:0; }
.tc-modal-close {
    width:30px; height:30px; border-radius:8px; border:none;
    background:#f1f5f9; color:#64748b; font-size:16px; cursor:pointer;
    display:flex; align-items:center; justify-content:center; transition:background .12s;
}
.tc-modal-close:hover { background:#e2e8f0; }
.tc-modal-body { padding:24px; overflow-y:auto; flex:1; }

/* Document preview inside modal */
.tc-doc-preview {
    font-family:Georgia, serif; font-size:13px; line-height:1.7;
    color:#1e293b; max-width:600px; margin:0 auto;
}
.tc-doc-title {
    text-align:center; font-size:17px; font-weight:700; margin-bottom:6px;
    text-transform:uppercase; letter-spacing:.04em;
}
.tc-doc-subtitle { text-align:center; font-size:12px; color:#64748b; margin-bottom:20px; }
.tc-doc-patient {
    background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;
    padding:12px 16px; margin-bottom:18px; font-size:12px;
    display:grid; grid-template-columns:1fr 1fr; gap:4px 16px;
}
.tc-doc-patient .lbl { color:#64748b; font-size:11px; text-transform:uppercase; letter-spacing:.04em; }
.tc-doc-patient .val { font-weight:600; }
.tc-doc-content p  { margin:0 0 10px; }
.tc-doc-content ol,
.tc-doc-content ul { margin:0 0 10px; padding-left:22px; }
.tc-doc-content li { margin-bottom:4px; }
.tc-doc-content strong { font-weight:700; }
.tc-doc-sig {
    margin-top:24px; padding-top:16px; border-top:1px solid #e2e8f0;
    display:grid; gap:16px;
}
.tc-doc-sig-col { text-align:center; }
.tc-doc-sig-line { border-top:1px solid #334155; width:80%; margin:0 auto 6px; padding-top:6px; }
.tc-doc-sig-label { font-size:11px; color:#64748b; }
.tc-page-sep {
    text-align:center; font-size:11px; font-weight:700; text-transform:uppercase;
    letter-spacing:.1em; color:#94a3b8; margin:28px 0 18px;
    position:relative;
}
.tc-page-sep::before, .tc-page-sep::after {
    content:''; position:absolute; top:50%; width:35%; height:1px; background:#e2e8f0;
}
.tc-page-sep::before { left:0; }
.tc-page-sep::after  { right:0; }
</style>
@endpush

<div class="tc-wrap">

    <div class="tc-header">
        <div>
            <h1>Tipos de Consentimiento</h1>
            <p>Plantillas de consentimiento informado para asignar a los pacientes.</p>
        </div>
        <a href="{{ route('admin.tipos-consentimiento.create') }}" class="tc-btn primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo tipo
        </a>
    </div>

    @if(session('message'))
    <div class="tc-alert">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('message') }}
    </div>
    @endif

    <div class="tc-card">
        <table class="tc-table">
            <thead>
                <tr>
                    <th>Plantilla</th>
                    <th>Firma profesional</th>
                    <th>Estado</th>
                    <th style="width:200px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tipos as $tipo)
                <tr>
                    <td>
                        <div class="tc-name">{{ $tipo->nombre }}</div>
                        @if($tipo->descripcion)
                        <div class="tc-desc">{{ $tipo->descripcion }}</div>
                        @endif
                    </td>
                    <td>
                        @if($tipo->requiere_firma_profesional)
                            <span class="tc-badge info">Requerida</span>
                        @else
                            <span class="tc-badge muted">No requerida</span>
                        @endif
                    </td>
                    <td>
                        @if($tipo->activo)
                            <span class="tc-badge success">Activo</span>
                        @else
                            <span class="tc-badge muted">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div class="tc-actions">
                            <button class="tc-act-btn" onclick="openPreview({{ $tipo->id }})">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Ver
                            </button>
                            <a href="{{ route('admin.tipos-consentimiento.edit', $tipo) }}" class="tc-act-btn">Editar</a>
                            <form method="POST" action="{{ route('admin.tipos-consentimiento.destroy', $tipo) }}" style="display:inline;" onsubmit="return confirm('¿Eliminar este tipo?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="tc-act-btn danger">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="tc-empty">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <strong>Sin plantillas configuradas</strong>
                            <p>Creá el primer tipo de consentimiento.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Preview modals --}}
@foreach($tipos as $tipo)
<div class="tc-modal-overlay" id="modal-{{ $tipo->id }}" onclick="closePreview({{ $tipo->id }})">
    <div class="tc-modal-box" onclick="event.stopPropagation()">
        <div class="tc-modal-head">
            <h2>{{ $tipo->nombre }}</h2>
            <button class="tc-modal-close" onclick="closePreview({{ $tipo->id }})">✕</button>
        </div>
        <div class="tc-modal-body">
            <div class="tc-doc-preview">
                <div class="tc-doc-title">{{ $tipo->nombre }}</div>
                <div class="tc-doc-subtitle">Vista previa de plantilla</div>

                <div class="tc-doc-patient">
                    <div><span class="lbl">Paciente</span><br><span class="val">Apellido, Nombre</span></div>
                    <div><span class="lbl">DNI</span><br><span class="val">12.345.678</span></div>
                    <div><span class="lbl">Fecha</span><br><span class="val">{{ now()->format('d/m/Y') }}</span></div>
                    <div><span class="lbl">Centro</span><br><span class="val">Centro médico demo</span></div>
                </div>

                @php
                    $paginas = $tipo->contenido_paginas ?? array_filter([$tipo->contenido_pagina1 ?? '', $tipo->contenido_pagina2 ?? '']);
                    $paginas = array_values($paginas);
                @endphp

                @foreach($paginas as $pgIdx => $pgContent)
                    @if($pgIdx > 0)
                    <div class="tc-page-sep">Página {{ $pgIdx + 1 }}</div>
                    @endif
                    @if($pgContent)
                    <div class="tc-doc-content">{!! $pgContent !!}</div>
                    @endif

                    <div class="tc-doc-sig" style="grid-template-columns: 1fr 1fr 1fr {{ $tipo->requiere_firma_profesional ? '1fr' : '' }};">
                        <div class="tc-doc-sig-col">
                            <div class="tc-doc-sig-line"></div>
                            <div class="tc-doc-sig-label">Firma del paciente</div>
                        </div>
                        <div class="tc-doc-sig-col">
                            <div class="tc-doc-sig-line"></div>
                            <div class="tc-doc-sig-label">Aclaración / DNI</div>
                        </div>
                        <div class="tc-doc-sig-col">
                            <div class="tc-doc-sig-line"></div>
                            <div class="tc-doc-sig-label">Fecha y lugar</div>
                        </div>
                        @if($tipo->requiere_firma_profesional)
                        <div class="tc-doc-sig-col">
                            <div class="tc-doc-sig-line"></div>
                            <div class="tc-doc-sig-label">Firma del profesional</div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
function openPreview(id) { document.getElementById('modal-'+id).classList.add('open'); }
function closePreview(id) { document.getElementById('modal-'+id).classList.remove('open'); }
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.tc-modal-overlay.open').forEach(function(m) { m.classList.remove('open'); });
    }
});
</script>
@endpush

@endsection
