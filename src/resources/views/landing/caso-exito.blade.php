<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $client->name }} — Caso de éxito | Arioli.dev</title>
    <meta name="description" content="{{ $client->short_description ?? ('Caso de éxito: los trabajos que Arioli.dev realizó para ' . $client->name) }}">
    @if($client->cover_image)
        <meta property="og:image" content="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($client->cover_image) }}">
    @endif
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
        body { font-family: var(--font-sans); background: var(--bg); color: var(--text); font-size: 15px; line-height: 1.6; -webkit-font-smoothing: antialiased; }

        /* ── Nav ── */
        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: 18px 0; transition: all .3s; }
        .nav.scrolled { background: rgba(8,13,26,.95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--card-border); padding: 12px 0; }
        .nav-inner { max-width: 1200px; margin: 0 auto; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; }
        .logo-icon { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #3b82f6, #6366f1); display: flex; align-items: center; justify-content: center; }
        .logo-text { font-size: 18px; font-weight: 700; color: var(--text); letter-spacing: -.02em; }
        .logo-text span { color: var(--accent); }
        .nav-links { display: flex; align-items: center; gap: 28px; list-style: none; flex: 1; }
        .nav-links a { color: var(--text2); text-decoration: none; font-size: 14px; font-weight: 500; transition: color .2s; }
        .nav-links a:hover { color: var(--text); }
        .nav-cta { display: flex; align-items: center; gap: 10px; }
        .btn-nav { padding: 9px 18px; border-radius: 9px; font-size: 14px; font-weight: 600; font-family: var(--font-sans); text-decoration: none; transition: all .2s; cursor: pointer; border: none; }
        .btn-outline { background: transparent; color: var(--text2); border: 1.5px solid var(--card-border); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }
        .btn-filled { background: var(--accent); color: #fff; box-shadow: 0 4px 16px rgba(59,130,246,.3); }
        .btn-filled:hover { background: #2563eb; }
        .nav-hamburger { display: none; background: none; border: none; color: var(--text2); cursor: pointer; padding: 4px; }
        .nav-mobile-menu { display: none; background: rgba(8,13,26,.97); border-top: 1px solid var(--card-border); padding: 20px 32px 24px; }
        .nav-mobile-menu a { display: block; padding: 12px 0; color: var(--text2); text-decoration: none; font-size: 15px; font-weight: 500; border-bottom: 1px solid var(--card-border); }
        .nav-mobile-menu a:last-child { border-bottom: none; }

        /* ── Case header (banner + tarjeta superpuesta) ── */
        .case-top { padding: 140px 32px 0; }
        .case-breadcrumb-row { max-width: 1200px; margin: 0 auto 20px; }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text3); }
        .breadcrumb a { color: var(--text3); text-decoration: none; }
        .breadcrumb a:hover { color: var(--text2); }
        .case-banner {
            max-width: 1200px; margin: 0 auto; height: 300px;
            border-radius: var(--radius); overflow: hidden; position: relative;
            background: var(--card); border: 1px solid var(--card-border);
        }
        .case-banner-bg {
            position: absolute; inset: 0;
            background-size: cover; background-position: center;
        }
        .case-header-row {
            max-width: 1200px; margin: -56px auto 0; padding: 0 32px;
            position: relative; z-index: 2;
            display: flex; align-items: flex-end; gap: 28px; flex-wrap: wrap;
        }
        .case-header-row--flat { margin-top: 0; align-items: center; }
        .case-logo-box {
            width: 128px; height: 128px; flex-shrink: 0; border-radius: 24px;
            background: var(--card); border: 1px solid var(--card-border);
            box-shadow: 0 16px 40px rgba(0,0,0,.45);
            display: flex; align-items: center; justify-content: center; padding: 14px;
        }
        .case-logo { width: 100%; height: 100%; object-fit: contain; }
        .case-logo-placeholder {
            width: 100%; height: 100%; border-radius: 16px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 44px;
        }
        .case-title-block { flex: 1; min-width: 260px; padding-bottom: 10px; }
        .case-category {
            display: inline-flex; align-items: center;
            background: rgba(59,130,246,.12); border: 1px solid rgba(59,130,246,.25);
            color: #93c5fd; padding: 5px 14px; border-radius: 999px;
            font-size: 13px; font-weight: 500; margin-bottom: 12px;
        }
        .case-title-block h1 { font-size: clamp(28px, 4vw, 42px); font-weight: 800; letter-spacing: -.03em; color: var(--text); line-height: 1.15; }
        .case-description { max-width: 1200px; margin: 28px auto 0; padding: 0 32px; }
        .case-description p { font-size: 17px; color: var(--text2); max-width: 680px; line-height: 1.7; }

        /* ── Sections (compartido) ── */
        .section { padding: 90px 32px; max-width: 1200px; margin: 0 auto; }
        .section-label { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; color: var(--accent); margin-bottom: 16px; }
        .section-title { font-size: clamp(26px, 4vw, 38px); font-weight: 800; letter-spacing: -.02em; color: var(--text); margin-bottom: 16px; line-height: 1.2; }
        .section-sub { font-size: 17px; color: var(--text2); max-width: 620px; line-height: 1.7; }
        .alt-bg { background: var(--bg2); padding: 1px 0; }

        /* ── Narrativa (problema/solución/resultado) ── */
        .steps-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-top: 48px; }
        .step { background: var(--card); border: 1px solid var(--card-border); border-radius: var(--radius); padding: 32px; }
        .step-num { display: inline-block; font-family: var(--font-mono); font-size: 13px; font-weight: 500; color: var(--accent); background: rgba(59,130,246,.1); border: 1px solid rgba(59,130,246,.2); padding: 4px 12px; border-radius: 8px; margin-bottom: 16px; }
        .step h3 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 10px; }
        .step p { font-size: 14px; color: var(--text2); line-height: 1.7; white-space: pre-line; }

        /* ── Proyectos ── */
        .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-top: 48px; }
        .product-card { background: var(--card); border: 1px solid var(--card-border); border-radius: var(--radius); overflow: hidden; display: flex; flex-direction: column; transition: all .25s; }
        .product-card:hover { border-color: rgba(59,130,246,.3); transform: translateY(-4px); box-shadow: 0 20px 60px rgba(0,0,0,.3); }
        .product-cover { width: 100%; height: 180px; object-fit: cover; background: var(--bg2); }
        .product-body { padding: 28px; display: flex; flex-direction: column; flex: 1; }
        .product-gallery-thumbs { display: flex; gap: 6px; padding: 0 28px 0; margin-top: -14px; }
        .product-gallery-thumbs img { width: 44px; height: 32px; object-fit: cover; border-radius: 6px; border: 2px solid var(--card); }
        .product-name { font-size: 20px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
        .product-meta { display: flex; gap: 10px; margin-bottom: 14px; }
        .product-meta span { font-size: 11.5px; color: var(--text2); background: rgba(255,255,255,.05); border-radius: 6px; padding: 3px 9px; }
        .product-desc { font-size: 14px; color: var(--text2); line-height: 1.7; margin-bottom: 16px; }
        .product-problem { font-size: 13px; color: var(--text2); line-height: 1.65; margin-bottom: 18px; padding: 12px 14px; background: rgba(255,255,255,.03); border-left: 2px solid var(--accent); border-radius: 0 8px 8px 0; }
        .product-problem strong { color: var(--text); }
        .product-features-list { list-style: none; margin-bottom: 22px; flex: 1; }
        .product-features-list li { display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: var(--text2); padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,.04); }
        .product-features-list li:last-child { border-bottom: none; }
        .product-features-list svg { color: var(--success); flex-shrink: 0; }
        .btn-project { align-self: flex-start; padding: 11px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; text-decoration: none; transition: all .2s; }
        .btn-project:hover { transform: translateY(-1px); }

        /* ── Testimonio ── */
        .testimonial { max-width: 780px; margin: 0 auto; text-align: center; }
        .testimonial blockquote { font-size: 22px; font-weight: 600; color: var(--text); line-height: 1.5; letter-spacing: -.01em; margin-bottom: 24px; }
        .testimonial blockquote::before { content: '“'; color: var(--accent); }
        .testimonial blockquote::after { content: '”'; color: var(--accent); }
        .testimonial cite { font-style: normal; font-size: 14px; color: var(--text2); }
        .testimonial cite strong { color: var(--text); display: block; font-size: 15px; margin-bottom: 2px; }

        /* ── CTA ── */
        .cta-wrap { max-width: 1200px; margin: 0 auto; padding: 0 32px 100px; }
        .cta-section { background: linear-gradient(135deg, rgba(59,130,246,.15), rgba(99,102,241,.1)); border: 1px solid rgba(59,130,246,.2); border-radius: 24px; padding: 80px 48px; text-align: center; }
        .cta-title { font-size: clamp(26px, 4vw, 40px); font-weight: 800; color: var(--text); letter-spacing: -.02em; margin-bottom: 14px; line-height: 1.2; }
        .cta-sub { font-size: 17px; color: var(--text2); margin-bottom: 36px; }

        /* ── Footer ── */
        .footer { border-top: 1px solid var(--card-border); padding: 60px 32px 40px; }
        .footer-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; }
        .footer-brand p { font-size: 13px; color: var(--text3); margin-top: 12px; line-height: 1.65; max-width: 240px; }
        .footer-col h4 { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--text3); margin-bottom: 16px; }
        .footer-col a { display: block; font-size: 14px; color: var(--text3); text-decoration: none; margin-bottom: 10px; }
        .footer-col a:hover { color: var(--text2); }
        .footer-bottom { max-width: 1200px; margin: 32px auto 0; padding-top: 24px; border-top: 1px solid var(--card-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .footer-bottom p { font-size: 13px; color: var(--text3); }

        @media (max-width: 900px) { .footer-inner { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 768px) {
            .nav-links, .nav-cta { display: none; }
            .nav-hamburger { display: block; }
            .case-top { padding: 110px 20px 0; }
            .case-breadcrumb-row { padding: 0 20px; margin-bottom: 16px; }
            .case-banner { height: 160px; margin: 0 20px; width: auto; }
            .case-header-row { margin-top: -40px; padding: 0 20px; align-items: flex-start; }
            .case-header-row--flat { margin-top: 20px; }
            .case-logo-box { width: 88px; height: 88px; border-radius: 18px; }
            .case-logo-placeholder { font-size: 30px; }
            .case-description { padding: 0 20px; }
            .section { padding: 56px 20px; }
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
            <li><a href="{{ route('landing.home') }}#casos-exito">Casos de éxito</a></li>
            <li><a href="#historia">Nuestra historia</a></li>
            <li><a href="#sistemas">Trabajos realizados</a></li>
        </ul>

        <div class="nav-cta">
            <a href="{{ route('landing.home') }}" class="btn-nav btn-outline">← Inicio</a>
            <a href="{{ route('landing.contact') }}" class="btn-nav btn-filled">Solicitar una reunión</a>
        </div>

        <button class="nav-hamburger" id="navHamburger" aria-label="Menú">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <div class="nav-mobile-menu" id="mobileMenu">
        <a href="{{ route('landing.home') }}">← Inicio</a>
        <a href="#historia" onclick="closeMenu()">Nuestra historia</a>
        <a href="#sistemas" onclick="closeMenu()">Trabajos realizados</a>
        <a href="{{ route('landing.contact') }}" style="color:var(--accent); margin-top:4px;">Solicitar una reunión →</a>
    </div>
</nav>

{{-- Header del caso --}}
<div class="case-top">
    <div class="case-breadcrumb-row">
        <div class="breadcrumb">
            <a href="{{ route('landing.home') }}">Inicio</a> / <a href="{{ route('landing.home') }}#casos-exito">Casos de éxito</a> / <span>{{ $client->name }}</span>
        </div>
    </div>

    @if($client->cover_image)
        <div class="case-banner">
            <div class="case-banner-bg" style="background-image:url('{{ \Illuminate\Support\Facades\Storage::disk('public')->url($client->cover_image) }}');"></div>
        </div>
    @endif

    <div class="case-header-row {{ $client->cover_image ? '' : 'case-header-row--flat' }}">
        <div class="case-logo-box">
            @if($client->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($client->logo_path) }}" alt="{{ $client->name }}" class="case-logo">
            @else
                <div class="case-logo-placeholder">{{ mb_substr($client->name, 0, 1) }}</div>
            @endif
        </div>

        <div class="case-title-block">
            @if($client->category)
                <div class="case-category">{{ $client->category }}</div>
            @endif
            <h1>{{ $client->name }}</h1>
        </div>
    </div>

    @if($client->short_description)
        <div class="case-description">
            <p>{{ $client->short_description }}</p>
        </div>
    @endif
</div>

{{-- Nuestra historia: problema / solución / resultado --}}
@if($client->challenge || $client->solution || $client->results)
<div class="alt-bg" id="historia">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Nuestra historia con {{ $client->name }}
        </div>
        <h2 class="section-title">Cómo trabajamos juntos</h2>
        <p class="section-sub">De un problema real a un trabajo que hoy forma parte de su operación diaria.</p>

        <div class="steps-grid">
            @if($client->challenge)
                <div class="step">
                    <div class="step-num">01</div>
                    <h3>¿Cuál era el problema?</h3>
                    <p>{{ $client->challenge }}</p>
                </div>
            @endif
            @if($client->solution)
                <div class="step">
                    <div class="step-num">02</div>
                    <h3>¿Qué solución desarrollamos?</h3>
                    <p>{{ $client->solution }}</p>
                </div>
            @endif
            @if($client->results)
                <div class="step">
                    <div class="step-num">03</div>
                    <h3>¿Qué resultados obtuvo?</h3>
                    <p>{{ $client->results }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Sistemas implementados --}}
@if($client->projects->isNotEmpty())
<section id="sistemas">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Trabajos realizados
        </div>
        <h2 class="section-title">Lo que desarrollamos para {{ $client->name }}</h2>
        <p class="section-sub">Cada trabajo fue diseñado a medida para resolver una necesidad concreta de la organización.</p>

        <div class="products-grid">
            @foreach($client->projects as $project)
                @php $cover = $project->coverImage(); @endphp
                <div class="product-card">
                    @if($cover)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cover->path) }}" alt="{{ $project->displayName() }}" class="product-cover">
                    @endif

                    @if($project->images->count() > 1)
                        <div class="product-gallery-thumbs">
                            @foreach($project->images->skip(1) as $img)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="{{ $img->title }}">
                            @endforeach
                        </div>
                    @endif

                    <div class="product-body">
                        <div class="product-name">{{ $project->displayName() }}</div>
                        <div class="product-meta">
                            @if($project->delivered_at)
                                <span>Implementado en {{ $project->delivered_at->year }}</span>
                            @endif
                            <span>{{ $project->publicStatusLabel() }}</span>
                        </div>

                        @if($project->commercial_description)
                            <div class="product-desc">{{ $project->commercial_description }}</div>
                        @endif

                        @if($project->problem_solved)
                            <div class="product-problem"><strong>Qué problema resuelve:</strong> {{ $project->problem_solved }}</div>
                        @endif

                        @if(!empty($project->key_features))
                            <ul class="product-features-list">
                                @foreach($project->key_features as $feature)
                                    <li>
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if($project->production_url)
                            <a href="{{ $project->production_url }}" target="_blank" class="btn-project">Visitar →</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Testimonio --}}
@if($client->testimonial_quote)
<div class="alt-bg">
    <div class="section">
        <div class="testimonial">
            <blockquote>{{ $client->testimonial_quote }}</blockquote>
            <cite>
                @if($client->testimonial_author)<strong>{{ $client->testimonial_author }}</strong>@endif
                {{ $client->testimonial_position }}
            </cite>
        </div>
    </div>
</div>
@endif

{{-- CTA final --}}
<div class="cta-wrap" style="padding-top:100px;">
    <div class="cta-section">
        <h2 class="cta-title">¿Tu empresa tiene desafíos parecidos?</h2>
        <p class="cta-sub">Podemos desarrollar una solución adaptada a tu organización.</p>
        <a href="{{ route('landing.contact') }}" class="btn-nav btn-filled" style="padding:14px 32px; font-size:15px; display:inline-block;">Solicitar una reunión</a>
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
            <a href="{{ route('landing.home') }}#casos-exito">Casos de éxito</a>
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
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});
document.getElementById('navHamburger').addEventListener('click', () => {
    const menu = document.getElementById('mobileMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
});
function closeMenu() { document.getElementById('mobileMenu').style.display = 'none'; }
</script>

</body>
</html>
