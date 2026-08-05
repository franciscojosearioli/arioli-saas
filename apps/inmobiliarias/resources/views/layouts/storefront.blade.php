<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $configuracion->nombre_comercial ?? config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #0f172a;
            --bg2:      #1e293b;
            --bg3:      #253044;
            --border:   rgba(255,255,255,.07);
            --text:     #f1f5f9;
            --text2:    #94a3b8;
            --text3:    #64748b;
            --accent:   #1d4ed8;
            --accent2:  #1e40af;
            --nav-h:    60px;
        }

        html, body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }

        /* ── NAVBAR ── */
        .navbar {
            position: sticky; top: 0; z-index: 300;
            height: var(--nav-h);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px;
            background: rgba(15,23,42,.9);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }
        .navbar-brand { display: flex; align-items: center; gap: 10px; flex-shrink: 0; height: 100%; }
        .navbar-brand img { height: 34px; width: auto; object-fit: contain; border-radius: 6px; }
        .navbar-brand-name { font-size: 16px; font-weight: 700; color: var(--text); letter-spacing: -.02em; white-space: nowrap; }
        .navbar-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; height: 100%; }

        .btn-ghost {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 500; font-family: 'DM Sans', sans-serif;
            background: rgba(255,255,255,.07); color: var(--text2);
            border: 1px solid var(--border);
            transition: background .15s, color .15s; white-space: nowrap;
            cursor: pointer;
        }
        .btn-ghost:hover { background: rgba(255,255,255,.13); color: var(--text); }

        .btn-primary { display: inline-flex; align-items: center; gap: 6px; padding: 7px 18px; border-radius: 8px;
            font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif;
            background: var(--accent); color: #fff; border: none;
            transition: background .15s, transform .12s; white-space: nowrap;
            box-shadow: 0 2px 10px rgba(29,78,216,.4); cursor: pointer;
        }
        .btn-primary:hover { background: var(--accent2); transform: translateY(-1px); }

        /* ── PAGE ── */
        .page { max-width: 1180px; margin: 0 auto; padding: 40px 24px 80px; }
        .eyebrow { font-size: 11px; font-weight: 700; color: var(--accent-label, #60a5fa); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 8px; }
        h1.title { font-size: 30px; font-weight: 700; letter-spacing: -.02em; margin-bottom: 6px; text-wrap: balance; }
        .sub { color: var(--text3); font-size: 15px; margin-bottom: 28px; }

        /* ── FOOTER ── */
        .page-footer { border-top: 1px solid var(--bg2); padding: 20px 24px; text-align: center; font-size: 12px; color: var(--text3); }
        .page-footer a { color: var(--text2); }
        .page-footer a:hover { color: var(--text); }

        @media (max-width: 600px) {
            .navbar { padding: 0 16px; }
            .navbar-brand-name { font-size: 14px; }
            .page { padding: 28px 16px 60px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar">
    <a href="{{ route('storefront.index') }}" class="navbar-brand">
        @if ($configuracion->logo_url)
            <img src="{{ $configuracion->logo_url }}" alt="{{ $configuracion->nombre_comercial ?? config('app.name') }}">
        @else
            <span class="navbar-brand-name">{{ $configuracion->nombre_comercial ?? config('app.name') }}</span>
        @endif
    </a>

    <div class="navbar-actions">
        @auth
            <a href="{{ route('dashboard') }}" class="btn-ghost">Mi panel</a>
        @else
            <a href="{{ route('login') }}" class="btn-ghost">Ingresar</a>
            <a href="{{ route('register') }}" class="btn-primary">Registrarse</a>
        @endauth
    </div>
</nav>

<div class="page">
    @yield('content')
</div>

<footer class="page-footer">
    @if ($configuracion->nombre_comercial)
        {{ $configuracion->nombre_comercial }} —
    @endif
    Powered by <a href="https://arioli.dev" target="_blank" rel="noopener">Arioli.dev</a>
</footer>

@stack('scripts')
</body>
</html>
