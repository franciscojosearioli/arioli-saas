<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Mi Panel' }} — Arioli.dev</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon-180.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --font-sans: 'DM Sans', sans-serif;
            --font-mono: 'DM Mono', monospace;
            --sidebar-bg:     #0f172a;
            --sidebar-border: #1e293b;
            --sidebar-text:   #94a3b8;
            --sidebar-hover:  #1e293b;
            --sidebar-active: #1d4ed8;
            --sidebar-width:  240px;
            --sidebar-collapsed-width: 70px;
            --topbar-bg:      #ffffff;
            --topbar-border:  #f1f5f9;
            --topbar-height:  60px;
            --body-bg:        #f8fafc;
            --card-bg:        #ffffff;
            --card-border:    #e8edf2;
            --card-shadow:    0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.04);
            --card-radius:    14px;
            --text-primary:   #0f172a;
            --text-secondary: #64748b;
            --text-muted:     #94a3b8;
            --accent:         #1d4ed8;
            --accent-light:   #eff6ff;
            --accent-hover:   #1e40af;
            --success:        #166534;
            --success-bg:     #f0fdf4;
            --success-border: #bbf7d0;
            --danger:         #e11d48;
            --danger-bg:      #fff1f2;
            --danger-border:  #fecdd3;
            --warning:        #f59e0b;
        }

        html, body {
            height: 100%;
            font-family: var(--font-sans);
            background: var(--body-bg);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0;
            z-index: 100; border-right: 1px solid var(--sidebar-border);
            transition: all .3s ease;
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }

        .sidebar.collapsed .nav-section,
        .sidebar.collapsed .logo-texts,
        .sidebar.collapsed .sidebar-footer { display: none; }

        .sidebar.collapsed .nav-link > span { display: none; }
        .sidebar.collapsed .nav-link { justify-content: center; padding-left: 0; padding-right: 0; }

        .sidebar.collapsed .sidebar-logo { justify-content: center; padding: 18px 0; }
        .sidebar.collapsed .logo-icon { margin: 0; }

        .sidebar.collapsed .nav-link:hover::after {
            content: attr(data-title);
            position: fixed; left: 78px;
            background: #111827; color: #fff;
            padding: 6px 10px; border-radius: 6px;
            white-space: nowrap; font-size: 12px;
            z-index: 99999; pointer-events: none;
        }

        .sidebar-logo {
            padding: 18px 20px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .logo-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .logo-text { font-size: 16px; font-weight: 700; color: #fff; letter-spacing: -.02em; }
        .logo-text span { color: #3b82f6; }
        .logo-small {
            font-size: 10px; color: var(--sidebar-text); display: block;
            margin-top: 1px; text-transform: uppercase; letter-spacing: .04em;
        }

        .sidebar-nav { flex: 1; padding: 12px 10px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: var(--sidebar-border) transparent; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--sidebar-border); border-radius: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb:hover { background: #334155; }
        .nav-section {
            font-size: 10px; font-weight: 600; color: #475569;
            text-transform: uppercase; letter-spacing: .08em; padding: 14px 10px 6px;
        }
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: var(--sidebar-text); font-size: 13.5px; font-weight: 500;
            text-decoration: none; transition: all .15s; margin-bottom: 1px;
        }
        .nav-link:hover { background: var(--sidebar-hover); color: #e2e8f0; }
        .nav-link.active { background: var(--accent); color: #fff; }
        .nav-link svg { width: 18px; height: 18px; min-width: 18px; flex-shrink: 0; opacity: .8; }
        .nav-link.active svg { opacity: 1; }

        /* Nav accordion groups */
        .nav-group { margin-bottom: 2px; }
        .nav-group-toggle {
            width: 100%; display: flex; align-items: center; justify-content: space-between;
            background: none; border: none; cursor: pointer; font-family: var(--font-sans);
        }
        .nav-group-chevron { width: 12px; height: 12px; transition: transform .2s; opacity: .7; flex-shrink: 0; }
        .nav-group.collapsed .nav-group-chevron { transform: rotate(-90deg); }
        .nav-group-items { overflow: hidden; max-height: 600px; transition: max-height .25s ease; }
        .nav-group.collapsed .nav-group-items { max-height: 0; }
        .sidebar.collapsed .nav-group-toggle { display: none; }
        .sidebar.collapsed .nav-group-items { max-height: none !important; }

        .sidebar-footer { padding: 14px 10px; border-top: 1px solid var(--sidebar-border); }
        .user-info {
            padding: 10px 12px; border-radius: 8px;
            background: rgba(255,255,255,.04); margin-bottom: 8px;
        }
        .user-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .user-email { font-size: 11px; color: var(--sidebar-text); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* Main */
        .main { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; transition: all .3s ease; }
        .main.sidebar-collapsed { margin-left: var(--sidebar-collapsed-width); }

        /* Topbar */
        .topbar {
            height: var(--topbar-height); background: var(--topbar-bg);
            border-bottom: 1px solid var(--topbar-border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; position: sticky; top: 0; z-index: 150;
        }
        .topbar-title { font-size: 15px; font-weight: 600; color: var(--text-primary); }
        .topbar-user { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text-secondary); }
        .topbar-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--accent); color: #fff;
            font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .sidebar-toggle {
            border: none; background: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center; margin-right: 12px;
        }
        .sidebar-toggle svg { width: 22px; height: 22px; }

        /* Content */
        .content { padding: 28px; flex: 1; }

        /* Cards */
        .card {
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: var(--card-radius); box-shadow: var(--card-shadow); overflow: hidden;
        }
        .card-body { padding: 24px; }
        .card-title {
            font-size: 13px; font-weight: 600; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: .06em; margin-bottom: 16px;
        }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 9px; font-size: 13.5px;
            font-weight: 600; font-family: var(--font-sans);
            text-decoration: none; border: none; cursor: pointer; transition: all .2s;
        }
        .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(29,78,216,.25); }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); }
        .btn-secondary { background: #f1f5f9; color: var(--text-secondary); }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger { background: var(--danger-bg); color: var(--danger); }
        .btn-danger:hover { background: #fecdd3; }

        /* Forms */
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--text-secondary); margin-bottom: 6px; }
        .form-input, .form-select {
            width: 100%; padding: 9px 14px;
            border: 1.5px solid var(--card-border); border-radius: 9px;
            font-size: 13.5px; font-family: var(--font-sans);
            color: var(--text-primary); background: var(--body-bg);
            transition: all .2s; outline: none;
        }
        .form-input::placeholder { color: var(--text-muted); opacity: 1; }
        .form-input:focus, .form-select:focus {
            background: var(--card-bg); border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,.1);
        }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-green { background: var(--success-bg); color: var(--success); }
        .badge-red   { background: var(--danger-bg); color: var(--danger); }
        .badge-yellow { background: rgba(245,158,11,.1); color: var(--warning); }
        .badge-blue  { background: var(--accent-light); color: var(--accent); }
        .badge-gray  { background: #f1f5f9; color: #64748b; }

        /* Action buttons for tables */
        .action-btn {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 500;
            font-family: var(--font-sans);
            transition: all .15s;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .action-view   { background: #f1f5f9; color: #475569; }
        .action-view:hover   { background: #e2e8f0; }
        .action-edit   { background: var(--accent-light); color: var(--accent); }
        .action-edit:hover   { background: #dbeafe; }
        .action-delete { background: var(--danger-bg); color: var(--danger); }
        .action-delete:hover { background: #fecdd3; }

        /* Search wrap for forms */
        .search-wrap { position: relative; }
        .search-wrap svg {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: var(--text-muted);
            pointer-events: none;
        }
        .search-input {
            width: 100%;
            padding: 9px 16px 9px 38px;
            border: 1.5px solid var(--card-border);
            border-radius: 9px;
            font-size: 13px;
            font-family: var(--font-sans);
            background: var(--body-bg);
            color: var(--text-primary);
            transition: all .2s;
            outline: none;
        }
        .search-input::placeholder {
            color: var(--text-muted);
            opacity: 1;
        }
        .search-input:focus {
            background: var(--card-bg);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,.1);
        }

        /* Mono font for IDs */
        .mono { font-family: var(--font-mono); }

        /* Alerts */
        .alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 10px; font-size: 13.5px; margin-bottom: 20px; }
        .alert-success { background: var(--success-bg); border: 1px solid var(--success-border); color: var(--success); }
        .alert-error   { background: var(--danger-bg); border: 1px solid var(--danger-border); color: var(--danger); }
        .alert-warning { background: rgba(245,158,11,.08); border: 1px solid rgba(245,158,11,.3); color: var(--warning); }

        /* Detail rows */
        .detail-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 0; border-bottom: 1px solid var(--card-border); font-size: 13.5px;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: var(--text-secondary); }
        .detail-value { color: var(--text-primary); font-weight: 500; }

        /* Page header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }
        .page-title { font-size: 22px; font-weight: 700; color: var(--text-primary); }
        .page-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--card-radius); box-shadow: var(--card-shadow); padding: 20px; }
        .stat-label { font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; font-weight: 600; margin-bottom: 10px; }
        .stat-value { font-size: 28px; font-weight: 800; color: var(--text-primary); line-height: 1; }

        /* Notifications */
        .notifications { position: relative; }
        .notif-badge {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 18px; height: 18px; padding: 0 5px; border-radius: 999px;
            background: var(--danger); color: #fff; font-size: 11px; font-weight: 700;
            position: absolute; top: -6px; right: -6px;
        }
        .notif-dropdown {
            position: absolute; right: 0; top: calc(100% + 10px); width: 300px;
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: 10px; box-shadow: var(--card-shadow);
            overflow: hidden; z-index: 200; display: none;
        }
        .notif-dropdown.active { display: block; }
        .notif-header { padding: 12px 14px; font-weight: 700; color: var(--text-primary); border-bottom: 1px solid var(--card-border); font-size: 13px; }
        .notif-list { max-height: 280px; overflow: auto; }
        .notif-item { padding: 12px 14px; border-bottom: 1px solid var(--card-border); font-size: 13px; color: var(--text-secondary); }
        .notif-item:last-child { border-bottom: none; }
        .notif-empty { padding: 24px; text-align: center; color: var(--text-muted); font-size: 13px; }
        .theme-toggle { display: inline-flex; align-items: center; gap: 8px; padding: 6px 10px; border-radius: 8px; }

        /* Dark theme */
        html.dark-theme {
            --sidebar-bg:     #0b1220;
            --sidebar-border: #111827;
            --sidebar-text:   #cbd5e1;
            --sidebar-hover:  #1a2744;
            --topbar-bg:      #0f1628;
            --topbar-border:  #1a2744;
            --body-bg:        #080d1a;
            --card-bg:        #0f1628;
            --card-border:    #1a2744;
            --card-shadow:    0 1px 3px rgba(0,0,0,.3);
            --text-primary:   #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted:     #475569;
            --accent:         #3b82f6;
            --accent-light:   #1e3a5f;
            --accent-hover:   #60a5fa;
            --success:        #4ade80;
            --success-bg:     #052e16;
            --success-border: #166534;
            --danger:         #f87171;
            --danger-bg:      #2d0a0a;
            --danger-border:  #7f1d1d;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); width: var(--sidebar-width); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main, .main.sidebar-collapsed { margin-left: 0; }
            .content { padding: 18px; }
            .topbar { padding: 0 16px; }
            .stats-grid { grid-template-columns: 1fr; }
        }

        #sidebarOverlay {
            position: fixed; inset: 0; background: rgba(0,0,0,.45);
            z-index: 90; display: none;
        }
        #sidebarOverlay.active { display: block; }
        /* Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            padding: 10px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .07em;
            background: var(--body-bg);
            border-bottom: 1px solid var(--card-border);
        }
        .data-table tbody tr {
            border-bottom: 1px solid var(--card-border);
            transition: background .12s;
        }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: var(--body-bg); }
        .data-table tbody td { padding: 14px 20px; font-size: 13.5px; color: var(--text-primary); }

        /* Empty state */
        .empty-state { padding: 60px 24px; text-align: center; color: var(--text-muted); }
        .empty-state svg { width: 44px; height: 44px; margin: 0 auto 14px; }
        .empty-state strong { display: block; color: var(--text-secondary); font-size: 14px; }
        .password-wrap { position: relative; }
        .password-wrap .form-input { padding-right: 42px; }
        .password-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); padding: 0;
            display: flex; align-items: center;
        }
        .password-toggle:hover { color: var(--text-secondary); }
    </style>
