<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Arioli.dev — Sistemas de gestión SaaS para empresas</title>
    <meta name="description" content="Sistemas de gestión SaaS argentinos: Loteos, Talleres (Servis), Historias Clínicas y Turnos. 100% en la nube, precios en pesos, soporte incluido.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon-180.png') }}">

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
            --accent:  #3b82f6;
            --accent2: #6366f1;
            --text:  #f1f5f9;
            --text2: #94a3b8;
            --text3: #475569;
            --success: #10b981;
            --radius: 16px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-sans);
            background: var(--bg);
            color: var(--text);
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Nav ── */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 18px 0;
            transition: all .3s;
        }
        .nav.scrolled {
            background: rgba(8,13,26,.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 12px 0;
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .logo-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center;
        }
        .logo-text {
            font-size: 18px; font-weight: 700;
            color: var(--text); letter-spacing: -.02em;
        }
        .logo-text span { color: var(--accent); }
        .nav-links {
            display: flex; align-items: center;
            gap: 28px; list-style: none; flex: 1;
        }
        .nav-links a {
            color: var(--text2); text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: color .2s;
        }
        .nav-links a:hover { color: var(--text); }
        .nav-cta { display: flex; align-items: center; gap: 10px; }
        .btn-nav {
            padding: 9px 18px; border-radius: 9px;
            font-size: 14px; font-weight: 600;
            font-family: var(--font-sans);
            text-decoration: none; transition: all .2s;
            cursor: pointer; border: none;
        }
        .btn-outline {
            background: transparent; color: var(--text2);
            border: 1.5px solid var(--card-border);
        }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }
        .btn-filled {
            background: var(--accent); color: #fff;
            box-shadow: 0 4px 16px rgba(59,130,246,.3);
        }
        .btn-filled:hover {
            background: #2563eb;
            box-shadow: 0 6px 24px rgba(59,130,246,.4);
            transform: translateY(-1px);
        }
        .nav-hamburger {
            display: none; background: none; border: none;
            color: var(--text2); cursor: pointer; padding: 4px;
        }
        .nav-mobile-menu {
            display: none;
            background: rgba(8,13,26,.97);
            border-top: 1px solid var(--card-border);
            padding: 20px 32px 24px;
        }
        .nav-mobile-menu a {
            display: block; padding: 12px 0;
            color: var(--text2); text-decoration: none;
            font-size: 15px; font-weight: 500;
            border-bottom: 1px solid var(--card-border);
            transition: color .2s;
        }
        .nav-mobile-menu a:last-child { border-bottom: none; }
        .nav-mobile-menu a:hover { color: var(--text); }

        /* ── Hero ── */
        .hero {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            text-align: center;
            padding: 120px 32px 80px;
            position: relative; overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(59,130,246,.18) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 80% 80%, rgba(99,102,241,.12) 0%, transparent 50%);
        }
        .hero-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .hero-content { position: relative; max-width: 820px; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(59,130,246,.12);
            border: 1px solid rgba(59,130,246,.25);
            color: #93c5fd;
            padding: 6px 16px; border-radius: 999px;
            font-size: 13px; font-weight: 500;
            margin-bottom: 28px;
        }
        .hero-badge span {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent); animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .4; } }
        .hero h1 {
            font-size: clamp(36px, 6vw, 64px);
            font-weight: 800; line-height: 1.1;
            letter-spacing: -.03em; color: var(--text);
            margin-bottom: 24px;
        }
        .hero h1 .gradient {
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: 18px; color: var(--text2);
            max-width: 580px; margin: 0 auto 40px; line-height: 1.7;
        }
        .hero-actions {
            display: flex; align-items: center;
            justify-content: center; gap: 14px; flex-wrap: wrap;
        }
        .btn-hero-primary {
            padding: 14px 32px; border-radius: 12px;
            font-size: 15px; font-weight: 700;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; text-decoration: none;
            box-shadow: 0 8px 32px rgba(59,130,246,.35);
            transition: all .2s; border: none; cursor: pointer;
            font-family: var(--font-sans);
        }
        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(59,130,246,.45);
        }
        .btn-hero-secondary {
            padding: 14px 32px; border-radius: 12px;
            font-size: 15px; font-weight: 600;
            background: transparent; color: var(--text2);
            text-decoration: none;
            border: 1.5px solid var(--card-border);
            transition: all .2s; font-family: var(--font-sans);
        }
        .btn-hero-secondary:hover { border-color: var(--accent); color: var(--text); }
        .hero-stats {
            display: flex; align-items: center;
            justify-content: center; gap: 40px;
            margin-top: 64px; padding-top: 40px;
            border-top: 1px solid var(--card-border); flex-wrap: wrap;
        }
        .hero-stat { text-align: center; }
        .hero-stat strong {
            display: block; font-size: 28px; font-weight: 800;
            color: var(--text); letter-spacing: -.02em;
        }
        .hero-stat span { font-size: 13px; color: var(--text2); margin-top: 2px; }

        /* ── Sections ── */
        .section {
            padding: 100px 32px;
            max-width: 1200px; margin: 0 auto;
        }
        .section-label {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .1em;
            color: var(--accent); margin-bottom: 16px;
        }
        .section-title {
            font-size: clamp(26px, 4vw, 40px);
            font-weight: 800; letter-spacing: -.02em;
            color: var(--text); margin-bottom: 16px; line-height: 1.2;
        }
        .section-sub {
            font-size: 17px; color: var(--text2);
            max-width: 560px; line-height: 1.7;
        }
        .alt-bg { background: var(--bg2); padding: 1px 0; }

        /* ── Products ── */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(280px, 1fr));
            gap: 24px; margin-top: 56px;
        }
        .product-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: var(--radius);
            padding: 32px; transition: all .25s;
            position: relative; overflow: hidden;
            display: flex; flex-direction: column;
        }
        .product-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            opacity: 0; transition: opacity .25s;
        }
        .product-card:hover {
            border-color: rgba(59,130,246,.3);
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }
        .product-card:hover::before { opacity: 1; }
        .product-icon {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; font-size: 26px;
        }
        .product-name {
            font-size: 21px; font-weight: 700;
            color: var(--text); margin-bottom: 10px;
        }
        .product-desc {
            font-size: 14px; color: var(--text2);
            line-height: 1.7; margin-bottom: 20px;
        }
        .product-features-list {
            list-style: none; margin-bottom: 24px; flex: 1;
        }
        .product-features-list li {
            display: flex; align-items: center; gap: 10px;
            font-size: 13.5px; color: var(--text2);
            padding: 7px 0;
            border-bottom: 1px solid rgba(255,255,255,.04);
        }
        .product-features-list li:last-child { border-bottom: none; }
        .product-features-list svg { color: var(--success); flex-shrink: 0; }
        .product-price {
            font-size: 13px; color: var(--text3);
            margin-bottom: 20px;
        }
        .product-price strong {
            font-size: 22px; font-weight: 800;
            color: var(--text); letter-spacing: -.02em;
        }
        .product-actions { display: flex; gap: 10px; }
        .btn-contract {
            flex: 1; padding: 11px 20px; border-radius: 10px;
            font-size: 14px; font-weight: 600;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; text-decoration: none; text-align: center;
            transition: all .2s;
            box-shadow: 0 4px 16px rgba(59,130,246,.25);
            font-family: var(--font-sans); border: none; cursor: pointer;
        }
        .btn-contract:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(59,130,246,.35);
        }
        .btn-demo {
            padding: 11px 18px; border-radius: 10px;
            font-size: 14px; font-weight: 600;
            background: transparent; color: var(--text2);
            text-decoration: none; text-align: center;
            border: 1.5px solid var(--card-border);
            transition: all .2s; font-family: var(--font-sans); cursor: pointer;
        }
        .btn-demo:hover { border-color: var(--accent); color: var(--text); }

        /* ── Steps ── */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 32px; margin-top: 56px;
            position: relative;
        }
        .step {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: var(--radius);
            padding: 32px; text-align: center;
        }
        .step-num {
            display: inline-block;
            font-family: var(--font-mono);
            font-size: 13px; font-weight: 500;
            color: var(--accent);
            background: rgba(59,130,246,.1);
            border: 1px solid rgba(59,130,246,.2);
            padding: 4px 12px; border-radius: 8px;
            margin-bottom: 16px; letter-spacing: .05em;
        }
        .step h3 {
            font-size: 18px; font-weight: 700;
            color: var(--text); margin-bottom: 10px;
        }
        .step p { font-size: 14px; color: var(--text2); line-height: 1.65; }

        /* ── Features ── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px; margin-top: 56px;
        }
        .feature-card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 14px; padding: 28px;
            transition: border-color .2s;
        }
        .feature-card:hover { border-color: rgba(59,130,246,.3); }
        .feature-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: rgba(59,130,246,.1); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .feature-title {
            font-size: 16px; font-weight: 700;
            color: var(--text); margin-bottom: 8px;
        }
        .feature-desc { font-size: 14px; color: var(--text2); line-height: 1.65; }

        /* ── FAQ ── */
        .faq-list { margin-top: 48px; max-width: 800px; }
        .faq-item {
            border-bottom: 1px solid var(--card-border);
        }
        .faq-btn {
            display: flex; align-items: center;
            justify-content: space-between; gap: 16px;
            width: 100%; background: none; border: none;
            padding: 22px 0; cursor: pointer;
            text-align: left; font-family: var(--font-sans);
            transition: color .2s;
        }
        .faq-btn:hover .faq-q-text { color: var(--text); }
        .faq-q-text {
            font-size: 16px; font-weight: 600;
            color: var(--text2); transition: color .2s; flex: 1;
        }
        .faq-icon {
            flex-shrink: 0; width: 28px; height: 28px;
            border-radius: 8px; background: var(--card);
            border: 1px solid var(--card-border);
            display: flex; align-items: center; justify-content: center;
            color: var(--text3); font-size: 16px; font-weight: 400;
            transition: all .2s;
        }
        .faq-item.open .faq-icon {
            background: rgba(59,130,246,.1);
            border-color: rgba(59,130,246,.3);
            color: var(--accent);
        }
        .faq-item.open .faq-q-text { color: var(--text); }
        .faq-answer {
            font-size: 14.5px; color: var(--text2);
            line-height: 1.75; padding-bottom: 22px;
            display: none;
        }
        .faq-item.open .faq-answer { display: block; }

        /* ── Casos de éxito (carrusel) ── */
        .clients-static-row {
            display: flex; flex-wrap: wrap; justify-content: center;
            gap: 20px; margin-top: 56px;
        }
        .clients-carousel {
            margin-top: 56px; overflow: hidden;
            -webkit-mask-image: linear-gradient(to right, transparent, #000 5%, #000 95%, transparent);
            mask-image: linear-gradient(to right, transparent, #000 5%, #000 95%, transparent);
        }
        .clients-track {
            display: flex; gap: 20px; width: max-content;
            animation: scroll-left 40s linear infinite;
        }
        .clients-carousel:hover .clients-track { animation-play-state: paused; }
        @keyframes scroll-left {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
        .client-card {
            display: flex; flex-direction: column; align-items: center; text-align: center;
            gap: 8px; width: 200px; flex-shrink: 0;
            background: var(--card); border: 1px solid var(--card-border);
            border-radius: var(--radius); padding: 28px 16px;
            text-decoration: none; transition: all .25s;
        }
        .client-card:hover { border-color: rgba(59,130,246,.3); transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,.25); }
        .client-logo { height: 44px; max-width: 130px; object-fit: contain; }
        .client-logo-placeholder {
            width: 44px; height: 44px; border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 18px;
        }
        .client-name { font-size: 14px; font-weight: 700; color: var(--text); }
        .client-category { font-size: 11.5px; color: var(--text2); }

        /* ── CTA ── */
        .cta-wrap { max-width: 1200px; margin: 0 auto; padding: 0 32px 100px; }
        .cta-section {
            background: linear-gradient(135deg, rgba(59,130,246,.15), rgba(99,102,241,.1));
            border: 1px solid rgba(59,130,246,.2);
            border-radius: 24px; padding: 80px 48px;
            text-align: center; position: relative; overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute; top: -60px; left: 50%; transform: translateX(-50%);
            width: 400px; height: 400px;
            background: radial-gradient(ellipse, rgba(59,130,246,.15), transparent 70%);
        }
        .cta-content { position: relative; }
        .cta-title {
            font-size: clamp(26px, 4vw, 40px); font-weight: 800;
            color: var(--text); letter-spacing: -.02em;
            margin-bottom: 14px; line-height: 1.2;
        }
        .cta-sub { font-size: 17px; color: var(--text2); margin-bottom: 36px; }

        /* ── Footer ── */
        .footer {
            border-top: 1px solid var(--card-border);
            padding: 60px 32px 40px;
        }
        .footer-inner {
            max-width: 1200px; margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
        }
        .footer-brand p {
            font-size: 13px; color: var(--text3);
            margin-top: 12px; line-height: 1.65; max-width: 240px;
        }
        .footer-col h4 {
            font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .08em;
            color: var(--text3); margin-bottom: 16px;
        }
        .footer-col a {
            display: block; font-size: 14px;
            color: var(--text3); text-decoration: none;
            margin-bottom: 10px; transition: color .2s;
        }
        .footer-col a:hover { color: var(--text2); }
        .footer-bottom {
            max-width: 1200px; margin: 32px auto 0;
            padding-top: 24px; border-top: 1px solid var(--card-border);
            display: flex; align-items: center;
            justify-content: space-between; flex-wrap: wrap; gap: 16px;
        }
        .footer-bottom p { font-size: 13px; color: var(--text3); }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .nav-links, .nav-cta { display: none; }
            .nav-hamburger { display: block; }
            .hero { padding: 100px 20px 60px; }
            .section { padding: 60px 20px; }
            .hero-stats { gap: 24px; }
            .cta-section { padding: 48px 24px; }
            .cta-wrap { padding: 0 16px 60px; }
            .footer { padding: 40px 20px 32px; }
            .footer-inner { grid-template-columns: 1fr; gap: 28px; }
            .products-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
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
            <li><a href="#productos">Productos</a></li>
            <li><a href="#como-funciona">Cómo funciona</a></li>
            <li><a href="#caracteristicas">Características</a></li>
            <li><a href="#faq">Preguntas frecuentes</a></li>
        </ul>

        <div class="nav-cta">
            <a href="https://{{ config('app.cliente_domain') }}" class="btn-nav btn-outline">Acceso clientes</a>
            <a href="#productos" class="btn-nav btn-filled">Ver sistemas</a>
        </div>

        <button class="nav-hamburger" id="navHamburger" aria-label="Menú">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <div class="nav-mobile-menu" id="mobileMenu">
        <a href="#productos" onclick="closeMenu()">Productos</a>
        <a href="#como-funciona" onclick="closeMenu()">Cómo funciona</a>
        <a href="#caracteristicas" onclick="closeMenu()">Características</a>
        <a href="#faq" onclick="closeMenu()">Preguntas frecuentes</a>
        <a href="https://{{ config('app.cliente_domain') }}" style="color:var(--accent); margin-top:4px;">Acceso clientes →</a>
    </div>
</nav>

{{-- Hero --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <span></span>
            Plataforma SaaS argentina
        </div>
        <h1>
            Tecnología para<br>
            <span class="gradient">gestionar, automatizar</span><br>
            y hacer crecer tu negocio
        </h1>
        <p>
            Sistemas de gestión empresariales en la nube, con soporte real
            y precios en pesos. Diseñados para el mercado argentino.
        </p>
        <div class="hero-actions">
            <a href="#productos" class="btn-hero-primary">Explorar sistemas</a>
            <a href="#como-funciona" class="btn-hero-secondary">¿Cómo funciona?</a>
        </div>

        <div class="hero-stats">
            <div class="hero-stat">
                <strong>{{ $products->count() }}</strong>
                <span>Sistemas disponibles</span>
            </div>
            <div class="hero-stat">
                <strong>100%</strong>
                <span>En la nube</span>
            </div>
            <div class="hero-stat">
                <strong>ARS</strong>
                <span>Precios en pesos</span>
            </div>
            <div class="hero-stat">
                <strong>Demo</strong>
                <span>Gratis, sin registro</span>
            </div>
        </div>
    </div>
</section>

{{-- Productos --}}
<section id="productos">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
            </svg>
            Nuestros sistemas
        </div>
        <h2 class="section-title">Soluciones para cada rubro</h2>
        <p class="section-sub">
            Cada sistema fue diseñado específicamente para su rubro,
            con las herramientas que realmente necesitás para operar mejor.
        </p>

        @php
            $productIcons = [
                'loteos'             => ['emoji' => '🏘️', 'bg' => 'rgba(59,130,246,.15)'],
                'tallerpro'          => ['emoji' => '🔧', 'bg' => 'rgba(245,158,11,.15)'],
                'historias-clinicas' => ['emoji' => '🏥', 'bg' => 'rgba(16,185,129,.15)'],
                'turnos'             => ['emoji' => '📅', 'bg' => 'rgba(129,140,248,.15)'],
                'subastas'           => ['emoji' => '🔨', 'bg' => 'rgba(29,78,216,.15)'],
            ];
            $productHighlights = [
                'loteos'             => ['Mapa de lotes interactivo', 'Recordatorios de cuotas por email', 'Portal para tus compradores'],
                'tallerpro'          => ['Órdenes de trabajo con seguimiento completo', 'Fotos de antes, durante y después', 'Mantenimiento preventivo con recordatorios'],
                'historias-clinicas' => ['Historia clínica electrónica', 'Agenda con recordatorios automáticos', 'Firma digital y consentimiento informado'],
                'turnos'             => ['Reservas online 24/7', 'Turnero con pantalla de llamado', 'Recordatorios automáticos'],
                'subastas'           => ['Pujas en tiempo real', 'Adjudicación automática', 'Gestión de pagos y comisiones'],
            ];
        @endphp

        <div class="products-grid">
            @foreach($products as $product)
                @php
                    $icon        = $productIcons[$product->slug] ?? ['emoji' => '💼', 'bg' => 'rgba(99,102,241,.15)'];
                    $highlights  = $productHighlights[$product->slug] ?? [];
                    $monthlyPlan = $product->plans->firstWhere('period', 'monthly');
                    $demoUrl     = 'http://demo.' . $product->public_domain . '.' . config('app.tenant_domain');
                @endphp

                <div class="product-card">
                    <div class="product-icon" style="background:{{ $icon['bg'] }}">
                        {{ $icon['emoji'] }}
                    </div>

                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-desc">{{ $product->description }}</div>

                    <ul class="product-features-list">
                        @foreach($highlights as $h)
                            <li>
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $h }}
                            </li>
                        @endforeach
                    </ul>

                    @if($monthlyPlan)
                        <div class="product-price">
                            Desde <strong>${{ number_format($monthlyPlan->price, 0, ',', '.') }}</strong>/mes de licencia
                            <br>
                            <a href="{{ route('landing.product', $product->slug) }}#compra-completa" style="color:var(--text3); text-decoration:underline;">
                                También se vende sin mensualidad →
                            </a>
                        </div>
                    @endif

                    <div class="product-actions">
                        <a href="{{ route('landing.product', $product->slug) }}" class="btn-contract">
                            Ver sistema →
                        </a>
                        <a href="{{ $demoUrl }}" target="_blank" class="btn-demo">Demo gratis</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Servicios --}}
