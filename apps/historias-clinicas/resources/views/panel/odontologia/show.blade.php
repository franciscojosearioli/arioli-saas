@extends('layouts.panel')
@section('title', 'Odontograma')

@php
    // Orden visual estándar de un odontograma (vista del profesional, no
    // del paciente): arcada superior 18→11 | 21→28, inferior 48→41 | 31→38.
    $filaSuperiorPerm = [18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28];
    $filaInferiorPerm = [48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38];
    $filaSuperiorTemp = [55,54,53,52,51,61,62,63,64,65];
    $filaInferiorTemp = [85,84,83,82,81,71,72,73,74,75];

    $piezasPorNumero = $odontograma->piezas->keyBy('numero_fdi');
    $tieneTemporal = $piezasPorNumero->contains(fn ($p) => $p->catalogo()['denticion'] === 'temporal');

    $estadosSuperficie = \App\Modules\Odontologia\Models\SuperficieOdontologica::estadosLabels();
    $superficiesLabels = \App\Modules\Odontologia\Models\SuperficieOdontologica::superficiesLabels();
    $estadosGenerales = \App\Modules\Odontologia\Models\PiezaOdontologica::estadosGeneralesLabels();
    $tiposTratamiento = \App\Modules\Odontologia\Models\TratamientoOdontologico::tiposLabels();
    $puedeEditar = \Gate::allows('odontologia_edit');

    $colorPorEstado = [
        'sana' => '#e2e8f0', 'cariada' => '#f87171', 'obturada' => '#60a5fa',
        'corona' => '#f59e0b', 'sellada' => '#34d399', 'fracturada' => '#a855f7',
    ];

    // Resumen visual por pieza: un conic-gradient de 5 franjas, una por
    // superficie, coloreada según su estado — da una lectura "de un
    // vistazo" de qué pieza tiene algo sin tener que abrirla.
    $gradientePieza = function ($pieza) use ($colorPorEstado) {
        if (! $pieza) {
            return $colorPorEstado['sana'];
        }
        if ($pieza->estado_general) {
            return $pieza->estado_general === 'ausente' ? 'repeating-linear-gradient(45deg,#f1f5f9,#f1f5f9 4px,#e2e8f0 4px,#e2e8f0 8px)' : '#94a3b8';
        }
        $superficies = $pieza->superficies;
        if ($superficies->isEmpty()) {
            return $colorPorEstado['sana'];
        }
        $n = $superficies->count();
        $paso = 360 / $n;
        $partes = [];
        foreach ($superficies->values() as $i => $s) {
            $color = $colorPorEstado[$s->estado] ?? $colorPorEstado['sana'];
            $desde = $i * $paso;
            $hasta = ($i + 1) * $paso;
            $partes[] = "{$color} {$desde}deg {$hasta}deg";
        }
        return 'conic-gradient(' . implode(', ', $partes) . ')';
    };

    // "Notas" — toda observación no vacía en superficies/piezas/tratamientos
    // de este odontograma, la vista de "qué se hizo en cada pieza" pedida.
    $notas = collect();
    foreach ($odontograma->piezas as $pieza) {
        $cat = $pieza->catalogo();
        if ($pieza->observaciones) {
            $notas->push(['numero' => $pieza->numero_fdi, 'nombre' => $cat['nombre'], 'parte' => 'Toda la pieza', 'detalle' => $estadosGenerales[$pieza->estado_general] ?? '—', 'texto' => $pieza->observaciones, 'fecha' => $pieza->updated_at]);
        }
        foreach ($pieza->superficies as $sup) {
            if ($sup->observaciones) {
                $notas->push(['numero' => $pieza->numero_fdi, 'nombre' => $cat['nombre'], 'parte' => $superficiesLabels[$sup->superficie] ?? $sup->superficie, 'detalle' => $estadosSuperficie[$sup->estado] ?? $sup->estado, 'texto' => $sup->observaciones, 'fecha' => $sup->updated_at]);
            }
        }
    }
    foreach ($odontograma->tratamientos as $trat) {
        if ($trat->observaciones) {
            $cat = config('piezas_dentales_catalogo.piezas.' . $trat->numero_fdi, ['nombre' => 'Pieza ' . $trat->numero_fdi]);
            $notas->push(['numero' => $trat->numero_fdi, 'nombre' => $cat['nombre'], 'parte' => $trat->superficie ? ($superficiesLabels[$trat->superficie] ?? $trat->superficie) : 'Toda la pieza', 'detalle' => $tiposTratamiento[$trat->tipo_tratamiento] ?? $trat->tipo_tratamiento, 'texto' => $trat->observaciones, 'fecha' => $trat->updated_at]);
        }
    }
    $notas = $notas->sortByDesc('fecha')->values();

    // Precalculado acá, no dentro de @json() en el <script> — un @json()
    // con una expresión multilínea/closures anidados confunde al parser
    // de directivas de Blade (encontrado en vivo: "Unclosed '[' does not
    // match ')'"). Una variable simple evita el problema por completo.
    $piezasDataJs = $odontograma->piezas->keyBy('id')->map(function ($p) {
        return [
            'numero' => $p->numero_fdi,
            'nombre' => $p->catalogo()['nombre'],
            'superficies' => $p->superficies->map(fn ($s) => [
                'id' => $s->id,
                'superficie' => $s->superficie,
                'estado' => $s->estado,
                'observaciones' => $s->observaciones,
            ])->values(),
        ];
    });
