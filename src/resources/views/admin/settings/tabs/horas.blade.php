@php
    $val = fn (string $key) => $settings->get("horas.$key")?->value;
@endphp

<h2 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0 0 8px;">Horas / Facturación</h2>
<p style="font-size:13px; color:var(--text-muted); margin:0 0 20px;">
    Tarifa por hora que se precarga (editable) al cargar una entrada de horas en un Cobro o Trabajo puntual.
</p>

<form method="POST" action="{{ route('configuracion.update', 'horas') }}">
    @csrf

    <div style="margin-bottom:24px;">
        <label class="form-label">Tarifa por hora default</label>
        <input type="number" step="0.01" min="0" name="hourly_rate_default" class="form-input" value="{{ old('hourly_rate_default', $val('hourly_rate_default')) }}" placeholder="Ej: 15000">
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
