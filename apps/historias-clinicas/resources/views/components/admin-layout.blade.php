@props(['header' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($sistemaConfig?->logo_principal ?? $sistemaConfig?->favicon_url)
    <link rel="icon" href="{{ $sistemaConfig->favicon_url ? asset('storage/' . $sistemaConfig->favicon_url) : asset('favicon-sistema-hc.png') }}" type="image/png">
    @else
    <link rel="icon" href="{{ asset('favicon-sistema-hc.png') }}" type="image/png">
    @endif
    <title>{{ $header ? $header . ' — ' : '' }}{{ $sistemaConfig?->nombre_sistema ?? 'Sistema de Historias Clínicas' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --font-sans: 'DM Sans', sans-serif;

            --sidebar-bg:       #0f172a;
            --sidebar-border:   #1e293b;
            --sidebar-text:     #94a3b8;
            --sidebar-hover:    #1e293b;
            --sidebar-active:   #1d4ed8;
            --sidebar-width:    240px;
            --sidebar-coll-w:   70px;

            --topbar-bg:        #ffffff;
            --topbar-border:    #f1f5f9;
            --topbar-height:    60px;

            --body-bg:          #f8fafc;
            --card-bg:          #ffffff;
            --card-border:      #e8edf2;
            --card-shadow:      0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.04);
            --card-radius:      14px;

            --text-primary:     #0f172a;
            --text-secondary:   #64748b;
            --text-muted:       #94a3b8;

            --accent:           #1d4ed8;
            --accent-light:     #eff6ff;
            --accent-hover:     #1e40af;

            --danger:           #e11d48;
            --danger-bg:        #fff1f2;
            --danger-border:    #fecdd3;

            --success:          #16a34a;
            --success-bg:       #f0fdf4;
            --success-border:   #bbf7d0;

            --warning:          #f59e0b;
            --warning-bg:       #fffbeb;
            --warning-border:   #fed7aa;
        }

        /* ── Dark theme ── */
        html.dark {
            --sidebar-bg:       #0b1120;
            --sidebar-border:   #1a2744;
            --sidebar-text:     #94a3b8;
            --sidebar-hover:    #1a2744;
            --sidebar-active:   #1d4ed8;

            --topbar-bg:        #0f172a;
            --topbar-border:    #1e293b;

            --body-bg:          #060d1a;
            --card-bg:          #0f172a;
            --card-border:      #1e293b;
            --card-shadow:      0 1px 3px rgba(0,0,0,.4), 0 4px 16px rgba(0,0,0,.3);

            --text-primary:     #f1f5f9;
            --text-secondary:   #94a3b8;
            --text-muted:       #475569;

            --accent:           #3b82f6;
            --accent-light:     #1e3a5f;
            --accent-hover:     #60a5fa;

            --danger:           #f87171;
            --danger-bg:        #2d0a0a;
            --danger-border:    #7f1d1d;
        }

        html, body {
            height: 100%;
            font-family: var(--font-sans);
            background: var(--body-bg);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            transition: background .2s, color .2s;
        }

        /* ── Layout ── */
        .layout { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        aside#sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg) !important;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            border-right: 1px solid var(--sidebar-border) !important;
            transition: all .3s ease;
            overflow: hidden;
        }

        aside#sidebar.collapsed { width: var(--sidebar-coll-w); }
        aside#sidebar.collapsed .nav-section  { display: none; }
        aside#sidebar.collapsed .logo-texts   { display: none; }
        aside#sidebar.collapsed .sidebar-logo { justify-content: center; padding: 18px 0; }
        aside#sidebar.collapsed .sidebar-footer { display: none; }

        @@media (min-width: 769px) {
            aside#sidebar.collapsed .nav-link > span { display: none; }
        }

        aside#sidebar.collapsed .nav-link {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        aside#sidebar.collapsed .nav-link[data-title]:hover::after {
            content: attr(data-title);
            position: fixed;
            left: 78px;
            background: #111827;
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            white-space: nowrap;
            font-size: 12px;
            z-index: 99999;
            pointer-events: none;
        }

        /* Logo */
        .sidebar-logo {
            padding: 18px 20px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none !important;
            flex-shrink: 0;
        }
        .logo-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .logo-icon svg { width: 18px; height: 18px; }
        .logo-icon img { width: 28px; height: 28px; object-fit: contain; border-radius: 6px; }
        .logo-texts { line-height: 1.2; }
        .logo-name {
            font-size: 15px; font-weight: 700;
            color: #f1f5f9 !important;
            letter-spacing: -.02em;
            display: block;
        }
        .logo-sub {
            font-size: 10px; color: var(--sidebar-text) !important;
            text-transform: uppercase; letter-spacing: .05em;
            margin-top: 1px; display: block;
        }

        /* Nav */
        .sidebar-nav { flex: 1; padding: 12px 10px; overflow-y: auto; overflow-x: hidden; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--sidebar-border); border-radius: 99px; }

        .nav-section {
            font-size: 10px; font-weight: 600;
            color: #475569 !important;
            text-transform: uppercase; letter-spacing: .08em;
            padding: 14px 10px 6px;
        }

        aside#sidebar .nav-link {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 9px 12px !important;
            border-radius: 8px !important;
            color: var(--sidebar-text) !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            transition: all .15s !important;
            margin-bottom: 1px !important;
            background: transparent !important;
            line-height: normal !important;
            height: auto !important;
        }
        aside#sidebar .nav-link:hover {
            background: var(--sidebar-hover) !important;
            color: #e2e8f0 !important;
        }
        aside#sidebar .nav-link.active {
            background: var(--accent) !important;
            color: #fff !important;
        }
        aside#sidebar .nav-link svg {
            width: 18px; height: 18px;
            min-width: 18px; flex-shrink: 0;
            opacity: .8;
        }
        aside#sidebar .nav-link.active svg { opacity: 1; }

        /* Footer */
        .sidebar-footer {
            padding: 14px 10px;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }
        .sidebar-user-card {
            padding: 10px 12px;
            border-radius: 8px;
            background: rgba(255,255,255,.04);
        }
        .sidebar-user-name {
            font-size: 13px; font-weight: 600;
            color: #e2e8f0 !important;
        }
        .sidebar-user-role {
            font-size: 11px; color: var(--sidebar-text) !important;
            margin-top: 2px;
        }

        /* ── Main ── */
        .main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: all .3s ease;
        }
        .main.sidebar-collapsed { margin-left: var(--sidebar-coll-w); }

        /* ── Topbar ── */
        .topbar {
            height: var(--topbar-height);
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--topbar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-title { font-size: 15px; font-weight: 600; color: var(--text-primary); }

        .sidebar-toggle {
            border: none; background: none;
            cursor: pointer; padding: 6px;
            border-radius: 8px; color: var(--text-secondary);
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        .sidebar-toggle:hover { background: #f1f5f9; }
        .sidebar-toggle svg { width: 20px; height: 20px; }

        .topbar-right { display: flex; align-items: center; gap: 8px; }

        /* Topbar icon btn */
        .topbar-icon-btn {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border: none; background: #f1f5f9;
            cursor: pointer; border-radius: 9px;
            color: var(--text-secondary); transition: background .15s;
            font-family: var(--font-sans);
        }
        .topbar-icon-btn:hover { background: #e2e8f0; }
        .topbar-icon-btn svg { width: 17px; height: 17px; }

        /* User dropdown */
        .user-menu-wrap { position: relative; }
        .topbar-user-btn {
            display: flex; align-items: center; gap: 8px;
            background: none; border: none; cursor: pointer;
            font-family: var(--font-sans); color: var(--text-secondary);
            font-size: 13px; font-weight: 500; padding: 5px 8px;
            border-radius: 9px; transition: background .15s;
        }
        .topbar-user-btn:hover { background: #f1f5f9; }
        .topbar-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--accent); color: #fff;
            font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .user-dropdown {
            display: none;
            position: absolute; right: 0; top: calc(100% + 8px);
            width: 210px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden; z-index: 200;
        }
        .user-dropdown.open { display: block; animation: dropIn .12s ease; }
        @@keyframes dropIn {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .user-dropdown-header {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .user-dropdown-header-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .user-dropdown-header-email { font-size: 11px; color: var(--text-muted); margin-top: 2px;
                                      white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-dropdown-body { padding: 6px; }
        .user-dropdown-item {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 10px; border-radius: 7px;
            font-size: 13px; color: var(--text-secondary);
            text-decoration: none; transition: background .12s;
            cursor: pointer; background: none; border: none;
            width: 100%; text-align: left; font-family: var(--font-sans);
        }
        .user-dropdown-item:hover { background: var(--body-bg); }
        .user-dropdown-item svg { width: 15px; height: 15px; flex-shrink: 0; }
        .user-dropdown-item.danger { color: var(--danger); }
        .user-dropdown-divider { height: 1px; background: #f1f5f9; margin: 4px 6px; }

        /* ── Page content ── */
        .page-content { flex: 1; padding: 28px; }

        /* ── Overlay mobile ── */
        #sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.45); z-index: 90;
        }
        #sidebar-overlay.active { display: block; }

        /* ── Mobile ── */
        @@media (max-width: 768px) {
            aside#sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }
            aside#sidebar.mobile-open { transform: translateX(0); }
            .main, .main.sidebar-collapsed { margin-left: 0; }
            .page-content { padding: 18px; }
            .topbar { padding: 0 16px; }
        }
    </style>

    <style>
        /* Flash / notify toasts */
        .hc-flash {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 16px; border-radius: 10px;
            font-size: 13px; font-weight: 500;
        }
        .hc-flash svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
        .f-ok  { background: var(--success-bg,  #f0fdf4); border: 1px solid var(--success-border, #bbf7d0); color: var(--success,  #166534); }
        .f-err { background: var(--danger-bg,   #fff1f2); border: 1px solid var(--danger-border,  #fecdd3); color: var(--danger,   #e11d48); }
        .f-war { background: var(--warning-bg,  #fffbeb); border: 1px solid var(--warning-border, #fed7aa); color: var(--warning,  #d97706); }
    </style>

    @stack('styles')
</head>
<body>
@include('notify::components.notify')
<div class="layout">

    {{-- ── SIDEBAR ── --}}
    <aside id="sidebar">

        {{-- Logo --}}
        <a href="{{ route('admin.dashboard.home') }}" class="sidebar-logo">
            <div class="logo-icon">
                @if($sistemaConfig?->logo_admin ?? $sistemaConfig?->logo_url)
                    <img src="{{ asset('storage/' . ($sistemaConfig->logo_admin ?? $sistemaConfig->logo_url)) }}" alt="">
                @else
                    <svg fill="none" stroke="#fff" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                @endif
            </div>
            <div class="logo-texts">
                <span class="logo-name">{{ $sistemaConfig?->nombre_sistema ?? 'Sistema HC' }}</span>
                <span class="logo-sub">Panel administrativo</span>
            </div>
        </a>

        {{-- Nav --}}
        <nav class="sidebar-nav">

            <div class="nav-section">Principal</div>
            <a href="{{ route('admin.dashboard.home') }}" data-title="Dashboard"
               class="nav-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 5v4M16 5v4"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('panel.home') }}" data-title="Panel Médico" class="nav-link" target="_blank">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <span>Panel Médico</span>
            </a>

            <div class="nav-section">Atención Clínica</div>
            <a href="{{ route('panel.paciente.index') }}" data-title="Pacientes"
               class="nav-link {{ request()->routeIs('panel.paciente.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
                <span>Pacientes</span>
            </a>
            <a href="{{ route('admin.paciente.index') }}" data-title="Admin Pacientes"
               class="nav-link {{ request()->routeIs('admin.paciente.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Admin Pacientes</span>
            </a>
            @if(Route::has('panel.agenda.index'))
            <a href="{{ route('panel.agenda.index') }}" data-title="Agenda"
               class="nav-link {{ request()->routeIs('panel.agenda.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Agenda</span>
            </a>
            @endif
            <a href="{{ route('panel.informe.index') }}" data-title="Informes"
               class="nav-link {{ request()->routeIs('panel.informe.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Informes</span>
            </a>
            <a href="{{ route('panel.medicacion.index') }}" data-title="Prescripciones"
               class="nav-link {{ request()->routeIs('panel.medicacion.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <span>Prescripciones</span>
            </a>

            <a href="{{ route('panel.recetas.index') }}" data-title="Recetas"
               class="nav-link {{ request()->routeIs('panel.recetas.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Recetas</span>
            </a>

            <div class="nav-section">Comunicación</div>
            <a href="{{ route('panel.messenger.index') }}" data-title="Mensajería"
               class="nav-link {{ request()->routeIs('panel.messenger.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Mensajería</span>
            </a>

            <div class="nav-section">Administración</div>
            <a href="{{ route('admin.users.index') }}" data-title="Usuarios"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Usuarios</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" data-title="Roles"
               class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Roles</span>
            </a>
            <a href="{{ route('admin.permissions.index') }}" data-title="Permisos"
               class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                <span>Permisos</span>
            </a>
            <a href="{{ route('admin.audit-logs.index') }}" data-title="Auditoría"
               class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Auditoría</span>
            </a>

            <div class="nav-section">Configuración</div>
            <a href="{{ route('admin.especialidades.index') }}" data-title="Especialidades"
               class="nav-link {{ request()->routeIs('admin.especialidades.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span>Especialidades</span>
            </a>
            <a href="{{ route('admin.tipos-consentimiento.index') }}" data-title="Tipos de Consentimiento"
               class="nav-link {{ request()->routeIs('admin.tipos-consentimiento.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span>Consentimientos</span>
            </a>
            <a href="{{ route('admin.configuracion.edit') }}" data-title="Configuración"
               class="nav-link {{ request()->routeIs('admin.configuracion.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Configuración</span>
            </a>

        </nav>

        {{-- Footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Usuario' }}</div>
                <div class="sidebar-user-role">Administrador</div>
            </div>
        </div>

    </aside>

    {{-- Overlay --}}
    <div id="sidebar-overlay"></div>

    {{-- ── MAIN ── --}}
    <div class="main" id="main">

        {{-- Topbar --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="topbar-title">
                    @isset($header)
                        {{ is_string($header) ? $header : strip_tags((string) $header) }}
                    @else
                        Dashboard
                    @endisset
                </span>
            </div>

            <div class="topbar-right">
                {{-- Mensajes --}}
                @php
                    $mensajesNoLeidos = \App\Models\QaMessage::whereNull('read_at')
                        ->where('sender_id', '!=', auth()->id())
                        ->whereHas('topic', function($query) {
                            $query->where('receiver_id', auth()->id())
                                  ->orWhere('creator_id', auth()->id());
                        })->count();
                    $ultimosMensajes = \App\Models\QaMessage::with(['topic', 'sender'])
                        ->whereHas('topic', function($query) {
                            $query->where('receiver_id', auth()->id())
                                  ->orWhere('creator_id', auth()->id());
                        })
                        ->orderBy('created_at', 'desc')
                        ->take(8)
                        ->get();
                @endphp
                <div style="position:relative;" id="messages-wrap">
                    <button class="topbar-icon-btn" id="messages-btn" title="Mensajes" onclick="toggleMessages()" style="position:relative;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        @if($mensajesNoLeidos > 0)
                        <span style="position:absolute;top:2px;right:2px;width:8px;height:8px;background:#dc2626;border-radius:50%;border:2px solid var(--topbar-bg);"></span>
                        @endif
                    </button>

                    <div id="messages-dropdown" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:320px;background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;box-shadow:var(--card-shadow);overflow:hidden;z-index:200;">
                        <div style="padding:12px 16px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:13px;font-weight:700;color:var(--text-primary);">
                                Mensajes
                                @if($mensajesNoLeidos > 0)
                                <span style="margin-left:6px;background:#dc2626;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:99px;">{{ $mensajesNoLeidos }}</span>
                                @endif
                            </span>
                            <a href="{{ route('panel.messenger.index') }}" style="font-size:11px;color:var(--accent);font-weight:600;text-decoration:none;">Ver todos</a>
                        </div>

                        <div style="max-height:300px;overflow-y:auto;">
                            @forelse($ultimosMensajes as $mensaje)
                            <div style="padding:10px 16px;border-bottom:1px solid var(--card-border);display:flex;gap:10px;align-items:flex-start;{{ !$mensaje->read_at ? 'background:var(--accent-light);' : '' }}">
                                <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:5px;background:{{ !$mensaje->read_at ? 'var(--accent)' : 'var(--card-border)' }};"></div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:11px;font-weight:{{ !$mensaje->read_at ? '700' : '500' }};color:var(--text-primary);margin-bottom:3px;">
                                        {{ $mensaje->sender->name ?? 'Usuario' }}
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);line-height:1.4;margin-bottom:4px;">
                                        {{ $mensaje->topic->subject ?? 'Sin asunto' }}
                                    </div>
                                    <div style="font-size:10px;color:var(--text-muted);">{{ $mensaje->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @empty
                            <div style="padding:28px;text-align:center;font-size:13px;color:var(--text-muted);">No tienes mensajes.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Agenda --}}
                @php
                    $citasHoy = \App\Models\Agenda::whereDate('fecha_hora_inicio', now())
                        ->where(function($query) {
                            $query->where('profesional_id', auth()->id())
                                  ->orWhere('creado_por', auth()->id());
                        })
                        ->orderBy('fecha_hora_inicio')
                        ->get();
                    $citasPendientes = $citasHoy->where('estado', 'pendiente')->count();
                @endphp
                <div style="position:relative;" id="agenda-wrap">
                    <button class="topbar-icon-btn" id="agenda-btn" title="Agenda del día" onclick="toggleAgenda()" style="position:relative;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        @if($citasPendientes > 0)
                        <span style="position:absolute;top:2px;right:2px;width:8px;height:8px;background:#f59e0b;border-radius:50%;border:2px solid var(--topbar-bg);"></span>
                        @endif
                    </button>

                    <div id="agenda-dropdown" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:320px;background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;box-shadow:var(--card-shadow);overflow:hidden;z-index:200;">
                        <div style="padding:12px 16px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:13px;font-weight:700;color:var(--text-primary);">
                                Agenda de hoy
                                @if($citasPendientes > 0)
                                <span style="margin-left:6px;background:#f59e0b;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:99px;">{{ $citasPendientes }}</span>
                                @endif
                            </span>
                            @if(Route::has('panel.agenda.index'))
                            <a href="{{ route('panel.agenda.index') }}" style="font-size:11px;color:var(--accent);font-weight:600;text-decoration:none;">Ver agenda</a>
                            @endif
                        </div>

                        <div style="max-height:300px;overflow-y:auto;">
                            @forelse($citasHoy as $cita)
                            <div style="padding:10px 16px;border-bottom:1px solid var(--card-border);display:flex;gap:10px;align-items:flex-start;">
                                @php
                                    $dotColorA = match($cita->estado ?? '') {
                                        'confirmado' => '#16a34a',
                                        'realizado'  => '#6c757d',
                                        'cancelado'  => '#dc2626',
                                        default      => '#f59e0b',
                                    };
                                    $bdgStyleA = match($cita->estado ?? '') {
                                        'confirmado' => 'background:#dcfce7;color:#16a34a;',
                                        'realizado'  => 'background:#f3f4f6;color:#6c757d;',
                                        'cancelado'  => 'background:#fee2e2;color:#dc2626;',
                                        default      => 'background:#fef3c7;color:#d97706;',
                                    };
                                    $bdgLabelA = \App\Models\Agenda::estadosLabels()[$cita->estado] ?? ucfirst($cita->estado ?? '');
                                @endphp
                                <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:5px;background:{{ $dotColorA }};"></div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:12px;font-weight:600;color:var(--text-primary);margin-bottom:3px;">
                                        {{ $cita->fecha_hora_inicio->format('H:i') }}
                                    </div>
                                    <div style="font-size:11px;color:var(--text-secondary);line-height:1.4;margin-bottom:2px;">
                                        {{ $cita->paciente->nombre ?? 'Sin paciente' }} {{ $cita->paciente->apellido ?? '' }}
                                    </div>
                                    <div style="font-size:10px;color:var(--text-muted);">
                                        {{ Str::limit($cita->motivo, 40) }}
                                    </div>
                                    <div style="margin-top:4px;">
                                        <span style="display:inline-block;padding:1px 6px;border-radius:99px;font-size:9px;font-weight:500;{{ $bdgStyleA }}">{{ $bdgLabelA }}</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div style="padding:28px;text-align:center;font-size:13px;color:var(--text-muted);">No tienes citas programadas para hoy.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Theme toggle --}}
                <button class="topbar-icon-btn" id="theme-toggle" title="Cambiar tema" aria-label="Cambiar tema">
                    <svg id="icon-sun" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1M12 20v1M4.2 4.2l.7.7M18.1 18.1l.7.7M1 12h1M22 12h1M4.2 19.8l.7-.7M18.1 5.9l.7-.7M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                    </svg>
                    <svg id="icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                </button>

                {{-- User menu --}}
                <div class="user-menu-wrap">
                    <button class="topbar-user-btn" id="user-menu-btn">
                        <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email ?? 'U', 0, 1)) }}</div>
                        {{ auth()->user()->name ?? 'Usuario' }}
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;opacity:.5;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div class="user-dropdown" id="user-dropdown">
                        <div class="user-dropdown-header">
                            <div class="user-dropdown-header-name">{{ auth()->user()->name ?? 'Usuario' }}</div>
                            <div class="user-dropdown-header-email">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="user-dropdown-body">
                            <a href="{{ route('panel.profile.index') }}" class="user-dropdown-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Mi perfil
                            </a>
                            <div class="user-dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="user-dropdown-item danger">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="page-content">
            {{ $slot }}
        </main>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar  = document.getElementById('sidebar');
    const main     = document.getElementById('main');
    const toggle   = document.getElementById('sidebar-toggle');
    const overlay  = document.getElementById('sidebar-overlay');
    const userBtn  = document.getElementById('user-menu-btn');
    const userDrop = document.getElementById('user-dropdown');

    const isMobile = () => window.innerWidth <= 768;

    // ── Theme toggle ──
    const themeBtn  = document.getElementById('theme-toggle');
    const iconSun   = document.getElementById('icon-sun');
    const iconMoon  = document.getElementById('icon-moon');

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            iconSun.style.display  = 'block';
            iconMoon.style.display = 'none';
        } else {
            document.documentElement.classList.remove('dark');
            iconSun.style.display  = 'none';
            iconMoon.style.display = 'block';
        }
    }

    applyTheme(localStorage.getItem('theme') || 'light');

    themeBtn?.addEventListener('click', () => {
        const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        applyTheme(next);
    });

    // Restore state
    if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        main.classList.add('sidebar-collapsed');
    }

    toggle?.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        } else {
            const c = sidebar.classList.toggle('collapsed');
            main.classList.toggle('sidebar-collapsed', c);
            localStorage.setItem('sidebarCollapsed', c);
        }
    });

    overlay?.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    // Messages dropdown
    window.toggleMessages = function() {
        const dd = document.getElementById('messages-dropdown');
        if (!dd) return;
        const isOpen = dd.style.display !== 'none';
        dd.style.display = isOpen ? 'none' : 'block';

        // Close agenda if open
        const agendaDd = document.getElementById('agenda-dropdown');
        if (agendaDd) agendaDd.style.display = 'none';
    };

    // Agenda dropdown
    window.toggleAgenda = function() {
        const dd = document.getElementById('agenda-dropdown');
        if (!dd) return;
        const isOpen = dd.style.display !== 'none';
        dd.style.display = isOpen ? 'none' : 'block';

        // Close messages if open
        const messagesDd = document.getElementById('messages-dropdown');
        if (messagesDd) messagesDd.style.display = 'none';
    };

    // Close dropdowns when clicking outside
    document.addEventListener('click', e => {
        const messagesWrap = document.getElementById('messages-wrap');
        const agendaWrap = document.getElementById('agenda-wrap');

        if (messagesWrap && !messagesWrap.contains(e.target)) {
            const dd = document.getElementById('messages-dropdown');
            if (dd) dd.style.display = 'none';
        }

        if (agendaWrap && !agendaWrap.contains(e.target)) {
            const dd = document.getElementById('agenda-dropdown');
            if (dd) dd.style.display = 'none';
        }
    });

    // User dropdown
    userBtn?.addEventListener('click', e => {
        e.stopPropagation();
        userDrop.classList.toggle('open');
    });
    document.addEventListener('click', () => userDrop?.classList.remove('open'));
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            userDrop?.classList.remove('open');
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';

            // Close notification dropdowns
            const messagesDd = document.getElementById('messages-dropdown');
            const agendaDd = document.getElementById('agenda-dropdown');
            if (messagesDd) messagesDd.style.display = 'none';
            if (agendaDd) agendaDd.style.display = 'none';
        }
    });

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});
</script>

@stack('scripts')
</body>
</html>