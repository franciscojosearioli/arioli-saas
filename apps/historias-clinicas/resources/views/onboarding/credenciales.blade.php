<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definí tu contraseña — Sistema de Salud</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 40%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.12);
            max-width: 440px;
            width: 100%;
            padding: 44px 40px;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #0ea5e9);
        }
        .app-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #2563eb;
            margin-bottom: 8px;
        }
        h1 { font-size: 24px; color: #1e3a8a; margin-bottom: 8px; letter-spacing: -.3px; }
        .subtitle { font-size: 14px; color: #64748b; margin-bottom: 28px; line-height: 1.5; }
        .error-box {
            background: #fef2f2;
            color: #b91c1c;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13.5px;
            margin-bottom: 20px;
        }
        label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14.5px;
            margin-bottom: 6px;
            font-family: inherit;
        }
        input:focus { outline: none; border-color: #2563eb; }
        .field-error { font-size: 12.5px; color: #dc2626; margin-bottom: 14px; }
        .field { margin-bottom: 16px; }
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 0;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="app-label">Sistema de Salud</div>
        <h1>Definí tu contraseña</h1>
        <p class="subtitle">Este es el último paso antes de entrar a tu sistema. Elegí una contraseña que solo vos vas a conocer.</p>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ url()->full() }}">
            @csrf
            <div class="field">
                <label for="password">Contraseña nueva</label>
                <input type="password" id="password" name="password" required minlength="8" autofocus>
                @error('password') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Repetir contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8">
            </div>
            <button type="submit" class="submit-btn">Guardar y entrar al sistema</button>
        </form>
    </div>
</body>
</html>
