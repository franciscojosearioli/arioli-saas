<x-cliente-layout title="Cobro">

    @php
        $chargeBadge = match ($charge->status->value) {
            'paid' => ['badge-green', 'Pagado'],
            'pending' => ['badge-yellow', 'Pendiente'],
            'rejected' => ['badge-red', 'Rechazado'],
            'cancelled' => ['badge-gray', 'Cancelado'],
            default => ['badge-gray', ucfirst($charge->status->value)],
        };
        [$cc, $cl] = $chargeBadge;
        $saldo = $charge->balance();
        $isClosed = in_array($charge->status->value, ['cancelled', 'rejected']);
    @endphp

    <div style="margin-bottom:20px;">
        <a href="{{ route('cliente.billing.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Facturación</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $charge->concept }}</h1>
            <p class="page-subtitle">Cobro del {{ $charge->created_at->format('d/m/Y') }}</p>
        </div>
        <span class="badge {{ $cc }}" style="font-size:13px; padding:6px 14px;">{{ $cl }}</span>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Monto total</div>
            <div class="stat-value">{{ $charge->currency->value }} {{ number_format($charge->amount, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pagado</div>
            <div class="stat-value" style="color:var(--success);">{{ $charge->currency->value }} {{ number_format($charge->amountPaid(), 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Saldo pendiente</div>
            <div class="stat-value" style="color:{{ $saldo > 0.01 && ! $isClosed ? 'var(--warning)' : 'var(--text-primary)' }};">
                {{ $isClosed ? '—' : $charge->currency->value . ' ' . number_format($saldo, 0, ',', '.') }}
            </div>
        </div>
    </div>

    @if(! $isClosed && $saldo > 0.01 && $charge->payment_url)
        <div class="card" style="margin-bottom:20px;">
            <div class="card-body">
                <div class="card-title" style="margin-bottom:16px;">Cómo pagar</div>
                <a href="{{ $charge->payment_url }}" target="_blank" class="btn btn-primary" style="margin-bottom:12px;">
                    Pagar con Mercado Pago{{ $charge->amount_with_fee && $charge->amount_with_fee != $charge->amount ? ' — $' . number_format($charge->amount_with_fee, 0, ',', '.') : '' }}
                </a>
                @if($transferAlias)
                    <p style="font-size:13px; color:var(--text-secondary);">
                        O por transferencia (sin comisión) — <strong>${{ number_format($saldo, 0, ',', '.') }}</strong> al alias <strong>{{ $transferAlias }}</strong>, y nos respondés el mail del cobro con el comprobante.
                    </p>
                @endif
            </div>
        </div>
    @endif

    @if($charge->hasInstallmentPlan())
        <div class="card" style="margin-bottom:20px;">
            <div style="padding:18px 20px 0;">
                <div class="card-title" style="margin-bottom:0;">
                    Plan de pagos — {{ $charge->installmentsPaidCount() }}/{{ $charge->installments_count }} cuotas pagadas
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cuota</th>
                            <th style="text-align:right;">Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($charge->installments as $installment)
                            <tr>
                                <td>Cuota {{ $installment->number }}</td>
                                <td style="text-align:right; font-weight:600;">{{ $charge->currency->value }} {{ number_format($installment->amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $installment->status === \App\Enums\ChargeInstallmentStatus::Paid ? 'badge-green' : 'badge-yellow' }}">
                                        {{ $installment->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="card">
        <div style="padding:18px 20px 0;">
            <div class="card-title" style="margin-bottom:0;">Pagos registrados</div>
        </div>
        @if($charge->payments->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <strong>Todavía no se registró ningún pago para este cobro</strong>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Medio de pago</th>
                            <th style="text-align:right;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($charge->payments as $payment)
                            <tr>
                                <td>{{ $payment->paid_at?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $payment->payment_method?->label() ?? '—' }}</td>
                                <td style="text-align:right; font-weight:600;">{{ $charge->currency->value }} {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($charge->invoice)
        <div style="margin-top:20px;">
            <a href="{{ route('cliente.billing.invoices.show', $charge->invoice) }}" target="_blank" class="action-btn action-view">Ver factura</a>
        </div>
    @endif

</x-cliente-layout>
