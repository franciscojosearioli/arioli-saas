@php
$content = [
    'desarrollo-web' => [
        'name'        => 'Desarrollo Web',
        'tagline'     => 'Sitios institucionales, blogs y tiendas online',
        'description' => 'Diseñamos y desarrollamos el sitio que tu negocio necesita, con un enfoque distinto según el tipo de proyecto. No es una plantilla genérica: es un sitio pensado para lo que realmente querés lograr.',
        'emoji'       => '🌐',
        'accent'      => '#60a5fa',
        'accent_bg'   => 'rgba(59,130,246,.15)',
        'cases' => [
            ['emoji' => '🏢', 'title' => 'Sitios institucionales',
             'desc'  => 'Presencia profesional para tu empresa: quiénes somos, servicios, equipo, casos de éxito y contacto. Diseño a medida, responsive y optimizado para que transmita confianza desde el primer segundo.'],
            ['emoji' => '📝', 'title' => 'Blogs',
             'desc'  => 'Plataforma de contenido con gestión simple de artículos, categorías y autores. Pensado para SEO desde el diseño, para que tu contenido se encuentre en los buscadores.'],
            ['emoji' => '🛒', 'title' => 'Tiendas online',
             'desc'  => 'E-commerce completo: catálogo de productos, carrito de compras, medios de pago (Mercado Pago y otros), gestión de pedidos y stock. Adaptado al volumen y tipo de productos que vendés.'],
        ],
        'steps' => [
            ['num' => '01', 'title' => 'Relevamiento',   'desc' => 'Entendemos tu negocio, objetivos y qué necesitás que el sitio resuelva.'],
            ['num' => '02', 'title' => 'Propuesta',      'desc' => 'Te enviamos alcance, tiempos y presupuesto claro, sin sorpresas.'],
            ['num' => '03', 'title' => 'Desarrollo',     'desc' => 'Diseñamos y construimos el sitio, con instancias de revisión en el camino.'],
            ['num' => '04', 'title' => 'Entrega',        'desc' => 'Publicamos el sitio en tu dominio y te dejamos capacitado para administrarlo.'],
        ],
        'faqs' => [
            ['q' => '¿El dominio y el hosting están incluidos?',
             'a' => 'No, van por separado. Te asesoramos para elegir la opción que más te convenga según el tipo de sitio.'],
            ['q' => '¿Puedo pedir cambios después de la entrega?',
             'a' => 'Sí. Ofrecemos mantenimiento y soporte como servicio aparte para cambios, actualizaciones y nuevas funcionalidades.'],
            ['q' => '¿Cuánto tarda un proyecto?',
             'a' => 'Depende del alcance — un sitio institucional simple puede estar en pocas semanas, una tienda online completa lleva más tiempo. Te damos un plazo estimado en la propuesta.'],
            ['q' => '¿Trabajan con mi diseño existente o parten de cero?',
             'a' => 'Ambas opciones. Si ya tenés una identidad de marca la respetamos, o diseñamos desde cero si estás empezando.'],
        ],
    ],
    'desarrollo-a-medida' => [
        'name'        => 'Desarrollos a Medida',
        'tagline'     => 'Software personalizado, themes, plugins y soluciones WordPress',
        'description' => 'Cuando un sistema estándar no alcanza, construimos exactamente lo que tu proyecto necesita — desde cero o extendiendo una plataforma existente.',
        'emoji'       => '🛠️',
        'accent'      => '#818cf8',
        'accent_bg'   => 'rgba(99,102,241,.15)',
        'cases' => [
            ['emoji' => '⚙️', 'title' => 'Desarrollos personalizados',
             'desc'  => 'Software a medida para necesidades específicas que no encajan en un producto estándar: automatizaciones, integraciones entre sistemas, herramientas internas y aplicaciones web.'],
            ['emoji' => '🎨', 'title' => 'Themes y plugins',
             'desc'  => 'Temas y plugins de WordPress hechos a medida, o modificación de existentes para que tu sitio haga exactamente lo que necesitás, sin depender de soluciones genéricas.'],
            ['emoji' => '🔧', 'title' => 'Soluciones WordPress',
             'desc'  => 'Instalación, configuración, optimización de velocidad, seguridad y migración de sitios WordPress. También resolución de problemas en sitios existentes.'],
        ],
        'steps' => [
            ['num' => '01', 'title' => 'Consulta',       'desc' => 'Nos contás el problema o la idea, sin importar qué tan específica o técnica sea.'],
            ['num' => '02', 'title' => 'Propuesta',      'desc' => 'Analizamos la mejor solución técnica y te la presentamos con presupuesto y plazos.'],
            ['num' => '03', 'title' => 'Desarrollo',     'desc' => 'Construimos la solución con instancias de prueba y ajuste durante el proceso.'],
            ['num' => '04', 'title' => 'Entrega y soporte', 'desc' => 'Implementamos en tu entorno y quedamos disponibles para ajustes posteriores.'],
        ],
        'faqs' => [
            ['q' => '¿Qué tipo de desarrollos personalizados hacen?',
             'a' => 'Desde automatizaciones puntuales hasta sistemas completos. Si tu necesidad es muy específica, contanos el caso y te decimos si es viable.'],
            ['q' => '¿Modifican plugins o themes que ya compré?',
             'a' => 'Sí, podemos adaptar temas y plugins existentes (propios o de terceros) a tus necesidades, dentro de lo que la licencia del producto original permita.'],
            ['q' => '¿Hacen mantenimiento de sitios WordPress ya existentes?',
             'a' => 'Sí. Revisamos el estado actual del sitio, resolvemos problemas de rendimiento o seguridad, y podemos hacernos cargo del mantenimiento continuo.'],
            ['q' => '¿Incluye hosting o solo el desarrollo?',
             'a' => 'Solo el desarrollo — el hosting y dominio van por separado, pero te podemos asesorar en la elección.'],
        ],
    ],
];

