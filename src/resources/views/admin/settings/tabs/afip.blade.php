@php
    $val = fn (string $key) => $settings->get("afip.$key")?->value;
@endphp

<h2 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0 0 8px;">Facturación electrónica (AFIP/ARCA)</h2>
<p style="font-size:13px; color:var(--text-muted); margin:0 0 20px;">
    Estos datos quedan guardados y listos para cuando se habilite la emisión automática de
    comprobantes (fase posterior de este mismo proyecto).
</p>

<form method="POST" action="{{ route('configuracion.update', 'afip') }}" enctype="multipart/form-data">
    @csrf

    <div style="margin-bottom:16px;">
        <label class="form-label">CUIT</label>
        <input type="text" name="cuit" class="form-input" value="{{ old('cuit', $val('cuit')) }}" placeholder="30-12345678-9">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Razón social</label>
        <input type="text" name="razon_social" class="form-input" value="{{ old('razon_social', $val('razon_social')) }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Nombre comercial</label>
        <input type="text" name="nombre_comercial" class="form-input" value="{{ old('nombre_comercial', $val('nombre_comercial')) }}" placeholder="ARIOLI.DEV">
        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">
            El nombre de fantasía que aparece en las facturas — puede ser distinto de la razón social.
        </p>
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Punto de venta</label>
        <input type="text" name="punto_venta" class="form-input" value="{{ old('punto_venta', $val('punto_venta')) }}" placeholder="0001">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Condición frente al IVA</label>
        <input type="text" name="condicion_iva" class="form-input" value="{{ old('condicion_iva', $val('condicion_iva')) }}" placeholder="Responsable Inscripto">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Ambiente</label>
        <select name="ambiente" class="form-select">
            <option value="testing" {{ old('ambiente', $val('ambiente')) === 'testing' ? 'selected' : '' }}>Homologación (testing)</option>
            <option value="production" {{ old('ambiente', $val('ambiente')) === 'production' ? 'selected' : '' }}>Producción</option>
        </select>
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Certificado (.crt)</label>
        @if($val('certificado_path'))
            <p style="font-size:12px; color:#059669; margin:0 0 6px;">Certificado cargado ✓</p>
        @endif
        <input type="file" name="certificado">
    </div>

    <div style="margin-bottom:24px;">
        <label class="form-label">Clave privada (.key)</label>
        @if($val('clave_privada_path'))
            <p style="font-size:12px; color:#059669; margin:0 0 6px;">Clave privada cargada ✓</p>
        @endif
        <input type="file" name="clave_privada">
        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">
            Se guarda en almacenamiento privado, nunca accesible por URL pública.
        </p>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

@include('admin.settings.tabs._test-button', ['group' => 'afip'])
