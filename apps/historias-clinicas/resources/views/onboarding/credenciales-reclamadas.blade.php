<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link ya utilizado — Sistema de Salud</title>
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
            max-width: 440px;
            width: 100%;
            padding: 44px 40px;
            text-align: center;
        }
        h1 { font-size: 20px; color: #1e3a8a; margin-bottom: 10px; }
        p { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 24px; }
        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Este link ya fue utilizado</h1>
        <p>Tu contraseña ya fue definida anteriormente. Si no recordás tus credenciales, contactá a soporte para recuperar el acceso.</p>
        <a href="/login" class="cta-btn">Ir al login</a>
    </div>
</body>
</html>
