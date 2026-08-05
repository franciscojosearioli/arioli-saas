@forelse($orders as $order)
    <tr style="border-bottom:1px solid var(--card-border); transition:background .12s;"
        onmouseover="this.style.background='var(--body-bg)'"
        onmouseout="this.style.background='transparent'">
        <td style="padding:13px 20px;">
            <div style="font-weight:600; color:var(--text-primary); font-size:14px;">{{ $order->customer_name }}</div>
            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ $order->customer_email }}</div>
        </td>
        <td style="padding:13px 20px;">
            <span style="font-size:13px; color:var(--text-secondary);">
                {{ $order->plan->product->name ?? '-' }}
            </span>
            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                {{ $order->plan->period_label ?? '-' }}
            </div>
        </td>
        <td style="padding:13px 20px;">
            <div style="font-weight:700; color:var(--text-primary); font-size:14px;">
                ${{ number_format($order->amount, 0, ',', '.') }}
            </div>
            <div style="font-size:11px; color:var(--text-muted);">ARS</div>
        </td>
        <td style="padding:13px 20px;">
            @if($order->status === 'approved')
                <span class="badge badge-green">Aprobado</span>
            @elseif($order->status === 'pending')
                <span class="badge" style="background:#fff7ed; color:#f59e0b;">Pendiente</span>
            @elseif($order->status === 'rejected')
                <span class="badge badge-red">Rechazado</span>
            @elseif($order->status === 'provision_failed')
                <span class="badge badge-red">Error provision</span>
            @else
                <span class="badge" style="background:var(--body-bg); color:var(--text-muted);">{{ $order->status }}</span>
            @endif
        </td>
        <td style="padding:13px 20px; color:var(--text-muted); font-size:12px;">
            {{ $order->created_at->format('d/m/Y H:i') }}
        </td>
        <td style="padding:13px 20px; font-family:var(--font-mono); font-size:11px; color:var(--text-muted);">
            {{ $order->tenant_id ?? '-' }}
        </td>
        <td style="padding:13px 20px;">
            <a href="{{ route('orders.show', $order->id) }}" class="action-btn action-view">Ver</a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <strong>
                    @if(!empty($search))
                        No se encontraron órdenes para "{{ $search }}"
                    @else
                        No hay órdenes registradas
                    @endif
                </strong>
            </div>
        </td>
    </tr>
@endforelse