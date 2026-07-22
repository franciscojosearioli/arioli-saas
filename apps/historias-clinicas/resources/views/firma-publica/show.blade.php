<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Firma de Consentimiento</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --accent:#1d4ed8; --green:#16a34a; --green-lt:#f0fdf4; --border:#e2e8f0; --text:#0f172a; --muted:#94a3b8; }
        html,body { height:100%; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:#f8fafc; color:var(--text); }
        .page { min-height:100vh; display:flex; flex-direction:column; align-items:center; padding:24px 16px 40px; }
        .hdr { width:100%;max-width:700px; display:flex;align-items:center;justify-content:space-between; margin-bottom:24px;flex-wrap:wrap;gap:10px; }
        .hdr-title { font-size:17px;font-weight:800;color:var(--accent);letter-spacing:-.02em; }
        .badge-email { display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:99px;background:#eff6ff;color:var(--accent);font-size:12px;font-weight:700;border:1px solid #bfdbfe; }
        .card { width:100%;max-width:700px;background:#fff;border:1px solid var(--border);border-radius:18px;box-shadow:0 4px 22px rgba(0,0,0,.06);overflow:hidden; }
        .sec { padding:20px 26px;border-bottom:1px solid var(--border); }
        .sec:last-child { border-bottom:none; }
        .sec-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px; }
        .doc-content { font-size:13px;line-height:1.75;color:#374151;max-height:280px;overflow-y:auto;padding:14px 16px;background:#f8fafc;border-radius:8px;border:1px solid var(--border); }
        .canvas-wrap { border:2px dashed var(--border);border-radius:10px;background:#fff;overflow:hidden;position:relative;touch-action:none; }
        .canvas-wrap.has-sig { border-color:var(--accent);border-style:solid; }
        #sign-canvas { display:block;width:100%;cursor:crosshair;touch-action:none; }
        .canvas-hint { position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:13px;color:var(--muted);pointer-events:none;text-align:center; }
        .canvas-hint svg { width:26px;height:26px;display:block;margin:0 auto 5px;opacity:.4; }
        .btn-clear { margin-top:8px;display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;border:1px solid var(--border);background:#fff;cursor:pointer;color:#64748b; }
        .btn-confirm { display:inline-flex;align-items:center;gap:7px;padding:12px 26px;border-radius:9px;font-size:14px;font-weight:700;background:var(--green);color:#fff;border:none;cursor:pointer;font-family:inherit;box-shadow:0 2px 8px rgba(22,163,74,.28); }
        .btn-confirm:disabled { background:#94a3b8;cursor:not-allowed;box-shadow:none; }
        .expiry-note { font-size:12px;color:#b45309;background:#fef9c3;padding:10px 14px;border-radius:8px;border:1px solid #fde68a;margin-top:10px; }
    </style>
</head>
<body>
<div class="page">
    <div class="hdr">
        <span class="hdr-title">Firma de Consentimiento Informado</span>
        <span class="badge-email">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Firma por correo
        </span>
    </div>

    <div class="card">
        {{-- Patient --}}
        <div class="sec" style="background:#f8fafc;">
            <div class="sec-label">Paciente</div>
            <strong style="font-size:16px;">{{ $consentimiento->paciente->apellido }}, {{ $consentimiento->paciente->nombre }}</strong>
            <p style="font-size:13px;color:var(--muted);">DNI: {{ $consentimiento->paciente->dni ?? '—' }}</p>
        </div>

        {{-- Document --}}
        <div class="sec">
            <div class="sec-label">Documento</div>
            <p style="font-size:15px;font-weight:600;margin-bottom:10px;">{{ $consentimiento->tipo->nombre }}</p>
            <div class="doc-content">{!! $consentimiento->tipo->contenido_pagina1 !!}</div>
            @if($consentimiento->tipo->contenido_pagina2)
            <div class="doc-content" style="margin-top:10px;">{!! $consentimiento->tipo->contenido_pagina2 !!}</div>
            @endif
        </div>

        {{-- Canvas --}}
        <div class="sec">
            <div class="sec-label">Tu firma</div>
            <div class="canvas-wrap" id="canvas-wrap">
                <canvas id="sign-canvas" height="160"></canvas>
                <div class="canvas-hint" id="canvas-hint">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Tocá o dibujá aquí
                </div>
            </div>
            <button type="button" class="btn-clear" id="btn-clear" style="display:none;" onclick="clearCanvas()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Limpiar firma
            </button>
            <div class="expiry-note">
                Este enlace vence el <strong>{{ $consentimiento->token_expires_at?->format('d/m/Y \a \l\a\s H:i') }}</strong>.
            </div>
        </div>

        {{-- Submit --}}
        <form method="POST" action="{{ route('consentimiento.guardarFirmaPublica', $consentimiento->token) }}" id="sign-form">
            @csrf
            <input type="hidden" name="firma_canvas_data" id="firma-data">
            <div style="padding:18px 26px;display:flex;justify-content:flex-end;">
                <button type="submit" class="btn-confirm" id="btn-confirm" disabled onclick="return prepareSubmit()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Firmar y enviar
                </button>
            </div>
        </form>
    </div>
</div>
<script>
(function(){
    const canvas=document.getElementById('sign-canvas'),wrap=document.getElementById('canvas-wrap'),
          hint=document.getElementById('canvas-hint'),btnClear=document.getElementById('btn-clear'),
          btnConf=document.getElementById('btn-confirm'),ctx=canvas.getContext('2d');
    let drawing=false,hasSig=false;
    function resize(){const w=wrap.clientWidth;canvas.width=w;canvas.height=160;ctx.strokeStyle='#1a1a1a';ctx.lineWidth=2.2;ctx.lineCap='round';ctx.lineJoin='round';}
    resize(); window.addEventListener('resize',resize);
    function pos(e){const r=canvas.getBoundingClientRect(),s=e.touches?e.touches[0]:e;return{x:s.clientX-r.left,y:s.clientY-r.top};}
    function start(e){e.preventDefault();drawing=true;const{x,y}=pos(e);ctx.beginPath();ctx.moveTo(x,y);}
    function move(e){if(!drawing)return;e.preventDefault();const{x,y}=pos(e);ctx.lineTo(x,y);ctx.stroke();if(!hasSig){hasSig=true;hint.style.display='none';btnClear.style.display='';wrap.classList.add('has-sig');if(btnConf)btnConf.disabled=false;}}
    function end(){drawing=false;}
    canvas.addEventListener('mousedown',start);canvas.addEventListener('mousemove',move);canvas.addEventListener('mouseup',end);canvas.addEventListener('mouseleave',end);
    canvas.addEventListener('touchstart',start,{passive:false});canvas.addEventListener('touchmove',move,{passive:false});canvas.addEventListener('touchend',end);
    window.clearCanvas=function(){ctx.clearRect(0,0,canvas.width,canvas.height);hasSig=false;hint.style.display='block';btnClear.style.display='none';wrap.classList.remove('has-sig');if(btnConf)btnConf.disabled=true;document.getElementById('firma-data').value='';};
    window.prepareSubmit=function(){if(!hasSig)return false;document.getElementById('firma-data').value=canvas.toDataURL('image/png');return true;};
})();
</script>
</body>
</html>