<div class="alt-bg" id="servicios">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            </svg>
            Servicios profesionales
        </div>
        <h2 class="section-title">También hacemos desarrollo a medida</h2>
        <p class="section-sub">
            Además de nuestros sistemas SaaS, ofrecemos servicios de desarrollo web
            y soluciones personalizadas para proyectos que necesitan algo distinto.
        </p>

        <div class="products-grid">
            <div class="product-card">
                <div class="product-icon" style="background:rgba(59,130,246,.15)">🌐</div>
                <div class="product-name">Desarrollo Web</div>
                <div class="product-desc">Sitios institucionales, blogs y tiendas online, diseñados a medida de tu negocio.</div>
                <ul class="product-features-list">
                    <li><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Sitios institucionales</li>
                    <li><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Blogs</li>
                    <li><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Tiendas online</li>
                </ul>
                <div class="product-actions">
                    <a href="{{ route('landing.service', 'desarrollo-web') }}" class="btn-contract">Ver servicio →</a>
                </div>
            </div>

            <div class="product-card">
                <div class="product-icon" style="background:rgba(99,102,241,.15)">🛠️</div>
                <div class="product-name">Desarrollos a Medida</div>
                <div class="product-desc">Software personalizado, themes, plugins y soluciones WordPress.</div>
                <ul class="product-features-list">
                    <li><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Desarrollos personalizados</li>
                    <li><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Themes y plugins</li>
                    <li><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Soluciones WordPress</li>
                </ul>
                <div class="product-actions">
                    <a href="{{ route('landing.service', 'desarrollo-a-medida') }}" class="btn-contract">Ver servicio →</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cómo funciona --}}