$slug   = $slug ?? 'desarrollo-web';
$c      = $content[$slug];
$accent = $c['accent'];
$acBg   = $c['accent_bg'];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $c['name'] }} — Arioli.dev</title>
    <meta name="description" content="{{ $c['tagline'] }}">
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
            --accent:  {{ $accent }};
            --accent2: #6366f1;
            --accent-bg: {{ $acBg }};
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

        /* ── Hero ── */
        .hero { min-height: 60vh; display: flex; align-items: center; padding: 140px 32px 60px; position: relative; overflow: hidden; }
        .hero-bg {
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 30% 30%, rgba(59,130,246,.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 70%, {{ $acBg }} 0%, transparent 50%);
        }
        .hero-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .hero-content { position: relative; max-width: 700px; }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text3); margin-bottom: 28px; }
        .breadcrumb a { color: var(--text3); text-decoration: none; transition: color .2s; }
        .breadcrumb a:hover { color: var(--text2); }
        .hero-icon { width: 72px; height: 72px; border-radius: 20px; background: var(--accent-bg); border: 1px solid rgba(255,255,255,.08); display: flex; align-items: center; justify-content: center; font-size: 36px; margin-bottom: 24px; }
        .hero h1 { font-size: clamp(32px, 5vw, 56px); font-weight: 800; line-height: 1.1; letter-spacing: -.03em; color: var(--text); margin-bottom: 14px; }
        .hero-tagline { font-size: 18px; font-weight: 500; color: var(--accent); margin-bottom: 20px; }
        .hero p { font-size: 17px; color: var(--text2); max-width: 560px; line-height: 1.7; margin-bottom: 36px; }
        .hero-actions { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .btn-hero-primary {
            padding: 14px 32px; border-radius: 12px; font-size: 15px; font-weight: 700;
            background: var(--accent); color: #fff; text-decoration: none;
            box-shadow: 0 8px 32px rgba(0,0,0,.25); transition: all .2s; border: none; cursor: pointer;
            font-family: var(--font-sans);
        }
        .btn-hero-primary:hover { opacity: .88; transform: translateY(-2px); }
        .btn-hero-secondary {
            padding: 14px 32px; border-radius: 12px; font-size: 15px; font-weight: 600;
            background: transparent; color: var(--text2); text-decoration: none; border: 1.5px solid var(--card-border);
            transition: all .2s; font-family: var(--font-sans);
        }
        .btn-hero-secondary:hover { border-color: var(--accent); color: var(--text); }

        /* ── Sections ── */
        .section { padding: 100px 32px; max-width: 1200px; margin: 0 auto; }
        .section-label { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; color: var(--accent); margin-bottom: 16px; }
        .section-title { font-size: clamp(26px, 4vw, 40px); font-weight: 800; letter-spacing: -.02em; color: var(--text); margin-bottom: 16px; line-height: 1.2; }
        .section-sub { font-size: 17px; color: var(--text2); max-width: 560px; line-height: 1.7; }
        .alt-bg { background: var(--bg2); padding: 1px 0; }

        /* ── Cases (features grid reutilizado) ── */
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 56px; }
        .feature-card { background: var(--card); border: 1px solid var(--card-border); border-radius: 14px; padding: 32px; transition: border-color .2s; }
        .feature-card:hover { border-color: var(--accent); }
        .feature-icon { width: 52px; height: 52px; border-radius: 14px; background: var(--accent-bg); display: flex; align-items: center; justify-content: center; margin-bottom: 20px; font-size: 26px; }
        .feature-title { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 10px; }
        .feature-desc { font-size: 14.5px; color: var(--text2); line-height: 1.7; }

        /* ── Steps ── */
        .steps-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-top: 56px; }
        .step { background: var(--card); border: 1px solid var(--card-border); border-radius: var(--radius); padding: 32px; text-align: center; }
        .step-num { display: inline-block; font-family: var(--font-mono); font-size: 13px; font-weight: 500; color: var(--accent); background: var(--accent-bg); border: 1px solid rgba(255,255,255,.1); padding: 4px 12px; border-radius: 8px; margin-bottom: 16px; letter-spacing: .05em; }
        .step h3 { font-size: 17px; font-weight: 700; color: var(--text); margin-bottom: 10px; }
        .step p { font-size: 14px; color: var(--text2); line-height: 1.65; }

        /* ── Form ── */
        .form-group { margin-bottom: 18px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--text2); margin-bottom: 7px; }
        .form-input, .form-textarea {
            width: 100%; padding: 11px 14px; background: rgba(255,255,255,.05);
            border: 1.5px solid var(--card-border); border-radius: 10px;
            font-size: 14px; font-family: var(--font-sans); color: var(--text); outline: none; transition: all .2s;
        }
        select.form-input { cursor: pointer; }
        .form-input option { background: var(--card); color: var(--text); }
        .form-textarea { resize: vertical; min-height: 110px; }
        .form-input:focus, .form-textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
        .form-input::placeholder, .form-textarea::placeholder { color: #475569; }
        .form-error { font-size: 12px; color: #f87171; margin-top: 5px; }
        .btn-submit {
            width: 100%; padding: 13px; background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; font-size: 15px; font-weight: 700; font-family: var(--font-sans); border: none;
            border-radius: 12px; cursor: pointer; box-shadow: 0 6px 24px rgba(59,130,246,.3); transition: all .2s;
        }
        .btn-submit:hover { transform: translateY(-1px); }
        .alert-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); border-radius: 10px; padding: 14px 16px; margin-bottom: 22px; font-size: 13.5px; color: #34d399; }
        .alert-error { background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.3); border-radius: 10px; padding: 14px 16px; margin-bottom: 22px; font-size: 13.5px; color: #f87171; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }

        /* ── FAQ ── */
        .faq-list { margin-top: 48px; max-width: 800px; }
        .faq-item { border-bottom: 1px solid var(--card-border); }
        .faq-btn { display: flex; align-items: center; justify-content: space-between; gap: 16px; width: 100%; background: none; border: none; padding: 22px 0; cursor: pointer; text-align: left; font-family: var(--font-sans); transition: color .2s; }
        .faq-q-text { font-size: 16px; font-weight: 600; color: var(--text2); transition: color .2s; flex: 1; }
        .faq-btn:hover .faq-q-text { color: var(--text); }
        .faq-icon { flex-shrink: 0; width: 28px; height: 28px; border-radius: 8px; background: var(--card); border: 1px solid var(--card-border); display: flex; align-items: center; justify-content: center; color: var(--text3); font-size: 16px; transition: all .2s; }
        .faq-item.open .faq-icon { background: var(--accent-bg); border-color: var(--accent); color: var(--accent); }
        .faq-item.open .faq-q-text { color: var(--text); }
        .faq-answer { font-size: 14.5px; color: var(--text2); line-height: 1.75; padding-bottom: 22px; display: none; }
        .faq-item.open .faq-answer { display: block; }

        /* ── Contact card ── */
        .contact-card { background: var(--card); border: 1px solid var(--card-border); border-radius: var(--radius); padding: 40px; max-width: 640px; margin-top: 40px; }

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
            .hero { padding: 120px 20px 40px; }
            .section { padding: 60px 20px; }
            .contact-card { padding: 28px; }
            .footer { padding: 40px 20px 32px; }
            .footer-inner { grid-template-columns: 1fr; gap: 28px; }
            .features-grid { grid-template-columns: 1fr; }
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
            <li><a href="{{ route('landing.home') }}#servicios">Servicios</a></li>
            <li><a href="{{ route('landing.home') }}#productos">Sistemas</a></li>
            <li><a href="{{ route('landing.contact') }}">Contacto</a></li>
        </ul>

        <div class="nav-cta">
            <a href="https://{{ config('app.cliente_domain') }}" class="btn-nav btn-outline">Panel cliente</a>
            <a href="#contacto" class="btn-nav btn-accent">Contactar</a>
        </div>

        <button class="nav-hamburger" id="navHamburger" aria-label="Menú">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <div class="nav-mobile-menu" id="mobileMenu">
        <a href="{{ route('landing.home') }}">← Inicio</a>
        <a href="{{ route('landing.home') }}#servicios">Servicios</a>
        <a href="{{ route('landing.home') }}#productos">Sistemas</a>
        <a href="{{ route('landing.contact') }}">Contacto</a>
    </div>
</nav>

{{-- Hero --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-content">
        <div class="breadcrumb">
            <a href="{{ route('landing.home') }}">Inicio</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('landing.home') }}#servicios">Servicios</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>{{ $c['name'] }}</span>
        </div>

        <div class="hero-icon">{{ $c['emoji'] }}</div>

        <h1>{{ $c['name'] }}</h1>
        <div class="hero-tagline">{{ $c['tagline'] }}</div>
        <p>{{ $c['description'] }}</p>

        <div class="hero-actions">
            <a href="#contacto" class="btn-hero-primary">Contactar ahora →</a>
            <a href="#casos" class="btn-hero-secondary">Ver detalle</a>
        </div>
    </div>
</section>

{{-- Casos / detalle --}}
<div class="alt-bg" id="casos">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Qué incluye
        </div>
        <h2 class="section-title">Detalle del servicio</h2>
        <p class="section-sub">Cada caso se aborda de forma distinta según lo que tu proyecto necesita.</p>

        <div class="features-grid">
            @foreach($c['cases'] as $case)
                <div class="feature-card">
                    <div class="feature-icon">{{ $case['emoji'] }}</div>
                    <div class="feature-title">{{ $case['title'] }}</div>
                    <div class="feature-desc">{{ $case['desc'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Cómo funciona --}}
<section>
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Proceso de trabajo
        </div>
        <h2 class="section-title">Cómo trabajamos</h2>
        <p class="section-sub">Un proceso simple y transparente, de la consulta inicial a la entrega.</p>

        <div class="steps-grid">
            @foreach($c['steps'] as $step)
                <div class="step">
                    <div class="step-num">{{ $step['num'] }}</div>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Contacto --}}
