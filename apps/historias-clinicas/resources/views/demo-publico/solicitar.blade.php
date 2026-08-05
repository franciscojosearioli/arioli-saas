<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $perfil->nombre }} — Demo — Sistema de Salud</title>
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
            --danger: #f87171;
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
        .checkout-wrap {
            width: 100%;
            max-width: 920px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            align-items: start;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 32px;
        }
        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; margin-bottom: 24px; }
        .logo-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center;
        }
        .logo-text { font-size: 16px; font-weight: 700; color: var(--text); }
        .logo-text span { color: var(--accent); }
        .back-link { display: inline-block; font-size: 13px; color: var(--text3); text-decoration: none; margin-bottom: 20px; }
        .back-link:hover { color: var(--accent); }
        .plan-name { font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 8px; }
        .plan-sub { font-size: 14px; color: var(--text2); margin-bottom: 24px; }
        .perfil-features { list-style: none; }
        .perfil-features li {
            font-size: 13.5px; color: var(--text2); padding: 8px 0 8px 24px; position: relative;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .perfil-features li:last-child { border-bottom: none; }
        .perfil-features li::before {
            content: '✓'; position: absolute; left: 0; color: var(--success); font-weight: 700;
        }
        .form-title { font-size: 20px; font-weight: 700; color: var(--text); margin-bottom: 24px; }
        .error-box {
            background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.3);
            border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13.5px; color: var(--danger);
        }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--text2); margin-bottom: 7px; }
        .form-input {
            width: 100%; padding: 11px 14px;
            background: rgba(255,255,255,.05);
            border: 1.5px solid var(--card-border);
            border-radius: 10px; font-size: 14px;
            font-family: var(--font-sans); color: var(--text);
            outline: none; transition: all .2s;
        }
        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
            background: rgba(59,130,246,.05);
        }
        .form-input::placeholder { color: var(--text3); }
        .field-error { font-size: 12px; color: var(--danger); margin-top: 5px; }
        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; font-size: 15px; font-weight: 700;
            font-family: var(--font-sans); border: none;
            border-radius: 12px; cursor: pointer;
            box-shadow: 0 6px 24px rgba(59,130,246,.3);
            transition: all .2s; margin-top: 8px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 10px 32px rgba(59,130,246,.4); }
        .wait-note { font-size: 12px; color: var(--text3); text-align: center; margin-top: 14px; }
        @media (max-width: 768px) { .checkout-wrap { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="checkout-wrap">

    <div class="card">
        <a href="https://arioli.dev" class="logo">
            <div class="logo-icon">
                <svg width="18" height="18" fill="none" stroke="#fff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="logo-text">Arioli<span>.dev</span></span>
        </a>
        <a href="{{ route('demo.publico.index') }}" class="back-link">← Elegir otro perfil</a>

        <div class="plan-name">{{ $perfil->nombre }}</div>
        <div class="plan-sub">{{ $perfil->descripcion }}</div>

        @if (!empty($perfil->caracteristicas))
            <ul class="perfil-features">
                @foreach ($perfil->caracteristicas as $caracteristica)
                    <li>{{ $caracteristica }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="card">
        <div class="form-title">Completá tus datos</div>

        @if (session('demo_error'))
            <div class="error-box">{{ session('demo_error') }}</div>
        @endif

        <form method="POST" action="{{ route('demo.publico.crear', ['perfil' => $perfilKey]) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-input" value="{{ old('nombre') }}" placeholder="Juan Pérez" required maxlength="150" autofocus>
                @error('nombre') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="juan@empresa.com" required maxlength="190">
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn-submit">Crear mi demo →</button>
            <p class="wait-note">Puede tardar unos segundos mientras preparamos todo.</p>
        </form>
    </div>

</div>
</body>
</html>
