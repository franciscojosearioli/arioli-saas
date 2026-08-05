<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contratar {{ $plan->product->name }} — Arioli.dev</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --font-sans: 'DM Sans', sans-serif;
            --font-mono: 'DM Mono', monospace;
            --bg: #080d1a;
            --card: #111827;
            --card-border: #1e2d45;
            --accent: #3b82f6;
            --text: #f1f5f9;
            --text2: #94a3b8;
            --text3: #475569;
            --danger: #f87171;
            --success: #10b981;
        }
        body {
            font-family: var(--font-sans);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .checkout-wrap {
            width: 100%;
            max-width: 960px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            align-items: start;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 32px;
        }
        .logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; margin-bottom: 32px;
        }
        .logo-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center;
        }
        .logo-text { font-size: 16px; font-weight: 700; color: var(--text); }
        .logo-text span { color: var(--accent); }
        .plan-name { font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
        .plan-sub  { font-size: 14px; color: var(--text2); margin-bottom: 28px; }
        .plan-price { font-size: 42px; font-weight: 800; color: var(--text); letter-spacing: -.02em; margin-bottom: 4px; }
        .plan-period { font-size: 14px; color: var(--text2); margin-bottom: 24px; }
        .plan-detail {
            background: rgba(255,255,255,.04);
            border: 1px solid var(--card-border);
            border-radius: 10px; padding: 16px;
        }
        .plan-detail-row {
            display: flex; justify-content: space-between;
            font-size: 13.5px; padding: 6px 0;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .plan-detail-row:last-child { border-bottom: none; }
        .plan-detail-row dt { color: var(--text2); }
        .plan-detail-row dd { color: var(--text); font-weight: 600; }
        .form-title { font-size: 20px; font-weight: 700; color: var(--text); margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--text2); margin-bottom: 7px; }
        .form-input {
            width: 100%; padding: 11px 14px;
            background: rgba(255,255,255,.05);
            border: 1.5px solid var(--card-border);
            border-radius: 10px; font-size: 14px;
            font-family: var(--font-sans); color: var(--text);
            outline: none; transition: all .2s;
        }
        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
            background: rgba(59,130,246,.05);
        }
        .form-input::placeholder { color: var(--text3); }
        .form-hint { font-size: 12px; color: var(--text3); margin-top: 5px; }
        .form-error { font-size: 12px; color: var(--danger); margin-top: 5px; }
        .domain-preview {
            display: flex; align-items: center;
            background: rgba(255,255,255,.05);
            border: 1.5px solid var(--card-border);
            border-radius: 10px; overflow: hidden;
        }
        .domain-preview input {
            flex: 1; padding: 11px 14px;
            background: transparent; border: none;
            font-size: 14px; font-family: var(--font-sans);
            color: var(--text); outline: none;
        }
        .domain-suffix {
            padding: 11px 14px;
            background: rgba(255,255,255,.04);
            border-left: 1px solid var(--card-border);
            font-size: 13px; color: var(--text3);
            font-family: var(--font-mono); white-space: nowrap;
        }
        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; font-size: 15px; font-weight: 700;
            font-family: var(--font-sans); border: none;
            border-radius: 12px; cursor: pointer;
            box-shadow: 0 6px 24px rgba(59,130,246,.3);
            transition: all .2s; margin-top: 8px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 10px 32px rgba(59,130,246,.4); }
        .secure-note {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; font-size: 12px; color: var(--text3); margin-top: 14px;
        }
        @media (max-width: 768px) {
            .checkout-wrap { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="checkout-wrap">

    {{-- Info del plan --}}
    <div class="card">
        <a href="http://{{ config('app.landing_domain') }}" class="logo">
            <div class="logo-icon">
                <svg width="18" height="18" fill="none" stroke="#fff" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="logo-text">Arioli<span>.dev</span></span>
        </a>

        <div class="plan-name">{{ $plan->product->name }}</div>
        <div class="plan-sub">{{ $plan->period_label }} · Acceso completo</div>

        <div class="plan-price">${{ number_format($plan->price, 0, ',', '.') }}</div>
        <div class="plan-period">
            Pago único por {{ $plan->period_months }} {{ $plan->period_months === 1 ? 'mes' : 'meses' }}
            @if($plan->discount_percent > 0)
                · <span style="color:#34d399;">{{ $plan->discount_percent }}% de descuento</span>
            @endif
        </div>

        <dl class="plan-detail">
            <div class="plan-detail-row">
                <dt>Sistema</dt>
                <dd>{{ $plan->product->name }}</dd>
            </div>
            <div class="plan-detail-row">
                <dt>Plan</dt>
                <dd>{{ $plan->period_label }}</dd>
            </div>
            <div class="plan-detail-row">
                <dt>Duración</dt>
                <dd>{{ $plan->period_months }} {{ $plan->period_months === 1 ? 'mes' : 'meses' }}</dd>
            </div>
            <div class="plan-detail-row">
                <dt>Precio base</dt>
                <dd>${{ number_format($plan->base_price, 0, ',', '.') }}/mes</dd>
            </div>
            @if($plan->discount_percent > 0)
                <div class="plan-detail-row">
                    <dt>Descuento</dt>
                    <dd style="color:#34d399;">−{{ $plan->discount_percent }}%</dd>
                </div>
            @endif
            <div class="plan-detail-row">
                <dt>Total a pagar</dt>
                <dd style="font-size:16px;">${{ number_format($plan->price, 0, ',', '.') }} ARS</dd>
            </div>
        </dl>
    </div>

    {{-- Formulario --}}
    <div class="card">
        <div class="form-title">Completá tus datos</div>

        @if($errors->any())
            <div style="background:rgba(248,113,113,.1); border:1px solid rgba(248,113,113,.3); border-radius:10px; padding:12px 16px; margin-bottom:20px; font-size:13.5px; color:var(--danger);">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.process', $plan->id) }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Nombre completo</label>
                <input type="text" name="customer_name" class="form-input"
                       value="{{ old('customer_name') }}"
                       placeholder="Juan Pérez" required>
                @error('customer_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="customer_email" id="customer_email" class="form-input"
                       value="{{ old('customer_email') }}"
                       placeholder="juan@empresa.com" required
                       onblur="verifyEmail(this.value)" oninput="resetEmailStatus()">
                <div id="email-status" class="form-hint"></div>
                @error('customer_email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nombre de tu empresa / negocio</label>
                <div class="domain-preview">
                    <input type="text" name="customer_company"
                           value="{{ old('customer_company') }}"
                           placeholder="miempresa" required
                           oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '')">
                    <span class="domain-suffix">{{ $domainSuffix }}</span>
                </div>
                <div class="form-hint">Este será el subdominio de tu sistema. Solo letras, números y guiones.</div>
                @error('customer_company')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            @if(config('services.turnstile.sitekey'))
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                <div style="display:flex; justify-content:center; margin-bottom:18px;">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.sitekey') }}" data-theme="dark"></div>
                </div>
            @endif

            <button type="submit" class="btn-submit" id="submit-btn">
                Pagar ${{ number_format($plan->price, 0, ',', '.') }} ARS →
            </button>

            <div class="secure-note">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Pago seguro procesado por MercadoPago
            </div>
        </form>
    </div>

</div>

<script>
let emailHardInvalid = false;

function resetEmailStatus() {
    emailHardInvalid = false;
    document.getElementById('submit-btn').disabled = false;
    document.getElementById('email-status').textContent = '';
}

async function verifyEmail(email) {
    const statusEl = document.getElementById('submit-btn') && document.getElementById('email-status');
    if (!email) { resetEmailStatus(); return; }

    statusEl.textContent = 'Verificando email...';
    statusEl.style.color = 'var(--text3)';

    try {
        const res = await fetch(`{{ route('checkout.verify-email') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email }),
        });
        const data = await res.json();

        emailHardInvalid = !data.valid;
        document.getElementById('submit-btn').disabled = emailHardInvalid;
        statusEl.textContent = data.valid ? '✓ ' + data.message : '✗ ' + data.message;
        statusEl.style.color = data.valid ? 'var(--success)' : 'var(--danger)';
    } catch (e) {
        // Si falla la verificación en sí (red, etc.) no bloqueamos el pago —
        // la validación server-side al enviar el formulario sigue aplicando.
        statusEl.textContent = '';
    }
}
</script>

</body>
</html>