<x-cliente-layout title="Pagos">

    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px;">
        <div>
            <h1 class="page-title">Historial de Pagos</h1>
            <p class="page-subtitle">Todos tus pagos y transacciones</p>
        </div>
        <div style="background:var(--card-bg); border:1px solid var(--card-border); border-radius:12px; padding:16px 24px; text-align:right; box-shadow:var(--card-shadow);">
            <div style="font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em;">Total invertido</div>
            <div style="font-size:22px; font-weight:800; color:var(--accent); margin-top:6px;">
                ${{ number_format($totalGastado, 0, ',', '.') }} ARS
            </div>
        </div>
    </div>

    <div class="card">
        @if($orders->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sistema</th>
                        <th>Plan</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <div style="font-weight:600; color:var(--text-primary); font-size:14px;">
                                    {{ $order->plan->product->name ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <span style="color:var(--text-secondary); font-size:13px;">
                                    {{ $order->plan->period_label ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight:700; color:var(--text-primary); font-size:14px;">
                                    ${{ number_format($order->amount, 0, ',', '.') }}
                                </div>
                                <div style="font-size:11px; color:var(--text-muted);">ARS</div>
                            </td>
                            <td>
                                @if($order->status === 'approved')
                                    <span class="badge badge-green">✓ Aprobado</span>
                                @elseif($order->status === 'pending')
                                    <span class="badge badge-yellow">⏳ Pendiente</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="badge" style="background:var(--body-bg); color:var(--text-muted); border:1px solid var(--card-border);">Cancelado</span>
                                @else
                                    <span class="badge badge-red">✗ Rechazado</span>
                                @endif
                            </td>
                            <td>
                                <div style="color:var(--text-primary); font-size:13px;">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                                    {{ $order->created_at->diffForHumans() }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($orders->hasPages())
                <div style="padding:14px 20px; border-top:1px solid var(--card-border);">
                    {{ $orders->links() }}
                </div>
            @endif

        @else
            <div style="padding:60px; text-align:center;">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="margin:0 auto 16px; color:var(--text-muted);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p style="font-size:15px; font-weight:600; color:var(--text-secondary); margin-bottom:8px;">
                    Sin historial de pagos
                </p>
                <p style="font-size:13px; color:var(--text-muted);">
                    Tus pagos aparecerán aquí una vez que contrates un sistema.
                </p>
            </div>
        @endif
    </div>

</x-cliente-layout>