@php
    $empresa = \App\Models\Setting::get('empresa.razon_social', \App\Models\Setting::get('empresa.nombre', 'Arioli.dev'));
    $eslogan = \App\Models\Setting::get('empresa.eslogan', 'Desarrollo de Software y Soluciones Web');
    $firmante = auth()->user()->name ?? $empresa;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 70px 55px 60px 55px; }

        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 11.5px; line-height: 1.6; }

        /* ── Letterhead ── */
        .letterhead { margin-bottom: 22px; padding-bottom: 14px; border-bottom: 3px solid #1d4ed8; }
        .brand { font-size: 11px; font-weight: bold; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; }
        .doc-title { font-size: 20px; font-weight: bold; color: #111827; margin: 0 0 6px; }
        .doc-summary { font-size: 11px; color: #4b5563; margin: 0 0 8px; }
        .doc-meta { font-size: 9.5px; color: #6b7280; }

        /* ── Cuerpo de texto (secciones estructuradas) ── */
        .text-block { font-size: 11.5px; line-height: 1.75; margin-bottom: 4px; }
        .text-block p { margin: 0 0 10px; }
        .text-block ul { margin: 4px 0 12px 20px; padding: 0; }
        .text-block li { margin-bottom: 4px; }

        .section-title { font-size: 12.5px; font-weight: bold; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.04em; margin: 22px 0 10px; padding-bottom: 6px; border-bottom: 2px solid #e5e7eb; }
        .subsection-title { font-size: 11.5px; font-weight: bold; color: #111827; margin: 14px 0 6px; }

        /* ── Resumen económico ── */
        .bucket { margin-bottom: 14px; }
        .bucket-label { font-size: 11px; font-weight: bold; color: #374151; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 6px; }
        .bucket-item { display: table; width: 100%; font-size: 11px; padding: 3px 0; border-bottom: 1px solid #f1f5f9; }
        .bucket-item .desc { display: table-cell; color: #1f2937; }
        .bucket-item .amount { display: table-cell; text-align: right; color: #1f2937; white-space: nowrap; }
        .bucket-total { text-align: right; margin-top: 6px; }
        .bucket-total .pill { display: inline-block; font-size: 12px; font-weight: bold; color: #111827; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 5px; padding: 5px 12px; margin-left: 8px; }

        /* ── Condiciones comerciales ── */
        .payment-box { margin-top: 6px; padding: 14px 16px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; }
        .payment-box .timeline { font-size: 10.5px; color: #374151; margin: 0 0 8px; }
        .payment-box .text-block { margin-bottom: 0; }
        .payment-box .subsection-title { margin-top: 0; }

        /* ── Mensaje final / firma ── */
        .final-message { margin-top: 22px; font-size: 11.5px; color: #374151; font-style: italic; }
        .signature { margin-top: 30px; padding-top: 14px; border-top: 1px solid #e5e7eb; }
        .signature .name { font-size: 12px; font-weight: bold; color: #111827; }
        .signature .role { font-size: 9.5px; color: #6b7280; margin-top: 2px; }
    </style>
</head>
<body>

    <div class="letterhead">
        <div class="brand">{{ $empresa }}</div>
        <div class="doc-title">{{ $quote->title }}</div>
        @if($quote->summary)
            <div class="doc-summary">{{ $quote->summary }}</div>
        @endif
        <div class="doc-meta">
            Para: {{ $quote->client->name }} &nbsp;·&nbsp; Fecha: {{ $quote->created_at->format('d/m/Y') }}
            @if($quote->valid_until) &nbsp;·&nbsp; Válida hasta: {{ $quote->valid_until->format('d/m/Y') }} @endif
        </div>
    </div>

    @if($quote->introduction)
        <div class="text-block"><x-proposal-block :text="$quote->introduction" /></div>
    @endif

    @if($quote->project_scope)
        <div class="subsection-title">Alcance del proyecto</div>
        <div class="text-block"><x-proposal-block :text="$quote->project_scope" /></div>
    @endif

    @if($quote->benefits)
        <div class="subsection-title">Beneficios</div>
        <div class="text-block"><x-proposal-block :text="$quote->benefits" /></div>
    @endif

    @if($quote->exclusions)
        <div class="subsection-title">No incluye</div>
        <div class="text-block"><x-proposal-block :text="$quote->exclusions" /></div>
    @endif

    @if($quote->warranty)
        <div class="subsection-title">Garantía</div>
        <div class="text-block"><x-proposal-block :text="$quote->warranty" /></div>
    @endif

    <div class="section-title">Resumen económico</div>
    @foreach($quote->economicSummary() as $bucket)
        <div class="bucket">
            <div class="bucket-label">{{ $bucket['label'] }}</div>
            @foreach($bucket['items'] as $item)
                <div class="bucket-item">
                    <div class="desc">{{ $item->description }}</div>
                    <div class="amount">{{ $item->currency->value }} {{ number_format($item->subtotal, 2) }}</div>
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
        <div class="section-title">Condiciones comerciales</div>
        <div class="payment-box">
            @if($quote->timeline_estimate)
                <p class="timeline"><strong>Tiempo estimado:</strong> {{ $quote->timeline_estimate }}</p>
            @endif
            @if($quote->payment_terms)
                <div class="subsection-title">Forma de pago</div>
                <div class="text-block"><x-proposal-block :text="$quote->payment_terms" /></div>
            @endif
            @if($quote->observations)
                <div class="subsection-title">Observaciones</div>
                <div class="text-block"><x-proposal-block :text="$quote->observations" /></div>
            @endif
        </div>
    @endif

    @if($quote->final_message)
        <div class="final-message"><x-proposal-block :text="$quote->final_message" /></div>
    @endif

    <div class="signature">
        <div class="name">{{ $firmante }}</div>
        <div class="role">{{ $empresa }} — {{ $eslogan }}</div>
    </div>

</body>
</html>