<div class="alt-bg" id="contacto">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Contacto
        </div>
        <h2 class="section-title">Contanos tu proyecto</h2>
        <p class="section-sub">Escribinos por WhatsApp o dejanos tus datos y te contactamos nosotros.</p>

        <a href="https://wa.me/5493435433577?text={{ urlencode('Hola! Quiero consultar sobre el servicio de ' . $c['name'] . '.') }}"
           target="_blank" class="btn-hero-primary" style="display:inline-flex; align-items:center; gap:8px; margin-top:32px; margin-bottom:8px;">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 004.79 1.22h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2zm0 18.13c-1.48 0-2.93-.4-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 01-1.26-4.36c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 012.42 5.83c0 4.55-3.7 8.21-8.26 8.21zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.17.24-.64.8-.78.97-.14.17-.29.19-.53.06-.25-.12-1.04-.38-1.99-1.22-.73-.66-1.23-1.46-1.37-1.71-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.15.16-.25.25-.42.08-.16.04-.31-.02-.43-.06-.13-.56-1.35-.77-1.85-.2-.48-.41-.42-.56-.43-.14-.01-.31-.01-.48-.01-.17 0-.43.06-.66.31-.23.24-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.57.12.16 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.53.59.19 1.13.16 1.55.1.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.07-.1-.23-.16-.48-.28z"/></svg>
            Escribinos por WhatsApp
        </a>

        <div class="contact-card">
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

            <form method="POST" action="{{ route('landing.service.inquiry', $slug) }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="tu@email.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono (opcional)</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+54 9 ...">
                </div>
                <div class="form-group">
                    <label class="form-label">Contanos tu proyecto</label>
                    <textarea name="message" class="form-textarea" placeholder="Qué necesitás, y cualquier detalle que nos ayude a entender el alcance" required>{{ old('message') }}</textarea>
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
</div>

