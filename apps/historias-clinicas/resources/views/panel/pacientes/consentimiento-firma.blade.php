<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Firma de Consentimiento — {{ $paciente->apellido }}, {{ $paciente->nombre }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent: #1d4ed8;
            --accent-hover: #1e40af;
            --green: #16a34a;
            --green-lt: #f0fdf4;
            --card-border: #e2e8f0;
            --text-primary: #0f172a;
            --text-muted: #94a3b8;
        }

        html, body {
            height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc; color: var(--text-primary);
        }

        .page {
            min-height: 100vh; display: flex; flex-direction: column;
            align-items: center; padding: 24px 16px 40px;
        }

        /* ── Header ── */
        .sign-header {
            width: 100%; max-width: 700px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px; flex-wrap: wrap; gap: 10px;
        }
        .sign-logo { font-size: 18px; font-weight: 800; color: var(--accent); letter-spacing: -.03em; }
        .sign-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 99px;
            background: var(--green-lt); color: var(--green);
            font-size: 12px; font-weight: 700; border: 1px solid #bbf7d0;
        }

        /* ── Card ── */
        .sign-card {
            width: 100%; max-width: 700px;
            background: #fff; border: 1px solid var(--card-border);
            border-radius: 20px; box-shadow: 0 4px 24px rgba(0,0,0,.07);
            overflow: hidden;
        }

        /* ── Patient info ── */
        .sign-patient {
            padding: 24px 28px; border-bottom: 1px solid var(--card-border);
            background: #f8fafc;
        }
        .sign-patient h2 {
            font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;
        }
        .sign-patient p { font-size: 13px; color: var(--text-muted); }

        /* ── Consent summary ── */
        .sign-summary {
            padding: 22px 28px; border-bottom: 1px solid var(--card-border);
            font-size: 13px; line-height: 1.7; color: #374151;
        }
        .sign-summary h3 {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .08em; color: var(--text-muted); margin-bottom: 10px;
        }
        .sign-summary ol { margin-left: 18px; }
        .sign-summary li { margin-bottom: 5px; }

        /* ── Signature canvas area ── */
        .sign-canvas-area { padding: 24px 28px; border-bottom: 1px solid var(--card-border); }
        .sign-canvas-area h3 {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .08em; color: var(--text-muted); margin-bottom: 12px;
        }
        .canvas-wrap {
            border: 2px dashed var(--card-border); border-radius: 12px;
            background: #fff; overflow: hidden; position: relative;
            touch-action: none;
        }
        .canvas-wrap.has-sig { border-color: var(--accent); border-style: solid; }
        #sign-canvas {
            display: block; width: 100%; cursor: crosshair;
            touch-action: none;
        }
        .canvas-hint {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
            font-size: 13px; color: var(--text-muted); pointer-events: none;
            text-align: center;
        }
        .canvas-hint svg { width: 28px; height: 28px; display: block; margin: 0 auto 6px; opacity: .4; }
        .btn-clear-canvas {
            margin-top: 10px; display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
            border: 1px solid var(--card-border); background: #fff; cursor: pointer;
            color: #64748b; transition: all .12s;
        }
        .btn-clear-canvas:hover { border-color: #dc2626; color: #dc2626; }
        .btn-clear-canvas svg { width: 12px; height: 12px; }

        /* ── Already signed ── */
        .sign-already {
            padding: 28px; text-align: center;
        }
        .sign-already-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: var(--green-lt); border: 2px solid #bbf7d0;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .sign-already-icon svg { width: 30px; height: 30px; color: var(--green); }
        .sign-already h3 { font-size: 17px; font-weight: 700; color: var(--green); margin-bottom: 6px; }
        .sign-already p { font-size: 13px; color: var(--text-muted); }

        /* ── Footer actions ── */
        .sign-actions {
            padding: 20px 28px; display: flex; gap: 12px; align-items: center;
            justify-content: flex-end; flex-wrap: wrap;
        }
        .btn-cancel {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600;
            border: 1px solid var(--card-border); background: #fff; text-decoration: none;
            color: #64748b; transition: all .12s;
        }
        .btn-cancel:hover { border-color: var(--accent); color: var(--accent); }
        .btn-confirm {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 11px 26px; border-radius: 10px; font-size: 14px; font-weight: 700;
            background: var(--green); color: #fff; border: none; cursor: pointer;
            font-family: inherit; transition: background .12s;
            box-shadow: 0 2px 10px rgba(22,163,74,.3);
        }
        .btn-confirm:hover { background: #15803d; }
        .btn-confirm:disabled { background: #94a3b8; cursor: not-allowed; box-shadow: none; }
        .btn-confirm svg { width: 16px; height: 16px; }
    </style>
</head>
<body>

<div class="page">

    <div class="sign-header">
        <span class="sign-logo">Firma de Consentimiento</span>
        <span class="sign-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Sesión presencial
        </span>
    </div>

    <div class="sign-card">

        {{-- Patient info --}}
        <div class="sign-patient">
            <h2>{{ $paciente->apellido }}, {{ $paciente->nombre }}</h2>
            <p>DNI: {{ $paciente->dni ?? '—' }}
               @if($paciente->fecha_nac) &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($paciente->fecha_nac)->age }} años @endif
            </p>
        </div>

        @if($paciente->consentimiento_firmado)
        {{-- Already signed --}}
        <div class="sign-already">
            <div class="sign-already-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3>Consentimiento ya firmado</h3>
            <p>
                Este paciente firmó el consentimiento el
                <strong>{{ \Carbon\Carbon::parse($paciente->consentimiento_firmado_at)->format('d/m/Y \a \l\a\s H:i') }}</strong>.
            </p>
            <div style="margin-top:20px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                @if($paciente->consentimiento_firma_imagen && \Illuminate\Support\Facades\Storage::disk('public')->exists($paciente->consentimiento_firma_imagen))
                <img src="{{ asset('storage/' . $paciente->consentimiento_firma_imagen) }}"
                     alt="Firma"
                     style="max-height:70px;border:1px solid var(--card-border);border-radius:8px;padding:6px;">
                @endif
            </div>
        </div>
        <div class="sign-actions">
            <a href="{{ route('panel.paciente.show', $paciente->id) }}" class="btn-cancel">Volver al paciente</a>
            <a href="{{ route('panel.paciente.consentimientoPaciente', $paciente->id) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:7px;padding:11px 22px;border-radius:10px;font-size:13px;font-weight:700;background:var(--accent);color:#fff;text-decoration:none;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Ver PDF firmado
            </a>
        </div>

        @else
        {{-- Consent summary --}}
        <div class="sign-summary">
            <h3>Lo que estás firmando</h3>
            <ol>
                <li>La naturaleza del tratamiento propuesto, sus objetivos, metodología y duración estimada.</li>
                <li>Los posibles beneficios, riesgos y alternativas disponibles.</li>
                <li>Mi derecho a retirar este consentimiento en cualquier momento, sin que ello afecte la calidad de la atención.</li>
                <li>La confidencialidad de mis datos personales y la información clínica, conforme a la normativa vigente.</li>
                <li>El reglamento interno de la institución, cuyas normas me comprometo a conocer y respetar.</li>
            </ol>
        </div>

        {{-- Canvas --}}
        <div class="sign-canvas-area">
            <h3>Dibujá tu firma aquí</h3>
            <div class="canvas-wrap" id="canvas-wrap">
                <canvas id="sign-canvas" height="160"></canvas>
                <div class="canvas-hint" id="canvas-hint">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Tocá o dibujá aquí
                </div>
            </div>
            <button type="button" class="btn-clear-canvas" id="btn-clear" style="display:none;" onclick="clearCanvas()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar firma
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('panel.paciente.guardarConsentimiento', $paciente->id) }}" id="sign-form">
            @csrf
            <input type="hidden" name="firma_canvas_data" id="firma-data">

            <div class="sign-actions">
                <a href="{{ route('panel.paciente.show', $paciente->id) }}" class="btn-cancel">Cancelar</a>
                <button type="submit" class="btn-confirm" id="btn-confirm" disabled onclick="return prepareSubmit()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Confirmar y guardar firma
                </button>
            </div>
        </form>
        @endif

    </div>

