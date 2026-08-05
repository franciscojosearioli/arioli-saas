@php($plan = $plan ?? null)

<div style="margin-bottom:16px;">
    <label class="form-label">Nombre</label>
    <input type="text" name="name" class="form-input" value="{{ old('name', $plan?->name) }}" placeholder="Hosting Profesional" required>
</div>

<div style="margin-bottom:16px;">
    <label class="form-label">Descripción de venta (opcional)</label>
    <input type="text" name="marketing_description" class="form-input" value="{{ old('marketing_description', $plan?->marketing_description) }}" placeholder="Ideal para sitios institucionales chicos">
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
    <div>
        <label class="form-label">Precio</label>
        <input type="number" name="price" class="form-input" value="{{ old('price', $plan?->price) }}" min="0" step="0.01" required>
    </div>
    <div>
        <label class="form-label">Moneda</label>
        <select name="currency" class="form-select">
            @foreach(['ARS','USD','EUR'] as $currency)
                <option value="{{ $currency }}" {{ old('currency', $plan?->currency ?? 'ARS') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
            @endforeach
        </select>
    </div>
</div>

<div style="margin-bottom:16px;">
    <label class="form-label">Ciclo de facturación</label>
    <select name="billing_cycle" class="form-select">
        <option value="mensual" {{ old('billing_cycle', $plan?->billing_cycle?->value ?? 'mensual') == 'mensual' ? 'selected' : '' }}>Mensual</option>
        <option value="anual" {{ old('billing_cycle', $plan?->billing_cycle?->value) == 'anual' ? 'selected' : '' }}>Anual</option>
        <option value="unico" {{ old('billing_cycle', $plan?->billing_cycle?->value) == 'unico' ? 'selected' : '' }}>Único</option>
    </select>
</div>

<div style="margin-bottom:16px;">
    <label class="form-label">Package de HestiaCP (opcional)</label>
    <input type="text" name="hestia_package" class="form-input" value="{{ old('hestia_package', $plan?->hestia_package) }}" placeholder="default">
    <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Nombre exacto del package configurado en HestiaCP para asignar los límites de este plan.</p>
</div>

<div style="margin-bottom:24px; display:flex; align-items:center; gap:10px;">
    <input type="checkbox" name="active" id="active" value="1" {{ old('active', $plan?->active ?? true) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:var(--accent);">
    <label for="active" class="form-label" style="margin:0;">Plan activo (visible en /contratar-hosting)</label>
</div>
