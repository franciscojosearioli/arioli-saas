<x-cliente-layout title="Facturación">

    @php
        $invoiceBadge = fn ($status) => match ($status) {
            'issued' => ['badge-green', 'Emitida'],
            'voided' => ['badge-red', 'Anulada'],
            default  => ['badge-gray', ucfirst($status)],
        };
        $chargeBadge = fn ($status) => match ($status) {
            'paid' => ['badge-green', 'Pagado'],
            'pending' => ['badge-yellow', 'Pendiente'],
            'rejected' => ['badge-red', 'Rechazado'],
            'cancelled' => ['badge-gray', 'Cancelado'],
            default => ['badge-gray', ucfirst($status)],
        };
        $quoteBadgeClass = fn ($color) => match ($color) {
            'green' => 'badge-green',
            'red' => 'badge-red',
            'amber' => 'badge-yellow',
            default => 'badge-gray',
        };

        $issuedInvoices = $invoices->where('status', 'issued');
        $totalFacturado = $issuedInvoices->sum('amount');
        $pendingCharges = $charges->where('status', \App\Enums\ChargeStatus::Pending);
        $totalPendiente = $pendingCharges->sum('amount');
        $paidCharges = $charges->where('status', \App\Enums\ChargeStatus::Paid);
        $totalPagado = $paidCharges->sum('amount');
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">Facturación</h1>
            <p class="page-subtitle">Tus facturas, presupuestos y cobros con Arioli.dev</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total facturado</div>
            <div class="stat-value">${{ number_format($totalFacturado, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total pagado</div>
            <div class="stat-value" style="color:var(--success);">${{ number_format($totalPagado, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pendiente de cobro</div>
            <div class="stat-value" style="color:{{ $totalPendiente > 0 ? 'var(--warning)' : 'var(--text-primary)' }};">
                ${{ number_format($totalPendiente, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div style="padding:18px 20px 0;">
            <div class="card-title" style="margin-bottom:0;">Facturas</div>
        </div>
        @if($invoices->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                <strong>No tenés facturas emitidas todavía</strong>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>N° de factura</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th style="text-align:right;">Monto</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            @php [$bc, $bl] = $invoiceBadge($invoice->status); @endphp
                            <tr>
                                <td class="mono">{{ $invoice->number ?? 'Pendiente de emisión' }}</td>
                                <td>{{ ($invoice->issued_at ?? $invoice->created_at)->format('d/m/Y') }}</td>
                                <td><span class="badge {{ $bc }}">{{ $bl }}</span></td>
                                <td style="text-align:right; font-weight:600;">${{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                <td><a href="{{ route('cliente.billing.invoices.show', $invoice) }}" target="_blank" class="action-btn action-view">Ver / Descargar</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div style="padding:18px 20px 0;">
            <div class="card-title" style="margin-bottom:0;">Historial de Cobros</div>
        </div>
        @if($charges->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <strong>Todavía no hay cobros registrados</strong>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th style="text-align:right;">Monto</th>
                            <th style="text-align:right;">Saldo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($charges as $charge)
                            @php
                                [$cc, $cl] = $chargeBadge($charge->status->value);
                                $saldo = $charge->balance();
                                $isClosed = in_array($charge->status->value, ['cancelled', 'rejected']);
                            @endphp
                            <tr>
                                <td>
                                    {{ $charge->concept }}
                                    @if($charge->hasInstallmentPlan())
                                        <div style="font-size:10.5px; color:var(--text-muted);">
                                            Cuotas: {{ $charge->installmentsPaidCount() }}/{{ $charge->installments_count }} pagadas ({{ number_format($charge->installment_amount, 2) }} c/u)
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $charge->created_at->format('d/m/Y') }}</td>
                                <td><span class="badge {{ $cc }}">{{ $cl }}</span></td>
                                <td style="text-align:right; font-weight:600;">{{ $charge->currency->value }} {{ number_format($charge->amount, 0, ',', '.') }}</td>
                                <td style="text-align:right;">
                                    @if($isClosed)
                                        <span style="color:var(--text-muted); font-size:11px;">—</span>
                                    @elseif($saldo > 0.01)
                                        <div style="font-weight:700; color:var(--warning); font-size:12.5px;">{{ $charge->currency->value }} {{ number_format($saldo, 0, ',', '.') }}</div>
                                    @else
                                        <div style="color:var(--success); font-size:12px;">Saldado</div>
                                    @endif
                                    @if($charge->amountPaid() > 0)
                                        <div style="font-size:10.5px; color:var(--text-muted);">Pagado: {{ number_format($charge->amountPaid(), 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cliente.billing.charges.show', $charge) }}" class="action-btn action-view">Ver detalle</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title" style="margin-bottom:16px;">Presupuestos</div>
            @forelse($quotes as $quote)
                <div class="detail-row" style="flex-direction:column; align-items:flex-start; gap:6px; padding:14px 0;">
                    <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
                        <span style="font-weight:600; color:var(--text-primary);">{{ $quote->title }}</span>
                        <span class="badge {{ $quoteBadgeClass($quote->status->color()) }}">{{ $quote->status->label() }}</span>
                    </div>
                    <div style="font-size:12.5px; color:var(--text-muted);">
                        @foreach($quote->totalsByCurrency() as $currency => $amount)
                            {{ $currency }} {{ number_format($amount, 0, ',', '.') }}
                        @endforeach
                        @if($quote->valid_until) · Válida hasta {{ $quote->valid_until->format('d/m/Y') }} @endif
                    </div>
                    <a href="{{ route('cliente.billing.quotes.download', $quote) }}" style="font-size:12px; color:var(--accent); text-decoration:none;">Descargar PDF</a>
                </div>
            @empty
                <p style="color:var(--text-secondary); font-size:13px;">No tenés presupuestos todavía.</p>
            @endforelse
        </div>
    </div>

</x-cliente-layout>
