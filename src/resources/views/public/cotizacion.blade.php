<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Propuesta — {{ $quote->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; color: #111827; padding: 24px 16px; }
        .wrap { max-width: 680px; margin: 0 auto; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 28px; margin-bottom: 20px; }
        h1 { font-size: 19px; margin-bottom: 4px; }
        .summary { font-size: 13.5px; color: #4b5563; margin-bottom: 10px; }
        h2.section { font-size: 14px; margin: 22px 0 10px; padding-bottom: 6px; border-bottom: 1px solid #eee; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.03em; }
        h3.subsection { font-size: 13.5px; margin: 14px 0 6px; color: #111827; }
        .sub { font-size: 13px; color: #6b7280; margin-bottom: 14px; }
        .text-block { font-size: 14px; line-height: 1.7; color: #1f2937; }
        .text-block p { margin: 0 0 10px; }
        .text-block ul { margin: 6px 0 10px 20px; }

        .bucket { margin-bottom: 14px; }
        .bucket-label { font-size: 11.5px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 6px; }
        .bucket-item { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; border-bottom: 1px solid #f1f5f9; }
        .bucket-total { text-align: right; margin-top: 6px; font-size: 13px; font-weight: 700; }
        .bucket-total .pill { display: inline-block; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 6px; padding: 5px 12px; margin-left: 8px; }

        .payment-box { margin-top: 4px; padding: 14px 16px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; }
        .timeline { font-size: 12.5px; color: #4b5563; margin-bottom: 10px; }
        .final-message { font-size: 13.5px; color: #374151; font-style: italic; margin-top: 6px; }

        .actions { display: flex; gap: 10px; margin-top: 20px; }
        button { flex: 1; padding: 13px; border: none; border-radius: 10px; font-size: 14.5px; font-weight: 700; cursor: pointer; }
        .btn-primary { background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; }
        .btn-danger { background: #fff; color: #dc2626; border: 1.5px solid #fca5a5; }
        .meta { font-size: 12px; color: #9ca3af; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>{{ $quote->title }}</h1>
        @if($quote->summary)
            <p class="summary">{{ $quote->summary }}</p>
        @endif
        <p class="sub">Revisá el detalle completo de la propuesta y decidí si la aceptás o rechazás.</p>

        @if($quote->introduction)
            <div class="text-block"><x-proposal-block :text="$quote->introduction" /></div>
        @endif

        @if($quote->project_scope)
            <h3 class="subsection">Alcance del proyecto</h3>
            <div class="text-block"><x-proposal-block :text="$quote->project_scope" /></div>
        @endif

        @if($quote->benefits)
            <h3 class="subsection">Beneficios</h3>
            <div class="text-block"><x-proposal-block :text="$quote->benefits" /></div>
        @endif

        @if($quote->exclusions)
            <h3 class="subsection">No incluye</h3>
            <div class="text-block"><x-proposal-block :text="$quote->exclusions" /></div>
        @endif

        @if($quote->warranty)
            <h3 class="subsection">Garantía</h3>
            <div class="text-block"><x-proposal-block :text="$quote->warranty" /></div>
        @endif

        <h2 class="section">Resumen económico</h2>
        @foreach($quote->economicSummary() as $bucket)
            <div class="bucket">
                <div class="bucket-label">{{ $bucket['label'] }}</div>
                @foreach($bucket['items'] as $item)
                    <div class="bucket-item">
                        <span>{{ $item->description }}</span>
                        <span>{{ $item->currency->value }} {{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
                <div class="bucket-total">
                    @foreach($bucket['totals'] as $currency => $amount)
                        <span class="pill">{{ $bucket['total_label'] }}: {{ $currency }} {{ number_format($amount, 2) }}</span>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if($quote->timeline_estimate || $quote->payment_terms || $quote->observations)
            <h2 class="section">Condiciones comerciales</h2>
            <div class="payment-box">
                @if($quote->timeline_estimate)
                    <p class="timeline"><strong>Tiempo estimado:</strong> {{ $quote->timeline_estimate }}</p>
                @endif
                @if($quote->payment_terms)
                    <h3 class="subsection" style="margin-top:0;">Forma de pago</h3>
                    <div class="text-block"><x-proposal-block :text="$quote->payment_terms" /></div>
                @endif
                @if($quote->observations)
                    <h3 class="subsection">Observaciones</h3>
                    <div class="text-block"><x-proposal-block :text="$quote->observations" /></div>
                @endif
            </div>
        @endif

        @if($quote->final_message)
            <p class="final-message">{{ $quote->final_message }}</p>
        @endif
    </div>

    <div class="card">
        <a href="{{ route('quotes.public.download', $quote->public_token) }}" style="display:block; text-align:center; padding:12px; border-radius:10px; border:1.5px solid #e5e7eb; color:#374151; text-decoration:none; font-size:13.5px; font-weight:600; margin-bottom:14px;">
            ↓ Descargar en PDF
        </a>
        <form method="POST" action="{{ route('quotes.public.accept', $quote->public_token) }}">
            @csrf
            <button type="submit" class="btn-primary" style="width:100%;">Aceptar propuesta</button>
        </form>
        <form method="POST" action="{{ route('quotes.public.reject', $quote->public_token) }}" onsubmit="return confirm('¿Rechazar esta propuesta? Esta acción no se puede deshacer.')" style="margin-top:10px;">
            @csrf
            <button type="submit" class="btn-danger" style="width:100%;">Rechazar</button>
        </form>
    </div>

    <p class="meta">
        Link válido hasta el {{ $quote->public_token_expires_at->format('d/m/Y') }}.
        @if($quote->valid_until) Propuesta válida hasta el {{ $quote->valid_until->format('d/m/Y') }}. @endif
    </p>
</div>
</body>
</html>
