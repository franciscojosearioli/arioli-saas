@extends('layouts.auth')

@section('title', 'Acceso al Sistema')

@section('content')
<div class="admin-badge">
    <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
    </svg>
    Acceso al Sistema
</div>

<h1>Iniciar Sesión</h1>
<p class="subtitle">Ingresá con tus credenciales de acceso</p>

@php
    // Demo oficial (viejo, un solo slot) — siempre subdominio 'demo'.
    // Subdomain check avoids HTTP call to SaaS Core that may fail before cache is populated.
    $isOfficialDemoLogin = (explode('.', request()->getHost())[0] ?? '') === 'demo';

    // Demos de autoservicio (Etapa 6.4) — DemoInstance vive en la DB
    // maestra, no en la del tenant actual, por eso mysql_tenant_admin
    // (que al inicio de un request normal sigue apuntando a la maestra).
    $tenantId = request()->attributes->get('tenant_id');
    $selfServiceDemo = $tenantId
        ? \App\Models\DemoInstance::on('mysql_tenant_admin')->where('tenant_key', $tenantId)->first()
        : null;

    $isDemoLogin = $isOfficialDemoLogin || $selfServiceDemo !== null;
    $demoCredentials = $selfServiceDemo
        ? [['role' => 'Admin', 'email' => 'admin@admin.com', 'password' => 'password']]
        : config('demo.credentials', []);
@endphp

@if($isDemoLogin && !empty($demoCredentials))
<div style="background:#fef3c7;border:1.5px solid #f59e0b;border-radius:10px;padding:14px 16px;margin-bottom:20px;">
    <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#92400e;margin:0 0 10px;">
        Modo Demo — Credenciales de acceso
    </p>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th style="text-align:left;color:#78350f;padding:2px 6px 4px 0;font-size:10px;font-weight:600;text-transform:uppercase;">Rol</th>
                <th style="text-align:left;color:#78350f;padding:2px 6px 4px 0;font-size:10px;font-weight:600;text-transform:uppercase;">Email</th>
                <th style="text-align:left;color:#78350f;padding:2px 0 4px;font-size:10px;font-weight:600;text-transform:uppercase;">Contraseña</th>
            </tr>
        </thead>
        <tbody>
            @foreach($demoCredentials as $cred)
            <tr onclick="fillDemoCredential('{{ $cred['email'] }}', '{{ $cred['password'] }}')"
                style="cursor:pointer;border-top:1px solid #fde68a;"
                onmouseover="this.style.background='#fef9c3'" onmouseout="this.style.background=''">
                <td style="padding:5px 6px 5px 0;font-size:12px;color:#92400e;font-weight:600;">{{ $cred['role'] }}</td>
                <td style="padding:5px 6px 5px 0;font-size:12px;color:#78350f;font-family:monospace;">{{ $cred['email'] }}</td>
                <td style="padding:5px 0;font-size:12px;color:#78350f;font-family:monospace;">{{ $cred['password'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="font-size:10px;color:#a16207;margin:8px 0 0;">↑ Hacé clic en una fila para completar el formulario</p>
</div>
<script>
function fillDemoCredential(email, password) {
    const emailField = document.querySelector('input[name="email"]');
    const passField  = document.querySelector('input[name="password"]');
    if (emailField) emailField.value = email;
    if (passField)  passField.value  = password;
}
</script>
@endif

@if(session('message'))
    <div class="alert alert-info">
        {{ session('message') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <label class="form-label">{{ trans('global.login_email') }}</label>
        <input type="email" name="email" class="form-input"
               value="{{ old('email') }}"
               placeholder="admin@sistema.com" required autofocus autocomplete="email">
    </div>
    <div class="form-group">
        <label class="form-label">{{ trans('global.login_password') }}</label>
        <div class="password-wrap">
            <input type="password" name="password" class="form-input"
                   placeholder="••••••••" required id="login_password" autocomplete="current-password">
            <button type="button" class="password-toggle" onclick="togglePassword('login_password', this)">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="auth-links">
        <div class="auth-checkbox">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Mantener conectado</label>
        </div>
        @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="auth-link">
                Recuperar contraseña
            </a>
        @endif
    </div>

    {{-- Turnstile --}}
    @if(config('services.turnstile.site_key'))
        <div class="turnstile-wrapper">
            <div class="cf-turnstile"
                 data-sitekey="{{ config('services.turnstile.site_key') }}"
                 data-theme="dark">
            </div>
        </div>
        @if($errors->has('cf-turnstile-response'))
            <div style="color:#f87171; font-size:12px; text-align:center; margin-bottom:16px;">
                {{ $errors->first('cf-turnstile-response') }}
            </div>
        @endif
    @endif

    <button type="submit" class="btn-submit">{{ trans('global.login') }}</button>
</form>
@endsection