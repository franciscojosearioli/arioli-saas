<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago pendiente — Arioli.dev</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #080d1a; color: #f1f5f9;
            min-height: 100vh; display: flex;
            align-items: center; justify-content: center; padding: 40px 20px;
        }
        .card {
            background: #111827; border: 1px solid #1e2d45;
            border-radius: 20px; padding: 48px; max-width: 520px;
            width: 100%; text-align: center;
        }
        .icon {
            width: 72px; height: 72px; border-radius: 50%;
            background: rgba(245,158,11,.15); color: #fbbf24;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px; font-size: 32px;
        }
        h1 { font-size: 26px; font-weight: 800; margin-bottom: 12px; }
        p  { font-size: 15px; color: #94a3b8; line-height: 1.7; margin-bottom: 32px; }
        .btn {
            display: inline-block; padding: 13px 28px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; font-size: 15px; font-weight: 700;
            text-decoration: none; border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⏳</div>
        <h1>Pago pendiente</h1>
        <p>
            Tu pago está siendo procesado.<br>
            Cuando se acredite vas a recibir un email con tus datos de acceso.<br>
            Esto puede demorar hasta 24 horas según el medio de pago.
        </p>
        <a href="http://{{ config('app.landing_domain') }}" class="btn">Volver al inicio</a>
    </div>
</body>
</html>