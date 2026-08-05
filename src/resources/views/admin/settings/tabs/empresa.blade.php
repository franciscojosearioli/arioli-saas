@php
    $val = fn (string $key) => $settings->get("empresa.$key")?->value;
@endphp

<h2 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0 0 20px;">Datos de la empresa</h2>

<form method="POST" action="{{ route('configuracion.update', 'empresa') }}" enctype="multipart/form-data">
    @csrf

    <div style="margin-bottom:16px;">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-input" value="{{ old('nombre', $val('nombre')) }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Razón social</label>
        <input type="text" name="razon_social" class="form-input" value="{{ old('razon_social', $val('razon_social')) }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">CUIT</label>
        <input type="text" name="cuit" class="form-input" value="{{ old('cuit', $val('cuit')) }}" placeholder="30-12345678-9">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-input" value="{{ old('direccion', $val('direccion')) }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-input" value="{{ old('telefono', $val('telefono')) }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-input" value="{{ old('email', $val('email')) }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Sitio web</label>
        <input type="text" name="sitio_web" class="form-input" value="{{ old('sitio_web', $val('sitio_web')) }}" placeholder="https://arioli.dev">
    </div>

    <div style="margin-bottom:24px;">
        <label class="form-label">Logo</label>
        @if($val('logo_path'))
            <div style="margin-bottom:8px;">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($val('logo_path')) }}" alt="Logo" style="height:40px;">
            </div>
        @endif
        <input type="file" name="logo" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

@include('admin.settings.tabs._test-button', ['group' => 'empresa'])
