<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Acceso al Sistema') — {{ $sistemaConfig?->nombre_sistema ?? 'Sistema de Salud' }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #080d1a; color: #f1f5f9;
            min-height: 100vh; display: flex;
            align-items: center; justify-content: center; padding: 40px 20px;
        }
        .auth-card {
            background: #111827; border: 1px solid #1e2d45;
            border-radius: 20px; padding: 40px; width: 100%; max-width: 420px;
        }
        .logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; margin-bottom: 32px; justify-content: center;
            flex-direction: column;
        }
        .logo-img {
            max-height: 80px; max-width: 200px; height: auto;
            object-fit: contain; margin-bottom: 12px;
        }
        .logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center;
        }
        .logo-text { font-size: 18px; font-weight: 700; color: #f1f5f9; }
        .logo-text span { color: #3b82f6; }
        .sistema-name { font-size: 20px; font-weight: 600; color: #f1f5f9; margin-bottom: 4px; }
        .institucion-name { font-size: 14px; color: #94a3b8; }
        h1 { font-size: 22px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .subtitle { font-size: 14px; color: #94a3b8; text-align: center; margin-bottom: 28px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 7px; }
        .form-input {
            width: 100%; padding: 11px 14px;
            background: rgba(255,255,255,.05);
            border: 1.5px solid #1e2d45; border-radius: 10px;
            font-size: 14px; font-family: 'DM Sans', sans-serif;
            color: #f1f5f9; outline: none; transition: all .2s;
        }
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }
        .form-input::placeholder { color: #475569; }
        .form-error { font-size: 12px; color: #f87171; margin-top: 5px; }
        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; font-size: 15px; font-weight: 700;
            font-family: 'DM Sans', sans-serif; border: none;
            border-radius: 12px; cursor: pointer;
            box-shadow: 0 6px 24px rgba(59,130,246,.3);
            transition: all .2s; margin-top: 8px;
        }
        .btn-submit:hover { transform: translateY(-1px); }
        .back-link {
            display: block; text-align: center; margin-top: 20px;
            font-size: 13px; color: #475569; text-decoration: none;
        }
        .back-link:hover { color: #94a3b8; }
        .password-wrap { position: relative; }
        .password-wrap .form-input { padding-right: 42px; }
        .password-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #475569; padding: 0;
            display: flex; align-items: center;
        }
        .password-toggle:hover { color: #94a3b8; }
        .admin-badge {
            display: flex; align-items: center; gap: 6px;
            background: rgba(99,102,241,.15); color: #818cf8;
            border: 1px solid rgba(99,102,241,.3);
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 600; text-transform: uppercase;
            letter-spacing: .06em; width: fit-content; margin: 0 auto 24px;
        }
        .alert {
            padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px;
        }
        .alert-info {
            background: rgba(59,130,246,.1); border: 1px solid rgba(59,130,246,.3); color: #60a5fa;
        }
        .alert-danger {
            background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.3); color: #f87171;
        }
        .turnstile-wrapper {
            display: flex; justify-content: center; margin: 20px 0;
        }
        .auth-links {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px; margin-top: 4px;
        }
        .auth-checkbox {
            display: flex; align-items: center; gap: 8px;
        }
        .auth-checkbox input {
            width: 15px; height: 15px; accent-color: #3b82f6;
        }
        .auth-checkbox label {
            font-size: 13px; color: #94a3b8;
        }
        .auth-link {
            font-size: 13px; color: #3b82f6; text-decoration: none;
        }
        .auth-link:hover {
            color: #60a5fa;
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <div class="logo">
            @if($sistemaConfig?->logo_login_url ?? $sistemaConfig?->logo_url)
                <img src="{{ $sistemaConfig->logo_login_url ?? $sistemaConfig->logo_url }}"
                     class="logo-img"
                     alt="{{ $sistemaConfig?->nombre_sistema ?? 'Sistema' }}">
            @else
                <div class="logo-icon">
                    <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            @endif

            <div style="text-align: center;">
                @if($sistemaConfig?->nombre_sistema)
                    <div class="sistema-name">{{ $sistemaConfig->nombre_sistema }}</div>
                @else
                    <span class="logo-text">Sistema<span>.HC</span></span>
                @endif

                @if($sistemaConfig?->nombre_institucion)
                    <div class="institucion-name">{{ $sistemaConfig->nombre_institucion }}</div>
                @endif
            </div>
        </div>

        @yield('content')
    </div>

    {{-- Turnstile Script --}}
    @if(config('services.turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.innerHTML = isPassword
                ? `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>`
                : `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>`;
        }
    </script>

    @stack('scripts')
</body>
</html>