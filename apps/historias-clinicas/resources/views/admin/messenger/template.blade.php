@extends('layouts.admin')

@section('content')
<style>
    .msg-shell {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 0;
        height: calc(100vh - var(--tb-h, 60px) - 56px);
        min-height: 500px;
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        animation: msgFadeUp .3s ease both;
    }
    @@keyframes msgFadeUp {
        from { opacity:0; transform:translateY(8px); }
        to   { opacity:1; transform:translateY(0); }
    }
    @@media (max-width: 768px) {
        .msg-shell { grid-template-columns: 1fr; height: auto; }
        .msg-sidebar { display: none; }
    }

    .msg-sidebar {
        border-right: 1px solid var(--card-border, #e8edf2);
        display: flex; flex-direction: column;
        background: var(--body-bg, #f8fafc);
    }
    html.dark .msg-sidebar { background: #0b1120; }

    .msg-sidebar-head {
        padding: 18px 16px 12px;
        border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-shrink: 0;
    }
    .msg-sidebar-title {
        font-size: 15px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        display: flex; align-items: center; justify-content: space-between;
    }
    .msg-btn-new {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px; border-radius: 8px;
        background: var(--accent, #1d4ed8); color: #fff;
        font-size: 12px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: background .15s;
    }
    .msg-btn-new:hover { background: var(--accent-hover, #1e40af); color: #fff; text-decoration: none; }
    .msg-btn-new svg { width: 13px; height: 13px; }

    .msg-nav { padding: 8px; flex-shrink: 0; }
    .msg-nav-link {
        display: flex; align-items: center; justify-content: space-between;
        padding: 8px 10px; border-radius: 8px;
        font-size: 13px; font-weight: 500;
        color: var(--text-secondary, #64748b);
        text-decoration: none; transition: all .12s;
        margin-bottom: 2px;
    }
    .msg-nav-link:hover { background: var(--card-bg, #fff); color: var(--text-primary, #0f172a); text-decoration: none; }
    .msg-nav-link.active { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); font-weight: 600; }
    html.dark .msg-nav-link:hover  { background: #0f172a; }
    html.dark .msg-nav-link.active { background: #1e3a5f; }
    .msg-nav-left { display: flex; align-items: center; gap: 8px; }
    .msg-nav-left svg { width: 15px; height: 15px; flex-shrink: 0; }
    .msg-badge {
        font-size: 10px; font-weight: 700; padding: 1px 7px;
        border-radius: 99px; background: #dc2626; color: #fff; line-height: 1.6;
    }
    .msg-badge.warn { background: #f59e0b; }

    .msg-main { display: flex; flex-direction: column; min-height: 0; }

    .msg-main-head {
        padding: 14px 20px;
        border-bottom: 1px solid var(--card-border, #e8edf2);
        display: flex; align-items: center; justify-content: space-between;
        flex-shrink: 0; gap: 10px;
    }
    .msg-main-title {
        font-size: 14px; font-weight: 600;
        color: var(--text-primary, #0f172a);
        display: flex; align-items: center; gap: 8px;
        min-width: 0;
    }
    .msg-main-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); flex-shrink: 0; }
    .msg-main-title span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .msg-content { flex: 1; overflow-y: auto; padding: 20px; min-height: 0; }
    .msg-content::-webkit-scrollbar { width: 4px; }
    .msg-content::-webkit-scrollbar-thumb { background: var(--card-border, #e8edf2); border-radius: 99px; }
</style>

<div class="msg-shell">

    {{-- ── Sidebar ── --}}
    <aside class="msg-sidebar">
        <div class="msg-sidebar-head">
            <div class="msg-sidebar-title">
                Mensajería
                <a href="{{ route('admin.messenger.createTopic') }}" class="msg-btn-new">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo
                </a>
            </div>
        </div>

        <nav class="msg-nav">
            <a href="{{ route('admin.messenger.index') }}"
               class="msg-nav-link {{ request()->routeIs('admin.messenger.index') ? 'active' : '' }}">
                <span class="msg-nav-left">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ trans('global.all_messages') }}
                </span>
            </a>
            <a href="{{ route('admin.messenger.showInbox') }}"
               class="msg-nav-link {{ request()->routeIs('admin.messenger.showInbox') ? 'active' : '' }}">
                <span class="msg-nav-left">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    {{ trans('global.inbox') }}
                </span>
                @if($unreads['inbox'] > 0)
                    <span class="msg-badge">{{ $unreads['inbox'] }}</span>
                @endif
            </a>
            <a href="{{ route('admin.messenger.showOutbox') }}"
               class="msg-nav-link {{ request()->routeIs('admin.messenger.showOutbox') ? 'active' : '' }}">
                <span class="msg-nav-left">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    {{ trans('global.outbox') }}
                </span>
                @if($unreads['outbox'] > 0)
                    <span class="msg-badge warn">{{ $unreads['outbox'] }}</span>
                @endif
            </a>
        </nav>
    </aside>

    {{-- ── Main ── --}}
    <div class="msg-main">
        <div class="msg-main-head">
            <div class="msg-main-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>@yield('title')</span>
            </div>
            @yield('msg-head-action')
        </div>

        <div class="msg-content">
            @yield('messenger-content')
        </div>
    </div>

</div>
@endsection