</head>
<body>
<div class="layout">

    <aside id="sidebar" class="sidebar">
        <a href="{{ route('cliente.dashboard') }}" class="sidebar-logo" style="text-decoration:none;">
            <div class="logo-icon" style="width:32px; height:32px; border-radius:8px; background:linear-gradient(135deg,#3b82f6,#6366f1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#fff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="logo-texts">
                <span style="font-size:16px; font-weight:700; color:#fff; letter-spacing:-.02em;">Arioli<span style="color:#3b82f6;">.dev</span></span>
                <small style="display:block; font-size:10px; color:var(--sidebar-text); margin-top:1px; text-transform:uppercase; letter-spacing:.04em;">Panel de Cliente</small>
            </div>
        </a>

        @php $chevron = '<svg class="nav-group-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>'; @endphp

        <nav class="sidebar-nav">
            @if(auth()->guard('cliente')->user()?->client_id)
                {{-- Portal de clientes de Hosting/Dominio (sin licencia SaaS) --}}
                <div class="nav-group" data-group="panel">
                    <button type="button" class="nav-section nav-group-toggle"><span>Panel</span>{!! $chevron !!}</button>
                    <div class="nav-group-items">
                        <a href="{{ route('cliente.dashboard') }}" data-title="Inicio"
                           class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Inicio</span>
                        </a>
                    </div>
                </div>

                <div class="nav-group" data-group="soporte">
                    <button type="button" class="nav-section nav-group-toggle"><span>Soporte</span>{!! $chevron !!}</button>
                    <div class="nav-group-items">
                        <a href="{{ route('cliente.tickets.index') }}" data-title="Mis Tickets"
                           class="nav-link {{ request()->routeIs('cliente.tickets.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Mis Tickets</span>
                        </a>
                        <a href="{{ route('cliente.service-status.index') }}" data-title="Estado de Servicios"
                           class="nav-link {{ request()->routeIs('cliente.service-status.index') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Estado de Servicios</span>
                        </a>
                        <a href="{{ route('cliente.help.index') }}" data-title="Centro de Ayuda"
                           class="nav-link {{ request()->routeIs('cliente.help.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Centro de Ayuda</span>
                        </a>
                    </div>
                </div>

                <div class="nav-group" data-group="productos">
                    <button type="button" class="nav-section nav-group-toggle"><span>Productos y Servicios</span>{!! $chevron !!}</button>
                    <div class="nav-group-items">
                        <a href="{{ route('cliente.systems.index') }}" data-title="Sistemas"
                           class="nav-link {{ request()->routeIs('cliente.systems.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>Sistemas</span>
                        </a>
                        <a href="{{ route('cliente.hosting.index') }}" data-title="Hosting"
                           class="nav-link {{ request()->routeIs('cliente.hosting.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-14 5h.01M9 17h.01"/>
                            </svg>
                            <span>Hosting</span>
                        </a>
                        <a href="{{ route('cliente.domains.index') }}" data-title="Dominios"
                           class="nav-link {{ request()->routeIs('cliente.domains.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 010 18M12 3a15 15 0 000 18"/>
                            </svg>
                            <span>Dominios</span>
                        </a>
                        <a href="{{ route('cliente.services.index') }}" data-title="Servicios Contratados"
                           class="nav-link {{ request()->routeIs('cliente.services.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Servicios Contratados</span>
                        </a>
                        <a href="{{ route('cliente.billing.index') }}" data-title="Facturación"
                           class="nav-link {{ request()->routeIs('cliente.billing.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Facturación</span>
                        </a>
                    </div>
                </div>

                <div class="nav-group" data-group="cuenta">
                    <button type="button" class="nav-section nav-group-toggle"><span>Cuenta</span>{!! $chevron !!}</button>
                    <div class="nav-group-items">
                        <a href="{{ route('cliente.perfil') }}" data-title="Mi Perfil"
                           class="nav-link {{ request()->routeIs('cliente.perfil') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="{{ route('cliente.account.users.index') }}" data-title="Usuarios"
                           class="nav-link {{ request()->routeIs('cliente.account.users.index') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-8.13a4 4 0 110 8 4 4 0 010-8zm-6 3a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Usuarios</span>
                        </a>
                    </div>
                </div>
            @else
                {{-- Portal SaaS (licencias por tenant) — mismo contenido de siempre, ahora en grupos colapsables --}}
                <div class="nav-group" data-group="panel">
                    <button type="button" class="nav-section nav-group-toggle"><span>Panel</span>{!! $chevron !!}</button>
                    <div class="nav-group-items">
                        <a href="{{ route('cliente.dashboard') }}" data-title="Inicio"
                           class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Inicio</span>
                        </a>

                        <a href="{{ route('cliente.licencias') }}" data-title="Licencias"
                           class="nav-link {{ request()->routeIs('cliente.licencias', 'cliente.licencia.*', 'cliente.renovar') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Mis Licencias</span>
                        </a>

                        <a href="{{ route('cliente.pagos') }}" data-title="Pagos"
                           class="nav-link {{ request()->routeIs('cliente.pagos') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Pagos</span>
                        </a>
                    </div>
                </div>

                <div class="nav-group" data-group="soporte">
                    <button type="button" class="nav-section nav-group-toggle"><span>Soporte</span>{!! $chevron !!}</button>
                    <div class="nav-group-items">
                        <a href="{{ route('cliente.tickets.index') }}" data-title="Mis Tickets"
                           class="nav-link {{ request()->routeIs('cliente.tickets.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Mis Tickets</span>
                        </a>
                        <a href="{{ route('cliente.help.index') }}" data-title="Centro de Ayuda"
                           class="nav-link {{ request()->routeIs('cliente.help.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Centro de Ayuda</span>
                        </a>
                    </div>
                </div>

                <div class="nav-group" data-group="cuenta">
                    <button type="button" class="nav-section nav-group-toggle"><span>Cuenta</span>{!! $chevron !!}</button>
                    <div class="nav-group-items">
                        <a href="{{ route('cliente.perfil') }}" data-title="Mi Perfil"
                           class="nav-link {{ request()->routeIs('cliente.perfil') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Mi Perfil</span>
                        </a>

                        <a href="http://{{ config('app.landing_domain') }}#productos" data-title="Ver sistemas"
                           class="nav-link" target="_blank">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Contratar sistema</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>

        <div class="sidebar-footer" style="padding:14px 10px;">
                <div style="padding:10px 12px; border-radius:8px; background:rgba(255,255,255,.04);">
                    <div style="font-size:13px; font-weight:600; color:#e2e8f0;">{{ auth()->guard('cliente')->user()->name ?? '' }}</div>
                    <div style="font-size:11px; color:var(--sidebar-text); margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->guard('cliente')->user()->email ?? '' }}</div>
                </div>
            </div>   
    </aside>

    <div class="main" id="mainContent">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button id="sidebarToggle" class="sidebar-toggle">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="topbar-title">{{ $title ?? 'Mi Panel' }}</span>
            </div>

            <div style="display:flex; align-items:center; gap:10px;">

                {{-- Notificaciones --}}
                <div class="notifications">
                    <button id="notifToggle" class="btn btn-secondary"
                            style="padding:8px; border-radius:8px; position:relative;"
                            title="Notificaciones">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php
                            $clienteUser = auth()->guard('cliente')->user();

                            if ($clienteUser->client_id) {
                                $navClient = $clienteUser->client()->with(['domains', 'hostings', 'sslCertificates', 'cloudflareServices'])->first();
                                $navRenewals = $navClient ? (new \App\Services\Clients\ClientUpcomingRenewalsService())->calculate($navClient) : collect();
                                $navPendingCharges = \App\Models\Charge::where('client_id', $clienteUser->client_id)
                                    ->where('status', \App\Enums\ChargeStatus::Pending)
                                    ->get();
                                $expiringCount = $navRenewals->count() + $navPendingCharges->count();
                            } else {
                                $expiringLicenses = \App\Models\License::with('plan.product')
                                    ->where('tenant_id', $clienteUser->tenant_id ?? '')
                                    ->where('active', true)
                                    ->where('expires_at', '<=', now()->addDays(30))
                                    ->where('expires_at', '>=', now())
                                    ->get();
                                $expiringCount = $expiringLicenses->count();
                            }
                        @endphp
                        @if($expiringCount > 0)
                            <span class="notif-badge">{{ $expiringCount }}</span>
                        @endif
                    </button>

                    <div id="notifDropdown" class="notif-dropdown">
                        <div class="notif-header">Notificaciones</div>
                        <div class="notif-list">
                            @if($clienteUser->client_id)
                                @forelse($navRenewals as $asset)
                                    @php
                                        $assetRoute = match(true) {
                                            $asset instanceof \App\Models\ClientDomain => route('cliente.domains.index'),
                                            $asset instanceof \App\Models\Hosting => route('cliente.hosting.index'),
                                            default => route('cliente.dashboard'),
                                        };
                                    @endphp
                                    <div class="notif-item">
                                        <div style="font-weight:600; color:var(--text-primary); font-size:13px;">
                                            {{ $asset->renewalStatusLabel() === 'Vencido' ? '⛔' : '⚠' }} {{ $asset->label() }}
                                        </div>
                                        <div style="font-size:12px; color:var(--text-muted); margin-top:3px;">
                                            {{ $asset->renewalStatusLabel() }} — vence el {{ $asset->expiresAt()->format('d/m/Y') }}
                                        </div>
                                        <a href="{{ $assetRoute }}" style="font-size:12px; color:var(--accent); text-decoration:none; margin-top:4px; display:inline-block;">
                                            Ver →
                                        </a>
                                    </div>
                                @empty
                                @endforelse
                                @forelse($navPendingCharges as $charge)
                                    <div class="notif-item">
                                        <div style="font-weight:600; color:var(--text-primary); font-size:13px;">
                                            💳 Cobro pendiente
                                        </div>
                                        <div style="font-size:12px; color:var(--text-muted); margin-top:3px;">
                                            {{ $charge->concept }} — {{ $charge->currency->value }} {{ number_format($charge->amount, 0, ',', '.') }}
                                        </div>
                                        <a href="{{ route('cliente.billing.index') }}" style="font-size:12px; color:var(--accent); text-decoration:none; margin-top:4px; display:inline-block;">
                                            Ver facturación →
                                        </a>
                                    </div>
                                @empty
                                @endforelse
                                @if($expiringCount === 0)
                                    <div class="notif-empty">Sin notificaciones</div>
                                @endif
                            @else
                                @forelse($expiringLicenses as $expLicense)
                                    <div class="notif-item">
                                        <div style="font-weight:600; color:var(--text-primary); font-size:13px;">
                                            ⚠ {{ $expLicense->plan->product->name ?? '-' }} por vencer
                                        </div>
                                        <div style="font-size:12px; color:var(--text-muted); margin-top:3px;">
                                            Vence el {{ $expLicense->expires_at->format('d/m/Y') }}
                                            ({{ $expLicense->daysRemaining() }} días)
                                        </div>
                                        <a href="{{ route('cliente.renovar', $expLicense->id) }}"
                                           style="font-size:12px; color:var(--accent); text-decoration:none; margin-top:4px; display:inline-block;">
                                            Renovar →
                                        </a>
                                    </div>
                                @empty
                                    <div class="notif-empty">Sin notificaciones</div>
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Theme toggle --}}
                <button id="themeToggle" class="btn btn-secondary theme-toggle" title="Cambiar tema">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1M12 20v1M4.2 4.2l.7.7M18.1 18.1l.7.7M1 12h1M22 12h1M4.2 19.8l.7-.7M18.1 5.9l.7-.7M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                    </svg>
                    <span id="themeText">Tema</span>
                </button>

                {{-- User dropdown --}}
                <div class="notifications" id="userMenuWrap">
                    <button id="userMenuToggle"
                            style="display:flex; align-items:center; gap:8px; background:none; border:none; cursor:pointer; font-family:var(--font-sans); color:var(--text-secondary); font-size:13px;">
                        <span>{{ auth()->guard('cliente')->user()->name ?? '' }}</span>
                        <div class="topbar-avatar">
                            {{ strtoupper(substr(auth()->guard('cliente')->user()->name ?? 'C', 0, 1)) }}
                        </div>
                    </button>

                    <div id="userMenuDropdown" class="notif-dropdown" style="width:220px;">
                        <div style="padding:12px 14px; border-bottom:1px solid var(--card-border);">
                            <div style="font-size:13px; font-weight:600; color:var(--text-primary);">
                                {{ auth()->guard('cliente')->user()->name ?? '' }}
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                                {{ auth()->guard('cliente')->user()->email ?? '' }}
                            </div>
                        </div>
                        <div style="padding:8px;">
                            <a href="{{ route('cliente.perfil') }}"
                            style="display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:7px; font-size:13px; color:var(--text-secondary); text-decoration:none; transition:background .15s;"
                            onmouseover="this.style.background='var(--body-bg)'"
                            onmouseout="this.style.background='transparent'">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Mi Perfil
                            </a>
                            <a href="http://{{ config('app.landing_domain') }}#productos"
                            target="_blank"
                            style="display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:7px; font-size:13px; color:var(--text-secondary); text-decoration:none; transition:background .15s;"
                            onmouseover="this.style.background='var(--body-bg)'"
                            onmouseout="this.style.background='transparent'">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"/>
                                </svg>
                                Contratar sistema
                            </a>
                            <form method="POST" action="{{ route('cliente.logout') }}">
                                @csrf
                                <button type="submit"
                                        style="display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:7px; font-size:13px; color:var(--danger); background:none; border:none; cursor:pointer; font-family:var(--font-sans); width:100%; text-align:left; transition:background .15s;"
                                        onmouseover="this.style.background='var(--body-bg)'"
                                        onmouseout="this.style.background='transparent'">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <main class="content">
            {{ $slot }}
        </main>
    </div>
