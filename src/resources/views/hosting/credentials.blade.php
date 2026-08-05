<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Definir contraseña — Arioli.dev</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: #080d1a; color: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { width: 100%; max-width: 440px; background: #111827; border: 1px solid #1e2d45; border-radius: 16px; padding: 32px; }
        h1 { font-size: 20px; margin-bottom: 8px; }
        p { font-size: 13.5px; color: #94a3b8; margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; }
        .form-input { width: 100%; padding: 11px 14px; background: rgba(255,255,255,.05); border: 1.5px solid #1e2d45; border-radius: 10px; font-size: 14px; color: #f1f5f9; outline: none; margin-bottom: 16px; }
        .btn { width: 100%; padding: 13px; background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; font-weight: 700; border: none; border-radius: 10px; cursor: pointer; }
        .error { font-size: 12px; color: #f87171; margin-top: -10px; margin-bottom: 14px; }
        .username { font-family: 'DM Mono', monospace; background: rgba(255,255,255,.06); padding: 3px 8px; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Definí tu contraseña</h1>
        <p>Esta contraseña te va a servir para entrar a tu <strong>Panel de Cliente</strong> en cliente.arioli.dev, donde vas a poder ver tu hosting, facturación y soporte.</p>

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="error">{{ $error }}</div>
            @endforeach
        @endif

        <form method="POST" action="{{ url()->full() }}">
            @csrf
            <label class="form-label">Contraseña nueva</label>
            <input type="password" name="password" class="form-input" minlength="8" required>
            <label class="form-label">Repetir contraseña</label>
            <input type="password" name="password_confirmation" class="form-input" minlength="8" required>
            <button type="submit" class="btn">Guardar contraseña</button>
        </form>
    </div>
</body>
</html>
