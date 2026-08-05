<x-admin-layout title="Detalle de Orden">

    <div style="margin-bottom:24px;">
        <a href="{{ route('orders.index') }}"
           style="font-size:13px; color:var(--text-muted); text-decoration:none;">
            ← Volver al listado
        </a>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; max-width:900px;">

        <div class="card" style="padding:24px;">
            <h3 style="font-size:13px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:16px;">Datos del Cliente</h3>
            <dl style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Nombre</dt>
                    <dd style="color:var(--text-primary); font-weight:600;">{{ $order->customer_name }}</dd>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Email</dt>
                    <dd style="color:var(--text-primary);">{{ $order->customer_email }}</dd>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Empresa</dt>
                    <dd style="color:var(--text-primary);">{{ $order->customer_company }}</dd>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Tenant ID</dt>
                    <dd style="font-family:var(--font-mono); font-size:12px; color:var(--text-primary);">{{ $order->tenant_id ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="card" style="padding:24px;">
            <h3 style="font-size:13px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:16px;">Datos del Pago</h3>
            <dl style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">UUID</dt>
                    <dd style="font-family:var(--font-mono); font-size:11px; color:var(--text-muted);">{{ $order->uuid }}</dd>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Sistema</dt>
                    <dd style="color:var(--text-primary); font-weight:600;">{{ $order->plan->product->name ?? '-' }}</dd>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Plan</dt>
                    <dd style="color:var(--text-primary);">{{ $order->plan->period_label ?? '-' }}</dd>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Monto</dt>
                    <dd style="font-size:18px; font-weight:800; color:var(--text-primary);">${{ number_format($order->amount, 0, ',', '.') }} ARS</dd>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Estado</dt>
                    <dd>
                        @if($order->status === 'approved')
                            <span class="badge badge-green">Aprobado</span>
                        @elseif($order->status === 'pending')
                            <span class="badge" style="background:#fff7ed; color:#f59e0b;">Pendiente</span>
                        @elseif($order->status === 'rejected')
                            <span class="badge badge-red">Rechazado</span>
                        @else
                            <span class="badge" style="background:var(--body-bg); color:var(--text-muted);">{{ $order->status }}</span>
                        @endif
                    </dd>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">MP Payment ID</dt>
                    <dd style="font-family:var(--font-mono); font-size:12px; color:var(--text-muted);">{{ $order->mp_payment_id ?? '-' }}</dd>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Fecha</dt>
                    <dd style="color:var(--text-primary);">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                @if($order->paid_at)
                <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                    <dt style="color:var(--text-muted);">Pagado el</dt>
                    <dd style="color:var(--success);">{{ $order->paid_at->format('d/m/Y H:i') }}</dd>
                </div>
                @endif
            </dl>
        </div>

    </div>

</x-admin-layout>