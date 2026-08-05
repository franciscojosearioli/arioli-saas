<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ya confirmado — Arioli.dev</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: #080d1a; color: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { width: 100%; max-width: 460px; background: #111827; border: 1px solid #1e2d45; border-radius: 16px; padding: 32px; text-align: center; }
        h1 { font-size: 20px; margin-bottom: 10px; }
        p { font-size: 13.5px; color: #94a3b8; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Ya confirmaste este mes</h1>
        <p>
            El mantenimiento de este mes ya está confirmado — si todavía no te llegó el mail con el backup, dale unos minutos más. Si tenés dudas, escribinos desde
            <a href="http://{{ config('app.landing_domain') }}/contacto" style="color:#3b82f6;">arioli.dev/contacto</a>.
        </p>
    </div>
</body>
</html>
