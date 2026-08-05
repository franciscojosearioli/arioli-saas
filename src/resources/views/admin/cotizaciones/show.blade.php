@php
    $statusColors = [
        'gray'  => ['bg' => '#f3f4f6', 'fg' => '#374151'],
        'amber' => ['bg' => '#fef3c7', 'fg' => '#92400e'],
        'green' => ['bg' => '#d1fae5', 'fg' => '#065f46'],
        'red'   => ['bg' => '#fee2e2', 'fg' => '#991b1b'],
    ];
    $c = $statusColors[$quote->status->color()] ?? $statusColors['gray'];
    $isBorrador = $quote->status->value === 'borrador';
@endphp
<x-admin-layout title="{{ $quote->title }}">

    <div style="margin-bottom:24px;">
        <a href="{{ route('cotizaciones.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Propuestas</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom:20px;">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0;">{{ $quote->title }}</h1>
            @if($quote->summary)
                <p style="font-size:13.5px; color:var(--text-secondary); margin:4px 0 0; max-width:560px;">{{ $quote->summary }}</p>
            @endif
            <p style="font-size:12.5px; color:var(--text-muted); margin:6px 0 0;">
                {{ $quote->client->name }} · Creada {{ $quote->created_at->format('d/m/Y') }}
                @if($quote->valid_until) · Válida hasta {{ $quote->valid_until->format('d/m/Y') }} @endif
            </p>
        </div>
        <span style="padding:4px 12px; border-radius:99px; font-size:12.5px; font-weight:600; background:{{ $c['bg'] }}; color:{{ $c['fg'] }}; flex-shrink:0;">
            {{ $quote->status->label() }}
        </span>
    </div>

    <div style="display:grid; grid-template-columns:1.4fr 1fr; gap:20px; align-items:start;">

        <div>
            @if($quote->introduction || $quote->project_scope || $quote->benefits || $quote->exclusions || $quote->warranty)
                <div class="card" style="padding:24px; margin-bottom:20px;">
                    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 14px;">Documento</h3>
                    <div style="font-size:13.5px; color:var(--text-primary); line-height:1.7;">

                        @if($quote->introduction)
                            <x-proposal-block :text="$quote->introduction" />
                        @endif

                        @if($quote->project_scope)
                            <h4 style="font-size:13px; font-weight:700; color:var(--text-primary); margin:16px 0 6px;">Alcance del proyecto</h4>
                            <x-proposal-block :text="$quote->project_scope" />
                        @endif

                        @if($quote->benefits)
                            <h4 style="font-size:13px; font-weight:700; color:var(--text-primary); margin:16px 0 6px;">Beneficios</h4>
                            <x-proposal-block :text="$quote->benefits" />
                        @endif

                        @if($quote->exclusions)
                            <h4 style="font-size:13px; font-weight:700; color:var(--text-primary); margin:16px 0 6px;">No incluye</h4>
                            <x-proposal-block :text="$quote->exclusions" />
                        @endif

                        @if($quote->warranty)
                            <h4 style="font-size:13px; font-weight:700; color:var(--text-primary); margin:16px 0 6px;">Garantía</h4>
                            <x-proposal-block :text="$quote->warranty" />
                        @endif

                    </div>
                </div>
            @endif

            <div class="card" style="padding:24px; margin-bottom:20px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Cotización</h3>
                <table class="data-table">
                    <thead><tr><th>Descripción</th><th>Cadencia</th><th>Cant.</th><th>Unidad</th><th>Precio unit.</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        @foreach($quote->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->billing_cycle?->label() ?? '—' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit_label ?: '—' }}</td>
                                <td>{{ $item->currency->value }} {{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $item->currency->value }} {{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h4 style="font-size:13px; font-weight:700; color:var(--text-primary); margin:20px 0 10px;">Resumen económico</h4>
                @foreach($quote->economicSummary() as $bucket)
                    <div style="margin-bottom:12px;">
                        <div style="font-size:12.5px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">{{ $bucket['label'] }}</div>
                        @foreach($bucket['items'] as $item)
                            <div style="font-size:13px; color:var(--text-primary); display:flex; justify-content:space-between; padding:2px 0;">
                                <span>{{ $item->description }}</span>
                                <span>{{ $item->currency->value }} {{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                        <div style="text-align:right; font-size:12.5px; font-weight:700; color:var(--text-primary); margin-top:2px;">
                            @foreach($bucket['totals'] as $currency => $amount)
                                <span style="margin-left:14px;">{{ $bucket['total_label'] }}: {{ $currency }} {{ number_format($amount, 2) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if($quote->creates_project)
                    <p style="font-size:12.5px; color:var(--text-muted); margin-top:12px;">
                        Al aceptarse genera el proyecto "<strong>{{ $quote->project_name }}</strong>".
                    </p>
                @endif
                @if($quote->initial_charge_amount)
                    <p style="font-size:12.5px; color:var(--text-muted);">
                        Cobro inicial al aceptarse: <strong>${{ number_format($quote->initial_charge_amount, 2) }}</strong>
                    </p>
                @endif
            </div>

            @if($quote->payment_terms || $quote->timeline_estimate || $quote->observations)
                <div class="card" style="padding:24px; margin-bottom:20px;">
                    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 14px;">Condiciones comerciales</h3>
                    @if($quote->timeline_estimate)
                        <p style="font-size:13px; color:var(--text-secondary); margin-bottom:12px;">
                            <strong>Tiempo estimado:</strong> {{ $quote->timeline_estimate }}
                        </p>
                    @endif
                    @if($quote->payment_terms)
                        <h4 style="font-size:13px; font-weight:700; color:var(--text-primary); margin:0 0 6px;">Forma de pago</h4>
                        <div style="font-size:13.5px; color:var(--text-primary); line-height:1.7; margin-bottom:12px;">
                            <x-proposal-block :text="$quote->payment_terms" />
                        </div>
                    @endif
                    @if($quote->observations)
                        <h4 style="font-size:13px; font-weight:700; color:var(--text-primary); margin:0 0 6px;">Observaciones</h4>
                        <div style="font-size:13.5px; color:var(--text-primary); line-height:1.7;">
                            <x-proposal-block :text="$quote->observations" />
                        </div>
                    @endif
                </div>
            @endif

            @if($quote->final_message)
                <div class="card" style="padding:24px; margin-bottom:20px;">
                    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 14px;">Mensaje final</h3>
                    <div style="font-size:13.5px; color:var(--text-primary); line-height:1.7;">
                        <x-proposal-block :text="$quote->final_message" />
                    </div>
                </div>
            @endif

            @if($quote->project || $quote->contract)
                <div class="card" style="padding:24px;">
                    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Generado al aceptarse</h3>
                    @if($quote->project)
                        <p style="font-size:13px;"><a href="{{ route('clients.projects.show', [$quote->client, $quote->project]) }}">Proyecto: {{ $quote->project->name }}</a></p>
                    @endif
                    @if($quote->contract)
                        <p style="font-size:13px;"><a href="{{ route('legales.contratos.show', $quote->contract) }}">Contrato: {{ $quote->contract->title }} ({{ $quote->contract->status->label() }})</a></p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <div class="card" style="padding:24px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Acciones</h3>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <a href="{{ route('cotizaciones.print', $quote) }}" target="_blank" class="btn btn-secondary" style="text-align:center;">Ver documento</a>
                    <a href="{{ route('cotizaciones.download', $quote) }}" class="btn btn-primary" style="text-align:center;">Descargar PDF</a>

                    @if($isBorrador)
                        <a href="{{ route('cotizaciones.edit', $quote) }}" class="btn btn-secondary" style="text-align:center;">Editar</a>

                        <form method="POST" action="{{ route('cotizaciones.send', $quote) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="width:100%;">Enviar al cliente</button>
                        </form>
                    @endif

                    @if(in_array($quote->status->value, ['borrador', 'enviada']))
                        <form method="POST" action="{{ route('cotizaciones.mark-accepted', $quote) }}" onsubmit="return confirm('¿Marcar como aceptada? Se generará el proyecto/servicios y el contrato a firmar.')">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="width:100%;">Marcar aceptada manualmente</button>
                        </form>
                        <form method="POST" action="{{ route('cotizaciones.reject', $quote) }}" onsubmit="return confirm('¿Rechazar esta propuesta?')">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="width:100%; color:#dc2626;">Rechazar</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </div>

</x-admin-layout>
