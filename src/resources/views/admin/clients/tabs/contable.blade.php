{{-- Trabajos --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Trabajos puntuales</h3>
        <x-admin.modal id="add-job" title="Agregar trabajo puntual" trigger-label="+ Agregar trabajo" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
            <form method="POST" action="{{ route('clients.jobs.store', $client) }}">
                @csrf
                <input type="text" name="title" class="form-input" placeholder="Título del trabajo" style="margin-bottom:8px;" required>
                <input type="number" step="0.01" name="amount" class="form-input" placeholder="Monto" style="margin-bottom:12px;" required>
                <input type="hidden" name="status" value="presupuestado">
                <button type="submit" class="btn btn-primary" style="width:100%;">Agregar trabajo</button>
            </form>
        </x-admin.modal>
    </div>
    @foreach($client->jobs as $job)
        @php $jc = $badge($job->status); @endphp
        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div>
                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $job->title }}</div>
                <div style="font-size:11.5px; color:var(--text-muted);">${{ number_format($job->amount, 2) }} @if($job->project) · {{ $job->project->name }} @endif</div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $jc['bg'] }}; color:{{ $jc['fg'] }};">{{ $job->status->label() }}</span>
                @include('admin.partials.time-entries-card', ['trackable' => $job, 'trackableType' => 'job'])
                <x-admin.modal id="edit-job-{{ $job->id }}" title="Editar trabajo" trigger-label="✎" trigger-style="font-size:12px;">
                    <form method="POST" action="{{ route('clients.jobs.update', [$client, $job]) }}">
                        @csrf @method('PATCH')
                        <input type="text" name="title" class="form-input" placeholder="Título del trabajo" style="margin-bottom:8px;" value="{{ $job->title }}" required>
                        <input type="number" step="0.01" name="amount" class="form-input" placeholder="Monto" style="margin-bottom:8px;" value="{{ $job->amount }}" {{ $job->timeEntries->isNotEmpty() ? 'readonly' : '' }} required>
                        @if($job->timeEntries->isNotEmpty())
                            <p style="font-size:11px; color:var(--text-muted); margin:-4px 0 8px;">Calculado desde el registro de horas ({{ $job->timeEntries->count() }} entrada{{ $job->timeEntries->count() === 1 ? '' : 's' }}).</p>
                        @endif
                        <select name="project_id" class="form-select" style="margin-bottom:8px;">
                            <option value="">— Sin proyecto —</option>
                            @foreach($client->projects as $proj)
                                <option value="{{ $proj->id }}" {{ $job->project_id === $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="form-select" style="margin-bottom:12px;">
                            @foreach(\App\Enums\JobStatus::cases() as $s)
                                <option value="{{ $s->value }}" {{ $job->status === $s ? 'selected' : '' }}>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary" style="width:100%;">Guardar cambios</button>
                    </form>
                    <form method="POST" action="{{ route('clients.jobs.destroy', [$client, $job]) }}" onsubmit="return confirm('¿Eliminar este trabajo?')" style="margin-top:10px; padding-top:10px; border-top:1px solid var(--card-border);">
                        @csrf @method('DELETE')
                        <button type="submit" style="border:none; background:none; color:#dc2626; cursor:pointer; font-size:12px;">Eliminar trabajo</button>
                    </form>
                </x-admin.modal>
            </div>
        </div>
    @endforeach
</div>

{{-- Cobros --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Cobros</h3>
        <x-admin.modal id="add-charge" title="Generar cobro" trigger-label="+ Generar cobro" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
            <form method="POST" action="{{ route('clients.charges.store', $client) }}">
                @csrf
                <input type="text" name="concept" class="form-input" placeholder="Concepto" style="margin-bottom:8px;" required>
                <div style="display:grid; grid-template-columns:2fr 1fr; gap:8px; margin-bottom:12px;">
                    <input type="number" step="0.01" name="amount" class="form-input" placeholder="Monto" required>
                    <select name="currency" class="form-select">
                        @foreach(\App\Enums\Currency::cases() as $cur)
                            <option value="{{ $cur->value }}">{{ $cur->value }}</option>
                        @endforeach
                    </select>
                </div>
                <label style="font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:5px; margin-bottom:8px;">
                    <input type="checkbox" name="already_paid" value="1">
                    Ya está pagado (no generar link de Mercado Pago)
                </label>
                <select name="payment_method" class="form-select" style="margin-bottom:12px;">
                    <option value="">Medio de pago (si ya está pagado)</option>
                    @foreach(\App\Enums\ChargePaymentMethod::cases() as $pm)
                        <option value="{{ $pm->value }}">{{ $pm->label() }}</option>
                    @endforeach
                </select>
                <div style="border-top:1px solid var(--card-border); padding-top:10px; margin-bottom:12px;">
                    <p style="font-size:11px; color:var(--text-muted); margin:0 0 6px;">Plan de pago sugerido (opcional — informativo, los pagos reales pueden ser de cualquier monto)</p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                        <input type="number" min="2" step="1" name="installments_count" class="form-input" placeholder="Cant. cuotas">
                        <input type="number" step="0.01" name="installment_amount" class="form-input" placeholder="Monto por cuota">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Generar cobro</button>
            </form>
        </x-admin.modal>
    </div>
    <table class="data-table">
        <thead><tr><th>Concepto</th><th>Monto</th><th>Saldo</th><th>Estado</th><th>Vencimiento</th><th>Link de pago</th><th></th></tr></thead>
        <tbody>
            @forelse($client->charges as $charge)
                @php $cc = $badge($charge->status); @endphp
                <tr>
                    <td>
                        {{ $charge->concept }}
                        <div style="margin-top:4px;">
                            @include('admin.partials.time-entries-card', ['trackable' => $charge, 'trackableType' => 'charge'])
                        </div>
                        @if($charge->bundled_into_charge_id)
                            <div style="font-size:10.5px; color:var(--text-muted); margin-top:2px;">→ Incluido en orden de pago #{{ $charge->bundled_into_charge_id }}</div>
                        @endif
                        @if($charge->status->value === 'paid')
                            <div style="margin-top:6px;">
                                @if($charge->invoice)
                                    @php
                                        $invoiceBadge = match($charge->invoice->status) {
                                            'draft' => ['#f3f4f6', '#374151', 'Borrador'],
                                            'issued' => ['#d1fae5', '#065f46', 'Emitida'],
                                            'voided' => ['#fee2e2', '#991b1b', 'Anulada'],
                                            default => ['#f3f4f6', '#374151', $charge->invoice->status],
                                        };
                                    @endphp
                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                        <span style="padding:2px 8px; border-radius:99px; font-size:10.5px; font-weight:600; background:{{ $invoiceBadge[0] }}; color:{{ $invoiceBadge[1] }};">Factura: {{ $invoiceBadge[2] }}{{ $charge->invoice->number ? ' '.$charge->invoice->number : '' }}</span>
                                        <a href="{{ route('finanzas.facturacion.download', $charge->invoice) }}" style="font-size:10.5px;">Descargar</a>
                                        @if($charge->invoice->isDraft())
                                            <form method="POST" action="{{ route('finanzas.facturacion.mark-issued', $charge->invoice) }}">
                                                @csrf
                                                <button type="submit" style="border:none; background:none; color:var(--accent); cursor:pointer; font-size:10.5px; text-decoration:underline; padding:0;">Emitir</button>
                                            </form>
                                        @endif
                                        <x-admin.modal id="send-invoice-{{ $charge->invoice->id }}" title="Enviar factura por correo" trigger-label="Enviar" trigger-style="font-size:10.5px;">
                                            <form method="POST" action="{{ route('finanzas.facturacion.send-email', $charge->invoice) }}">
                                                @csrf
                                                <select name="contact_id" class="form-select" style="margin-bottom:12px;" required>
                                                    <option value="">— Elegir contacto —</option>
                                                    @foreach($client->contacts as $contact)
                                                        <option value="{{ $contact->id }}" {{ ! $contact->email ? 'disabled' : '' }}>{{ $contact->name }}{{ $contact->email ? " ({$contact->email})" : ' (sin email)' }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-primary" style="width:100%;">Enviar</button>
                                            </form>
                                        </x-admin.modal>
                                    </div>
                                @else
                                    <x-admin.modal id="invoice-charge-{{ $charge->id }}" title="Generar factura" trigger-label="🧾 Generar factura" trigger-style="font-size:11px;">
                                        <form method="POST" action="{{ route('clients.charges.invoice.store', [$client, $charge]) }}">
                                            @csrf
                                            <input type="text" name="customer_cuit" class="form-input" placeholder="CUIT del cliente (opcional)" value="{{ $client->cuit }}" style="margin-bottom:12px;">
                                            <button type="submit" class="btn btn-primary" style="width:100%;">Generar factura</button>
                                        </form>
                                    </x-admin.modal>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>
                        {{ $charge->currency->value }} {{ number_format($charge->amount, 2) }}
                        @if($charge->hasInstallmentPlan())
                            <div style="font-size:10.5px; color:var(--text-muted);">
                                Cuotas: {{ $charge->installmentsPaidCount() }}/{{ $charge->installments_count }} pagadas ({{ number_format($charge->installment_amount, 2) }} c/u)
                            </div>
                        @endif
                    </td>
                    <td>
                        @php $saldo = $charge->balance(); @endphp
                        @if($charge->status->value !== 'cancelled' && $charge->status->value !== 'rejected')
                            @if($saldo > 0.01)
                                <div style="font-weight:700; color:#92400e; font-size:12.5px;">{{ $charge->currency->value }} {{ number_format($saldo, 2) }}</div>
                            @else
                                <div style="color:var(--success); font-size:12px;">Saldado</div>
                            @endif
                            @if($charge->amountPaid() > 0)
                                <div style="font-size:10.5px; color:var(--text-muted);">Cobrado: {{ number_format($charge->amountPaid(), 2) }}</div>
                            @endif
                            <div style="display:flex; gap:8px; align-items:center; margin-top:4px; flex-wrap:wrap;">
                                @if($saldo > 0.01)
                                    <x-admin.modal id="pay-charge-{{ $charge->id }}" title="Registrar pago — {{ $charge->concept }}" trigger-label="+ Registrar pago" trigger-style="font-size:10.5px; padding:2px 6px;">
                                        <form method="POST" action="{{ route('clients.charges.payments.store', [$client, $charge]) }}">
                                            @csrf
                                            <p style="font-size:12px; color:var(--text-muted); margin:0 0 10px;">Saldo pendiente: {{ $charge->currency->value }} {{ number_format($saldo, 2) }}</p>
                                            @php $pendingInstallments = $charge->installments->where('status', \App\Enums\ChargeInstallmentStatus::Pending); @endphp
                                            @if($pendingInstallments->isNotEmpty())
                                                <div style="border:1px solid var(--card-border); border-radius:8px; padding:8px; margin-bottom:10px; max-height:140px; overflow-y:auto;">
                                                    <p style="font-size:10.5px; color:var(--text-muted); margin:0 0 6px;">Cuotas pendientes (opcional — marcalas si este pago corresponde a ellas)</p>
                                                    @foreach($pendingInstallments as $installment)
                                                        <label style="display:flex; align-items:center; gap:6px; font-size:12px; padding:2px 0;">
                                                            <input type="checkbox" name="installment_ids[]" value="{{ $installment->id }}" class="installment-check-{{ $charge->id }}" data-amount="{{ $installment->amount }}" onchange="sumInstallments({{ $charge->id }})">
                                                            Cuota {{ $installment->number }} — {{ number_format($installment->amount, 2) }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <input type="number" step="0.01" max="{{ $saldo }}" name="amount" id="pay-amount-{{ $charge->id }}" class="form-input" placeholder="Monto cobrado" value="{{ $saldo }}" style="margin-bottom:8px;" required>
                                            <select name="payment_method" class="form-select" style="margin-bottom:8px;">
                                                <option value="">Medio de pago</option>
                                                @foreach(\App\Enums\ChargePaymentMethod::cases() as $pm)
                                                    <option value="{{ $pm->value }}">{{ $pm->label() }}</option>
                                                @endforeach
                                            </select>
                                            <input type="date" name="paid_at" class="form-input" style="margin-bottom:8px;" value="{{ now()->toDateString() }}">
                                            <input type="text" name="notes" class="form-input" placeholder="Notas (opcional)" style="margin-bottom:12px;">
                                            <button type="submit" class="btn btn-primary" style="width:100%;">Registrar pago</button>
                                        </form>
                                    </x-admin.modal>
                                @endif
                                @if($charge->payments->isNotEmpty())
                                    <x-admin.modal id="payments-history-{{ $charge->id }}" title="Pagos registrados — {{ $charge->concept }}" trigger-label="Ver pagos ({{ $charge->payments->count() }})" trigger-style="font-size:10.5px; padding:2px 6px;">
                                        <div style="display:flex; flex-direction:column; gap:8px; max-height:320px; overflow-y:auto;">
                                            @foreach($charge->payments as $payment)
                                                <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; padding-bottom:8px; border-bottom:1px solid #f3f4f6;">
                                                    <div>
                                                        <div style="font-size:12.5px; font-weight:600; color:var(--text-primary);">{{ $charge->currency->value }} {{ number_format($payment->amount, 2) }}</div>
                                                        <div style="font-size:10.5px; color:var(--text-muted);">
                                                            {{ $payment->paid_at?->format('d/m/Y') }}
                                                            @if($payment->payment_method) — {{ $payment->payment_method->label() }} @endif
                                                            @if($payment->createdBy) — {{ $payment->createdBy->name }} @endif
                                                        </div>
                                                        @if($payment->notes)
                                                            <div style="font-size:10.5px; color:var(--text-muted); font-style:italic;">{{ $payment->notes }}</div>
                                                        @endif
                                                    </div>
                                                    <form method="POST" action="{{ route('clients.charges.payments.destroy', [$client, $charge, $payment]) }}" onsubmit="return confirm('¿Eliminar este pago? El saldo del cobro se recalcula.')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-secondary" style="font-size:10px; padding:2px 6px; color:#dc2626;">×</button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    </x-admin.modal>
                                @endif
                            </div>
                        @else
                            <span style="color:var(--text-muted); font-size:11px;">—</span>
                        @endif
                    </td>
                    <td><span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $cc['bg'] }}; color:{{ $cc['fg'] }};">{{ $charge->status->label() }}</span></td>
                    <td style="color:var(--text-muted); font-size:12px;">{{ $charge->due_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>
                        @if($charge->payment_url)
                            <div style="display:flex; gap:6px; align-items:center;">
                                <a href="{{ $charge->payment_url }}" target="_blank" style="font-size:11px;">Abrir link</a>
                                @if($charge->status->value === 'pending')
                                    <form method="POST" action="{{ route('clients.charges.regenerate-link', [$client, $charge]) }}" title="Regenerar con el monto actual">
                                        @csrf
                                        <button class="btn btn-secondary" style="font-size:10px; padding:2px 6px;">↻</button>
                                    </form>
                                @endif
                            </div>
                        @elseif($charge->status->value === 'pending')
                            <form method="POST" action="{{ route('clients.charges.regenerate-link', [$client, $charge]) }}">
                                @csrf
                                <button class="btn btn-secondary" style="font-size:11px; padding:4px 8px;">Generar link</button>
                            </form>
                        @else
                            <span style="color:var(--text-muted); font-size:11px;">{{ $charge->payment_method?->label() ?? '—' }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:6px; align-items:center;">
                            @if($charge->status->value === 'pending')
                                <form method="POST" action="{{ route('clients.charges.mark-paid', [$client, $charge]) }}" style="display:flex; gap:4px;">
                                    @csrf
                                    <select name="payment_method" style="font-size:11px; border:1px solid var(--card-border); border-radius:6px; padding:2px 4px; background:var(--card-bg); color:var(--text-primary);">
                                        <option value="">Medio...</option>
                                        @foreach(\App\Enums\ChargePaymentMethod::cases() as $pm)
                                            <option value="{{ $pm->value }}">{{ $pm->label() }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-secondary" style="font-size:11px; padding:4px 8px;">Marcar pagado</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('clients.charges.destroy', [$client, $charge]) }}" onsubmit="return confirm('¿Eliminar este cobro?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-secondary" style="font-size:11px; padding:4px 8px; color:#dc2626;">×</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; padding:20px; color:var(--text-muted);">Sin cobros todavía.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Historial de cuenta: cargos y pagos mezclados en orden cronológico --}}
@php
    $ledgerEntries = collect();
    foreach ($client->charges->whereNull('bundled_into_charge_id') as $c) {
        $ledgerEntries->push((object) [
            'type' => 'cargo', 'date' => $c->created_at, 'charge' => $c, 'payment' => null, 'amount' => $c->amount,
        ]);
        foreach ($c->payments as $p) {
            $ledgerEntries->push((object) [
                'type' => 'pago', 'date' => $p->paid_at, 'charge' => $c, 'payment' => $p, 'amount' => $p->amount,
            ]);
        }
    }
    $ledgerEntries = $ledgerEntries->sortByDesc('date')->values();
@endphp
<div class="card" style="padding:24px; margin-bottom:20px;">
    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 6px;">Historial de cuenta</h3>
    <p style="font-size:12px; color:var(--text-muted); margin:0 0 16px;">Todos los cargos y pagos del cliente, en orden.</p>
    @forelse($ledgerEntries as $entry)
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding:8px 0; border-bottom:1px solid #f3f4f6; font-size:12.5px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="padding:2px 8px; border-radius:99px; font-size:10.5px; font-weight:600; background:{{ $entry->type === 'cargo' ? '#fef3c7' : '#d1fae5' }}; color:{{ $entry->type === 'cargo' ? '#92400e' : '#065f46' }};">{{ $entry->type === 'cargo' ? 'Cargo' : 'Pago' }}</span>
                <span style="color:var(--text-primary);">{{ $entry->charge->concept }}</span>
                @if($entry->payment?->payment_method)
                    <span style="color:var(--text-muted); font-size:11px;">({{ $entry->payment->payment_method->label() }})</span>
                @endif
            </div>
            <div style="display:flex; align-items:center; gap:14px;">
                <span style="color:var(--text-muted); font-size:11px; white-space:nowrap;">{{ $entry->date?->format('d/m/Y') }}</span>
                <span style="font-weight:700; white-space:nowrap; color:{{ $entry->type === 'cargo' ? '#92400e' : '#065f46' }};">{{ $entry->type === 'cargo' ? '+' : '−' }}{{ $entry->charge->currency->value }} {{ number_format($entry->amount, 2) }}</span>
            </div>
        </div>
    @empty
        <p style="font-size:12.5px; color:var(--text-muted);">Sin movimientos todavía.</p>
    @endforelse
</div>

{{-- Orden de pago --}}
@php
    $pendingChargesForOrder = $client->charges->filter(fn ($c) => $c->status->value === 'pending' && ! $c->bundled_into_charge_id);
    $pendingJobsForOrder = $client->jobs->filter(fn ($j) => $j->charges->isEmpty());
@endphp
<div class="card" style="padding:24px; margin-bottom:20px;">
    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 6px;">Orden de pago</h3>
    <p style="font-size:12px; color:var(--text-muted); margin:0 0 16px;">
        Combiná varios cobros pendientes y/o trabajos sin cobrar todavía en un solo link de pago (misma comisión de Mercado Pago + alternativa de transferencia que un cobro normal).
    </p>

    @if($pendingChargesForOrder->isEmpty() && $pendingJobsForOrder->isEmpty())
        <p style="font-size:12.5px; color:var(--text-muted);">No hay cobros ni trabajos pendientes para agrupar.</p>
    @else
        <form method="POST" action="{{ route('clients.payment-order.store', $client) }}">
            @csrf
            <div style="max-height:240px; overflow-y:auto; margin-bottom:12px;">
                @foreach($pendingChargesForOrder as $charge)
                    <label style="display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid #f3f4f6; font-size:12.5px; cursor:pointer;">
                        <span><input type="checkbox" name="charge_ids[]" value="{{ $charge->id }}" class="order-item-checkbox" data-amount="{{ $charge->amount }}"> {{ $charge->concept }} <span style="color:var(--text-muted);">({{ $charge->currency->value }})</span></span>
                        <strong>${{ number_format($charge->amount, 2) }}</strong>
                    </label>
                @endforeach
                @foreach($pendingJobsForOrder as $job)
                    <label style="display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid #f3f4f6; font-size:12.5px; cursor:pointer;">
                        <span><input type="checkbox" name="job_ids[]" value="{{ $job->id }}" class="order-item-checkbox" data-amount="{{ $job->amount }}"> {{ $job->title }} <span style="color:var(--text-muted);">(trabajo puntual)</span></span>
                        <strong>${{ number_format($job->amount, 2) }}</strong>
                    </label>
                @endforeach
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:13px; color:var(--text-primary);">Total seleccionado: <strong id="payment-order-total">$0,00</strong></span>
                <button type="submit" class="btn btn-primary" style="font-size:12px; padding:6px 14px;">Generar orden de pago</button>
            </div>
        </form>
        <script>
            document.querySelectorAll('.order-item-checkbox').forEach(cb => {
                cb.addEventListener('change', () => {
                    const total = Array.from(document.querySelectorAll('.order-item-checkbox:checked'))
                        .reduce((sum, el) => sum + parseFloat(el.dataset.amount || '0'), 0);
                    document.getElementById('payment-order-total').textContent = '$' + total.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                });
            });
        </script>
    @endif
</div>

<script>
    function sumInstallments(chargeId) {
        const checks = document.querySelectorAll('.installment-check-' + chargeId + ':checked');
        if (checks.length === 0) return;

        let total = 0;
        checks.forEach(c => total += parseFloat(c.dataset.amount));

        document.getElementById('pay-amount-' + chargeId).value = total.toFixed(2);
    }
</script>
