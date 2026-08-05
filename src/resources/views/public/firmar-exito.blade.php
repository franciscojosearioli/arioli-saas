<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($rejected ?? false) ? 'Documento rechazado' : 'Firma registrada' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; color: #111827; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 36px; max-width: 440px; text-align: center; }
        .icon { width: 52px; height: 52px; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        p { font-size: 13.5px; color: #6b7280; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        @if($rejected ?? false)
            <div class="icon" style="background:#fee2e2; color:#dc2626;">✕</div>
            <h1>Documento rechazado</h1>
            <p>Registramos tu rechazo. El remitente fue notificado.</p>
        @else
            <div class="icon" style="background:#d1fae5; color:#059669;">✓</div>
            <h1>Firma registrada correctamente</h1>
            <p>Gracias, {{ $signer->name }}. Tu firma quedó registrada con fecha, hora e IP como evidencia.</p>
        @endif
    </div>
</body>
</html>
