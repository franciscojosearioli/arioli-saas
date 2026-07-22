<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Firma — {{ $consentimiento->tipo->nombre }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --accent:#1d4ed8; --green:#16a34a; --green-lt:#f0fdf4; --border:#e2e8f0; --text:#0f172a; --muted:#94a3b8; }
        html,body { height:100%; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:#f8fafc; color:var(--text); }
        .page { min-height:100vh; display:flex; flex-direction:column; align-items:center; padding:24px 16px 40px; }
        .hdr { width:100%;max-width:680px; display:flex;align-items:center;justify-content:space-between; margin-bottom:24px;flex-wrap:wrap;gap:10px; }
        .hdr-title { font-size:17px;font-weight:800;color:var(--accent);letter-spacing:-.02em; }
        .badge-presencial { display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:99px;background:var(--green-lt);color:var(--green);font-size:12px;font-weight:700;border:1px solid #bbf7d0; }
        .card { width:100%;max-width:680px;background:#fff;border:1px solid var(--border);border-radius:18px;box-shadow:0 4px 22px rgba(0,0,0,.06);overflow:hidden; }
        .card-patient { padding:20px 26px;border-bottom:1px solid var(--border);background:#f8fafc; }
        .card-patient h2 { font-size:18px;font-weight:700;margin-bottom:3px; }
        .card-patient p { font-size:13px;color:var(--muted); }
        .card-tipo { padding:16px 26px;border-bottom:1px solid var(--border); }
        .card-tipo h3 { font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:6px; }
        .card-tipo p { font-size:14px;font-weight:600; }
        .card-canvas { padding:22px 26px;border-bottom:1px solid var(--border); }
        .card-canvas h3 { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:10px; }
        .canvas-wrap { border:2px dashed var(--border);border-radius:10px;background:#fff;overflow:hidden;position:relative;touch-action:none; }
        .canvas-wrap.has-sig { border-color:var(--accent);border-style:solid; }
        #sign-canvas { display:block;width:100%;cursor:crosshair;touch-action:none; }
        .canvas-hint { position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:13px;color:var(--muted);pointer-events:none;text-align:center; }
        .canvas-hint svg { width:26px;height:26px;display:block;margin:0 auto 5px;opacity:.4; }
        .btn-clear { margin-top:8px;display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;border:1px solid var(--border);background:#fff;cursor:pointer;color:#64748b; }
        .btn-clear:hover { border-color:#dc2626;color:#dc2626; }
        .actions { padding:18px 26px;display:flex;gap:10px;align-items:center;justify-content:flex-end;flex-wrap:wrap; }
        .btn-cancel { display:inline-flex;align-items:center;padding:10px 18px;border-radius:9px;font-size:13px;font-weight:600;border:1px solid var(--border);background:#fff;text-decoration:none;color:#64748b; }
        .btn-confirm { display:inline-flex;align-items:center;gap:7px;padding:11px 24px;border-radius:9px;font-size:14px;font-weight:700;background:var(--green);color:#fff;border:none;cursor:pointer;font-family:inherit;box-shadow:0 2px 8px rgba(22,163,74,.28); }
        .btn-confirm:disabled { background:#94a3b8;cursor:not-allowed;box-shadow:none; }
    </style>
</head>
<body>
<div class="page">
    <div class="hdr">
        <span class="hdr-title">Firma de documento</span>
        <span class="badge-presencial">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Firma presencial
        </span>
    </div>

    <div class="card">
        <div class="card-patient">
            <h2>{{ $consentimiento->paciente->apellido }}, {{ $consentimiento->paciente->nombre }}</h2>
            <p>DNI: {{ $consentimiento->paciente->dni ?? '—' }}
               @if($consentimiento->paciente->fecha_nac) · {{ \Carbon\Carbon::parse($consentimiento->paciente->fecha_nac)->age }} años @endif
            </p>
        </div>
        <div class="card-tipo">
            <h3>Documento a firmar</h3>
            <p>{{ $consentimiento->tipo->nombre }}</p>
            @if($consentimiento->tipo->descripcion)
            <p style="font-size:13px;color:#64748b;font-weight:400;margin-top:3px;">{{ $consentimiento->tipo->descripcion }}</p>
            @endif
        </div>
        <div class="card-canvas">
            <h3>Dibujá tu firma</h3>
            <div class="canvas-wrap" id="canvas-wrap">
                <canvas id="sign-canvas" height="150"></canvas>
                <div class="canvas-hint" id="canvas-hint">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Tocá o dibujá aquí
                </div>
            </div>
            <button type="button" class="btn-clear" id="btn-clear" style="display:none;" onclick="clearCanvas()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Limpiar
            </button>
        </div>

        <form method="POST" action="{{ route('panel.consentimiento.guardarFirmaPresencial', $consentimiento->id) }}" id="sign-form">
            @csrf
            <input type="hidden" name="firma_canvas_data" id="firma-data">
            <div class="actions">
                <a href="{{ route('panel.paciente.show', $consentimiento->paciente_id) }}#consentimientos" class="btn-cancel">Cancelar</a>
                <button type="submit" class="btn-confirm" id="btn-confirm" disabled onclick="return prepareSubmit()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Confirmar firma
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
    function resize(){const w=wrap.clientWidth;canvas.width=w;canvas.height=150;ctx.strokeStyle='#1a1a1a';ctx.lineWidth=2.2;ctx.lineCap='round';ctx.lineJoin='round';}
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
