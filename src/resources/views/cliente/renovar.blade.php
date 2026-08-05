<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Renovar licencia — Arioli.dev</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #080d1a; --card: #111827; --border: #1e2d45;
            --text: #f1f5f9; --text2: #94a3b8; --text3: #475569;
            --accent: #3b82f6; --success: #10b981; --danger: #f87171;
            --font: 'DM Sans', sans-serif; --mono: 'DM Mono', monospace;
        }
        body { font-family: var(--font); background: var(--bg); color: var(--text); min-height: 100vh; }
        .topbar {
            background: #0d1426; border-bottom: 1px solid var(--border);
            padding: 0 32px; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .logo-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center;
        }
        .logo-text { font-size: 16px; font-weight: 700; color: var(--text); }
        .logo-text span { color: var(--accent); }
        .content { max-width: 800px; margin: 0 auto; padding: 40px 32px; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 28px; }
        .plan-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 14px; margin: 24px 0; }
        .plan-option {
            border: 2px solid var(--border); border-radius: 12px; padding: 20px;
            cursor: pointer; transition: all .2s; position: relative;
        }
        .plan-option:hover { border-color: var(--accent); }
        .plan-option input[type="radio"] { position: absolute; opacity: 0; }
        .plan-option.selected { border-color: var(--accent); background: rgba(59,130,246,.06); }
        .plan-option.best { border-color: var(--success); }
        .plan-option.best.selected { background: rgba(16,185,129,.06); }
        .plan-name { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
        .plan-price { font-size: 26px; font-weight: 800; color: var(--text); }
        .plan-price span { font-size: 13px; font-weight: 400; color: var(--text2); }
        .plan-discount { font-size: 12px; color: var(--success); font-weight: 600; margin-top: 4px; }
        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; font-size: 15px; font-weight: 700;
            font-family: var(--font); border: none; border-radius: 12px;
            cursor: pointer; box-shadow: 0 6px 24px rgba(59,130,246,.3);
            transition: all .2s;
        }
        .btn-submit:hover { transform: translateY(-1px); }
        .back-link { font-size: 13px; color: var(--text2); text-decoration: none; display: inline-block; margin-bottom: 24px; }
        .current-info {
            background: rgba(255,255,255,.04); border: 1px solid var(--border);
            border-radius: 10px; padding: 16px; margin-bottom: 24px;
        }
        .current-row { display: flex; justify-content: space-between; font-size: 13.5px; padding: 5px 0; }
        .current-row dt { color: var(--text2); }
        .current-row dd { color: var(--text); font-weight: 500; }
    </style>
</head>
<body>

<header class="topbar">
    <a href="{{ route('cliente.dashboard') }}" class="logo">
        <div class="logo-icon">
            <svg width="18" height="18" fill="none" stroke="#fff" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <span class="logo-text">Arioli<span>.dev</span></span>
    </a>
</header>

<div class="content">
    <a href="{{ route('cliente.dashboard') }}" class="back-link">← Volver al panel</a>

    <div class="card">
        <h2 style="font-size:20px; font-weight:700; margin-bottom:6px;">
            Renovar licencia — {{ $license->plan->product->name }}
        </h2>
        <p style="font-size:14px; color:var(--text2); margin-bottom:24px;">
            Elegí el período de renovación que mejor se adapte a tu negocio.
        </p>

        {{-- Info actual --}}
        <div class="current-info">
            <div class="current-row">
                <dt>Plan actual</dt>
                <dd>{{ $license->plan->period_label }}</dd>
            </div>
            <div class="current-row">
                <dt>Vencimiento actual</dt>
                <dd style="color:{{ $license->daysRemaining() <= 7 ? 'var(--danger)' : 'var(--text)' }};">
                    {{ $license->expires_at->format('d/m/Y') }}
                    ({{ $license->daysRemaining() }} días restantes)
                </dd>
            </div>
        </div>

        @if(session('error'))
            <div style="background:rgba(248,113,113,.1); border:1px solid rgba(248,113,113,.3); border-radius:10px; padding:12px 16px; margin-bottom:20px; font-size:13.5px; color:var(--danger);">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('cliente.renovar.post', $license->id) }}" id="renovarForm">
            @csrf

            <div class="plan-grid">
                @foreach($plans as $plan)
                    <label class="plan-option {{ $plan->period === 'annual' ? 'best' : '' }} {{ $plan->id === $license->plan_id ? 'selected' : '' }}"
                           onclick="selectPlan(this, {{ $plan->id }})">
                        <input type="radio" name="plan_id" value="{{ $plan->id }}"
                               {{ $plan->id === $license->plan_id ? 'checked' : '' }}>

                        @if($plan->period === 'annual')
                            <div style="position:absolute; top:-1px; right:14px; background:var(--success); color:#fff; font-size:10px; font-weight:700; padding:3px 8px; border-radius:0 0 6px 6px;">
                                MEJOR PRECIO
                            </div>
                        @endif

                        <div class="plan-name">{{ $plan->period_label }}</div>
                        <div class="plan-price">
                            ${{ number_format($plan->price, 0, ',', '.') }}
                            <span>ARS</span>
                        </div>
                        <div style="font-size:12px; color:var(--text2); margin-top:4px;">
                            {{ $plan->period_months }} {{ $plan->period_months === 1 ? 'mes' : 'meses' }}
                        </div>
                        @if($plan->discount_percent > 0)
                            <div class="plan-discount">{{ $plan->discount_percent }}% de descuento</div>
                        @endif
                        @php
                            $newExpiry = $license->isExpired() 
                                ? now()->addMonths($plan->period_months) 
                                : $license->expires_at->copy()->addMonths($plan->period_months);
                        @endphp
                        <div style="font-size:11px; color:var(--text3); margin-top:6px;">
                            Vence el {{ $newExpiry->format('d/m/Y') }}
                        </div>
                    </label>
                @endforeach
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                Renovar y pagar →
            </button>

            <div style="display:flex; align-items:center; justify-content:center; gap:6px; font-size:12px; color:var(--text3); margin-top:14px;">
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
function selectPlan(el, planId) {
    document.querySelectorAll('.plan-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input[type="radio"]').checked = true;

    const price = el.querySelector('.plan-price').textContent.trim();
    document.getElementById('submitBtn').textContent = `Renovar ${price} →`;
}
</script>

</body>
</html>