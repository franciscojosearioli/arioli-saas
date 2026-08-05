{{-- Propuestas --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Propuestas</h3>
        <a href="{{ route('cotizaciones.create', ['client_id' => $client->id]) }}" style="font-size:12px; color:var(--accent); text-decoration:none;">+ Nueva</a>
    </div>
    @forelse($client->quotes as $quote)
        <a href="{{ route('cotizaciones.show', $quote) }}" style="display:block; text-decoration:none; padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $quote->title }}</div>
                <span style="font-size:11px; color:var(--text-muted);">{{ $quote->status->label() }}</span>
            </div>
            <div style="font-size:11.5px; color:var(--text-muted);">
                @forelse($quote->totalsByCurrency() as $currency => $amount)
                    {{ $currency }} {{ number_format($amount, 2) }}
                @empty
                    —
                @endforelse
            </div>
        </a>
    @empty
        <p style="font-size:12.5px; color:var(--text-muted);">Sin propuestas.</p>
    @endforelse
</div>

{{-- Contratos --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Contratos</h3>
    @forelse($client->contracts as $contract)
        <a href="{{ route('legales.contratos.show', $contract) }}" style="display:block; text-decoration:none; padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $contract->title }}</div>
            <div style="font-size:11.5px; color:var(--text-muted);">{{ $contract->status->label() }}</div>
        </a>
    @empty
        <p style="font-size:12.5px; color:var(--text-muted);">Sin contratos.</p>
    @endforelse
</div>
