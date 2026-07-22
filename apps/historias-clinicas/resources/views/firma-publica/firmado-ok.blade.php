<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma completada</title>
    <style>
        *, *::before, *::after { box-sizing:border-box;margin:0;padding:0; }
        html,body { height:100%;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f8fafc;color:#0f172a; }
        .page { min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px; }
        .card { background:#fff;border:1px solid #e2e8f0;border-radius:18px;box-shadow:0 4px 22px rgba(0,0,0,.06);padding:40px 36px;max-width:460px;width:100%;text-align:center; }
        .icon { width:72px;height:72px;border-radius:50%;background:#f0fdf4;border:2px solid #bbf7d0;display:flex;align-items:center;justify-content:center;margin:0 auto 20px; }
        .icon svg { width:34px;height:34px;color:#16a34a; }
        h1 { font-size:20px;font-weight:700;color:#16a34a;margin-bottom:10px; }
        p { font-size:14px;color:#64748b;line-height:1.65;margin-bottom:8px; }
        .doc-name { font-size:15px;font-weight:600;color:#0f172a;margin:14px 0; }
    </style>
</head>
<body>
<div class="page">
    <div class="card">
        <div class="icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1>¡Firma registrada!</h1>
        <p>Tu firma fue guardada correctamente para el documento:</p>
        <p class="doc-name">{{ $consentimiento->tipo->nombre }}</p>
        <p>
            <strong>{{ $consentimiento->paciente->apellido }}, {{ $consentimiento->paciente->nombre }}</strong><br>
            <span style="font-size:12px;">Firmado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</span>
        </p>
        <p style="margin-top:16px;font-size:12px;color:#94a3b8;">Podés cerrar esta página.</p>
    </div>
</div>
</body>
</html>
