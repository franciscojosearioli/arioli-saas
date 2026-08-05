<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $invoice->number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #111827; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111827; padding-bottom: 20px; margin-bottom: 24px; }
        .company h1 { font-size: 20px; margin: 0 0 4px; }
        .company p { font-size: 12px; color: #6b7280; margin: 2px 0; }
        .invoice-meta { text-align: right; }
        .invoice-meta h2 { font-size: 18px; margin: 0 0 4px; }
        .invoice-meta p { font-size: 12px; color: #6b7280; margin: 2px 0; }
        .section { margin-bottom: 24px; }
        .section h3 { font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        th { color: #6b7280; font-weight: 600; font-size: 11px; text-transform: uppercase; }
        .total-row td { font-weight: 700; font-size: 15px; border-bottom: none; border-top: 2px solid #111827; }
        .draft-watermark { color: #dc2626; font-weight: 700; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="header">
        <div class="company">
            @php
                $nombreComercial = \App\Models\Setting::get('afip.nombre_comercial');
                $razonSocial = \App\Models\Setting::get('afip.razon_social', \App\Models\Setting::get('empresa.razon_social', \App\Models\Setting::get('empresa.nombre', 'Arioli')));
            @endphp
            <h1>{{ $nombreComercial ?: $razonSocial }}</h1>
            @if($nombreComercial && $nombreComercial !== $razonSocial)
                <p>{{ $razonSocial }}</p>
            @endif
            @if(\App\Models\Setting::get('afip.cuit', \App\Models\Setting::get('empresa.cuit')))
                <p>CUIT: {{ \App\Models\Setting::get('afip.cuit', \App\Models\Setting::get('empresa.cuit')) }}</p>
            @endif
            @if(\App\Models\Setting::get('empresa.direccion'))
                <p>{{ \App\Models\Setting::get('empresa.direccion') }}</p>
            @endif
            @if(\App\Models\Setting::get('empresa.email'))
                <p>{{ \App\Models\Setting::get('empresa.email') }}</p>
            @endif
        </div>
        <div class="invoice-meta">
            @if($invoice->isAfipIssued())
                <h2>{{ $invoice->cbteTipoLabel() }}</h2>
                <p style="font-weight:600; color:#111827;">N° {{ $invoice->number }}</p>
            @else
                <h2>{{ $invoice->number ?? 'BORRADOR' }}</h2>
                @if($invoice->isDraft())
                    <p class="draft-watermark">Borrador — no válido como comprobante fiscal</p>
                @endif
            @endif
            <p>Fecha: {{ ($invoice->issued_at ?? $invoice->created_at)->format('d/m/Y') }}</p>
            @if($invoice->isAfipIssued())
                <p>CAE: {{ $invoice->cae }}</p>
                <p>Vencimiento CAE: {{ \Illuminate\Support\Carbon::parse($invoice->cae_vencimiento)->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

    <div class="section">
        <h3>Cliente</h3>
        <p style="font-size:14px; margin:0;">{{ $invoice->customer_name }}</p>
        @if($invoice->customer_cuit)
            <p style="font-size:13px; color:#6b7280; margin:2px 0 0;">CUIT: {{ $invoice->customer_cuit }}</p>
        @endif
        @if(! $invoice->isAfipIssued() && $invoice->tenant_id)
            <p style="font-size:13px; color:#6b7280; margin:2px 0 0;">Tenant: {{ $invoice->tenant_id }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th style="text-align:right;">Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $invoice->charge?->concept ?? $invoice->order?->plan?->product?->name ?? $invoice->notes ?? 'Servicio' }}
                </td>
                <td style="text-align:right;">${{ number_format($invoice->amount, 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Total</td>
                <td style="text-align:right;">${{ number_format($invoice->amount, 2, ',', '.') }} {{ $invoice->currency }}</td>
            </tr>
        </tbody>
    </table>

    @unless($invoice->isAfipIssued())
        <p style="font-size:11px; color:#9ca3af; margin-top:32px;">
            Comprobante de numeración interna — no es un comprobante fiscal válido ante AFIP.
        </p>
    @endunless

</body>
</html>
