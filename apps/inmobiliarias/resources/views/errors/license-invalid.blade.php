<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Licencia no válida — {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: system-ui, sans-serif; background: #f8fafc; color: #1e293b; display: flex; min-height: 100vh; align-items: center; justify-content: center; margin: 0; }
        .box { max-width: 28rem; padding: 2rem; text-align: center; }
        h1 { font-size: 1.25rem; margin-bottom: .5rem; }
        p { color: #64748b; }
    </style>
</head>
<body>
    <div class="box">
        <h1>La licencia de esta cuenta no está activa</h1>
        <p>Motivo: {{ $reason ?? 'desconocido' }}. Contactá a soporte para regularizar el acceso.</p>
    </div>
</body>
</html>
