<x-cliente-layout title="Inicio">

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Pagos pendientes --}}
    @if($pendingOrders->count() > 0)
        <div class="alert alert-warning" style="flex-direction:column; align-items:flex-start; gap:12px;">
            <div style="display:flex; align-items:center; gap:8px; font-weight:600;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tenés pagos pendientes
            </div>
            @foreach($pendingOrders as $pending)
                <div style="display:flex; align-items:center; justify-content:space-between; width:100%; padding:10px 0; border-top:1px solid rgba(245,158,11,.2);">
                    <div>
                        <div style="font-size:13.5px; font-weight:600;">{{ $pending->plan->product->name ?? '-' }} — {{ $pending->plan->period_label ?? '-' }}</div>
                        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">${{ number_format($pending->amount, 0, ',', '.') }} ARS · {{ $pending->created_at->diffForHumans() }}</div>
                    </div>
                    <div style="display:flex; gap:8px;">
                        @if($pending->mp_preference_id)
                            <a href="{{ config('mercadopago.mode') === 'production' ? 'https://www.mercadopago.com.ar/checkout/v1/redirect?pref_id=' . $pending->mp_preference_id : 'https://sandbox.mercadopago.com.ar/checkout/v1/redirect?pref_id=' . $pending->mp_preference_id }}"
                               class="btn btn-primary" style="font-size:12px; padding:7px 14px;">
                                Completar pago
                            </a>
                        @endif
                        <form method="POST" action="{{ route('cliente.order.cancel', $pending->id) }}"
                              onsubmit="return confirm('¿Cancelar esta orden?')">
                            @csrf
                            <button type="submit" class="btn btn-danger" style="font-size:12px; padding:7px 14px;">Cancelar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Page header --}}
    <div class="page-header">
        <h1 class="page-title">Bienvenido, {{ $user->name }}</h1>
        <p class="page-subtitle">Resumen de tu cuenta en Arioli.dev</p>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Sistema activo</div>
            <div class="stat-value" style="font-size:18px; margin-top:6px;">
                {{ $license->plan->product->name ?? 'Ninguno' }}
            </div>
            @if($license)
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">{{ $license->plan->period_label }}</div>
            @endif
        </div>
        <div class="stat-card">
            <div class="stat-label">Días restantes</div>
            <div class="stat-value" style="color:{{ !$license || $license->expires_at === null || $license->daysRemaining() > 30 ? 'var(--success)' : 'var(--warning)' }};">
                {{ $license ? ($license->expires_at === null ? '∞' : $license->daysRemaining()) : '-' }}
            </div>
            @if($license)
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">{{ $license->expires_at ? 'Vence ' . $license->expires_at->format('d/m/Y') : 'Sin vencimiento' }}</div>
            @endif
        </div>
        <div class="stat-card">
            <div class="stat-label">Total invertido</div>
            <div class="stat-value" style="font-size:20px; color:var(--accent); margin-top:6px;">
                ${{ number_format($totalPagos, 0, ',', '.') }}
            </div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">ARS</div>
        </div>
    </div>

    {{-- Estadísticas de Soporte --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">

        <div class="stat-card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <div class="stat-label">Mis Tickets</div>
                <div style="width:36px; height:36px; border-radius:10px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $myTickets }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">total creados</div>
        </div>

        <div class="stat-card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <div class="stat-label">Pendientes</div>
                <div style="width:36px; height:36px; border-radius:10px; background:#fff7ed; color:#f59e0b; display:flex; align-items:center; justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value" style="color:#f59e0b;">{{ $openTickets }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">en proceso</div>
        </div>

        <div class="stat-card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <div class="stat-label">Resueltos</div>
                <div style="width:36px; height:36px; border-radius:10px; background:var(--success-bg); color:var(--success); display:flex; align-items:center; justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value" style="color:var(--success);">{{ $resolvedTickets }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">completados</div>
        </div>

        <div class="stat-card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <div class="stat-label">Esta Semana</div>
                <div style="width:36px; height:36px; border-radius:10px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $recentTickets }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">últimos 7 días</div>
        </div>

    </div>

    {{-- Acceso rápido a tickets --}}
    @if($openTickets > 0 || $myTickets == 0)
        <div class="card" style="margin-bottom:20px;">
            <div class="card-body" style="text-align:center; padding:24px;">
                @if($openTickets > 0)
                    <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:16px;">
                        <div style="width:40px; height:40px; border-radius:50%; background:#fff7ed; color:#f59e0b; display:flex; align-items:center; justify-content:center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div style="text-align:left;">
                            <div style="font-size:16px; font-weight:600; color:var(--text-primary);">Tenés {{ $openTickets }} ticket{{ $openTickets != 1 ? 's' : '' }} pendiente{{ $openTickets != 1 ? 's' : '' }}</div>
                            <div style="font-size:13px; color:var(--text-secondary);">Nuestro equipo está trabajando en {{ $openTickets == 1 ? 'tu consulta' : 'tus consultas' }}</div>
                        </div>
                    </div>
                @else
                    <div style="margin-bottom:16px;">
                        <div style="font-size:16px; font-weight:600; color:var(--text-primary); margin-bottom:8px;">¿Necesitás ayuda?</div>
                        <div style="font-size:13px; color:var(--text-secondary);">Creá un ticket de soporte y nuestro equipo te ayudará</div>
                    </div>
                @endif
                <div style="display:flex; gap:12px; justify-content:center;">
                    <a href="{{ route('cliente.tickets.index') }}" class="btn btn-secondary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Ver Mis Tickets
                    </a>
                    <a href="{{ route('cliente.tickets.create') }}" class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ $myTickets == 0 ? 'Crear mi Primer Ticket' : 'Nuevo Ticket' }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Licencia activa --}}
    @if($license)
        <div class="card" style="margin-bottom:20px;">
            <div class="card-body">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                    <div class="card-title" style="margin:0;">Licencia activa</div>
                    @if($license->isValid())
                        <span class="badge badge-green">● Activa</span>
                    @else
                        <span class="badge badge-red">Expirada</span>
                    @endif
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0 32px;">
                    <div class="detail-row">
                        <span class="detail-label">Sistema</span>
                        <span class="detail-value">{{ $license->plan->product->name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Plan</span>
                        <span class="detail-value">{{ $license->plan->period_label }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Inicio</span>
                        <span class="detail-value">{{ $license->starts_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Vencimiento</span>
                        <span class="detail-value">{{ $license->expires_at ? $license->expires_at->format('d/m/Y') : 'Sin vencimiento' }}</span>
                    </div>
                </div>

                <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
                    <a href="{{ route('cliente.licencia.show', $license->id) }}" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Gestionar
                    </a>
                    @if($domain)
                        <a href="https://{{ $domain->domain }}" target="_blank" class="btn btn-secondary">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Acceder
                        </a>
                    @endif
                    @unless($license->expires_at === null)
                        <a href="{{ route('cliente.renovar', $license->id) }}" class="btn btn-secondary">
                            Renovar
                        </a>
                    @endunless
                </div>
            </div>
        </div>
    @else
        <div class="card" style="margin-bottom:20px;">
            <div class="card-body" style="text-align:center; padding:40px;">
                <p style="color:var(--text-secondary); margin-bottom:16px;">No tenés sistemas contratados.</p>
                <a href="https://{{ config('app.landing_domain') }}#productos" class="btn btn-primary">Ver sistemas disponibles</a>
            </div>
        </div>
    @endif

</x-cliente-layout>