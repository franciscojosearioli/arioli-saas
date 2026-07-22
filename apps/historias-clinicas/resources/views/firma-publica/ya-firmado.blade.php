<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ya firmado</title>
    <style>*, *::before, *::after { box-sizing:border-box;margin:0;padding:0; }html,body{height:100%;font-family:-apple-system,'Segoe UI',sans-serif;background:#f8fafc;}
    .page{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}.card{background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:40px 36px;max-width:440px;width:100%;text-align:center;}
    .icon{width:64px;height:64px;border-radius:50%;background:#eff6ff;border:2px solid #bfdbfe;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;}.icon svg{width:30px;height:30px;color:#1d4ed8;}h1{font-size:18px;font-weight:700;color:#1d4ed8;margin-bottom:8px;}p{font-size:13px;color:#64748b;line-height:1.65;}</style>
</head>
<body>
<div class="page"><div class="card">
    <div class="icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
    <h1>Este documento ya fue firmado</h1>
    <p>El consentimiento <strong>{{ $consentimiento->tipo->nombre }}</strong> de <strong>{{ $consentimiento->paciente->nombre }} {{ $consentimiento->paciente->apellido }}</strong> ya fue firmado el {{ $consentimiento->firmado_paciente_at?->format('d/m/Y') }}. Podés cerrar esta página.</p>
</div></div>
</body></html>
