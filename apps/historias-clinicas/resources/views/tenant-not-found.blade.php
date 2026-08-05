<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenantId }} — Sistema de Salud</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 40%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.12);
            max-width: 520px;
            width: 100%;
            padding: 48px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #0ea5e9);
        }
        .app-icon {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25);
        }
        .app-icon svg { width: 38px; height: 38px; color: #fff; }
        .app-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #2563eb;
            margin-bottom: 8px;
        }
        .available-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dcfce7;
            color: #16a34a;
            font-size: 13px;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 20px;
            margin-bottom: 24px;
        }
        .available-badge svg { width: 14px; height: 14px; }
        .subdomain-display {
            font-size: 32px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: -.5px;
            margin-bottom: 6px;
            word-break: break-all;
        }
        .subdomain-url {
            font-size: 13px;
            color: #94a3b8;
            font-family: ui-monospace, 'Cascadia Code', monospace;
            margin-bottom: 24px;
        }
        .description {
            font-size: 15px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 32px;
            padding: 0 8px;
        }
        .description strong { color: #1e40af; }
        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: transform .15s, box-shadow .15s;
            margin-bottom: 20px;
            width: 100%;
        }
        .cta-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
        }
        .features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 28px;
            text-align: left;
        }
        .feature {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #64748b;
        }
        .feature svg { width: 16px; height: 16px; color: #2563eb; flex-shrink: 0; }
        .divider { border: none; border-top: 1px solid #f1f5f9; margin: 20px 0; }
        .footer-note {
            font-size: 12px;
            color: #94a3b8;
        }
        .footer-note a { color: #2563eb; text-decoration: none; }
        .footer-note a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="app-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M9 12h6M12 9v6M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18z" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <div class="app-label">Sistema de Salud</div>

        @if($reservado ?? false)
            <div class="available-badge" style="background:#f3f4f6; color:#6b7280;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Subdominio reservado
            </div>

            <div class="subdomain-display">{{ $tenantId }}</div>
            <div class="subdomain-url">{{ request()->getHost() }}</div>

            <p class="description">
                El subdominio <strong>{{ $tenantId }}</strong> está reservado para uso interno
                de la plataforma y no está disponible para contratar.
            </p>

            <hr class="divider">
        @else
            <div class="available-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Subdominio disponible
            </div>

            <div class="subdomain-display">{{ $tenantId }}</div>
            <div class="subdomain-url">{{ request()->getHost() }}</div>

            <p class="description">
                El subdominio <strong>{{ $tenantId }}</strong> está disponible para
                <strong>Sistema de Salud</strong>. Podés contratar este espacio para tu consultorio,
                clínica o centro de salud y adaptarlo a tu especialidad.
            </p>

            <div class="features">
                <div class="feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Historial de pacientes
                </div>
                <div class="feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Turnos y agenda
                </div>
                <div class="feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Recetas digitales
                </div>
                <div class="feature">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Multi-usuario
                </div>
            </div>

            <a href="http://{{ $landingHost }}" class="cta-btn">
                Contratar este subdominio
            </a>

            <hr class="divider">
        @endif

        <p class="footer-note">
            ¿Ya tenés una cuenta?
            <a href="http://{{ $clienteHost }}/login">Iniciá sesión en el panel</a>
        </p>
    </div>
</body>
</html>
