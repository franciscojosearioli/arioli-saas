<x-admin-layout title="Facturación">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-2-1-2 1-2-1-2 1-2-1-2 1V5a2 2 0 012-2h8a2 2 0 012 2v16z"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Facturación</h1>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">
                    Numeración interna provisoria — la emisión real ante AFIP se habilita en una fase posterior
                </p>
            </div>
        </div>
        <a href="{{ route('finanzas.facturacion.create') }}" class="btn btn-primary">Nueva factura</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Borradores</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--text-primary); margin-top:8px; line-height:1;">{{ $stats['draft'] }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Emitidas</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--success); margin-top:8px; line-height:1;">{{ $stats['issued'] }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Anuladas</p>
            <h3 style="font-size:32px; font-weight:700; color:#dc2626; margin-top:8px; line-height:1;">{{ $stats['voided'] }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Facturado</p>
            <h3 style="font-size:24px; font-weight:700; color:var(--accent); margin-top:8px; line-height:1;">
                ${{ number_format($stats['total'], 0, ',', '.') }}
            </h3>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card" style="margin-bottom:16px; padding:14px 20px;">
        <form method="GET" style="display:flex; gap:16px; align-items:center;">
            <input type="text" name="search" class="search-input" style="flex:1;"
                   value="{{ $search }}" placeholder="Buscar por cliente, tenant o número...">
            <select name="status" class="form-select" style="width:180px;">
                <option value="">Todos los estados</option>
                <option value="draft"  {{ $status === 'draft'  ? 'selected' : '' }}>Borrador</option>
                <option value="issued" {{ $status === 'issued' ? 'selected' : '' }}>Emitida</option>
                <option value="voided" {{ $status === 'voided' ? 'selected' : '' }}>Anulada</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Sistema</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('finanzas.facturacion.show', $invoice) }}'">
                        <td style="font-family:monospace; font-size:12px;">{{ $invoice->number ?? '— (borrador)' }}</td>
                        <td>{{ $invoice->customer_name }}</td>
                        <td>{{ $invoice->order?->plan?->product?->name ?? '—' }}</td>
                        <td>${{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $badge = match($invoice->status) {
                                    'draft'  => ['bg' => '#f3f4f6', 'fg' => '#374151', 'label' => 'Borrador'],
                                    'issued' => ['bg' => '#d1fae5', 'fg' => '#065f46', 'label' => 'Emitida'],
                                    'voided' => ['bg' => '#fee2e2', 'fg' => '#991b1b', 'label' => 'Anulada'],
                                    default  => ['bg' => '#f3f4f6', 'fg' => '#374151', 'label' => $invoice->status],
                                };
                            @endphp
                            <span style="padding:3px 10px; border-radius:99px; font-size:11.5px; font-weight:600; background:{{ $badge['bg'] }}; color:{{ $badge['fg'] }};">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted); font-size:12.5px;">{{ $invoice->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">No hay facturas todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px;">
            {{ $invoices->links() }}
        </div>
    </div>

</x-admin-layout>