{{-- FAQ --}}
<section id="faq">
    <div class="section">
        <div class="section-label">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Preguntas frecuentes
        </div>
        <h2 class="section-title">Dudas sobre {{ $c['name'] }}</h2>

        <div class="faq-list" id="faqList">
            @foreach($c['faqs'] as $i => $faq)
                <div class="faq-item" data-index="{{ $i }}">
                    <button class="faq-btn" onclick="toggleFaq({{ $i }})">
                        <span class="faq-q-text">{{ $faq['q'] }}</span>
                        <span class="faq-icon" id="faqIcon{{ $i }}">+</span>
                    </button>
                    <div class="faq-answer" id="faqAnswer{{ $i }}">{{ $faq['a'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

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
            <p>Sistemas de gestión SaaS y desarrollo web a medida para empresas argentinas.</p>
        </div>

        <div class="footer-col">
            <h4>Sistemas</h4>
            <a href="{{ route('landing.product', 'loteos') }}">Loteos</a>
            <a href="{{ route('landing.product', 'tallerpro') }}">Servis — Talleres</a>
            <a href="{{ route('landing.product', 'historias-clinicas') }}">Clínica — Historias</a>
        </div>

        <div class="footer-col">
            <h4>Servicios</h4>
            <a href="{{ route('landing.service', 'desarrollo-web') }}">Desarrollo Web</a>
            <a href="{{ route('landing.service', 'desarrollo-a-medida') }}">Desarrollos a Medida</a>
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

let openFaq = null;
function toggleFaq(i) {
    const item   = document.querySelector(`.faq-item[data-index="${i}"]`);
    const answer = document.getElementById(`faqAnswer${i}`);
    const icon   = document.getElementById(`faqIcon${i}`);
    if (openFaq !== null && openFaq !== i) {
        document.querySelector(`.faq-item[data-index="${openFaq}"]`).classList.remove('open');
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
