<x-admin-layout title="Dashboard">

    {{-- Header --}}
    <div style="margin-bottom:28px;">
        <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">
            Bienvenido, {{ auth()->user()->name }} 👋
        </h1>
        <p style="font-size:13px; color:var(--text-muted); margin:4px 0 0;">
            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </p>
    </div>

    {{-- Resumen del negocio --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
        <h2 style="font-size:13px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin:0;">Resumen del negocio</h2>
    </div>
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:32px;">

        <div class="card" style="padding:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Clientes</p>
                <div style="width:36px; height:36px; border-radius:10px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                    </svg>
                </div>
            </div>
            <h3 style="font-size:32px; font-weight:700; color:var(--text-primary); line-height:1;">{{ $totalClientes }}</h3>
            <p style="font-size:12px; color:var(--success); margin-top:6px;">+{{ $monthlyNew }} este mes</p>
        </div>

        <div class="card" style="padding:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Licencias activas</p>
                <div style="width:36px; height:36px; border-radius:10px; background:var(--success-bg); color:var(--success); display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <h3 style="font-size:32px; font-weight:700; color:var(--success); line-height:1;">{{ $activeLicenses }}</h3>
            <p style="font-size:12px; color:var(--text-muted); margin-top:6px;">de {{ $totalLicenses }} totales</p>
        </div>

        <div class="card" style="padding:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Ingresos del mes (licencias)</p>
                <div style="width:36px; height:36px; border-radius:10px; background:rgba(99,102,241,.1); color:#6366f1; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h3 style="font-size:26px; font-weight:700; color:#6366f1; line-height:1;">
                ${{ number_format($monthlyRevenue, 0, ',', '.') }}
            </h3>
            <p style="font-size:12px; color:var(--text-muted); margin-top:6px;">Total: ${{ number_format($totalRevenue, 0, ',', '.') }} ARS</p>
        </div>

        <div class="card" style="padding:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Por vencer</p>
                <div style="width:36px; height:36px; border-radius:10px; background:#fff7ed; color:#f59e0b; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h3 style="font-size:32px; font-weight:700; color:#f59e0b; line-height:1;">{{ $expiringLicenses }}</h3>
            <p style="font-size:12px; color:var(--text-muted); margin-top:6px;">próximos 30 días</p>
        </div>

    </div>

    {{-- Finanzas --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
        <h2 style="font-size:13px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin:0;">Finanzas</h2>
        <p style="font-size:11px; color:var(--text-muted); margin:0;">Cobros a clientes de mantenimiento — separado de ingresos por licencias SaaS de arriba</p>
    </div>
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:32px;">

        <div class="card" style="padding:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Cobrado este mes</p>
                <div style="width:36px; height:36px; border-radius:10px; background:var(--success-bg); color:var(--success); display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h3 style="font-size:26px; font-weight:700; color:var(--success); line-height:1;">{{ $paidThisMonth }}</h3>
            <p style="font-size:12px; color:var(--text-muted); margin-top:6px;">pagos registrados este mes</p>
        </div>

        <div class="card" style="padding:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Saldo por cobrar</p>
                <div style="width:36px; height:36px; border-radius:10px; background:#fff7ed; color:#f59e0b; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h5M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                    </svg>
                </div>
            </div>
            <h3 style="font-size:26px; font-weight:700; color:#f59e0b; line-height:1;">{{ $pendingBalance }}</h3>
            <p style="font-size:12px; color:var(--text-muted); margin-top:6px;">de todos los clientes</p>
        </div>

        <div class="card" style="padding:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Cobrado acumulado</p>
                <div style="width:36px; height:36px; border-radius:10px; background:rgba(99,102,241,.1); color:#6366f1; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                    </svg>
                </div>
            </div>
            <h3 style="font-size:26px; font-weight:700; color:#6366f1; line-height:1;">{{ $totalCollected }}</h3>
            <p style="font-size:12px; color:var(--text-muted); margin-top:6px;">histórico, todos los clientes</p>
        </div>

        <div class="card" style="padding:20px; {{ $overdueCharges > 0 ? 'border-color:var(--danger-border);' : '' }}">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Cobros vencidos</p>
                <div style="width:36px; height:36px; border-radius:10px; background:var(--danger-bg); color:var(--danger); display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
            <h3 style="font-size:32px; font-weight:700; color:var(--danger); line-height:1;">{{ $overdueCharges }}</h3>
            <p style="font-size:12px; color:var(--text-muted); margin-top:6px;">con vencimiento pasado</p>
        </div>

    </div>

    {{-- Soporte --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
        <h2 style="font-size:13px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin:0;">Soporte — necesita atención</h2>
        <a href="{{ route('tickets.index') }}" style="font-size:12px; color:var(--accent); text-decoration:none; font-weight:600;">Ver todos los tickets →</a>
    </div>
    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-bottom:32px;">

        <div class="card" style="padding:16px 20px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <p style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600; margin-bottom:6px;">Pendientes</p>
                    <h3 style="font-size:24px; font-weight:700; color:#f59e0b; line-height:1;">{{ $openTickets }}</h3>
                    <p style="font-size:11px; color:var(--text-muted); margin-top:4px;">abiertos o esperando respuesta</p>
                </div>
                <div style="width:30px; height:30px; border-radius:8px; background:#fff7ed; color:#f59e0b; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card" style="padding:16px 20px; {{ $criticalTickets > 0 ? 'border-color:var(--danger-border);' : '' }}">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <p style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600; margin-bottom:6px;">Críticos</p>
                    <h3 style="font-size:24px; font-weight:700; color:var(--danger); line-height:1;">{{ $criticalTickets }}</h3>
                    <p style="font-size:11px; color:var(--text-muted); margin-top:4px;">atención urgente</p>
                </div>
                <div style="width:30px; height:30px; border-radius:8px; background:var(--danger-bg); color:var(--danger); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Actividad --}}
    <div style="margin-bottom:14px;">
        <h2 style="font-size:13px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin:0;">Actividad</h2>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        {{-- Próximas a vencer --}}
        <div class="card" style="padding:24px;">
            <h3 style="font-size:14px; font-weight:600; color:var(--text-primary); margin:0 0 20px;">Próximas a vencer</h3>
            @forelse($upcomingRenewalsList as $item)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--card-border);">
                    <div style="font-size:13.5px; color:var(--text-primary);">{{ $item->label }}</div>
                    <div style="text-align:right; font-size:12px; font-weight:600; color:{{ $item->expires_at->isPast() ? 'var(--danger)' : '#f59e0b' }}; white-space:nowrap;">
                        {{ $item->expires_at->format('d/m/Y') }}
                    </div>
                </div>
            @empty
                <p style="font-size:13px; color:var(--text-muted);">Nada por vencer pronto.</p>
            @endforelse
        </div>

        {{-- Clientes recientes --}}
        <div class="card" style="padding:24px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--text-primary); margin:0;">Clientes recientes</h3>
                <a href="{{ route('clients.index') }}"
                   style="font-size:12px; color:var(--accent); text-decoration:none;">Ver todos →</a>
            </div>
            @forelse($recentClients as $recentClient)
                <div style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid var(--card-border);">
                    <div style="width:34px; height:34px; border-radius:50%; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0;">
                        {{ strtoupper(substr($recentClient->name, 0, 1)) }}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:13.5px; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $recentClient->name }}
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            {{ $recentClient->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <a href="{{ $recentClient->url }}" class="action-btn action-view">Ver</a>
                </div>
            @empty
                <p style="font-size:13px; color:var(--text-muted);">No hay clientes registrados.</p>
            @endforelse
        </div>

    </div>

</x-admin-layout>