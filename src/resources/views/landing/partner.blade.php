<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ser partner — Arioli.dev</title>
    <meta name="description" content="Sumate como partner para implementar o revender los sistemas de Arioli.dev.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --font-sans: 'DM Sans', sans-serif;
            --font-mono: 'DM Mono', monospace;
            --bg:  #080d1a;
            --bg2: #0d1426;
            --card: #111827;
            --card-border: #1e2d45;
            --accent:  #818cf8;
            --accent2: #6366f1;
            --accent-bg: rgba(99,102,241,.15);
            --text:  #f1f5f9;
            --text2: #94a3b8;
            --text3: #475569;
            --success: #10b981;
            --radius: 16px;
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font-sans);
            background: var(--bg); color: var(--text);
            font-size: 15px; line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Nav ── */
        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: 18px 0; transition: all .3s; }
        .nav.scrolled { background: rgba(8,13,26,.95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--card-border); padding: 12px 0; }
        .nav-inner { max-width: 1200px; margin: 0 auto; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; }
        .logo-icon { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6, #6366f1); display: flex; align-items: center; justify-content: center; }
        .logo-text { font-size: 18px; font-weight: 700; color: var(--text); letter-spacing: -.02em; }
        .logo-text span { color: #3b82f6; }
        .nav-links { display: flex; align-items: center; gap: 28px; list-style: none; flex: 1; }
        .nav-links a { color: var(--text2); text-decoration: none; font-size: 14px; font-weight: 500; transition: color .2s; }
        .nav-links a:hover { color: var(--text); }
        .nav-cta { display: flex; align-items: center; gap: 10px; }
        .btn-nav { padding: 9px 18px; border-radius: 9px; font-size: 14px; font-weight: 600; font-family: var(--font-sans); text-decoration: none; transition: all .2s; cursor: pointer; border: none; }
        .btn-outline { background: transparent; color: var(--text2); border: 1.5px solid var(--card-border); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }
        .btn-accent { background: var(--accent); color: #fff; }
        .btn-accent:hover { opacity: .88; transform: translateY(-1px); }
        .nav-hamburger { display: none; background: none; border: none; color: var(--text2); cursor: pointer; padding: 4px; }
        .nav-mobile-menu { display: none; background: rgba(8,13,26,.97); border-top: 1px solid var(--card-border); padding: 20px 32px 24px; }
        .nav-mobile-menu a { display: block; padding: 12px 0; color: var(--text2); text-decoration: none; font-size: 15px; font-weight: 500; border-bottom: 1px solid var(--card-border); transition: color .2s; }
        .nav-mobile-menu a:last-child { border-bottom: none; }
        .nav-mobile-menu a:hover { color: var(--text); }

        /* ── Header simple ── */
        .page-header { padding: 160px 32px 60px; max-width: 800px; margin: 0 auto; text-align: center; }
        .breadcrumb { display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; color: var(--text3); margin-bottom: 24px; }
        .breadcrumb a { color: var(--text3); text-decoration: none; transition: color .2s; }
        .breadcrumb a:hover { color: var(--text2); }
        .page-header h1 { font-size: clamp(30px, 5vw, 44px); font-weight: 800; letter-spacing: -.03em; color: var(--text); margin-bottom: 14px; }
        .page-header p { font-size: 16px; color: var(--text2); line-height: 1.7; max-width: 560px; margin: 0 auto; }

        /* ── Benefits ── */
        .benefits { max-width: 800px; margin: 0 auto; padding: 0 32px 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .benefit { background: var(--card); border: 1px solid var(--card-border); border-radius: 14px; padding: 22px; text-align: center; }
        .benefit svg { color: var(--accent); margin-bottom: 10px; }
        .benefit-title { font-size: 13.5px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
        .benefit-desc { font-size: 12.5px; color: var(--text3); line-height: 1.5; }

        /* ── Form ── */
        .form-wrap { max-width: 560px; margin: 0 auto; padding: 60px 32px 120px; }
        .form-card { background: var(--card); border: 1px solid var(--card-border); border-radius: var(--radius); padding: 40px; }
        .form-group { margin-bottom: 18px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--text2); margin-bottom: 7px; }
        .form-input, .form-textarea {
            width: 100%; padding: 11px 14px;
            background: rgba(255,255,255,.05);
            border: 1.5px solid var(--card-border); border-radius: 10px;
            font-size: 14px; font-family: var(--font-sans);
            color: var(--text); outline: none; transition: all .2s;
        }
        .form-textarea { resize: vertical; min-height: 110px; }
        .form-input:focus, .form-textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
        .form-input::placeholder, .form-textarea::placeholder { color: #475569; }
        .form-error { font-size: 12px; color: #f87171; margin-top: 5px; }
        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #818cf8, #6366f1);
            color: #fff; font-size: 15px; font-weight: 700;
            font-family: var(--font-sans); border: none;
            border-radius: 12px; cursor: pointer;
            box-shadow: 0 6px 24px rgba(99,102,241,.3);
            transition: all .2s; margin-top: 8px;
        }
        .btn-submit:hover { transform: translateY(-1px); }
        .alert-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); border-radius: 10px; padding: 14px 16px; margin-bottom: 22px; font-size: 13.5px; color: #34d399; }
        .alert-error { background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.3); border-radius: 10px; padding: 14px 16px; margin-bottom: 22px; font-size: 13.5px; color: #f87171; }

        /* ── Footer ── */
        .footer { border-top: 1px solid var(--card-border); padding: 60px 32px 40px; }
        .footer-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; }
        .footer-brand p { font-size: 13px; color: var(--text3); margin-top: 12px; line-height: 1.65; max-width: 240px; }
        .footer-col h4 { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--text3); margin-bottom: 16px; }
        .footer-col a { display: block; font-size: 14px; color: var(--text3); text-decoration: none; margin-bottom: 10px; transition: color .2s; }
        .footer-col a:hover { color: var(--text2); }
        .footer-bottom { max-width: 1200px; margin: 32px auto 0; padding-top: 24px; border-top: 1px solid var(--card-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .footer-bottom p { font-size: 13px; color: var(--text3); }

        @media (max-width: 900px) { .footer-inner { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 768px) {
            .nav-links, .nav-cta { display: none; }
            .nav-hamburger { display: block; }
            .page-header { padding: 130px 20px 40px; }
            .benefits { grid-template-columns: 1fr; padding: 0 20px 10px; }
            .form-wrap { padding: 40px 16px 60px; }
            .form-card { padding: 28px; }
            .form-row { grid-template-columns: 1fr; }
            .footer { padding: 40px 20px 32px; }
            .footer-inner { grid-template-columns: 1fr; gap: 28px; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

{{-- Nav --}}
<nav class="nav" id="navbar">
    <div class="nav-inner">
        <a href="{{ route('landing.home') }}" class="logo">
            <div class="logo-icon">
                <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="logo-text">Arioli<span>.dev</span></span>
        </a>

        <ul class="nav-links">
            <li><a href="{{ route('landing.home') }}#productos">Sistemas</a></li>
            <li><a href="{{ route('landing.home') }}#como-funciona">Cómo funciona</a></li>
            <li><a href="{{ route('landing.contact') }}">Contacto</a></li>
        </ul>

        <div class="nav-cta">
            <a href="https://{{ config('app.cliente_domain') }}" class="btn-nav btn-outline">Panel cliente</a>
            <a href="{{ route('landing.home') }}#productos" class="btn-nav btn-accent">Ver sistemas</a>
        </div>

        <button class="nav-hamburger" id="navHamburger" aria-label="Menú">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <div class="nav-mobile-menu" id="mobileMenu">
        <a href="{{ route('landing.home') }}">← Inicio</a>
        <a href="{{ route('landing.home') }}#productos">Sistemas</a>
        <a href="{{ route('landing.home') }}#como-funciona">Cómo funciona</a>
        <a href="{{ route('landing.contact') }}">Contacto</a>
    </div>
</nav>

{{-- Header --}}
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('landing.home') }}">Inicio</a>
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>Ser partner</span>
    </div>
    <h1>Sumate como partner</h1>
    <p>Para desarrolladores, agencias y emprendedores que quieran implementar o revender nuestros sistemas.</p>
</div>

{{-- Benefits --}}
<div class="benefits">
    <div class="benefit">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="benefit-title">Comisión por venta</div>
        <div class="benefit-desc">Ganás un porcentaje por cada cliente que contratás a través tuyo.</div>
    </div>
    <div class="benefit">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <div class="benefit-title">Soporte técnico directo</div>
        <div class="benefit-desc">Acceso prioritario a nuestro equipo para resolver dudas de implementación.</div>
    </div>
    <div class="benefit">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <div class="benefit-title">Marca blanca disponible</div>
        <div class="benefit-desc">Posibilidad de implementar los sistemas con tu propia marca para tus clientes.</div>
    </div>
</div>

{{-- Form --}}
<div class="form-wrap">
    <div class="form-card">
        @if(session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('landing.partner.send') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Tu nombre" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="tu@email.com" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Empresa (opcional)</label>
                    <input type="text" name="company" class="form-input" value="{{ old('company') }}" placeholder="Tu empresa o marca">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono (opcional)</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+54 9 ...">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Contanos tu propuesta</label>
                <textarea name="message" class="form-textarea" placeholder="Qué te interesa implementar o revender, y en qué zona/mercado" required>{{ old('message') }}</textarea>
            </div>

            @if(config('services.turnstile.sitekey'))
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                <div style="display:flex; justify-content:center; margin-bottom:18px;">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.sitekey') }}" data-theme="dark"></div>
                </div>
            @endif

            <button type="submit" class="btn-submit">Enviar consulta</button>
        </form>
    </div>
</div>

{{-- Footer --}}
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="{{ route('landing.home') }}" class="logo">
                <div class="logo-icon">
                    <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="logo-text" style="margin-left:10px;">Arioli<span>.dev</span></span>
            </a>
            <p>Sistemas de gestión SaaS para empresas argentinas. En la nube, con soporte real y precios en pesos.</p>
        </div>

        <div class="footer-col">
            <h4>Sistemas</h4>
            <a href="{{ route('landing.product', 'loteos') }}">Loteos</a>
            <a href="{{ route('landing.product', 'tallerpro') }}">Servis — Talleres</a>
            <a href="{{ route('landing.product', 'historias-clinicas') }}">Clínica — Historias</a>
        </div>

        <div class="footer-col">
            <h4>Plataforma</h4>
            <a href="{{ route('landing.home') }}#como-funciona">Cómo funciona</a>
            <a href="{{ route('landing.home') }}#caracteristicas">Características</a>
            <a href="{{ route('landing.home') }}#faq">Preguntas frecuentes</a>
        </div>

        <div class="footer-col">
            <h4>Clientes</h4>
            <a href="https://{{ config('app.cliente_domain') }}">Panel del cliente</a>
            <a href="{{ route('landing.contact') }}">Contacto</a>
            <a href="{{ route('landing.partner') }}">Ser partner</a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© {{ date('Y') }} Arioli.dev — Todos los derechos reservados.</p>
        <p>Sistemas de gestión SaaS · Argentina</p>
    </div>
</footer>

<script>
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);
});
document.getElementById('navHamburger').addEventListener('click', () => {
    const menu = document.getElementById('mobileMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
});
</script>

</body>
</html>
