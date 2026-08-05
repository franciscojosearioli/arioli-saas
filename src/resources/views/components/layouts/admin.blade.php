<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel' }} — Arioli.dev</title>
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

        /* ── Layout ── */
        .layout { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            border-right: 1px solid var(--sidebar-border);
            transition: all .3s ease;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar.collapsed .nav-section { display: none; }
        .sidebar.collapsed .logo-texts { display: none; }
        .sidebar.collapsed .sidebar-logo { justify-content: center; padding: 18px 0; }
        .sidebar.collapsed .logo-icon { margin: 0; }
        .sidebar.collapsed .sidebar-footer { display: none; }
        
        @media (min-width: 769px) {

            .sidebar.collapsed .nav-link > span,
            .sidebar.collapsed .sidebar-footer button > span {
                display: none;
            }

        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
            position: relative;
        }

        .sidebar.collapsed .sidebar-logo {
            text-align: center;
        }

        .sidebar.collapsed .nav-link:hover::after {
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

        .sidebar-logo {
            padding: 18px 20px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-nav { flex: 1; padding: 12px 10px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #1e293b transparent; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb:hover { background: #334155; }

        .nav-section {
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 14px 10px 6px;
        }

        /* ── Nav groups (rubros desplegables) ── */
        .nav-group-toggle {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            background: none;
            border: none;
            cursor: pointer;
            font-family: var(--font-sans);
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 14px 10px 6px;
        }
        .nav-group-toggle:hover { color: #94a3b8; }
        .nav-group-toggle .chevron {
            width: 12px;
            height: 12px;
            flex-shrink: 0;
            opacity: .7;
            transition: transform .18s ease;
        }
        .nav-group.collapsed .nav-group-toggle .chevron {
            transform: rotate(-90deg);
        }
        .nav-group-body {
            display: grid;
            grid-template-rows: 1fr;
            transition: grid-template-rows .18s ease;
        }
        .nav-group.collapsed .nav-group-body {
            grid-template-rows: 0fr;
        }
        .nav-group-body-inner {
            overflow: hidden;
            min-height: 0;
        }
        .sidebar.collapsed .nav-group-toggle { display: none; }
        .sidebar.collapsed .nav-group-body { grid-template-rows: 1fr; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--sidebar-text);
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all .15s;
            margin-bottom: 1px;
        }
        .nav-link:hover { background: var(--sidebar-hover); color: #e2e8f0; }
        .nav-link.active { background: var(--accent); color: #fff; }
        .nav-link svg {
            width: 18px;
            height: 18px;
            min-width: 18px;
            flex-shrink: 0;
            opacity: .8;
        }
        .nav-link.active svg { opacity: 1; }

        .sidebar-footer {
            padding: 14px 10px;
            border-top: 1px solid var(--sidebar-border);
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

        .main.sidebar-collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

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
            z-index: 150;
        }
        .topbar-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
        }
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--text-secondary);
        }
        .topbar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Content ── */
        .content { padding: 28px; flex: 1; }

        /* ── Cards ── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: var(--font-sans);
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all .2s;
            letter-spacing: .01em;
        }
        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(29,78,216,.25);
        }
        .btn-primary:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 16px rgba(29,78,216,.35);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #f1f5f9;
            color: var(--text-secondary);
        }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger {
            background: var(--danger-bg);
            color: var(--danger);
        }
        .btn-danger:hover { background: #fecdd3; }

        /* ── Action buttons (table) ── */
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

        /* Additional badge colors for tickets */
        .badge-gray   { background: #f1f5f9; color: #64748b; }
        .badge-yellow { background: #fef3c7; color: #92400e; }

        /* ── Forms ── */
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        .form-input, .form-select {
            width: 100%;
            padding: 9px 14px;
            border: 1.5px solid var(--card-border);
            border-radius: 9px;
            font-size: 13.5px;
            font-family: var(--font-sans);
            color: var(--text-primary);
            background: var(--body-bg);
            transition: all .2s;
            outline: none;
        }
        .form-input::placeholder {
            color: var(--text-muted);
            opacity: 1;
        }
        .form-input:focus, .form-select:focus {
            background: var(--card-bg);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,.1);
        }
        .form-select option {
            background: var(--card-bg);
            color: var(--text-primary);
        }

        /* ── Search ── */
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

        /* ── Table ── */
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
            border-bottom: 1px solid #f1f5f9;
            transition: background .12s;
        }
        .data-table tbody tr:hover { background: #f8faff; }
        .data-table tbody td { padding: 13px 20px; font-size: 13.5px; color: var(--text-primary); }

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-blue  { background: var(--accent-light); color: var(--accent); font-family: var(--font-mono); }
        .badge-green { background: var(--success-bg); color: var(--success); }
        .badge-red   { background: var(--danger-bg); color: var(--danger); }

        /* ── Alerts ── */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success);
        }
        .alert-error {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger);
        }

        /* ── Page header ── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }
        .page-title { font-size: 22px; font-weight: 700; color: var(--text-primary); }
        .page-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 3px; }

        /* ── Mono ── */
        .mono { font-family: var(--font-mono); }

        /* ── Empty state ── */
        .empty-state { padding: 60px 24px; text-align: center; color: var(--text-muted); }
        .empty-state svg { width: 44px; height: 44px; margin: 0 auto 14px; color: #d1d5db; }
        .empty-state strong { display: block; color: var(--text-secondary); font-size: 14px; }
        .empty-state p { font-size: 13px; margin-top: 4px; }

        .sidebar-toggle {
            border: none;
            background: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .sidebar-toggle svg {
            width: 22px;
            height: 22px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main,
            .main.sidebar-collapsed {
                margin-left: 0;
            }

            .content {
                padding: 18px;
            }

            .topbar {
                padding: 0 16px;
            }
        }
        #sidebarOverlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 90;
            display: none;
        }

        #sidebarOverlay.active {
            display: block;
        }

        /* ── Dark theme overrides ── */
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

        /* ── Notifications ── */
        .notifications { position: relative; }
        .notif-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--danger);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            position: absolute;
            top: -6px;
            right: -6px;
        }
        .notif-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 320px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            z-index: 200;
            display: none;
        }
        .notif-dropdown.active { display: block; }
        .notif-header { padding: 12px 14px; font-weight:700; color:var(--text-primary); border-bottom:1px solid var(--card-border); }
        .notif-list { max-height: 300px; overflow:auto; }
        .notif-item { padding: 10px 14px; border-bottom:1px solid rgba(0,0,0,0.04); font-size:13px; color:var(--text-secondary); }
        .notif-item.unread { background: var(--accent-light); color: #fff; }
        .notif-footer { padding: 8px 12px; text-align:center; }
        .notif-footer a { color: var(--accent); font-weight:600; text-decoration:none; }

        .notif-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 24px;
            gap: 4px;
        }
        .notif-empty svg {
            width: 30px;
            height: 30px;
            color: #cbd5e1;
            margin-bottom: 8px;
        }
        .notif-empty strong {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
        }
        .notif-empty span {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── Theme toggle styles ── */
        .theme-toggle { display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:8px; }

        /* ── Modales (popups de acciones) ── */
        dialog.admin-modal {
            border: none;
            border-radius: var(--card-radius);
            padding: 0;
            width: 100%;
            max-width: 480px;
            max-height: 85vh;
            margin: auto;
            box-shadow: var(--card-shadow);
            background: var(--card-bg);
            color: var(--text-primary);
        }
        dialog.admin-modal.admin-modal-wide { max-width: 640px; }
        dialog.admin-modal::backdrop { background: rgba(15,23,42,.55); }
        .admin-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--card-border);
        }
        .admin-modal-header h3 { font-size: 14.5px; font-weight: 700; color: var(--text-primary); margin: 0; }
        .admin-modal-close {
            border: none;
            background: none;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
            color: var(--text-muted);
            padding: 2px 6px;
            border-radius: 6px;
        }
        .admin-modal-close:hover { color: var(--text-primary); background: var(--body-bg); }
        .admin-modal-body { padding: 20px; max-height: calc(85vh - 57px); overflow-y: auto; }
        .modal-trigger {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: none;
            background: none;
            color: var(--accent);
            font-size: 11.5px;
            font-weight: 600;
            font-family: var(--font-sans);
            cursor: pointer;
            padding: 0;
        }
        .modal-trigger:hover { color: var(--accent-hover); text-decoration: underline; }
    </style>
