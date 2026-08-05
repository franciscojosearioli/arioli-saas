@php
    $val = fn (string $key) => $settings->get("correos.$key")?->value;
@endphp

<h2 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0 0 8px;">Correos</h2>
<p style="font-size:13px; color:var(--text-muted); margin:0 0 20px;">
    Estos valores quedan guardados para una fase posterior. El envío de correos del sistema
    (bienvenida, tickets, vencimientos) sigue usando la configuración actual del servidor.
</p>

<form method="POST" action="{{ route('configuracion.update', 'correos') }}">
    @csrf

    <div style="margin-bottom:16px;">
        <label class="form-label">Mailer</label>
        <input type="text" name="mail_mailer" class="form-input" value="{{ old('mail_mailer', $val('mail_mailer')) }}" placeholder="smtp">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Host SMTP</label>
        <input type="text" name="mail_host" class="form-input" value="{{ old('mail_host', $val('mail_host')) }}" placeholder="mail.arioli.dev">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Puerto</label>
        <input type="number" name="mail_port" class="form-input" value="{{ old('mail_port', $val('mail_port')) }}" placeholder="587">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Usuario</label>
        <input type="text" name="mail_username" class="form-input" value="{{ old('mail_username', $val('mail_username')) }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Contraseña</label>
        <input type="password" name="mail_password" class="form-input" value="{{ old('mail_password') }}"
               placeholder="{{ $val('mail_password') ? '••••••••••••••••' : '' }}">
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Remitente (email)</label>
        <input type="email" name="mail_from_address" class="form-input" value="{{ old('mail_from_address', $val('mail_from_address')) }}">
    </div>

    <div style="margin-bottom:24px;">
        <label class="form-label">Remitente (nombre)</label>
        <input type="text" name="mail_from_name" class="form-input" value="{{ old('mail_from_name', $val('mail_from_name')) }}">
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

@include('admin.settings.tabs._test-button', ['group' => 'correos'])
