<x-admin-layout title="Factura">

    <div style="margin-bottom:24px;">
        <a href="{{ route('finanzas.facturacion.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">
            ← Volver a Facturación
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <div class="card" style="max-width:640px; padding:28px;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <div>
                <h2 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0;">
                    {{ $invoice->number ?? 'Borrador #' . $invoice->id }}
                </h2>
                <p style="font-size:13px; color:var(--text-muted); margin:4px 0 0;">{{ $invoice->tenant_id }}</p>
            </div>
            @php
                $badge = match($invoice->status) {
                    'draft'  => ['bg' => '#f3f4f6', 'fg' => '#374151', 'label' => 'Borrador'],
                    'issued' => ['bg' => '#d1fae5', 'fg' => '#065f46', 'label' => 'Emitida'],
                    'voided' => ['bg' => '#fee2e2', 'fg' => '#991b1b', 'label' => 'Anulada'],
                };
            @endphp
            <span style="padding:4px 12px; border-radius:99px; font-size:12.5px; font-weight:600; background:{{ $badge['bg'] }}; color:{{ $badge['fg'] }};">
                {{ $badge['label'] }}
            </span>
        </div>

        <div style="background:#f9fafb; border-radius:8px; padding:16px; margin-bottom:20px; font-size:13px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="color:var(--text-muted);">Cliente</span>
                <span style="font-weight:600;">{{ $invoice->customer_name }}</span>
            </div>
            @if($invoice->customer_cuit)
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="color:var(--text-muted);">CUIT</span>
                <span style="font-weight:600;">{{ $invoice->customer_cuit }}</span>
            </div>
            @endif
            @if($invoice->order)
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="color:var(--text-muted);">Sistema</span>
                <span style="font-weight:600;">{{ $invoice->order->plan?->product?->name }} — {{ $invoice->order->plan?->period_label }}</span>
            </div>
            @endif
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="color:var(--text-muted);">Monto</span>
                <span style="font-weight:700; font-size:16px;">${{ number_format($invoice->amount, 2, ',', '.') }} {{ $invoice->currency }}</span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-muted);">Creada</span>
                <span style="font-weight:600;">{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
            </div>
            @if($invoice->issued_at)
            <div style="display:flex; justify-content:space-between; margin-top:8px;">
                <span style="color:var(--text-muted);">Emitida</span>
                <span style="font-weight:600;">{{ $invoice->issued_at->format('d/m/Y H:i') }}</span>
            </div>
            @endif
            @if($invoice->isAfipIssued())
            <div style="display:flex; justify-content:space-between; margin-top:8px;">
                <span style="color:var(--text-muted);">Comprobante</span>
                <span style="font-weight:600;">{{ $invoice->cbteTipoLabel() }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:8px;">
                <span style="color:var(--text-muted);">CAE</span>
                <span style="font-weight:600;">{{ $invoice->cae }} (vto. {{ \Illuminate\Support\Carbon::parse($invoice->cae_vencimiento)->format('d/m/Y') }})</span>
            </div>
            @endif
            @if($invoice->voided_at)
            <div style="display:flex; justify-content:space-between; margin-top:8px;">
                <span style="color:#dc2626;">Anulada</span>
                <span style="font-weight:600; color:#dc2626;">{{ $invoice->voided_at->format('d/m/Y H:i') }}</span>
            </div>
            @endif
        </div>

        @if($invoice->notes)
        <div style="margin-bottom:20px;">
            <p style="font-size:12px; color:var(--text-muted); font-weight:600; margin:0 0 4px;">Notas internas</p>
            <p style="font-size:13px; color:var(--text-primary); margin:0;">{{ $invoice->notes }}</p>
        </div>
        @endif

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            @if($invoice->isDraft())
                <form method="POST" action="{{ route('finanzas.facturacion.mark-issued', $invoice) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="return confirm('¿Emitir esta factura? Si AFIP está configurado, esto crea un comprobante fiscal REAL e irreversible (CAE) — solo se puede revertir con una Nota de Crédito, no se puede borrar.')">
                        Emitir factura
                    </button>
                </form>
                <form method="POST" action="{{ route('finanzas.facturacion.destroy', $invoice) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('¿Eliminar este borrador?')">
                        Eliminar borrador
                    </button>
                </form>
            @endif

            @if($invoice->isIssued())
                <a href="{{ route('finanzas.facturacion.print', $invoice) }}" target="_blank" class="btn btn-secondary">
                    Ver / Imprimir
                </a>
                <a href="{{ route('finanzas.facturacion.download', $invoice) }}" class="btn btn-primary">
                    Descargar PDF
                </a>
                <form method="POST" action="{{ route('finanzas.facturacion.void', $invoice) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('¿Anular esta factura? Esta acción no se puede deshacer.')">
                        Anular
                    </button>
                </form>
            @endif
        </div>

    </div>

</x-admin-layout>