</div>

<div id="sidebarOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar  = document.getElementById('sidebar');
    const main     = document.getElementById('mainContent');
    const toggle   = document.getElementById('sidebarToggle');
    const overlay  = document.getElementById('sidebarOverlay');
    const notifBtn = document.getElementById('notifToggle');
    const notifDrop = document.getElementById('notifDropdown');
    const themeToggle = document.getElementById('themeToggle');
    const themeText   = document.getElementById('themeText');

    function isMobile() { return window.innerWidth <= 768; }

    // Sidebar collapse
    if (localStorage.getItem('clienteSidebarCollapsed') === 'true' && !isMobile()) {
        sidebar.classList.add('collapsed');
        main.classList.add('sidebar-collapsed');
    }

    toggle?.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        } else {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('sidebar-collapsed');
            localStorage.setItem('clienteSidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    });

    overlay?.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Notifications
    notifBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDrop.classList.toggle('active');
    });
    document.addEventListener('click', () => notifDrop?.classList.remove('active'));

    // User menu
    document.getElementById('userMenuToggle')?.addEventListener('click', (e) => {
        e.stopPropagation();
        document.getElementById('notifDropdown')?.classList.remove('active');
        document.getElementById('userMenuDropdown').classList.toggle('active');
    });

    document.addEventListener('click', () => {
        document.getElementById('notifDropdown')?.classList.remove('active');
        document.getElementById('userMenuDropdown')?.classList.remove('active');
    });

    // Theme
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark-theme');
            if (themeText) themeText.textContent = 'Oscuro';
        } else {
            document.documentElement.classList.remove('dark-theme');
            if (themeText) themeText.textContent = 'Claro';
        }
    }

    applyTheme(localStorage.getItem('theme') || 'light');

    themeToggle?.addEventListener('click', () => {
        const next = (localStorage.getItem('theme') || 'light') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        applyTheme(next);
    });

    // Nav accordion groups
    document.querySelectorAll('.nav-group').forEach((group) => {
        const key = 'clienteNavGroup_' + group.dataset.group;
        const hasActive = group.querySelector('.nav-link.active') !== null;
        const stored = localStorage.getItem(key);
        const collapsed = stored !== null ? stored === 'true' : !hasActive;
        group.classList.toggle('collapsed', collapsed);

        group.querySelector('.nav-group-toggle')?.addEventListener('click', () => {
            group.classList.toggle('collapsed');
            localStorage.setItem(key, group.classList.contains('collapsed'));
        });
    });
});
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.innerHTML = isPassword
        ? `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
           </svg>`
        : `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
           </svg>`;
}
</script>
</body>
</html>