</div>

<script>
(function() {
    const canvas  = document.getElementById('sign-canvas');
    if (!canvas) return;

    const wrap    = document.getElementById('canvas-wrap');
    const hint    = document.getElementById('canvas-hint');
    const btnClear= document.getElementById('btn-clear');
    const btnConf = document.getElementById('btn-confirm');
    const ctx     = canvas.getContext('2d');

    let drawing = false, hasSig = false;

    function resizeCanvas() {
        const w = wrap.clientWidth;
        canvas.width  = w;
        canvas.height = 160;
        ctx.strokeStyle = '#1a1a1a';
        ctx.lineWidth   = 2.2;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    function getPos(e) {
        const r = canvas.getBoundingClientRect();
        const src = e.touches ? e.touches[0] : e;
        return { x: src.clientX - r.left, y: src.clientY - r.top };
    }

    function startDraw(e) {
        e.preventDefault();
        drawing = true;
        const { x, y } = getPos(e);
        ctx.beginPath();
        ctx.moveTo(x, y);
    }
    function draw(e) {
        if (!drawing) return;
        e.preventDefault();
        const { x, y } = getPos(e);
        ctx.lineTo(x, y);
        ctx.stroke();
        if (!hasSig) {
            hasSig = true;
            hint.style.display   = 'none';
            btnClear.style.display = '';
            wrap.classList.add('has-sig');
            if (btnConf) btnConf.disabled = false;
        }
    }
    function endDraw(e) { drawing = false; }

    canvas.addEventListener('mousedown',  startDraw);
    canvas.addEventListener('mousemove',  draw);
    canvas.addEventListener('mouseup',    endDraw);
    canvas.addEventListener('mouseleave', endDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove',  draw,      { passive: false });
    canvas.addEventListener('touchend',   endDraw);

    window.clearCanvas = function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSig = false;
        hint.style.display    = 'block';
        btnClear.style.display= 'none';
        wrap.classList.remove('has-sig');
        if (btnConf) btnConf.disabled = true;
        document.getElementById('firma-data').value = '';
    };

    window.prepareSubmit = function() {
        if (!hasSig) return false;
        document.getElementById('firma-data').value = canvas.toDataURL('image/png');
        return true;
    };
})();
</script>

</body>
</html>
