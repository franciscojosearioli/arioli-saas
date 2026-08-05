@php
    $val = fn (string $key) => $settings->get("mercadopago.$key")?->value;
@endphp

<h2 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0 0 8px;">Mercado Pago</h2>
<p style="font-size:13px; color:var(--text-muted); margin:0 0 20px;">
    Estos valores quedan guardados y disponibles para módulos nuevos (Facturación, Cobros).
    El checkout actual sigue usando su configuración propia hasta que se decida migrarlo
    explícitamente en una fase posterior.
</p>

<form method="POST" action="{{ route('configuracion.update', 'mercadopago') }}">
    @csrf

    <div style="margin-bottom:16px;">
        <label class="form-label">Modo</label>
        <select name="mode" class="form-select">
            <option value="test" {{ old('mode', $val('mode')) === 'test' ? 'selected' : '' }}>Prueba (sandbox)</option>
            <option value="production" {{ old('mode', $val('mode')) === 'production' ? 'selected' : '' }}>Producción</option>
        </select>
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Public Key (prueba)</label>
        <input type="text" name="public_key_test" class="form-input" value="{{ old('public_key_test', $val('public_key_test')) }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Access Token (prueba)</label>
        <input type="password" name="access_token_test" class="form-input" value="{{ old('access_token_test') }}"
               placeholder="{{ $val('access_token_test') ? '••••••••••••••••' : '' }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Public Key (producción)</label>
        <input type="text" name="public_key_prod" class="form-input" value="{{ old('public_key_prod', $val('public_key_prod')) }}">
    </div>

    <div style="margin-bottom:24px;">
        <label class="form-label">Access Token (producción)</label>
        <input type="password" name="access_token_prod" class="form-input" value="{{ old('access_token_prod') }}"
               placeholder="{{ $val('access_token_prod') ? '••••••••••••••••' : '' }}">
    </div>

    <div style="margin-bottom:24px;">
        <label class="form-label">Clave secreta del webhook</label>
        <input type="password" name="webhook_secret" class="form-input" value="{{ old('webhook_secret') }}"
               placeholder="{{ $val('webhook_secret') ? '••••••••••••••••' : '' }}">
        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">
            La que te muestra Mercado Pago en Tu integración → Webhooks. Verifica que las notificaciones realmente vengan de Mercado Pago.
        </p>
    </div>

    <hr style="border:none; border-top:1px solid var(--card-border); margin:24px 0;">

    <div style="margin-bottom:16px;">
        <label class="form-label">Comisión de Mercado Pago (%)</label>
        <input type="number" step="0.01" min="0" max="100" name="fee_percent" class="form-input" value="{{ old('fee_percent', $val('fee_percent')) }}" placeholder="Ej: 6.29">
        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">
            Se suma automáticamente al monto de cada link de pago, para que después de la comisión te quede el monto original del cobro. Dejalo en 0 (o vacío) para no agregar nada.
        </p>
    </div>

    <div style="margin-bottom:24px;">
        <label class="form-label">Alias para transferencia (sin comisión)</label>
        <input type="text" name="transfer_alias" class="form-input" value="{{ old('transfer_alias', $val('transfer_alias')) }}" placeholder="Ej: arioli.dev.pagos">
        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">
            Se muestra en el mail de cada cobro como alternativa a Mercado Pago — el cliente paga el monto original, sin la comisión, transfiriendo a este alias.
        </p>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

@include('admin.settings.tabs._test-button', ['group' => 'mercadopago'])
