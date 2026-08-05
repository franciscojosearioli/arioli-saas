<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu demo está lista — Sistema de Salud</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --font-sans: 'DM Sans', sans-serif;
            --font-mono: 'DM Mono', monospace;
            --bg: #080d1a;
            --card: #111827;
            --card-border: #1e2d45;
            --accent: #3b82f6;
            --text: #f1f5f9;
            --text2: #94a3b8;
            --text3: #475569;
            --success: #10b981;
        }
        body {
            font-family: var(--font-sans);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            max-width: 460px;
            width: 100%;
            padding: 36px;
            text-align: center;
        }
        .check-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(16,185,129,.25);
        }
        h1 { font-size: 21px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
        .subtitle { font-size: 13.5px; color: var(--text2); margin-bottom: 26px; }
        .info-box {
            background: rgba(255,255,255,.04);
            border: 1px solid var(--card-border);
            border-radius: 12px; padding: 18px; text-align: left; margin-bottom: 24px;
        }
        .info-row { margin-bottom: 14px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text3); margin-bottom: 3px; }
        .info-value { font-size: 14px; color: var(--text); font-family: var(--font-mono); word-break: break-all; }
        .btn-submit {
            display: block; width: 100%; padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; font-size: 15px; font-weight: 700;
            font-family: var(--font-sans); border: none; text-decoration: none;
            border-radius: 12px; cursor: pointer;
            box-shadow: 0 6px 24px rgba(59,130,246,.3);
            margin-bottom: 16px;
        }
        .expira-note { font-size: 12px; color: var(--text3); }
    </style>
</head>
<body>
    <div class="card">
        <div class="check-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1>Tu demo está lista</h1>
        <p class="subtitle">Ya podés ingresar con datos de ejemplo cargados.</p>

        <div class="info-box">
            <div class="info-row">
                <div class="info-label">URL</div>
                <div class="info-value">{{ $url }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Usuario</div>
                <div class="info-value">admin@admin.com</div>
            </div>
            <div class="info-row">
                <div class="info-label">Contraseña</div>
                <div class="info-value">password</div>
            </div>
        </div>

        <a href="{{ $url }}" class="btn-submit">Ingresar al sistema</a>

        @if ($demo?->expires_at)
            <p class="expira-note">Esta demo estará disponible hasta {{ $demo->expires_at->timezone(config('app.timezone'))->translatedFormat('d/m/Y H:i') }}.</p>
        @endif
    </div>
</body>
</html>