@endphp

@section('content')
<div style="max-width:1180px; margin:30px auto; padding:0 20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px; flex-wrap:wrap; gap:8px;">
        <h1 style="font-size:18px; font-weight:700; margin:0;">Odontograma</h1>
        <a href="{{ route('panel.paciente.show', $odontograma->paciente) }}" style="font-size:12.5px; color:var(--text-secondary,#64748b); text-decoration:none;">← Volver a la ficha</a>
    </div>
    <p style="font-size:13px; color:var(--text-secondary,#64748b); margin-bottom:20px;">
        {{ $odontograma->paciente->apellido }}, {{ $odontograma->paciente->nombre }}
    </p>

    <div class="odo-layout">
        <div class="odo-main">

            {{-- Leyenda --}}
            <div class="odo-legend">
                @foreach($estadosSuperficie as $key => $label)
                <div class="odo-legend-item">
                    <span class="odo-dot" style="background:{{ $colorPorEstado[$key] }}"></span>
                    {{ $label }}
                </div>
                @endforeach
                <div class="odo-legend-item"><span class="odo-dot" style="background:#94a3b8"></span> Ausente/Extraída</div>
            </div>

            {{-- Gráfico --}}
            <div class="odo-chart-card">
                <div class="odo-chart-title">Dentición permanente</div>
                <div class="odo-scroll">
                    <div class="odo-fila">
                        @foreach($filaSuperiorPerm as $i => $numero)
                            @if($i === 8)<div class="odo-divisor"></div>@endif
                            @php $pieza = $piezasPorNumero[$numero] ?? null; @endphp
                            <button type="button" class="odo-tooth" data-pieza-id="{{ $pieza->id ?? '' }}" data-numero="{{ $numero }}"
                                    style="background:{{ $gradientePieza($pieza) }}" title="Pieza {{ $numero }}">
                                <span class="odo-num">{{ $numero }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div style="height:22px;"></div>
                    <div class="odo-fila">
                        @foreach($filaInferiorPerm as $i => $numero)
                            @if($i === 8)<div class="odo-divisor"></div>@endif
                            @php $pieza = $piezasPorNumero[$numero] ?? null; @endphp
                            <button type="button" class="odo-tooth" data-pieza-id="{{ $pieza->id ?? '' }}" data-numero="{{ $numero }}"
                                    style="background:{{ $gradientePieza($pieza) }}" title="Pieza {{ $numero }}">
                                <span class="odo-num">{{ $numero }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                @if($tieneTemporal)
                <div class="odo-chart-title" style="margin-top:26px;">Dentición temporal</div>
                <div class="odo-scroll">
                    <div class="odo-fila">
                        @foreach($filaSuperiorTemp as $numero)
                            @php $pieza = $piezasPorNumero[$numero] ?? null; @endphp
                            <button type="button" class="odo-tooth odo-tooth-sm" data-pieza-id="{{ $pieza->id ?? '' }}" data-numero="{{ $numero }}"
                                    style="background:{{ $gradientePieza($pieza) }}" title="Pieza {{ $numero }}">
                                <span class="odo-num">{{ $numero }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div style="height:16px;"></div>
                    <div class="odo-fila">
                        @foreach($filaInferiorTemp as $numero)
                            @php $pieza = $piezasPorNumero[$numero] ?? null; @endphp
                            <button type="button" class="odo-tooth odo-tooth-sm" data-pieza-id="{{ $pieza->id ?? '' }}" data-numero="{{ $numero }}"
                                    style="background:{{ $gradientePieza($pieza) }}" title="Pieza {{ $numero }}">
                                <span class="odo-num">{{ $numero }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @elseif($puedeEditar)
                <form method="POST" action="{{ route('panel.odontologia.denticionTemporal', $odontograma) }}" style="margin-top:18px;">
                    @csrf
                    <button type="submit" class="odo-btn-ghost">+ Agregar dentición temporal</button>
                </form>
                @endif
            </div>

            {{-- Detalle de pieza seleccionada --}}
            <div class="odo-detail-card" id="odo-detail">
                <div class="odo-detail-empty">Seleccioná una pieza para ver su detalle.</div>
            </div>

            {{-- Tratamientos --}}
            <div class="odo-chart-card" style="margin-top:16px;">
                <div class="odo-chart-title">Tratamientos</div>
                @if($puedeEditar)
                <form id="odo-form-tratamiento" style="display:flex; flex-wrap:wrap; gap:8px; margin:12px 0 16px;">
                    <input type="number" id="odo-trat-pieza" placeholder="N° pieza" min="11" max="85" required
                           style="width:90px; padding:8px 10px; border:1px solid var(--card-border,#e2e8f0); border-radius:7px; font-size:12.5px;">
                    <select id="odo-trat-tipo" required style="padding:8px 10px; border:1px solid var(--card-border,#e2e8f0); border-radius:7px; font-size:12.5px;">
                        <option value="">Tipo de tratamiento</option>
                        @foreach($tiposTratamiento as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <select id="odo-trat-superficie" style="padding:8px 10px; border:1px solid var(--card-border,#e2e8f0); border-radius:7px; font-size:12.5px;">
                        <option value="">Toda la pieza</option>
                        @foreach($superficiesLabels as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="text" id="odo-trat-material" placeholder="Material (opcional)"
                           style="width:150px; padding:8px 10px; border:1px solid var(--card-border,#e2e8f0); border-radius:7px; font-size:12.5px;">
                    <input type="date" id="odo-trat-fecha" style="padding:8px 10px; border:1px solid var(--card-border,#e2e8f0); border-radius:7px; font-size:12.5px;">
                    <button type="submit" class="odo-btn-primary">+ Agregar</button>
                </form>
                @endif
                <div id="odo-tratamientos-lista">
                    @forelse($odontograma->tratamientos as $trat)
                    <div class="odo-trat-row" data-id="{{ $trat->id }}">
                        <div>
                            <strong>({{ $trat->numero_fdi }})</strong> {{ $tiposTratamiento[$trat->tipo_tratamiento] ?? $trat->tipo_tratamiento }}
                            @if($trat->superficie) — {{ $superficiesLabels[$trat->superficie] ?? $trat->superficie }} @endif
                            @if($trat->material) <span class="odo-muted">· {{ $trat->material }}</span> @endif
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="odo-badge odo-badge-{{ $trat->estado_tratamiento }}">{{ ucfirst($trat->estado_tratamiento) }}</span>
                            @if($puedeEditar && $trat->estado_tratamiento === 'pendiente')
                            <button type="button" class="odo-btn-mini odo-completar-trat" data-id="{{ $trat->id }}">Marcar realizado</button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="odo-muted" style="padding:8px 0;">Sin tratamientos registrados.</div>
                    @endforelse
                </div>
            </div>

            @if(!$puedeEditar)
            <p style="font-size:12px; color:var(--text-muted,#94a3b8); margin-top:14px;">Sin permiso para editar — vista de solo lectura.</p>
            @endif
        </div>

        {{-- Panel de notas --}}
        <div class="odo-sidebar">
            <div class="odo-chart-title">Notas de todas las piezas</div>
            <div class="odo-notas-lista">
                @forelse($notas as $nota)
                <div class="odo-nota-row">
                    <div class="odo-nota-head">({{ $nota['numero'] }}) {{ $nota['nombre'] }}</div>
                    <div class="odo-nota-sub">» {{ $nota['parte'] }} — {{ $nota['detalle'] }}</div>
                    <div class="odo-nota-texto">{{ $nota['texto'] }}</div>
                </div>
                @empty
                <div class="odo-muted" style="padding:8px 0;">Sin anotaciones todavía.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Popover de edición de superficie --}}
<div id="odo-popover" class="odo-popover">
    <div class="odo-pop-title">Pieza <span id="odo-pop-numero"></span> — <span id="odo-pop-superficie"></span></div>
    <label class="odo-pop-label">Estado</label>
    <select id="odo-pop-estado" class="odo-pop-input">
        @foreach($estadosSuperficie as $key => $label)
        <option value="{{ $key }}">{{ $label }}</option>
        @endforeach
    </select>
    <label class="odo-pop-label">Observaciones</label>
    <textarea id="odo-pop-obs" rows="2" maxlength="500" class="odo-pop-input" style="resize:vertical;"></textarea>
    <div style="display:flex; gap:8px; margin-top:4px;">
        <button type="button" id="odo-pop-guardar" class="odo-btn-primary" style="flex:1;">Guardar</button>
        <button type="button" id="odo-pop-cancelar" class="odo-btn-ghost">Cancelar</button>
    </div>
    <div id="odo-pop-error" class="odo-pop-error"></div>
</div>

<style>
.odo-layout { display:grid; grid-template-columns:1fr 320px; gap:18px; align-items:start; }
@media (max-width:900px) { .odo-layout { grid-template-columns:1fr; } }

.odo-legend { display:flex; flex-wrap:wrap; gap:14px; margin-bottom:16px; padding:12px 16px; background:var(--body-bg,#f8fafc); border:1px solid var(--card-border,#e2e8f0); border-radius:10px; }
.odo-legend-item { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--text-secondary,#64748b); }
.odo-dot { width:14px; height:14px; border-radius:50%; display:inline-block; border:1px solid rgba(0,0,0,.08); }

.odo-chart-card { background:#fff; border:1px solid var(--card-border,#e2e8f0); border-radius:14px; padding:22px 20px; }
.odo-chart-title { font-size:13px; font-weight:700; color:var(--text-primary,#0f172a); margin-bottom:14px; }
.odo-scroll { overflow-x:auto; }
.odo-fila { display:flex; align-items:center; gap:6px; width:max-content; }
.odo-divisor { width:1px; align-self:stretch; background:var(--card-border,#e2e8f0); margin:0 4px; }

.odo-tooth {
    width:42px; height:42px; border-radius:50%; border:2px solid #fff; box-shadow:0 0 0 1px var(--card-border,#cbd5e1);
    cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0;
    transition:transform .12s, box-shadow .12s; position:relative;
}
.odo-tooth-sm { width:30px; height:30px; }
.odo-tooth:hover { transform:translateY(-2px) scale(1.05); box-shadow:0 0 0 2px #1d4ed8; }
.odo-tooth.odo-selected { box-shadow:0 0 0 2px #1d4ed8, 0 4px 10px rgba(29,78,216,.3); }
.odo-num { font-size:10px; font-weight:700; color:#334155; background:rgba(255,255,255,.75); border-radius:50%; width:18px; height:18px; display:flex; align-items:center; justify-content:center; pointer-events:none; }

.odo-detail-card { background:#fff; border:1px solid var(--card-border,#e2e8f0); border-radius:14px; padding:18px 20px; margin-top:16px; }
.odo-detail-empty { font-size:12.5px; color:var(--text-muted,#94a3b8); }
.odo-detail-title { font-size:14px; font-weight:700; margin-bottom:2px; }
.odo-detail-sub { font-size:12px; color:var(--text-secondary,#64748b); margin-bottom:14px; }
.odo-surface-row { display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--body-bg,#f1f5f9); font-size:12.5px; }
.odo-surface-row:last-child { border-bottom:none; }
.odo-surface-edit { background:none; border:1px solid var(--card-border,#e2e8f0); border-radius:6px; padding:4px 10px; font-size:11.5px; cursor:pointer; color:var(--text-secondary,#64748b); }
.odo-general-row { display:flex; align-items:center; justify-content:space-between; padding:10px 0; margin-bottom:6px; border-bottom:1.5px solid var(--card-border,#e2e8f0); font-size:12.5px; font-weight:600; }

.odo-sidebar { background:#fff; border:1px solid var(--card-border,#e2e8f0); border-radius:14px; padding:18px 18px; max-height:820px; overflow-y:auto; }
.odo-notas-lista { display:flex; flex-direction:column; gap:12px; margin-top:10px; }
.odo-nota-row { padding:10px 12px; background:var(--body-bg,#f8fafc); border-radius:9px; border:1px solid var(--card-border,#e2e8f0); }
.odo-nota-head { font-size:12.5px; font-weight:700; color:var(--text-primary,#0f172a); }
.odo-nota-sub { font-size:11px; color:#1d4ed8; margin:2px 0 4px; }
.odo-nota-texto { font-size:12px; color:var(--text-secondary,#64748b); }

.odo-btn-primary { padding:8px 16px; border:none; border-radius:7px; background:#1d4ed8; color:#fff; font-size:12.5px; font-weight:600; cursor:pointer; }
.odo-btn-ghost { padding:8px 16px; border:1px solid var(--card-border,#e2e8f0); border-radius:7px; background:#fff; color:var(--text-secondary,#64748b); font-size:12.5px; cursor:pointer; }
.odo-btn-mini { padding:5px 10px; border:1px solid #1d4ed8; border-radius:6px; background:#fff; color:#1d4ed8; font-size:11px; font-weight:600; cursor:pointer; }
.odo-muted { font-size:12px; color:var(--text-muted,#94a3b8); }

.odo-trat-row { display:flex; align-items:center; justify-content:space-between; padding:9px 0; border-bottom:1px solid var(--body-bg,#f1f5f9); font-size:12.5px; }
.odo-badge { padding:3px 9px; border-radius:99px; font-size:11px; font-weight:600; }
.odo-badge-pendiente { background:#fef3c7; color:#92400e; }
.odo-badge-realizado { background:#d1fae5; color:#065f46; }
.odo-badge-cancelado { background:#f1f5f9; color:#64748b; }

.odo-popover { display:none; position:fixed; z-index:1000; background:#fff; border:1px solid var(--card-border,#e2e8f0); border-radius:12px; box-shadow:0 12px 32px rgba(15,23,42,.18); padding:16px; width:260px; }
.odo-pop-title { font-size:13px; font-weight:700; margin-bottom:10px; }
.odo-pop-label { font-size:11.5px; font-weight:600; color:var(--text-secondary,#64748b); display:block; margin-bottom:4px; margin-top:8px; }
.odo-pop-input { width:100%; padding:7px 8px; border:1px solid var(--card-border,#e2e8f0); border-radius:7px; font-size:12.5px; box-sizing:border-box; }
.odo-pop-error { display:none; font-size:11.5px; color:#dc2626; margin-top:8px; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const puedeEditar = @json($puedeEditar);
    const superficiesLabels = @json($superficiesLabels);
    const estadosLabels = @json($estadosSuperficie);
    const piezasData = @json($piezasDataJs);

    const detailBox = document.getElementById('odo-detail');
    const popover = document.getElementById('odo-popover');
    const popNumero = document.getElementById('odo-pop-numero');
    const popSuperficie = document.getElementById('odo-pop-superficie');
    const selEstado = document.getElementById('odo-pop-estado');
    const txtObs = document.getElementById('odo-pop-obs');
    const btnGuardar = document.getElementById('odo-pop-guardar');
    const btnCancelar = document.getElementById('odo-pop-cancelar');
    const errorBox = document.getElementById('odo-pop-error');
    let superficieActiva = null;

    function csrf() { return document.querySelector('meta[name="csrf-token"]').content; }

    function renderDetalle(piezaId) {
        const p = piezasData[piezaId];
        if (!p) return;
        let html = `<div class="odo-detail-title">(${p.numero}) ${p.nombre}</div>`;
        html += `<div class="odo-detail-sub">Superficies</div>`;
        p.superficies.forEach(s => {
            const label = superficiesLabels[s.superficie] || s.superficie;
            const estadoLabel = estadosLabels[s.estado] || s.estado;
            html += `<div class="odo-surface-row">
                <span>${label} — <strong>${estadoLabel}</strong>${s.observaciones ? ' · ' + s.observaciones : ''}</span>
                ${puedeEditar ? `<button type="button" class="odo-surface-edit" data-superficie-id="${s.id}" data-numero="${p.numero}" data-superficie-label="${label}" data-estado="${s.estado}" data-obs="${(s.observaciones || '').replace(/"/g, '&quot;')}">Editar</button>` : ''}
            </div>`;
        });
        detailBox.innerHTML = html;
        detailBox.querySelectorAll('.odo-surface-edit').forEach(btn => {
            btn.addEventListener('click', () => abrirPopover(btn));
        });
    }

    document.querySelectorAll('.odo-tooth').forEach(btn => {
        if (!btn.dataset.piezaId) return;
        btn.addEventListener('click', () => {
            document.querySelectorAll('.odo-tooth').forEach(b => b.classList.remove('odo-selected'));
            btn.classList.add('odo-selected');
            renderDetalle(btn.dataset.piezaId);
        });
    });

    function abrirPopover(btn) {
        superficieActiva = btn;
        popNumero.textContent = btn.dataset.numero;
        popSuperficie.textContent = btn.dataset.superficieLabel;
        selEstado.value = btn.dataset.estado;
        txtObs.value = btn.dataset.obs || '';
        errorBox.style.display = 'none';

        const rect = btn.getBoundingClientRect();
        popover.style.display = 'block';
        const popW = 260;
        let left = Math.max(10, Math.min(rect.left, window.innerWidth - popW - 10));
        let top = rect.bottom + 8;
        if (top + 220 > window.innerHeight) top = rect.top - 228;
        popover.style.left = left + 'px';
        popover.style.top = top + 'px';
    }

    function cerrarPopover() { popover.style.display = 'none'; superficieActiva = null; }
    btnCancelar.addEventListener('click', cerrarPopover);
    document.addEventListener('click', (e) => {
        if (popover.style.display === 'block' && !popover.contains(e.target) && !e.target.classList.contains('odo-surface-edit')) {
            cerrarPopover();
        }
    });

    btnGuardar.addEventListener('click', async () => {
        if (!superficieActiva) return;
        btnGuardar.disabled = true;
        btnGuardar.textContent = 'Guardando...';
        errorBox.style.display = 'none';
        try {
            const resp = await fetch(`{{ url('odontologia/superficie') }}/${superficieActiva.dataset.superficieId}`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ estado: selEstado.value, observaciones: txtObs.value }),
            });
            if (!resp.ok) throw new Error('No se pudo guardar.');
            location.reload();
        } catch (err) {
            errorBox.textContent = err.message;
            errorBox.style.display = 'block';
            btnGuardar.disabled = false;
            btnGuardar.textContent = 'Guardar';
        }
    });

    document.querySelectorAll('.odo-completar-trat').forEach(btn => {
        btn.addEventListener('click', async () => {
            btn.disabled = true;
            try {
                const resp = await fetch(`{{ url('odontologia/tratamientos') }}/${btn.dataset.id}/completar`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                if (!resp.ok) throw new Error();
                location.reload();
            } catch { btn.disabled = false; }
        });
    });

    const formTrat = document.getElementById('odo-form-tratamiento');
    if (formTrat) {
        formTrat.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = formTrat.querySelector('button[type="submit"]');
            btn.disabled = true;
            try {
                const resp = await fetch('{{ route("panel.odontologia.tratamientos.crear", $odontograma) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        numero_fdi: document.getElementById('odo-trat-pieza').value,
                        tipo_tratamiento: document.getElementById('odo-trat-tipo').value,
                        superficie: document.getElementById('odo-trat-superficie').value || null,
                        material: document.getElementById('odo-trat-material').value || null,
                        fecha_planificada: document.getElementById('odo-trat-fecha').value || null,
                    }),
                });
                if (!resp.ok) throw new Error();
                location.reload();
            } catch {
                btn.disabled = false;
            }
        });
    }
});
</script>
@endpush
