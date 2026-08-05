<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link no disponible</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; color: #111827; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 36px; max-width: 440px; text-align: center; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        p { font-size: 13.5px; color: #6b7280; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        @php
            $messages = [
                'not_found'        => 'Este link no es válido.',
                'expired'          => 'Este link venció. Pedile al remitente que te reenvíe la propuesta.',
                'already_accepted' => 'Esta propuesta ya fue aceptada — no hace falta hacer nada más.',
                'already_rejected' => 'Ya rechazaste esta propuesta previamente.',
            ];
        @endphp
        <h1>Link no disponible</h1>
        <p>{{ $messages[$reason] ?? 'No pudimos procesar tu solicitud.' }}</p>
    </div>
</body>
</html>
