@php
    $val = fn (string $key) => $settings->get("firma_digital.$key")?->value;
    $driver = old('driver', $val('driver') ?? 'self_hosted');
@endphp

<h2 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0 0 8px;">Firma Digital</h2>
<p style="font-size:13px; color:var(--text-muted); margin:0 0 20px;">
    El proveedor de firma se puede cambiar en cualquier momento sin modificar el sistema.
    El flujo completo de firma (envío de contrato, evidencia) se habilita junto con el
    módulo de Legales.
</p>

<form method="POST" action="{{ route('configuracion.update', 'firma_digital') }}">
    @csrf

    <div style="margin-bottom:16px;">
        <label class="form-label">Proveedor</label>
        <select name="driver" class="form-select">
            <option value="self_hosted" {{ $driver === 'self_hosted' ? 'selected' : '' }}>Autogestionado (gratuito)</option>
            <option value="manual" {{ $driver === 'manual' ? 'selected' : '' }}>Manual (el admin marca como firmado)</option>
            <option value="docusign" disabled>DocuSign (próximamente)</option>
            <option value="signaturit" disabled>Signaturit (próximamente)</option>
            <option value="dropbox_sign" disabled>Dropbox Sign (próximamente)</option>
            <option value="zoho_sign" disabled>Zoho Sign (próximamente)</option>
        </select>
    </div>

    <div style="margin-bottom:24px;">
        <label class="form-label">API Key (proveedores externos)</label>
        <input type="password" name="api_key" class="form-input" value="{{ old('api_key') }}"
               placeholder="{{ $val('api_key') ? '••••••••••••••••' : 'No aplica para Autogestionado / Manual' }}">
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

@include('admin.settings.tabs._test-button', ['group' => 'firma_digital'])