</head>
<body>
<div class="layout">

    {{-- Sidebar --}}
    <aside id="sidebar" class="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-logo" style="text-decoration:none;">
    <div class="logo-icon" style="width:32px; height:32px; border-radius:8px; background:linear-gradient(135deg,#3b82f6,#6366f1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="18" height="18" fill="none" stroke="#fff" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
    </div>
    <div class="logo-texts">
        <span style="font-size:16px; font-weight:700; color:#fff; letter-spacing:-.02em;">Arioli<span style="color:#3b82f6;">.dev</span></span>
        <small style="display:block; font-size:10px; color:var(--sidebar-text); margin-top:1px; text-transform:uppercase; letter-spacing:.04em;">Panel Administrativo</small>
    </div>
</a>

        <nav class="sidebar-nav">
            <div class="nav-section">Principal</div>

            <a href="{{ route('dashboard') }}"
                data-title="Dashboard"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <div class="nav-group" data-group="gestion">
                <button type="button" class="nav-group-toggle" data-group-toggle="gestion">
                    <span>Gestión</span>
                    <svg class="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-group-body"><div class="nav-group-body-inner">

                    <a href="{{ route('clients.index') }}"
                       data-title="Clientes"
                       class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                        </svg>
                        <span>Clientes</span>
                    </a>

                    <a href="{{ route('tenants.index') }}"
                       data-title="Tenants"
                       class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 12a7 7 0 1114 0 7 7 0 01-14 0zm7-9v2m0 14v2m9-9h-2M5 12H3m14.657-6.657l-1.414 1.414M7.757 16.243l-1.414 1.414m0-11.314l1.414 1.414m9.9 9.9l1.414 1.414"/>
                        </svg>
                        <span>Tenants</span>
                    </a>

                    <a href="{{ route('licenses.index') }}"
                       data-title="Licencias"
                       class="nav-link {{ request()->routeIs('licenses.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Licencias</span>
                    </a>

                    <a href="{{ route('plans.index') }}"
                       data-title="Planes"
                       class="nav-link {{ request()->routeIs('plans.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>Planes</span>
                    </a>

                    <a href="{{ route('hosting-plans.index') }}"
                       data-title="Planes de Hosting"
                       class="nav-link {{ request()->routeIs('hosting-plans.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 12a2 2 0 012-2h10a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6zm0-2V7a2 2 0 012-2h10a2 2 0 012 2v3M8 16h.01M12 16h4"/>
                        </svg>
                        <span>Planes de Hosting</span>
                    </a>

                    <a href="{{ route('centro-de-ayuda.index') }}"
                       data-title="Centro de Ayuda"
                       class="nav-link {{ request()->routeIs('centro-de-ayuda.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Centro de Ayuda</span>
                    </a>

                </div></div>
            </div>

            <div class="nav-group" data-group="finanzas">
                <button type="button" class="nav-group-toggle" data-group-toggle="finanzas">
                    <span>Finanzas</span>
                    <svg class="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-group-body"><div class="nav-group-body-inner">

                    <a href="{{ route('orders.index') }}"
                        data-title="Órdenes"
                        class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>Órdenes</span>
                        </a>

                    @can('manage-invoices')
                    <a href="{{ route('finanzas.facturacion.index') }}"
                        data-title="Facturación"
                        class="nav-link {{ request()->routeIs('finanzas.facturacion.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-2-1-2 1-2-1-2 1-2-1-2 1V5a2 2 0 012-2h8a2 2 0 012 2v16z"/>
                            </svg>
                            <span>Facturación</span>
                        </a>
                    @endcan

                    @can('manage-clients')
                    <a href="{{ route('cotizaciones.index') }}"
                       data-title="Propuestas"
                       class="nav-link {{ request()->routeIs('cotizaciones.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                        </svg>
                        <span>Propuestas</span>
                    </a>
                    @endcan

                </div></div>
            </div>

            <div class="nav-group" data-group="soporte">
                <button type="button" class="nav-group-toggle" data-group-toggle="soporte">
                    <span>Soporte</span>
                    <svg class="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-group-body"><div class="nav-group-body-inner">

                    <a href="{{ route('tickets.index') }}"
                       data-title="Tickets"
                       class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Tickets</span>
                    </a>

                </div></div>
            </div>

            <div class="nav-group" data-group="sistema">
                <button type="button" class="nav-group-toggle" data-group-toggle="sistema">
                    <span>Sistema</span>
                    <svg class="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-group-body"><div class="nav-group-body-inner">

                    <a href="{{ route('demos.index') }}"
                       data-title="Demos"
                       class="nav-link {{ request()->routeIs('demos.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Demos</span>
                    </a>

                    <a href="{{ route('app-versions.index') }}"
                       data-title="Versiones"
                       class="nav-link {{ request()->routeIs('app-versions.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2z"/>
                        </svg>
                        <span>Versiones</span>
                    </a>

                    @can('manage-settings')
                    <a href="{{ route('configuracion.index') }}"
                       data-title="Configuración"
                       class="nav-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Configuración</span>
                    </a>
                    @endcan

                </div></div>
            </div>

            @can('manage-legal')
            <div class="nav-group" data-group="legales">
                <button type="button" class="nav-group-toggle" data-group-toggle="legales">
                    <span>Legales</span>
                    <svg class="chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-group-body"><div class="nav-group-body-inner">

                    <a href="{{ route('legales.index') }}"
                       data-title="Legales"
                       class="nav-link {{ request()->routeIs('legales.index') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Legales</span>
                    </a>

                    <a href="{{ route('legales.contratos.index') }}"
                       data-title="Contratos"
                       class="nav-link {{ request()->routeIs('legales.contratos.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Contratos</span>
                    </a>

                    <a href="{{ route('legales.plantillas.index') }}"
                       data-title="Plantillas"
                       class="nav-link {{ request()->routeIs('legales.plantillas.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <span>Plantillas</span>
                    </a>

                </div></div>
            </div>
            @endcan

            <div class="nav-section">Cuenta</div>

            <a href="{{ route('profile.edit') }}"
                data-title="Perfil"
               class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Perfil</span>
            </a>
        </nav>

            <div class="sidebar-footer" style="padding:14px 10px;">
                <div style="padding:10px 12px; border-radius:8px; background:rgba(255,255,255,.04);">
                    <div style="font-size:13px; font-weight:600; color:#e2e8f0;">{{ auth()->user()->name ?? '' }}</div>
                    <div style="font-size:11px; color:var(--sidebar-text); margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>     
    </aside>

    {{-- Main --}}
    <div class="main">

        {{-- Topbar --}}
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">

                <button id="sidebarToggle" class="sidebar-toggle" aria-controls="sidebar" aria-expanded="false">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <span class="topbar-title">
                    {{ $title ?? 'Panel Administrativo' }}
                </span>

            </div>

            <div style="display:flex;align-items:center;gap:10px;">
                <div class="topbar-actions" style="display:flex;align-items:center;gap:10px;">

                    {{-- Notificaciones --}}
                    <div class="notifications">
                        <button id="notifToggle" class="btn btn-secondary"
                                style="padding:8px; border-radius:8px; position:relative;"
                                title="Notificaciones">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span id="notifBadge" class="notif-badge" style="display:none;">0</span>
                        </button>

                        <div id="notifDropdown" class="notif-dropdown">
                            <div class="notif-header" style="display:flex; justify-content:space-between; align-items:center;">
                                <span>Notificaciones</span>
                                <button id="markAllReadBtn" style="font-size:11px; color:var(--accent); background:none; border:none; cursor:pointer; font-family:var(--font-sans);">
                                    Marcar todas leídas
                                </button>
                            </div>
                            <div id="notifList" class="notif-list">
                                <div class="notif-empty"><span>Cargando notificaciones…</span></div>
                            </div>
                            <div class="notif-footer">
                                <a href="{{ route('notifications.index') }}">Ver todas las notificaciones</a>
                            </div>
                        </div>
                    </div>

                    {{-- Theme toggle --}}
                    <button id="themeToggle" class="btn btn-secondary theme-toggle" title="Cambiar tema" aria-pressed="false">
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
                            <span>{{ auth()->user()->name ?? '' }}</span>
                            <div class="topbar-avatar">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                        </button>

                        <div id="userMenuDropdown" class="notif-dropdown" style="width:200px;">
                            <div style="padding:12px 14px; border-bottom:1px solid var(--card-border);">
                                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ auth()->user()->name ?? '' }}</div>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ auth()->user()->email ?? '' }}</div>
                            </div>
                            <div style="padding:8px;">
                                <a href="{{ route('profile.edit') }}"
                                style="display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:7px; font-size:13px; color:var(--text-secondary); text-decoration:none; transition:background .15s;"
                                onmouseover="this.style.background='var(--body-bg)'"
                                onmouseout="this.style.background='transparent'">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Mi Perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
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

        {{-- Content --}}
        <main class="content">
            {{ $slot }}
        </main>

    </div>
