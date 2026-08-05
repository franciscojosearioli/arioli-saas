{{-- Requiere: $trackable (con timeEntries cargado), $trackableType ('charge'|'job') --}}
@php
    $totalHoras = $trackable->timeEntries->sum('hours');
    $totalMonto = $trackable->timeEntries->sum('subtotal');
@endphp
<x-admin.modal id="time-entries-{{ $trackableType }}-{{ $trackable->id }}"
    title="Registro de horas"
    trigger-label="🕒 Horas{{ $trackable->timeEntries->isNotEmpty() ? ' ('.rtrim(rtrim(number_format($totalHoras, 2), '0'), '.').'h)' : '' }}"
    trigger-style="font-size:11px;">

    @if($trackable->timeEntries->isNotEmpty())
        <div style="margin-bottom:12px; max-height:220px; overflow-y:auto;">
            @foreach($trackable->timeEntries as $entry)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid var(--card-border); font-size:12px;">
                    <div>
                        <div style="color:var(--text-primary);">{{ $entry->worked_on->format('d/m/Y') }} — {{ $entry->hours }}h × ${{ number_format($entry->rate_per_hour, 2) }}</div>
                        @if($entry->description)
                            <div style="color:var(--text-muted); font-size:11px;">{{ $entry->description }}</div>
                        @endif
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <strong>${{ number_format($entry->subtotal, 2) }}</strong>
                        <form method="POST" action="{{ route('time-entries.destroy', $entry) }}" onsubmit="return confirm('¿Eliminar esta entrada de horas?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="border:none; background:none; color:#dc2626; cursor:pointer; font-size:12px;">×</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="font-size:12.5px; font-weight:700; color:var(--text-primary); margin-bottom:12px; text-align:right;">
            Total: {{ number_format($totalHoras, 2) }}h — ${{ number_format($totalMonto, 2) }}
        </div>
        @if($trackableType === 'charge' && $trackable->payment_url)
            <div style="font-size:11.5px; color:#92400e; background:#fef3c7; border-radius:8px; padding:8px 10px; margin-bottom:12px;">
                ⚠ Este cobro ya tiene un link de pago generado — no se actualiza solo con las horas nuevas. Si cambiaste el total, regenerá el link desde la columna "Link de pago" para que coincida.
            </div>
        @endif
    @else
        <p style="font-size:12.5px; color:var(--text-muted); margin-bottom:12px;">Sin entradas todavía.</p>
    @endif

    <form method="POST" action="{{ route('time-entries.store') }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
        @csrf
        <input type="hidden" name="trackable_type" value="{{ $trackableType }}">
        <input type="hidden" name="trackable_id" value="{{ $trackable->id }}">
        <input type="date" name="worked_on" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
        <input type="number" step="0.25" min="0.01" name="hours" class="form-input" placeholder="Horas" required>
        <input type="number" step="0.01" min="0" name="rate_per_hour" class="form-input" placeholder="Tarifa/hora" value="{{ \App\Models\Setting::get('horas.hourly_rate_default', '') }}" required>
        <input type="text" name="description" class="form-input" placeholder="Descripción (opcional)">
        <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Agregar entrada</button>
    </form>
</x-admin.modal>
