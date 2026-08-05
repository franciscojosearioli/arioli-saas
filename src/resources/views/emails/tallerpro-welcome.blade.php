<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #0f766e; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .body { padding: 32px 40px; }
        .body p { color: #374151; line-height: 1.6; margin: 0 0 16px; }
        .credentials { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 20px; margin: 24px 0; }
        .credentials p { margin: 6px 0; font-size: 14px; }
        .credentials strong { color: #15803d; }
        .btn { display: inline-block; background: #0f766e; color: #fff !important; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; margin: 16px 0; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tu sistema TallerPro está listo</h1>
        </div>
        <div class="body">
            <p>Hola <strong>{{ $clientName }}</strong>,</p>
            <p>Tu sistema de gestión de taller ya está configurado y disponible. Podés acceder con las siguientes credenciales:</p>

            <div class="credentials">
                <p><strong>URL del sistema:</strong><br>
                   <a href="{{ $systemUrl }}">{{ $systemUrl }}</a></p>
                <p><strong>Usuario:</strong> {{ $clientEmail }}</p>
                <p><strong>Contraseña:</strong> {{ $adminPassword }}</p>
            </div>

            <a href="{{ $systemUrl }}" class="btn">Ingresar al sistema</a>

            <p>Desde el sistema podés gestionar clientes, equipos, órdenes de trabajo, presupuestos y cobros.</p>
            <p>Te recomendamos cambiar tu contraseña en el primer ingreso desde la sección de configuración.</p>
            <p>Si tenés alguna duda, respondé este email o escribinos a <a href="mailto:soporte@arioli.dev">soporte@arioli.dev</a>.</p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Arioli.dev — Todos los derechos reservados
        </div>
    </div>
</body>
</html>