</div>
<div id="sidebarOverlay"></div>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const sidebar = document.querySelector('.sidebar');
    const main = document.querySelector('.main');
    const toggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');

    const themeToggle = document.getElementById('themeToggle');
    const themeText = document.getElementById('themeText');

    function isMobile() {
        return window.innerWidth <= 768;
    }

    // Sidebar

    if (
        localStorage.getItem('sidebarCollapsed') === 'true' &&
        !isMobile()
    ) {
        sidebar.classList.add('collapsed');
        main.classList.add('sidebar-collapsed');
    }

    toggle?.addEventListener('click', () => {

        if (isMobile()) {

            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');

            document.body.style.overflow =
                sidebar.classList.contains('mobile-open')
                    ? 'hidden'
                    : '';

        } else {

            sidebar.classList.toggle('collapsed');
            main.classList.toggle('sidebar-collapsed');

            localStorage.setItem(
                'sidebarCollapsed',
                sidebar.classList.contains('collapsed')
            );
        }
    });

    overlay?.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    // Rubros desplegables del menú (acordeón)
    document.querySelectorAll('.nav-group').forEach(group => {
        const key = 'navGroupCollapsed_' + group.dataset.group;
        if (localStorage.getItem(key) === '1') {
            group.classList.add('collapsed');
        }
    });

    // La sección que contiene el link activo siempre se muestra expandida
    document.querySelectorAll('.nav-link.active').forEach(link => {
        link.closest('.nav-group')?.classList.remove('collapsed');
    });

    document.querySelectorAll('.nav-group-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const group = btn.closest('.nav-group');
            group.classList.toggle('collapsed');
            localStorage.setItem(
                'navGroupCollapsed_' + group.dataset.group,
                group.classList.contains('collapsed') ? '1' : '0'
            );
        });
    });

    document.querySelectorAll('.nav-link').forEach(link => {

        link.addEventListener('click', () => {

            if (isMobile()) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    window.addEventListener('resize', () => {

        if (!isMobile()) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('keydown', (e) => {

        if (e.key === 'Escape') {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Theme

    function applyTheme(theme) {

        if (theme === 'dark') {

            document.documentElement.classList.add('dark-theme');

            if (themeText) {
                themeText.textContent = 'Oscuro';
            }

        } else {

            document.documentElement.classList.remove('dark-theme');

            if (themeText) {
                themeText.textContent = 'Claro';
            }
        }
    }

    const savedTheme =
        localStorage.getItem('theme') || 'light';

    applyTheme(savedTheme);

    themeToggle?.addEventListener('click', () => {

        const current =
            localStorage.getItem('theme') || 'light';

        const next =
            current === 'dark'
                ? 'light'
                : 'dark';

        localStorage.setItem('theme', next);

        applyTheme(next);
    });

    // Notificaciones dinámicas
    async function loadNotifications() {
        try {
            const res = await fetch('{{ route('notifications.unread') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();

            const badge = document.getElementById('notifBadge');
            const list  = document.getElementById('notifList');

            if (data.count > 0) {
                badge.style.display = 'inline-flex';
                badge.textContent = data.count > 99 ? '99+' : data.count;
            } else {
                badge.style.display = 'none';
            }

            if (data.notifications.length === 0) {
                list.innerHTML = `
                    <div class="notif-empty">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <strong>Todo al día</strong>
                        <span>No tenés notificaciones nuevas</span>
                    </div>
                `;
                return;
            }

            const colors = { green: '#10b981', blue: '#3b82f6', red: '#f87171', yellow: '#f59e0b' };

            list.innerHTML = data.notifications.map(n => `
                <a href="${n.link || '#'}" class="notif-item" style="display:block; text-decoration:none;"
                onmouseover="this.style.background='var(--body-bg)'"
                onmouseout="this.style.background='transparent'">
                    <div style="display:flex; align-items:flex-start; gap:10px;">
                        <div style="width:8px; height:8px; border-radius:50%; background:${colors[n.color] || colors.blue}; margin-top:5px; flex-shrink:0;"></div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13px; font-weight:600; color:var(--text-primary);">${n.title}</div>
                            <div style="font-size:12px; color:var(--text-secondary); margin-top:2px; white-space:normal; line-height:1.4;">${n.message}</div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">${n.time}</div>
                        </div>
                    </div>
                </a>
            `).join('');
        } catch (e) {
            console.error('Error loading notifications:', e);
        }
    }

    async function markAllRead() {
        await fetch('{{ route('notifications.markRead') }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });
        document.getElementById('notifBadge').style.display = 'none';
        document.getElementById('notifList').innerHTML = `
            <div class="notif-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <strong>Todo al día</strong>
                <span>No tenés notificaciones nuevas</span>
            </div>
        `;
    }

    document.getElementById('markAllReadBtn')?.addEventListener('click', markAllRead);

    // Cargar notificaciones al abrir dropdown
    document.getElementById('notifToggle')?.addEventListener('click', (e) => {
        e.stopPropagation();
        const drop = document.getElementById('notifDropdown');
        drop.classList.toggle('active');
        if (drop.classList.contains('active')) loadNotifications();
    });

    // User menu
    document.getElementById('userMenuToggle')?.addEventListener('click', (e) => {
        e.stopPropagation();
        document.getElementById('userMenuDropdown').classList.toggle('active');
    });

    document.addEventListener('click', () => {
        document.getElementById('notifDropdown')?.classList.remove('active');
        document.getElementById('userMenuDropdown')?.classList.remove('active');
    });

    // Cargar notificaciones cada 60 segundos
    loadNotifications();
    setInterval(loadNotifications, 60000);

    // Modales: cerrar al hacer click en el backdrop (fuera del contenido)
    document.querySelectorAll('dialog.admin-modal').forEach(dialog => {
        dialog.addEventListener('click', (e) => {
            const rect = dialog.getBoundingClientRect();
            const inDialog = e.clientY >= rect.top && e.clientY <= rect.bottom
                && e.clientX >= rect.left && e.clientX <= rect.right;
            if (!inDialog) dialog.close();
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