<div class="alt-bg" id="como-funciona">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Proceso simple
        </div>
        <h2 class="section-title">Tan simple como 3 pasos</h2>
        <p class="section-sub">
            Sin instalaciones, sin servidores, sin configuraciones complejas.
            Tu sistema activo en minutos.
        </p>

        <div class="steps-grid">
            <div class="step">
                <div class="step-num">01</div>
                <h3>Elegís el sistema</h3>
                <p>Seleccionás el sistema que mejor se adapta a tu rubro. Podés probarlo gratis con la demo antes de decidir.</p>
            </div>
            <div class="step">
                <div class="step-num">02</div>
                <h3>Probás gratis la demo</h3>
                <p>Accedés a la demo sin registrarte ni ingresar datos de pago. Recorrés el sistema con datos de ejemplo reales.</p>
            </div>
            <div class="step">
                <div class="step-num">03</div>
                <h3>Contratás y arrancás</h3>
                <p>Elegís el plan, pagás en pesos con tarjeta o transferencia y tu empresa queda activa en minutos, lista para usar.</p>
            </div>
        </div>
    </div>
</div>

{{-- Características de la plataforma --}}
<section id="caracteristicas">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Por qué Arioli.dev
        </div>
        <h2 class="section-title">Todo lo que necesitás en un solo lugar</h2>
        <p class="section-sub">
            Infraestructura profesional, soporte real y sistemas diseñados para el mercado argentino.
        </p>

        @php
            $features = [
                ['path' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z',
                 'title' => '100% en la nube',
                 'desc'  => 'Sin instalaciones ni servidores propios. Accedé desde cualquier dispositivo con internet, en cualquier momento.'],
                ['path' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
                 'title' => 'Datos seguros y aislados',
                 'desc'  => 'Cada cliente tiene su propia base de datos. Tu información nunca se mezcla con la de otros clientes.'],
                ['path' => 'M13 10V3L4 14h7v7l9-11h-7z',
                 'title' => 'Acceso inmediato',
                 'desc'  => 'Tu sistema queda activo en minutos después de contratar. Sin esperas ni configuraciones técnicas complejas.'],
                ['path' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z',
                 'title' => 'Soporte incluido',
                 'desc'  => 'Ayuda real de personas reales. Respondemos tus consultas y resolvemos problemas rápidamente por ticket o WhatsApp.'],
                ['path' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                 'title' => 'Actualizaciones incluidas',
                 'desc'  => 'Tu sistema siempre tiene las últimas mejoras sin costo adicional. Las actualizaciones se aplican automáticamente.'],
                ['path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                 'title' => 'Precios en pesos',
                 'desc'  => 'Sin sorpresas por variaciones del dólar. Todos nuestros planes están en pesos argentinos con precios claros.'],
            ];
        @endphp

        <div class="features-grid">
            @foreach($features as $f)
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['path'] }}"/>
                        </svg>
                    </div>
                    <div class="feature-title">{{ $f['title'] }}</div>
                    <div class="feature-desc">{{ $f['desc'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ general --}}
<div class="alt-bg" id="faq">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Preguntas frecuentes
        </div>
        <h2 class="section-title">Resolvemos tus dudas</h2>
        <p class="section-sub">
            Las preguntas más comunes sobre la plataforma, los sistemas y el proceso de contratación.
        </p>

        @php
            $faqs = [
                ['q' => '¿Cómo contrato un sistema?',
                 'a' => 'Elegís el sistema y el plan que más te convenga, completás el formulario con tus datos (nombre de empresa, email y dominio deseado) y el sistema queda activo en minutos después de confirmar el pago. Sin instalaciones ni configuraciones complejas.'],
                ['q' => '¿Puedo probar antes de pagar?',
                 'a' => 'Sí. Todos los sistemas tienen una demo gratuita disponible en todo momento. Podés acceder a la demo sin registrarte ni ingresar datos de pago, y recorrés el sistema con datos de ejemplo reales para evaluar si se adapta a tu negocio.'],
                ['q' => '¿Los datos de la demo son reales?',
                 'a' => 'No. La demo contiene datos de ejemplo generados para que puedas ver cómo funciona el sistema en la práctica. Tu empresa y tus datos reales solo existen en tu cuenta propia, una vez que la contratás.'],
                ['q' => '¿Puedo cancelar cuando quiera?',
                 'a' => 'Sí. No hay contratos de permanencia. Al finalizar el período contratado podés no renovar y tu acceso se cierra sin cargos adicionales. También podés contratar por períodos más cortos si preferís mayor flexibilidad.'],
                ['q' => '¿Mis datos están seguros?',
                 'a' => 'Sí. Cada empresa tiene su propia base de datos completamente aislada. Tu información nunca se mezcla con la de otros clientes. Realizamos copias de seguridad periódicas automáticas en servidores seguros.'],
                ['q' => '¿Qué incluye el soporte técnico?',
                 'a' => 'El soporte está incluido en todos los planes. Podés contactarnos por ticket desde el panel de cliente, por email o por WhatsApp. Respondemos consultas sobre el funcionamiento del sistema y resolvemos problemas técnicos sin costo adicional.'],
                ['q' => '¿Quiero ser partner developer o revender los sistemas?',
                 'a' => 'Tenemos un programa de partners para desarrolladores, agencias y emprendedores que quieran implementar o revender nuestros sistemas. Completá el formulario en nuestra página de Ser Partner para conocer las condiciones y beneficios del programa.'],
                ['q' => '¿Tienen planes para múltiples empresas o sucursales?',
                 'a' => 'Cada contratación corresponde a una empresa o sucursal con acceso independiente. Si necesitás gestionar múltiples empresas o sucursales, podés contratar una cuenta por cada una. Contactanos para consultar descuentos por volumen.'],
            ];
        @endphp

        <div class="faq-list" id="faqList">
            @foreach($faqs as $i => $faq)
                <div class="faq-item" data-index="{{ $i }}">
                    <button class="faq-btn" onclick="toggleFaq({{ $i }})">
                        <span class="faq-q-text">{{ $faq['q'] }}</span>
                        <span class="faq-icon" id="faqIcon{{ $i }}">+</span>
                    </button>
                    <div class="faq-answer" id="faqAnswer{{ $i }}">{{ $faq['a'] }}</div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:40px;">
            <p style="font-size:14px; color:var(--text3);">
                ¿No encontrás tu respuesta?
                <a href="{{ route('landing.contact') }}" style="color:var(--accent); text-decoration:none; font-weight:600;">
                    Escribinos por el formulario de contacto
                </a>
            </p>
        </div>
    </div>
</div>

{{-- Casos de éxito --}}
@if($successCases->isNotEmpty())
<div class="alt-bg" id="casos-exito">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Casos de éxito
        </div>
        <h2 class="section-title">Casos de éxito</h2>
        <p class="section-sub">
            Conocé algunas de las empresas e instituciones que ya trabajan con Arioli.dev
            y descubrí cómo nuestros sistemas ayudaron a digitalizar sus procesos.
        </p>

        @if($successCases->count() < 4)
            {{-- Con pocos casos, la animación en loop duplicando la lista se ve repetida en
                 vez de fluida — se muestra una fila estática sin duplicar. --}}
            <div class="clients-static-row">
                @foreach($successCases as $case)
                    @include('landing.partials.casos-exito-card')
                @endforeach
            </div>
        @else
            <div class="clients-carousel">
                <div class="clients-track">
                    @foreach($successCases as $case)
                        @include('landing.partials.casos-exito-card')
                    @endforeach
                    @foreach($successCases as $case)
                        @include('landing.partials.casos-exito-card')
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endif

{{-- CTA --}}
<div class="cta-wrap" style="padding-top:100px;">
    <div class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">¿Listo para digitalizar tu negocio?</h2>
            <p class="cta-sub">Probá la demo gratis hoy. Sin instalaciones, sin complicaciones.</p>
            <div style="display:flex; align-items:center; justify-content:center; gap:14px; flex-wrap:wrap;">
                <a href="#productos" class="btn-hero-primary">Ver sistemas disponibles</a>
                <a href="{{ route('landing.contact') }}" class="btn-hero-secondary">Hablar con nosotros</a>
            </div>
        </div>
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
            @foreach($products as $product)
                <a href="{{ route('landing.product', $product->slug) }}">{{ $product->name }}</a>
            @endforeach
        </div>

        <div class="footer-col">
            <h4>Plataforma</h4>
            <a href="#como-funciona">Cómo funciona</a>
            <a href="#caracteristicas">Características</a>
            <a href="#faq">Preguntas frecuentes</a>
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
        <p style="font-size:13px; color:var(--text3);">Sistemas de gestión SaaS · Argentina</p>
    </div>
</footer>

<script>
// Nav scroll
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Mobile menu
document.getElementById('navHamburger').addEventListener('click', () => {
    const menu = document.getElementById('mobileMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
});
function closeMenu() {
    document.getElementById('mobileMenu').style.display = 'none';
}

// FAQ accordion
let openFaq = null;
function toggleFaq(i) {
    const item    = document.querySelector(`.faq-item[data-index="${i}"]`);
    const answer  = document.getElementById(`faqAnswer${i}`);
    const icon    = document.getElementById(`faqIcon${i}`);

    if (openFaq !== null && openFaq !== i) {
        const prev = document.querySelector(`.faq-item[data-index="${openFaq}"]`);
        prev.classList.remove('open');
        document.getElementById(`faqAnswer${openFaq}`).style.display = 'none';
        document.getElementById(`faqIcon${openFaq}`).textContent = '+';
    }

    const isOpen = item.classList.contains('open');
    item.classList.toggle('open', !isOpen);
    answer.style.display = isOpen ? 'none' : 'block';
    icon.textContent = isOpen ? '+' : '−';
    openFaq = isOpen ? null : i;
}
</script>

</body>
</html>
