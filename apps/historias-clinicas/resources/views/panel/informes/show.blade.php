@extends('layouts.panel')

@section('title', 'Informe — ' . ($Informe->tipo->name ?? ''))

@section('content')

@push('styles')
<style>
.inf-show-wrap {
    display: flex; flex-direction: column; gap: 16px;
    max-width: 920px; margin: 0 auto;
    animation: infFadeUp .35s ease both;
}
@@keyframes infFadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── top bar ── */
.inf-topbar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
}
.inf-back {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 10px;
    border: 1px solid var(--card-border, #e8edf2);
    background: var(--card-bg, #fff);
    font-size: 12px; font-weight: 600; color: var(--text-secondary, #64748b);
    text-decoration: none; transition: all .12s;
}
.inf-back:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); }
.inf-back svg { width: 13px; height: 13px; }

.inf-top-actions { display: flex; gap: 8px; }
.inf-edit-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 18px; border-radius: 10px;
    background: var(--accent, #1d4ed8); color: #fff;
    font-size: 12px; font-weight: 600;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(29,78,216,.25);
    transition: background .12s, transform .12s;
}
.inf-edit-btn:hover { background: var(--accent-hover, #1e40af); color: #fff; transform: translateY(-1px); }
.inf-edit-btn svg { width: 13px; height: 13px; }

/* ── cards ── */
.inf-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--card-border, #e8edf2);
    border-radius: var(--card-radius, 14px);
    box-shadow: var(--card-shadow); overflow: hidden;
}
.inf-card-hdr {
    display: flex; align-items: center; gap: 8px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--card-border, #e8edf2);
    font-size: 14px; font-weight: 600;
    color: var(--text-primary, #0f172a);
}
.inf-card-hdr svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); flex-shrink: 0; }
.inf-card-body { padding: 16px 20px; }

/* ── meta grid ── */
.meta-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 0;
}
.meta-cell {
    padding: 11px 14px;
    border-bottom: 1px solid var(--card-border, #e8edf2);
    border-right: 1px solid var(--card-border, #e8edf2);
}
.meta-cell:nth-child(even) { border-right: none; }
.meta-cell:nth-last-child(-n+2) { border-bottom: none; }
.meta-cell.full { grid-column: 1 / -1; border-right: none; }
.meta-cell.full:last-child { border-bottom: none; }
.meta-lbl {
    font-size: 10px; font-weight: 600; color: var(--text-muted, #94a3b8);
    text-transform: uppercase; letter-spacing: .07em; margin-bottom: 3px;
}
.meta-val { font-size: 13px; color: var(--text-primary, #0f172a); }

/* ── tipo badge ── */
.tipo-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px;
    background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8);
    font-size: 11px; font-weight: 600;
    border: 1px solid rgba(29,78,216,.15);
}

/* ── cie badge ── */
.cie-badge {
    display: inline-block;
    padding: 2px 8px; border-radius: 6px;
    background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;
    font-size: 11px; font-weight: 700; margin-left: 8px;
}

/* ── pdf viewer ── */
.pdf-viewer-wrap {
    border: 1px solid var(--card-border, #e8edf2); border-radius: 8px;
    overflow: hidden; background: var(--body-bg, #f8fafc);
}
.pdf-viewer-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 14px; background: var(--body-bg, #f8fafc);
    border-bottom: 1px solid var(--card-border, #e8edf2);
    font-size: 12px; color: var(--text-secondary, #64748b);
}
.pdf-viewer-toolbar a {
    display: inline-flex; align-items: center; gap: 5px;
    color: var(--accent, #1d4ed8); font-weight: 600; text-decoration: none; font-size: 12px;
}
.pdf-viewer-toolbar a:hover { text-decoration: underline; }
.pdf-viewer-toolbar a svg { width: 13px; height: 13px; }
.pdf-iframe { width: 100%; height: 700px; border: none; display: block; }

.no-files {
    padding: 30px; text-align: center;
    color: var(--text-muted, #94a3b8); font-size: 13px;
}
.no-files svg { width: 32px; height: 32px; margin: 0 auto 8px; display: block; opacity: .35; }

/* ── firma status ── */
.firma-status-signed {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 14px 18px; border-radius: 10px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
}
.firma-status-signed .fss-icon {
    width: 36px; height: 36px; flex-shrink: 0;
    background: #16a34a; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}
.firma-status-signed .fss-icon svg { width: 18px; height: 18px; color: #fff; }
.firma-status-signed .fss-body { flex: 1; }
.firma-status-signed .fss-title { font-size: 13px; font-weight: 700; color: #166534; margin-bottom: 2px; }
.firma-status-signed .fss-detail { font-size: 12px; color: #15803d; }

.firma-unsigned-box {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    padding: 14px 18px; border-radius: 10px;
    background: var(--body-bg, #f8fafc); border: 1px solid var(--card-border, #e8edf2);
    flex-wrap: wrap;
}
.firma-unsigned-box .fub-info { font-size: 12px; color: var(--text-secondary, #64748b); }
.firma-unsigned-box .fub-info strong { display: block; font-size: 13px; color: var(--text-primary, #0f172a); margin-bottom: 2px; }
.btn-firmar {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 20px; border-radius: 10px;
    background: #16a34a; color: #fff; font-size: 12px; font-weight: 700;
    border: none; cursor: pointer; transition: background .12s;
    text-decoration: none;
}
.btn-firmar:hover { background: #15803d; color: #fff; }
.btn-firmar svg { width: 14px; height: 14px; }

/* ── recetas ── */
.receta-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
.receta-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 14px; border-radius: 10px;
    border: 1px solid var(--card-border, #e8edf2);
    background: var(--body-bg, #f8fafc);
}
.receta-item img {
    width: 52px; height: 52px; object-fit: cover;
    border-radius: 6px; border: 1px solid var(--card-border, #e8edf2); flex-shrink: 0;
}
.receta-item .rec-icon {
    width: 52px; height: 52px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: #fee2e2; border-radius: 6px;
}
.receta-item .rec-icon svg { width: 24px; height: 24px; color: #dc2626; }
.receta-item .rec-body { flex: 1; min-width: 0; }
.receta-item .rec-name { font-size: 13px; font-weight: 600; color: var(--text-primary, #0f172a); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.receta-item .rec-meta { font-size: 11px; color: var(--text-muted, #94a3b8); }
.receta-item .rec-actions { display: flex; gap: 6px; flex-shrink: 0; }
.btn-receta-dl {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 600;
    background: var(--accent, #1d4ed8); color: #fff; text-decoration: none; transition: background .12s;
}
.btn-receta-dl:hover { background: var(--accent-hover, #1e40af); color: #fff; }
.btn-receta-del {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 600;
    background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; cursor: pointer; transition: background .12s;
}
.btn-receta-del:hover { background: #fecaca; }
</style>
@endpush

<div class="inf-show-wrap">

    {{-- Top bar --}}
    <div class="inf-topbar">
        <a href="{{ url()->previous() }}" class="inf-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>
        <div class="inf-top-actions">
            @can('informe_edit')
            @if(auth()->user()->is_admin || $Informe->profesional_id == auth()->id())
            <a href="{{ route('panel.informe.edit', $Informe->id) }}{{ request('from_paciente') ? '?from_paciente=' . request('from_paciente') : '' }}"
               class="inf-edit-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar informe
            </a>
            @endif
            @endcan
        </div>
    </div>

    {{-- Datos del informe --}}
    <div class="inf-card">
        <div class="inf-card-hdr">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Datos del Informe
        </div>
        <div class="meta-grid">
            <div class="meta-cell">
                <div class="meta-lbl">Paciente</div>
                <div class="meta-val">
                    <strong>{{ $Informe->paciente->apellido ?? '—' }}, {{ $Informe->paciente->nombre ?? '' }}</strong>
                    @if($Informe->paciente->dni)
                        <span style="font-size:11px; color:var(--t2); margin-left:6px;">DNI {{ $Informe->paciente->dni }}</span>
                    @endif
                </div>
            </div>
            <div class="meta-cell">
                <div class="meta-lbl">Tipo</div>
                <div class="meta-val">
                    <span class="tipo-badge">{{ $Informe->tipo->name ?? '—' }}</span>
                </div>
            </div>
            <div class="meta-cell">
                <div class="meta-lbl">Fecha</div>
                <div class="meta-val">{{ $Informe->fecha ? \Carbon\Carbon::parse($Informe->fecha)->format('d/m/Y') : '—' }}</div>
            </div>
            <div class="meta-cell">
                <div class="meta-lbl">Profesional</div>
                <div class="meta-val">{{ $Informe->profesional->name ?? '—' }}</div>
            </div>
            @if($Informe->diagnostico)
            <div class="meta-cell full">
                <div class="meta-lbl">Diagnóstico</div>
                <div class="meta-val">
                    {{ $Informe->diagnostico }}
                    @if($Informe->codigo_cie10)
                        <span class="cie-badge">{{ $Informe->codigo_cie10 }}</span>
                    @endif
                </div>
            </div>
            @endif
            @if($Informe->agenda)
            <div class="meta-cell full">
                <div class="meta-lbl">Cita asociada</div>
                <div class="meta-val">
                    {{ \Carbon\Carbon::parse($Informe->agenda->fecha_hora_inicio)->format('d/m/Y H:i') }}
                    — {{ $Informe->agenda->motivo }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Documento --}}
    <div class="inf-card">
        <div class="inf-card-hdr">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Documento
        </div>
        <div class="inf-card-body" style="padding: 0;">
            @if(!empty($attachedFiles))
                @foreach($attachedFiles as $index => $file)
                @php $fileUrl = asset('storage/uploads/' . $Informe->paciente_id . '/' . $Informe->tipo_id . '/' . $file); @endphp
                <div class="pdf-viewer-wrap" style="{{ $index > 0 ? 'margin-top:1px;' : '' }}">
                    <div class="pdf-viewer-toolbar">
                        <span>PDF {{ count($attachedFiles) > 1 ? ($index + 1) . ' / ' . count($attachedFiles) : '' }}</span>
                        <a href="{{ $fileUrl }}" target="_blank">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Abrir en nueva pestaña
                        </a>
                    </div>
                    <iframe class="pdf-iframe"
                        src="{{ $fileUrl }}#toolbar=0&navpanes=0&scrollbar=1"
                        title="Informe PDF {{ $index + 1 }}">
                    </iframe>
                </div>
                @endforeach
            @else
                <div class="no-files">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    Este informe no tiene documento adjunto.
                </div>
            @endif
        </div>
    </div>

    {{-- Firma digital --}}
    <div class="inf-card">
        <div class="inf-card-hdr">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Firma digital
        </div>
        <div class="inf-card-body">
            @if($Informe->firmado)
            <div class="firma-status-signed">
                <div class="fss-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="fss-body">
                    <div class="fss-title">Documento firmado digitalmente</div>
                    <div class="fss-detail">
                        Firmado por <strong>{{ $Informe->firmadoPor->name ?? '—' }}</strong>
                        @if($Informe->firmado_at)
                        el {{ \Carbon\Carbon::parse($Informe->firmado_at)->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }}
                        @endif
                    </div>
                </div>
            </div>
            @else
            @php
                $canSign = !$Informe->firmado
                    && $Informe->tipo_seleccion === 'redaccion'
                    && (auth()->user()->is_admin || $Informe->profesional_id == auth()->id())
                    && auth()->user()->firma_nombre;
            @endphp
            @if($canSign)
            <div class="firma-unsigned-box">
                <div class="fub-info">
                    <strong>Sin firma digital</strong>
                    Este informe aún no ha sido firmado. Podés firmarlo ahora con tu firma digital configurada.
                </div>
                <form method="POST" action="{{ route('panel.informe.firmar', $Informe->id) }}"
                      onsubmit="return confirm('¿Confirmar firma digital? El documento quedará bloqueado y no podrá editarse.')">
                    @csrf
                    <button type="submit" class="btn-firmar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Firmar documento
                    </button>
                </form>
            </div>
            @elseif(!$Informe->firmado && $Informe->tipo_seleccion === 'redaccion' && !auth()->user()->firma_nombre)
            <div class="firma-unsigned-box">
                <div class="fub-info">
                    <strong>Sin firma digital</strong>
                    Configurá tu firma digital en el perfil para poder firmar este documento.
                </div>
                <a href="{{ route('panel.profile.index') }}" class="btn-firmar" style="background:#64748b;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Configurar firma
                </a>
            </div>
            @else
            <div style="padding:10px 0; font-size:13px; color:var(--text-muted,#94a3b8);">
                Este informe no tiene firma digital.
            </div>
            @endif
            @endif
        </div>
    </div>

    {{-- Recetas médicas --}}
    @if($Informe->recetas->count() > 0 || (!$Informe->firmado && (auth()->user()->is_admin || $Informe->profesional_id == auth()->id())))
    <div class="inf-card">
        <div class="inf-card-hdr">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Recetas médicas
        </div>
        <div class="inf-card-body">
            @if($Informe->recetas->count() > 0)
            <ul class="receta-list">
                @foreach($Informe->recetas as $receta)
                <li class="receta-item">
                    @if($receta->isImage())
                    <img src="{{ $receta->url() }}" alt="{{ $receta->nombre_original }}">
                    @else
                    <div class="rec-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                    <div class="rec-body">
                        <div class="rec-name">{{ $receta->nombre_original }}</div>
                        <div class="rec-meta">{{ strtoupper(pathinfo($receta->nombre_original, PATHINFO_EXTENSION)) }}</div>
                    </div>
                    <div class="rec-actions">
                        <a href="{{ $receta->url() }}" download="{{ $receta->nombre_original }}" class="btn-receta-dl">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar
                        </a>
                        @if(!$Informe->firmado && (auth()->user()->is_admin || $Informe->profesional_id == auth()->id()))
                        @can('informe_edit')
                        <form method="POST" action="{{ route('panel.receta.destroy', $receta->id) }}"
                              onsubmit="return confirm('¿Eliminar esta receta?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-receta-del">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar
                            </button>
                        </form>
                        @endcan
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="no-files" style="padding:20px 0;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                No hay recetas adjuntas.
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

@endsection
