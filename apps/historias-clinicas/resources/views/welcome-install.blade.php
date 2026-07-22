<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido — Clínica</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 24px;
            padding: 56px 52px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(12px);
        }
        .icon {
            width: 72px; height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
        }
        h1 { font-size: 28px; font-weight: 700; color: #f1f5f9; margin: 0 0 12px; }
        p  { font-size: 15px; color: #94a3b8; margin: 0 0 36px; line-height: 1.6; }
        .btn {
            display: inline-block;
            background: #0369a1;
            color: #fff;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            transition: background .2s, transform .15s;
        }
        .btn:hover { background: #075985; transform: translateY(-1px); }
        .footer { margin-top: 32px; font-size: 12px; color: #475569; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="36" height="36" fill="none" stroke="#fff" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>

        <h1>¡Tu sistema está listo!</h1>
        <p>Iniciá sesión con tu cuenta de administrador para configurar el nombre de la institución y los datos de tu clínica.</p>

        <a href="/login" class="btn">Iniciar instalación →</a>

        <div class="footer">
            Powered by <strong style="color:#64748b;">Arioli.dev</strong>
        </div>
    </div>
</body>
</html>
