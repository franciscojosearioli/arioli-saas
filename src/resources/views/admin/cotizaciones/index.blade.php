<x-admin-layout title="Propuestas">

    @php
        $statusColors = [
            'gray'  => ['bg' => '#f3f4f6', 'fg' => '#374151'],
            'amber' => ['bg' => '#fef3c7', 'fg' => '#92400e'],
            'green' => ['bg' => '#d1fae5', 'fg' => '#065f46'],
            'red'   => ['bg' => '#fee2e2', 'fg' => '#991b1b'],
        ];
    @endphp

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Propuestas</h1>
        <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary">Nueva propuesta</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <div class="card" style="margin-bottom:16px; padding:14px 20px;">
        <form method="GET" style="display:flex; gap:16px; align-items:center;">
            <select name="status" class="form-select" style="width:220px;">
                <option value="">Todos los estados</option>
                @foreach(\App\Enums\QuoteStatus::cases() as $s)
                    <option value="{{ $s->value }}" {{ $status === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
        </form>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotes as $quote)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('cotizaciones.show', $quote) }}'">
                        <td>{{ $quote->title }}</td>
                        <td>{{ $quote->client->name }}</td>
                        <td>
                            @forelse($quote->totalsByCurrency() as $currency => $amount)
                                <div>{{ $currency }} {{ number_format($amount, 2) }}</div>
                            @empty
                                —
                            @endforelse
                        </td>
                        <td>
                            @php $c = $statusColors[$quote->status->color()] ?? $statusColors['gray']; @endphp
                            <span style="padding:3px 10px; border-radius:99px; font-size:11.5px; font-weight:600; background:{{ $c['bg'] }}; color:{{ $c['fg'] }};">
                                {{ $quote->status->label() }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted); font-size:12.5px;">{{ $quote->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; padding:32px; color:var(--text-muted);">No hay propuestas todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px;">
            {{ $quotes->links() }}
        </div>
    </div>

</x-admin-layout>
