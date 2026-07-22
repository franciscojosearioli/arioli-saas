@php
    $alertsCount = auth()->user()?->userUserAlerts()?->wherePivot('read', false)->count() ?? 0;
    try {
        $notifRecent = auth()->user()?->userUserAlerts()
            ->withPivot('read')
            ->orderByDesc('user_alerts.created_at')
            ->limit(6)
            ->get() ?? collect();
    } catch(\Exception $e) {
        $notifRecent = collect();
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') — {{ $sistemaConfig?->nombre_sistema ?? 'Sistema HC' }}</title>
    <link rel="icon" href="{{ $sistemaConfig?->favicon_url ?? asset('favicon.svg') }}" type="{{ $sistemaConfig?->favicon_url ? 'image/x-icon' : 'image/svg+xml' }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Bootstrap CSS (requerido por DataTables/forms existentes) --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.0/css/select.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css">

    {{-- jQuery antes que todo --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <style>
        /* ── Reset ── */
        html, body { height: 100%; margin: 0; padding: 0; }
        *, *::before, *::after { box-sizing: border-box; }
        body { padding: 0 !important; }

        /* ── Design tokens ── */
        :root {
            --font-sans: 'DM Sans', sans-serif;
            --font-mono: 'DM Mono', monospace;

            --sidebar-bg:      #0f172a;
            --sidebar-border:  #1e293b;
            --sidebar-text:    #94a3b8;
            --sidebar-hover:   #1e293b;
            --sidebar-active:  #1d4ed8;
            --sidebar-width:   240px;
            --sidebar-coll-w:  68px;

            --topbar-bg:       #ffffff;
            --topbar-border:   #f1f5f9;
            --topbar-height:   60px;

            --body-bg:         #f8fafc;
            --card-bg:         #ffffff;
            --card-border:     #e8edf2;
            --card-shadow:     0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.04);
            --card-radius:     14px;

            --text-primary:    #0f172a;
            --text-secondary:  #64748b;
            --text-muted:      #94a3b8;

            --accent:          #1d4ed8;
            --accent-light:    #eff6ff;
            --accent-hover:    #1e40af;

            --danger:          #e11d48;
            --danger-bg:       #fff1f2;
            --danger-border:   #fecdd3;

            /* Semantic */
            --ok:  #166534; --ok-bg:  #f0fdf4; --ok-bd:  #bbf7d0;
            --err: #e11d48; --err-bg: #fff1f2; --err-bd: #fecdd3;
            --war: #d97706; --war-bg: #fffbeb; --war-bd: #fed7aa;

            /* Aliases para vistas hijas */
            --bg:            var(--body-bg);
            --card:          var(--card-bg);
            --border:        var(--card-border);
            --shadow:        var(--card-shadow);
            --radius:        var(--card-radius);
            --t1:            var(--text-primary);
            --t2:            var(--text-secondary);
            --t3:            var(--text-muted);
            --accent-lt:     var(--accent-light);
            --accent-hv:     var(--accent-hover);
            --sb-bg:         var(--sidebar-bg);
            --sb-border:     var(--sidebar-border);
            --sb-text:       var(--sidebar-text);
            --sb-w:          var(--sidebar-width);
            --sb-cw:         var(--sidebar-coll-w);
            --tb-bg:         var(--topbar-bg);
            --tb-border:     var(--topbar-border);
            --tb-h:          var(--topbar-height);
            --success: var(--ok); --success-bg: var(--ok-bg); --success-border: var(--ok-bd);
        }

        html.dark {
            --sidebar-bg:      #0b1120;
            --sidebar-border:  #1a2744;
            --sidebar-text:    #94a3b8;
            --sidebar-hover:   #1a2744;
            --sidebar-active:  #3b82f6;
            --topbar-bg:       #0f172a;
            --topbar-border:   #1e293b;
            --body-bg:         #060d1a;
            --card-bg:         #0f172a;
            --card-border:     #1e293b;
            --card-shadow:     0 1px 3px rgba(0,0,0,.4), 0 4px 16px rgba(0,0,0,.3);
            --text-primary:    #f1f5f9;
            --text-secondary:  #94a3b8;
            --text-muted:      #475569;
            --accent:          #3b82f6;
            --accent-light:    #1e3a5f;
            --accent-hover:    #60a5fa;
            --danger:          #f87171;
            --danger-bg:       #2d0a0a;
            --danger-border:   #7f1d1d;
            --err: #f87171; --err-bg: #2d0a0a; --err-bd: #7f1d1d;
            --war: #fbbf24; --war-bg: #1c1302; --war-bd: #78350f;
        }

        html, body {
            font-family: var(--font-sans);
            background: var(--body-bg);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            transition: background .2s, color .2s;
        }

        /* ── Layout shell ── */
        .layout { display: flex; min-height: 100vh; }
        body.demo-mode aside#sidebar { top: 44px; }
        body.demo-mode .layout { padding-top: 44px; }

        /* ── Sidebar ── */
        aside#sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg) !important;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 200;
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
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        aside#sidebar.collapsed .nav-link[data-title]:hover::after {
            content: attr(data-title);
            position: fixed;
            left: calc(var(--sidebar-coll-w) + 8px);
            background: #111827;
            color: #fff;
            padding: 5px 10px;
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
            background: var(--sidebar-active) !important;
            color: #fff !important;
        }
        aside#sidebar .nav-link > svg {
            width: 18px; height: 18px;
            min-width: 18px; flex-shrink: 0;
            opacity: .8;
        }
        aside#sidebar .nav-link.active > svg { opacity: 1; }

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
        .sidebar-user-name { font-size: 13px; font-weight: 600; color: #e2e8f0 !important; }
        .sidebar-user-role { font-size: 11px; color: var(--sidebar-text) !important; margin-top: 2px; }

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
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 150;
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-title { font-size: 15px; font-weight: 600; color: var(--text-primary); }
        .topbar-right { display: flex; align-items: center; gap: 6px; }

        .sidebar-toggle {
            border: none; background: none;
            cursor: pointer; padding: 6px;
            border-radius: 8px; color: var(--text-secondary);
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        .sidebar-toggle:hover { background: var(--card-border); }
        .sidebar-toggle svg { width: 20px; height: 20px; }

        .topbar-icon-btn {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border: none; background: var(--body-bg);
            cursor: pointer; border-radius: 9px;
            color: var(--text-secondary); transition: background .15s;
            font-family: var(--font-sans); position: relative;
        }
        .topbar-icon-btn:hover { background: var(--card-border); }
        .topbar-icon-btn svg { width: 17px; height: 17px; }
        .tb-dot {
            position: absolute; top: 5px; right: 5px;
            width: 7px; height: 7px; border-radius: 50%;
            background: #dc2626; border: 2px solid var(--topbar-bg);
        }

        /* User button */
        .user-menu-wrap { position: relative; }
        .topbar-user-btn {
            display: flex; align-items: center; gap: 8px;
            background: none; border: none; cursor: pointer;
            font-family: var(--font-sans); color: var(--text-secondary);
            font-size: 13px; font-weight: 500; padding: 5px 8px;
            border-radius: 9px; transition: background .15s;
        }
        .topbar-user-btn:hover { background: var(--body-bg); }
        .topbar-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--accent); color: #fff;
            font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* Dropdown shared */
        .hc-dd { position: relative; }
        .hc-dd-menu {
            display: none;
            position: absolute; right: 0; top: calc(100% + 8px);
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden; z-index: 300;
        }
        .hc-dd-menu.open { display: block; animation: ddIn .12s ease; }
        @@keyframes ddIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

        .dd-head { padding: 12px 14px; border-bottom: 1px solid var(--card-border); }
        .dd-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .dd-email { font-size: 11px; color: var(--text-muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dd-body  { padding: 6px; }
        .dd-item {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 10px; border-radius: 7px;
            font-size: 13px; color: var(--text-secondary);
            text-decoration: none !important; transition: background .12s;
            cursor: pointer; background: none; border: none;
            width: 100%; text-align: left; font-family: var(--font-sans);
        }
        .dd-item:hover { background: var(--body-bg); color: var(--text-primary); }
        .dd-item svg { width: 14px; height: 14px; flex-shrink: 0; }
        .dd-item.red { color: var(--danger) !important; }
        .dd-div { height: 1px; background: var(--card-border); margin: 4px 6px; }

        /* Logs mini-menu */
        .dd-logs { width: 290px; }
        .log-row { padding: 10px 14px; border-bottom: 1px solid var(--card-border); font-size: 12px; color: var(--text-primary); }
        .log-row:last-child { border-bottom: none; }
        .log-time { font-size: 10px; color: var(--text-muted); margin-top: 2px; }

        /* Notificaciones mini-menu */
        .dd-notif { width: 300px; }
        .notif-row { padding: 10px 14px; border-bottom: 1px solid var(--card-border); display: flex; gap: 8px; align-items: flex-start; }
        .notif-row:last-child { border-bottom: none; }
        .notif-row.unread { background: var(--accent-light); }
        .notif-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; background: var(--card-border); }
        .notif-dot.unread-dot { background: var(--accent); }
        .notif-text { font-size: 12px; color: var(--text-primary); line-height: 1.4; margin-bottom: 2px; }
        .notif-time { font-size: 10px; color: var(--text-muted); }
        .notif-link-tag { font-size: 10px; color: var(--accent); text-decoration: none; display: block; margin-bottom: 2px; }
        .notif-link-tag:hover { text-decoration: underline; }

        /* ── Page content ── */
        .page-content { flex: 1; padding: 28px; }

        /* ── Mobile overlay ── */
        #sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.45); z-index: 190;
        }
        #sidebar-overlay.active { display: block; }

        /* ── Flash messages ── */
        .hc-flash {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 16px; border-radius: 10px;
            font-size: 13px; font-weight: 500; margin-bottom: 20px;
        }
        .hc-flash svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
        .f-ok  { background: var(--ok-bg);  border: 1px solid var(--ok-bd);  color: var(--ok);  }
        .f-err { background: var(--err-bg); border: 1px solid var(--err-bd); color: var(--err); }
        .f-war { background: var(--war-bg); border: 1px solid var(--war-bd); color: var(--war); }

        /* ── Mobile ── */
        @@media (max-width: 768px) {
            aside#sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; transition: transform .3s ease; }
            aside#sidebar.mobile-open { transform: translateX(0); }
            .main, .main.sidebar-collapsed { margin-left: 0; }
            .page-content { padding: 18px; }
            .topbar { padding: 0 16px; }
            .topbar-user-btn span:not(.topbar-avatar) { display: none; }
        }

        /* ── DataTables overrides ── */
        .dataTables_wrapper { font-family: var(--font-sans); font-size: 13px; color: var(--text-primary); }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter { margin-bottom: 14px; }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label { font-size: 12px; color: var(--text-secondary); font-weight: 500; display: flex; align-items: center; gap: 6px; }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--card-border) !important; border-radius: 8px !important;
            padding: 6px 10px !important; font-size: 12px !important;
            color: var(--text-primary) !important; background: var(--body-bg) !important;
            font-family: var(--font-sans) !important; box-shadow: none !important; outline: none;
            transition: border-color .15s;
        }
        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus { border-color: var(--accent) !important; }

        table.dataTable { border-collapse: collapse !important; border-spacing: 0 !important; width: 100% !important; }
        table.dataTable thead th,
        table.dataTable thead td {
            background: var(--body-bg) !important; color: var(--text-muted) !important;
            font-size: 10px !important; font-weight: 700 !important;
            text-transform: uppercase !important; letter-spacing: .07em !important;
            border-top: none !important; border-bottom: 2px solid var(--card-border) !important;
            padding: 10px 14px !important; white-space: nowrap;
        }
        table.dataTable thead th.sorting, table.dataTable thead th.sorting_asc,
        table.dataTable thead th.sorting_desc { cursor: pointer; }
        table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:after { opacity: .5; }

        table.dataTable tbody tr { background: var(--card-bg) !important; }
        table.dataTable tbody tr:hover > td { background: var(--body-bg) !important; }
        table.dataTable.stripe tbody tr.odd,
        table.dataTable.display tbody tr.odd { background: var(--card-bg) !important; }
        table.dataTable tbody td {
            border-top: none !important; border-bottom: 1px solid var(--card-border) !important;
            padding: 11px 14px !important; font-size: 13px !important;
            color: var(--text-primary) !important; vertical-align: middle !important;
        }
        table.dataTable tbody tr:last-child td { border-bottom: none !important; }
        table.dataTable tbody tr.selected > td { background: var(--accent-light) !important; }
        /* ── select-checkbox column ── */
        table.dataTable td.select-checkbox,
        table.dataTable th.select-checkbox {
            text-align: center !important; position: relative !important;
            width: 36px !important; min-width: 36px !important;
            padding-left: 10px !important; padding-right: 10px !important;
            cursor: pointer;
        }
        table.dataTable td.select-checkbox:before,
        table.dataTable th.select-checkbox:before {
            display: block; position: absolute; content: '';
            top: 50%; left: 50%;
            width: 16px; height: 16px;
            margin-top: -8px; margin-left: -8px;
            border: 2px solid var(--card-border) !important;
            border-radius: 4px; box-sizing: border-box;
            background: var(--card-bg);
            transition: border-color .15s, background .15s;
        }
        /* checked state — data rows */
        table.dataTable tr.selected td.select-checkbox:before {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
        }
        table.dataTable tr.selected td.select-checkbox:after {
            display: block; position: absolute; content: '';
            top: 50%; left: 50%;
            width: 9px; height: 5px;
            margin-top: -4px; margin-left: -5px;
            border-bottom: 2px solid #fff !important;
            border-left: 2px solid #fff !important;
            transform: rotate(-45deg);
        }
        /* checked state — header select-all */
        table.dataTable th.select-checkbox::after { display: none; }
        table.dataTable th.select-checkbox.selected:before {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
        }
        table.dataTable th.select-checkbox.selected::after {
            display: block !important; position: absolute; content: '';
            top: 50%; left: 50%;
            width: 9px; height: 5px;
            margin-top: -4px; margin-left: -5px;
            border-bottom: 2px solid #fff !important;
            border-left: 2px solid #fff !important;
            transform: rotate(-45deg);
        }

        .dataTables_wrapper .dataTables_info { font-size: 12px; color: var(--text-muted); padding-top: 10px; }
        .dataTables_wrapper .dataTables_paginate { padding-top: 8px; }
        .dataTables_wrapper .dataTables_paginate .pagination { gap: 3px; }
        .dataTables_wrapper .dataTables_paginate .page-item .page-link {
            border-radius: 7px !important; font-size: 12px !important; font-weight: 500 !important;
            border: 1px solid var(--card-border) !important; color: var(--text-secondary) !important;
            background: var(--card-bg) !important; padding: 5px 11px !important; transition: all .12s;
        }
        .dataTables_wrapper .dataTables_paginate .page-item .page-link:hover { background: var(--body-bg) !important; color: var(--text-primary) !important; }
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link { background: var(--accent) !important; border-color: var(--accent) !important; color: #fff !important; }
        .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link { opacity: .4; pointer-events: none; }

        .dt-buttons { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
        .dt-button, .dt-buttons .btn {
            border-radius: 8px !important; font-size: 11px !important; font-weight: 600 !important;
            padding: 6px 12px !important; border: 1px solid var(--card-border) !important;
            background: var(--card-bg) !important; color: var(--text-secondary) !important;
            box-shadow: none !important; transition: background .12s, color .12s !important; cursor: pointer;
        }
        .dt-button:hover, .dt-buttons .btn:hover { background: var(--body-bg) !important; color: var(--text-primary) !important; }
        .dt-button.btn-danger, .dt-buttons .btn-danger { background: #fff1f2 !important; color: #e11d48 !important; border-color: #fecdd3 !important; }
        .dt-button.btn-primary, .dt-buttons .btn-primary { background: var(--accent) !important; color: #fff !important; border-color: var(--accent) !important; }

        table.dataTable .btn-xs { border-radius: 6px !important; font-size: 11px !important; font-weight: 600 !important; padding: 3px 10px !important; border: none !important; box-shadow: none !important; }
        table.dataTable .btn-primary  { background: var(--accent-light) !important; color: var(--accent) !important; }
        table.dataTable .btn-info     { background: #f0fdfa !important; color: #0891b2 !important; }
        table.dataTable .btn-danger   { background: #fff1f2 !important; color: #e11d48 !important; }
        table.dataTable .btn-success  { background: #f0fdf4 !important; color: #16a34a !important; }
        table.dataTable .btn-warning  { background: #fffbeb !important; color: #d97706 !important; }
        table.dataTable .btn-secondary{ background: var(--body-bg) !important; color: var(--text-secondary) !important; border: 1px solid var(--card-border) !important; }

        html.dark table.dataTable thead th,
        html.dark table.dataTable thead td { background: #0b1120 !important; }
        html.dark table.dataTable tbody tr { background: var(--card-bg) !important; }
        html.dark table.dataTable tbody tr:hover > td { background: #0b1120 !important; }
        html.dark .dt-button, html.dark .dt-buttons .btn { background: #0f172a !important; color: #94a3b8 !important; border-color: #1e293b !important; }
        html.dark .dataTables_wrapper .dataTables_paginate .page-item .page-link { background: #0f172a !important; color: #94a3b8 !important; border-color: #1e293b !important; }
        html.dark .dataTables_wrapper .dataTables_length select,
        html.dark .dataTables_wrapper .dataTables_filter input { background: #0f172a !important; color: #f1f5f9 !important; border-color: #1e293b !important; }
    </style>

    @stack('styles')
</head>
@php $isDemoMode = app(\App\Services\License\LicenseClientInterface::class)->isDemo(); @endphp
<body{{ $isDemoMode ? ' class="demo-mode"' : '' }}>
@if($isDemoMode)
<div style="position:fixed;top:0;left:0;right:0;height:44px;background:#f59e0b;color:#78350f;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;z-index:9999;border-bottom:2px solid #d97706;box-sizing:border-box;padding:0 16px;">
    ENTORNO DEMO — Los datos se restablecen automáticamente cada 24 horas.
    <a href="{{ config('demo.saas_portal_url') }}" target="_blank"
       style="margin-left:16px;color:#1e3a5f;text-decoration:underline;font-weight:800;">
        Contratar licencia →
    </a>
</div>
@endif

@include('notify::components.notify')

@php $isAdmin = auth()->user()?->is_admin ?? false; @endphp

<div class="layout">

    {{-- ── SIDEBAR ── --}}
    <aside id="sidebar">

        <a href="{{ $isAdmin ? route('admin.dashboard.home') : route('panel.home') }}" class="sidebar-logo">
            <div class="logo-icon">
                @if($sistemaConfig?->logo_url)
                    <img src="{{ $sistemaConfig->logo_url }}" alt="Logo">
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff">
                        <rect x="9" y="3" width="6" height="18" rx="2" fill="white" fill-opacity=".95" stroke="none"/>
                        <rect x="3" y="9" width="18" height="6" rx="2" fill="white" fill-opacity=".95" stroke="none"/>
                    </svg>
                @endif
            </div>
            <div class="logo-texts">
                <span class="logo-name">{{ $sistemaConfig?->nombre_sistema ?? 'Sistema HC' }}</span>
                <span class="logo-sub">{{ $isAdmin ? 'Panel administrativo' : 'Panel operativo' }}</span>
            </div>
        </a>

        <nav class="sidebar-nav">

            <div class="nav-section">Principal</div>

            @if($isAdmin)
            <a href="{{ route('admin.dashboard.home') }}" data-title="Dashboard"
               class="nav-link {{ request()->routeIs('admin.dashboard.home') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Dashboard</span>
            </a>
            @else
            <a href="{{ route('panel.home') }}" data-title="Dashboard"
               class="nav-link {{ request()->routeIs('panel.home') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Dashboard</span>
            </a>
            @endif

            <div class="nav-section">Atención Clínica</div>

            <a href="{{ route('panel.paciente.index') }}" data-title="Pacientes"
               class="nav-link {{ request()->routeIs('panel.paciente.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Pacientes</span>
            </a>

            <a href="{{ route('panel.agenda.index') }}" data-title="Agenda"
               class="nav-link {{ request()->routeIs('panel.agenda.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Agenda</span>
            </a>

            <a href="{{ route('panel.informe.index') }}" data-title="Informes"
               class="nav-link {{ request()->routeIs('panel.informe.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Informes</span>
            </a>

            <a href="{{ route('panel.medicacion.index') }}" data-title="Prescripciones"
               class="nav-link {{ request()->routeIs('panel.medicacion.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span>Prescripciones</span>
            </a>

            <a href="{{ route('panel.recetas.index') }}" data-title="Recetas"
               class="nav-link {{ request()->routeIs('panel.recetas.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Recetas</span>
            </a>

            <div class="nav-section">Comunicación</div>

            <a href="{{ $isAdmin ? route('admin.messenger.index') : route('panel.messenger.index') }}" data-title="Mensajería"
               class="nav-link {{ request()->routeIs('panel.messenger.*') || request()->routeIs('admin.messenger.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <span>Mensajería</span>
            </a>

            <a href="{{ $isAdmin ? route('admin.user-alerts.index') : route('panel.notificaciones.index') }}" data-title="Notificaciones"
               class="nav-link {{ request()->routeIs('panel.notificaciones.*') || request()->routeIs('admin.user-alerts.*') ? 'active' : '' }}">
                <div style="position:relative;width:18px;height:18px;flex-shrink:0;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;opacity:.8;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($alertsCount > 0)
                    <span style="position:absolute;top:-3px;right:-4px;width:8px;height:8px;border-radius:50%;background:#dc2626;border:1.5px solid var(--sidebar-bg,#0f172a);"></span>
                    @endif
                </div>
                <span>
                    Notificaciones
                    @if($alertsCount > 0)
                    <span style="margin-left:4px;background:#dc2626;color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:99px;vertical-align:middle;">{{ $alertsCount }}</span>
                    @endif
                </span>
            </a>

            @if($isAdmin)

            <div class="nav-section">Administración</div>

            <a href="{{ route('admin.users.index') }}" data-title="Usuarios"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Usuarios</span>
            </a>

            <a href="{{ route('admin.roles.index') }}" data-title="Roles"
               class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                <span>Roles</span>
            </a>

            <a href="{{ route('admin.permissions.index') }}" data-title="Permisos"
               class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Permisos</span>
            </a>

            <a href="{{ route('admin.audit-logs.index') }}" data-title="Auditoría"
               class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Auditoría</span>
            </a>

            <div class="nav-section">Configuración</div>

            <a href="{{ route('admin.informes.tipos.index') }}" data-title="Tipos de Informe"
               class="nav-link {{ request()->routeIs('admin.informes.tipos.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Tipos de Informe</span>
            </a>

            <a href="{{ route('admin.especialidades.index') }}" data-title="Especialidades"
               class="nav-link {{ request()->routeIs('admin.especialidades.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <span>Especialidades</span>
            </a>

            <a href="{{ route('admin.tipos-consentimiento.index') }}" data-title="Consentimientos"
               class="nav-link {{ request()->routeIs('admin.tipos-consentimiento.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Consentimientos</span>
            </a>

            <a href="{{ route('admin.configuracion.edit') }}" data-title="Configuración"
               class="nav-link {{ request()->routeIs('admin.configuracion.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Configuración</span>
            </a>

            <a href="{{ route('license.index') }}" data-title="Licencia"
               class="nav-link {{ request()->routeIs('license.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Licencia</span>
            </a>

            <a href="{{ route('system-version.index') }}" data-title="Versión"
               class="nav-link {{ request()->routeIs('system-version.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Versión</span>
            </a>

            @endif

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <div class="sidebar-user-name">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                <div class="sidebar-user-role">{{ $isAdmin ? 'Administrador' : 'Operativo' }}</div>
            </div>
        </div>

    </aside>

    <div id="sidebar-overlay"></div>

    {{-- ── MAIN ── --}}
    <div class="main" id="main">

        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Menú">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="topbar-title">@yield('title', 'Panel')</span>
            </div>

            <div class="topbar-right">

                {{-- Dark mode --}}
                <button class="topbar-icon-btn" id="theme-toggle" title="Modo oscuro/claro">
                    <svg id="icon-sun" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1M12 20v1M4.2 4.2l.7.7M18.1 18.1l.7.7M1 12h1M22 12h1M4.2 19.8l.7-.7M18.1 5.9l.7-.7M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                    </svg>
                    <svg id="icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                </button>

                {{-- Notificaciones --}}
                <div class="hc-dd">
                    <button class="topbar-icon-btn" id="notif-btn" title="Notificaciones" style="position:relative;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($alertsCount > 0)<span class="tb-dot"></span>@endif
                    </button>
                    <div class="hc-dd-menu dd-notif" id="notif-drop">
                        <div class="dd-head" style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:13px;font-weight:700;color:var(--text-primary);">
                                Notificaciones
                                @if($alertsCount > 0)
                                <span style="margin-left:6px;background:#dc2626;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:99px;">{{ $alertsCount }}</span>
                                @endif
                            </span>
                            <a href="{{ $isAdmin ? route('admin.user-alerts.index') : route('panel.notificaciones.index') }}"
                               style="font-size:11px;color:var(--accent);font-weight:600;text-decoration:none;">Ver todas</a>
                        </div>
                        <div style="max-height:280px;overflow-y:auto;">
                            @forelse($notifRecent as $notif)
                            @php $isRead = (bool)($notif->pivot->read ?? true); @endphp
                            <div class="notif-row {{ !$isRead ? 'unread' : '' }}">
                                <div class="notif-dot {{ !$isRead ? 'unread-dot' : '' }}"></div>
                                <div style="flex:1;min-width:0;">
                                    <div class="notif-text">{{ Str::limit($notif->alert_text, 70) }}</div>
                                    @if($notif->alert_link)
                                    <a href="{{ $notif->alert_link }}" class="notif-link-tag" target="_blank">Ver enlace</a>
                                    @endif
                                    <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @empty
                            <div style="padding:24px;text-align:center;font-size:12px;color:var(--text-muted);">Sin notificaciones</div>
                            @endforelse
                        </div>
                        @if($alertsCount > 0)
                        <div style="padding:10px 14px;border-top:1px solid var(--card-border);text-align:center;">
                            <button onclick="markAllNotifsRead()" style="font-size:11px;color:var(--accent);background:none;border:none;cursor:pointer;font-family:var(--font-sans);font-weight:600;">
                                Marcar todas como leídas
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Logs auditoría — solo admin --}}
                @if($isAdmin)
                @php $logsCount = auth()->user()?->AuditLogs()?->where('read', false)->count() ?? 0; @endphp
                <div class="hc-dd">
                    <button class="topbar-icon-btn" id="logs-btn" title="Logs de auditoría" style="position:relative;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        @if($logsCount > 0)<span class="tb-dot"></span>@endif
                    </button>
                    <div class="hc-dd-menu dd-logs" id="logs-drop">
                        <div class="dd-head" style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:13px;font-weight:700;color:var(--text-primary);">Auditoría</span>
                            @if($logsCount > 0)
                            <button onclick="markAllLogsRead()" style="font-size:11px;color:var(--accent);background:none;border:none;cursor:pointer;font-family:var(--font-sans);">Marcar leídos</button>
                            @endif
                        </div>
                        <div style="max-height:260px;overflow-y:auto;">
                            @php $logs = auth()->user()?->AuditLogs()?->where('read',false)->latest()->limit(8)->get() ?? collect(); @endphp
                            @forelse($logs as $log)
                            <div class="log-row">
                                <div>{{ $log->description }}</div>
                                <div class="log-time">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                            @empty
                            <div style="padding:24px;text-align:center;font-size:12px;color:var(--text-muted);">Sin logs pendientes</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif

                {{-- Mensajes --}}
                @php
                    try {
                        $mensajesNoLeidos = \App\Models\QaMessage::whereNull('read_at')
                            ->where('sender_id', '!=', auth()->id())
                            ->whereHas('topic', function($q) {
                                $q->where('receiver_id', auth()->id())
                                  ->orWhere('creator_id', auth()->id());
                            })->count();
                        $ultimosMensajes = \App\Models\QaMessage::with(['topic', 'sender'])
                            ->whereHas('topic', function($q) {
                                $q->where('receiver_id', auth()->id())
                                  ->orWhere('creator_id', auth()->id());
                            })
                            ->orderBy('created_at', 'desc')
                            ->take(8)->get();
                    } catch(\Exception $e) {
                        $mensajesNoLeidos = 0;
                        $ultimosMensajes  = collect();
                    }
                @endphp
                <div class="hc-dd">
                    <button class="topbar-icon-btn" id="msg-btn" title="Mensajes" style="position:relative;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        @if($mensajesNoLeidos > 0)<span class="tb-dot"></span>@endif
                    </button>
                    <div class="hc-dd-menu" id="msg-drop" style="width:320px;">
                        <div class="dd-head" style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:13px;font-weight:700;color:var(--text-primary);">
                                Mensajes
                                @if($mensajesNoLeidos > 0)
                                <span style="margin-left:6px;background:#dc2626;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:99px;">{{ $mensajesNoLeidos }}</span>
                                @endif
                            </span>
                            <a href="{{ route($isAdmin ? 'admin.messenger.index' : 'panel.messenger.index') }}"
                               style="font-size:11px;color:var(--accent);font-weight:600;text-decoration:none;">Ver todos</a>
                        </div>
                        <div style="max-height:300px;overflow-y:auto;">
                            @forelse($ultimosMensajes as $msg)
                            <div style="padding:10px 16px;border-bottom:1px solid var(--card-border);display:flex;gap:10px;align-items:flex-start;{{ !$msg->read_at ? 'background:var(--accent-light);' : '' }}">
                                <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:5px;background:{{ !$msg->read_at ? 'var(--accent)' : 'var(--card-border)' }};"></div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:11px;font-weight:{{ !$msg->read_at ? '700' : '500' }};color:var(--text-primary);margin-bottom:2px;">{{ optional($msg->sender)->name ?? 'Usuario' }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);line-height:1.4;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ optional($msg->topic)->subject ?? 'Sin asunto' }}</div>
                                    <div style="font-size:10px;color:var(--text-muted);">{{ $msg->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @empty
                            <div style="padding:28px;text-align:center;font-size:13px;color:var(--text-muted);">No tenés mensajes.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Agenda --}}
                @php
                    try {
                        $citasHoy = \App\Models\Agenda::whereDate('fecha_hora_inicio', now())
                            ->where(function($q) {
                                $q->where('profesional_id', auth()->id())
                                  ->orWhere('creado_por', auth()->id());
                            })
                            ->orderBy('fecha_hora_inicio')
                            ->get();
                        $citasPendientes = $citasHoy->where('estado', 'pendiente')->count();
                    } catch(\Exception $e) {
                        $citasHoy        = collect();
                        $citasPendientes = 0;
                    }
                @endphp
                <div class="hc-dd">
                    <button class="topbar-icon-btn" id="agenda-btn" title="Agenda del día" style="position:relative;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        @if($citasPendientes > 0)<span class="tb-dot" style="background:#f59e0b;"></span>@endif
                    </button>
                    <div class="hc-dd-menu" id="agenda-drop" style="width:320px;">
                        <div class="dd-head" style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:13px;font-weight:700;color:var(--text-primary);">
                                Agenda de hoy
                                @if($citasPendientes > 0)
                                <span style="margin-left:6px;background:#f59e0b;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:99px;">{{ $citasPendientes }}</span>
                                @endif
                            </span>
                            <a href="{{ route('panel.agenda.index') }}"
                               style="font-size:11px;color:var(--accent);font-weight:600;text-decoration:none;">Ver agenda</a>
                        </div>
                        <div style="max-height:300px;overflow-y:auto;">
                            @forelse($citasHoy as $cita)
                            @php
                                $dotColor = match($cita->estado ?? '') {
                                    'confirmado' => '#16a34a',
                                    'realizado'  => '#6c757d',
                                    'cancelado'  => '#dc2626',
                                    default      => '#f59e0b',
                                };
                                $bdgStyle = match($cita->estado ?? '') {
                                    'confirmado' => 'background:#dcfce7;color:#16a34a;',
                                    'realizado'  => 'background:#f3f4f6;color:#6c757d;',
                                    'cancelado'  => 'background:#fee2e2;color:#dc2626;',
                                    default      => 'background:#fef3c7;color:#d97706;',
                                };
                                $bdgLabel = \App\Models\Agenda::estadosLabels()[$cita->estado] ?? ucfirst($cita->estado ?? '');
                            @endphp
                            <div style="padding:10px 16px;border-bottom:1px solid var(--card-border);display:flex;gap:10px;align-items:flex-start;">
                                <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:5px;background:{{ $dotColor }};"></div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:12px;font-weight:600;color:var(--text-primary);margin-bottom:2px;">{{ optional($cita->fecha_hora_inicio)->format('H:i') ?? '—' }}</div>
                                    <div style="font-size:11px;color:var(--text-secondary);line-height:1.4;margin-bottom:2px;">{{ optional($cita->paciente)->nombre ?? 'Sin paciente' }} {{ optional($cita->paciente)->apellido ?? '' }}</div>
                                    <div style="font-size:10px;color:var(--text-muted);margin-bottom:4px;">{{ Str::limit($cita->motivo ?? '', 40) }}</div>
                                    <span style="display:inline-block;padding:1px 7px;border-radius:99px;font-size:9px;font-weight:600;{{ $bdgStyle }}">{{ $bdgLabel }}</span>
                                </div>
                            </div>
                            @empty
                            <div style="padding:28px;text-align:center;font-size:13px;color:var(--text-muted);">No tenés citas para hoy.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User menu --}}
                <div class="user-menu-wrap">
                    <button class="topbar-user-btn" id="user-menu-btn">
                        <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()?->name ?? auth()->user()?->email ?? 'U', 0, 1)) }}</div>
                        <span>{{ auth()->user()?->name ?? 'Usuario' }}</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;opacity:.5;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="hc-dd-menu" id="user-dropdown" style="min-width:200px;">
                        <div class="dd-head">
                            <div class="dd-name">{{ auth()->user()?->name ?? 'Usuario' }}</div>
                            <div class="dd-email">{{ auth()->user()?->email ?? '' }}</div>
                        </div>
                        <div class="dd-body">
                            <a href="{{ route('panel.profile.index') }}" class="dd-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mi perfil
                            </a>
                            <div class="dd-div"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dd-item red">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <main class="page-content">

            {{-- Flash messages --}}
            @if(session('success') || session('message'))
            <div class="hc-flash f-ok">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') ?? session('message') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="hc-flash f-err">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif
            @if($errors->count())
            <div class="hc-flash f-err">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <ul style="margin:0;padding:0;list-style:none;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>

{{-- Scripts CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const sidebar  = document.getElementById('sidebar');
    const main     = document.getElementById('main');
    const toggle   = document.getElementById('sidebar-toggle');
    const overlay  = document.getElementById('sidebar-overlay');
    const mob      = () => window.innerWidth <= 768;

    // ── Theme ──
    const themeBtn = document.getElementById('theme-toggle');
    const iconSun  = document.getElementById('icon-sun');
    const iconMoon = document.getElementById('icon-moon');

    function applyTheme(t) {
        document.documentElement.classList.toggle('dark', t === 'dark');
        if (iconSun)  iconSun.style.display  = t === 'dark' ? 'block' : 'none';
        if (iconMoon) iconMoon.style.display = t === 'dark' ? 'none'  : 'block';
    }
    applyTheme(localStorage.getItem('theme') || 'light');
    themeBtn?.addEventListener('click', () => {
        const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        applyTheme(next);
    });

    // ── Sidebar ──
    if (!mob() && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        main.classList.add('sidebar-collapsed');
    }
    toggle?.addEventListener('click', () => {
        if (mob()) {
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
    window.addEventListener('resize', () => {
        if (!mob()) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // ── Dropdowns ──
    function closeAll() {
        document.querySelectorAll('.hc-dd-menu').forEach(el => el.classList.remove('open'));
    }
    ['notif-btn','logs-btn','msg-btn','agenda-btn'].forEach(id => {
        const btn = document.getElementById(id);
        const dropId = id.replace('-btn', '-drop');
        const drop = document.getElementById(dropId);
        btn?.addEventListener('click', e => {
            e.stopPropagation();
            const wasOpen = drop?.classList.contains('open');
            closeAll();
            if (!wasOpen && drop) drop.classList.add('open');
        });
    });

    const userBtn  = document.getElementById('user-menu-btn');
    const userDrop = document.getElementById('user-dropdown');
    userBtn?.addEventListener('click', e => {
        e.stopPropagation();
        const wasOpen = userDrop?.classList.contains('open');
        closeAll();
        if (!wasOpen) userDrop?.classList.add('open');
    });

    document.addEventListener('click', closeAll);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeAll();
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // ── DataTables ──
    const dtLang = { 'es': '{{ asset("json/Spanish.json") }}' };
    $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, { className: 'btn' });
    $.extend(true, $.fn.dataTable.defaults, {
        language: { url: dtLang['{{ app()->getLocale() }}'] },
        columnDefs: [
            { orderable: false, className: 'select-checkbox', targets: 0 },
            { orderable: false, searchable: false, targets: -1 }
        ],
        select: { style: 'multi+shift', selector: 'td:first-child' },
        order: [], pageLength: 100,
        dom: 'lBfrtip<"actions">',
        buttons: [
            { extend: 'selectAll',  className: 'btn-primary', text: '{{ trans("global.select_all") }}',
              action: (e, dt) => { e.preventDefault(); dt.rows().deselect(); dt.rows({search:'applied'}).select(); } },
            { extend: 'selectNone', className: 'btn-primary', text: '{{ trans("global.deselect_all") }}' },
            { extend: 'copy',   className: 'btn-default', text: '{{ trans("global.datatables.copy") }}',   exportOptions:{columns:':visible'} },
            { extend: 'csv',    className: 'btn-default', text: '{{ trans("global.datatables.csv") }}',    exportOptions:{columns:':visible'} },
            { extend: 'excel',  className: 'btn-default', text: '{{ trans("global.datatables.excel") }}',  exportOptions:{columns:':visible'} },
            { extend: 'pdf',    className: 'btn-default', text: '{{ trans("global.datatables.pdf") }}',    exportOptions:{columns:':visible'} },
            { extend: 'print',  className: 'btn-default', text: '{{ trans("global.datatables.print") }}',  exportOptions:{columns:':visible'} },
            { extend: 'colvis', className: 'btn-default', text: '{{ trans("global.datatables.colvis") }}', exportOptions:{columns:':visible'} }
        ]
    });
    $.fn.dataTable.ext.classes.sPageButton = '';

    // Header checkbox — select / deselect all visible rows
    $(document).on('click', 'table.dataTable thead th.select-checkbox', function () {
        var dt = $(this).closest('table').DataTable();
        var $th = $(this);
        if ($th.hasClass('selected')) {
            dt.rows({ search: 'applied' }).deselect();
        } else {
            dt.rows({ search: 'applied' }).select();
        }
    });
    $(document).on('select.dt deselect.dt', 'table.dataTable', function () {
        var dt = $(this).DataTable();
        var total    = dt.rows({ search: 'applied' }).count();
        var selected = dt.rows({ selected: true, search: 'applied' }).count();
        $(this).find('thead th.select-checkbox').toggleClass('selected', total > 0 && selected === total);
    });

});

async function markAllLogsRead() {
    try {
        await $.get('{{ auth()->id() ? route("admin.audit-log.markAllAsRead", auth()->id()) : "#" }}');
        location.reload();
    } catch(e) { console.error(e); }
}

async function markAllNotifsRead() {
    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        await fetch('{{ route("panel.notificaciones.readAll") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }
        });
        location.reload();
    } catch(e) { console.error(e); }
}
</script>

@stack('scripts')
</body>
</html>
