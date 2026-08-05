<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Probá Sistema de Salud — Demo gratis</title>
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
            padding: 56px 20px 64px;
        }
        .wrap { max-width: 980px; margin: 0 auto; }
        .logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; margin-bottom: 48px; justify-content: center;
        }
        .logo-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center;
        }
        .logo-text { font-size: 16px; font-weight: 700; color: var(--text); }
        .logo-text span { color: var(--accent); }
        .hero { text-align: center; margin-bottom: 48px; }
        .eyebrow {
            font-size: 12px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
            color: var(--accent); margin-bottom: 12px;
        }
        h1 { font-size: 34px; font-weight: 800; letter-spacing: -.02em; margin-bottom: 12px; }
        .subtitle { font-size: 15px; color: var(--text2); max-width: 560px; margin: 0 auto; }
        .perfiles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }
        .perfil-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 28px;
            text-decoration: none;
            display: block;
            transition: all .2s;
        }
        .perfil-card:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(59,130,246,.15);
        }
        .perfil-nombre { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
        .perfil-desc { font-size: 13.5px; color: var(--text2); line-height: 1.5; margin-bottom: 18px; min-height: 40px; }
        .perfil-features { list-style: none; margin-bottom: 22px; }
        .perfil-features li {
            font-size: 12.5px; color: var(--text2); padding: 4px 0 4px 20px; position: relative;
        }
        .perfil-features li::before {
            content: '✓'; position: absolute; left: 0; color: var(--success); font-weight: 700;
        }
        .perfil-cta { font-size: 13px; font-weight: 600; color: var(--accent); }
    </style>
</head>
<body>
    <div class="wrap">
        <a href="https://arioli.dev" class="logo">
            <div class="logo-icon">
                <svg width="18" height="18" fill="none" stroke="#fff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="logo-text">Arioli<span>.dev</span></span>
        </a>

        <div class="hero">
            <div class="eyebrow">Sistema de Salud</div>
            <h1>Elegí qué querés probar</h1>
            <p class="subtitle">Preparamos una demo con datos de ejemplo, lista para usar en unos segundos — sin tarjeta, sin compromiso.</p>
        </div>

        <div class="perfiles">
            @foreach ($perfiles as $perfilKey => $perfil)
                <a href="{{ route('demo.publico.solicitar', ['perfil' => $perfilKey]) }}" class="perfil-card">
                    <div class="perfil-nombre">{{ $perfil->nombre }}</div>
                    <div class="perfil-desc">{{ $perfil->descripcion }}</div>
                    @if (!empty($perfil->caracteristicas))
                        <ul class="perfil-features">
                            @foreach (array_slice($perfil->caracteristicas, 0, 4) as $caracteristica)
                                <li>{{ $caracteristica }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="perfil-cta">Probar este perfil →</div>
                </a>
            @endforeach
        </div>
    </div>
</body>
</html>
