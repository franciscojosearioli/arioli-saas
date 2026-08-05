<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña — Arioli.dev</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #080d1a; color: #f1f5f9;
            min-height: 100vh; display: flex;
            align-items: center; justify-content: center; padding: 40px 20px;
        }
        .card {
            background: #111827; border: 1px solid #1e2d45;
            border-radius: 20px; padding: 40px; width: 100%; max-width: 420px;
        }
        .logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; margin-bottom: 32px; justify-content: center;
        }
        .logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center;
        }
        .logo-text { font-size: 18px; font-weight: 700; color: #f1f5f9; }
        .logo-text span { color: #3b82f6; }
        h1 { font-size: 22px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .subtitle { font-size: 14px; color: #94a3b8; text-align: center; margin-bottom: 28px; line-height: 1.6; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 7px; }
        .form-input {
            width: 100%; padding: 11px 14px;
            background: rgba(255,255,255,.05);
            border: 1.5px solid #1e2d45; border-radius: 10px;
            font-size: 14px; font-family: 'DM Sans', sans-serif;
            color: #f1f5f9; outline: none; transition: all .2s;
        }
        .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
        .form-input::placeholder { color: #475569; }
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
        .admin-badge {
            display: flex; justify-content: center;
            margin: 0 auto 24px;
        }
        .admin-badge span {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(99,102,241,.15); color: #818cf8;
            border: 1px solid rgba(99,102,241,.3);
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 600; text-transform: uppercase;
            letter-spacing: .06em;
        }
    </style>
</head>
<body>
    <div class="card">
        <a href="http://{{ config('app.admin_domain') }}/login" class="logo">
            <div class="logo-icon">
                <svg width="20" height="20" fill="none" stroke="#fff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="logo-text">Arioli<span>.dev</span></span>
        </a>

        <div class="admin-badge">
            <span>
                <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                </svg>
                Panel Administrativo
            </span>
        </div>

        <h1>Recuperar contraseña</h1>
        <p class="subtitle">Ingresá tu email y te enviaremos un link para restablecer tu contraseña.</p>

        @if(session('status'))
            <div style="background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.3); border-radius:10px; padding:12px 16px; margin-bottom:20px; font-size:13.5px; color:#34d399;">
                Te enviamos el link de recuperación a tu email.
            </div>
        @endif

        @if($errors->any())
            <div style="background:rgba(248,113,113,.1); border:1px solid rgba(248,113,113,.3); border-radius:10px; padding:12px 16px; margin-bottom:20px; font-size:13.5px; color:#f87171;">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input"
                       value="{{ old('email') }}"
                       placeholder="admin@arioli.dev" required autofocus>
            </div>
            <button type="submit" class="btn-submit">Enviar link de recuperación</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">← Volver al login</a>
    </div>
</body>
</html>