<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Firma de Consentimiento</title>
<style>
body { font-family: Arial, sans-serif; background:#f8fafc; margin:0; padding:0; }
.wrap { max-width:580px; margin:40px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 16px rgba(0,0,0,.08); }
.hdr { background:#1d4ed8; padding:28px 32px; color:#fff; }
.hdr h1 { margin:0; font-size:20px; }
.body { padding:28px 32px; color:#1e293b; font-size:14px; line-height:1.7; }
.btn { display:inline-block; margin:20px 0; padding:13px 28px; background:#16a34a; color:#fff; border-radius:8px; text-decoration:none; font-weight:700; font-size:14px; }
.notice { margin-top:20px; padding:14px; background:#fef9c3; border-radius:8px; font-size:12px; color:#713f12; }
.footer { padding:16px 32px; background:#f8fafc; font-size:11px; color:#94a3b8; border-top:1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="wrap">
    <div class="hdr">
        <h1>Firma de Consentimiento Informado</h1>
    </div>
    <div class="body">
        <p>Hola <strong>{{ $consentimiento->paciente->nombre }} {{ $consentimiento->paciente->apellido }}</strong>,</p>

        <p>Se le solicita que firme el siguiente documento:</p>
        <p><strong>{{ $consentimiento->tipo->nombre }}</strong></p>

        <p>Por favor, hacé click en el siguiente botón para leer el documento y firmar digitalmente:</p>

        <a href="{{ route('consentimiento.firmaPublica', $consentimiento->token) }}" class="btn">
            Firmar consentimiento
        </a>

        <div class="notice">
            <strong>Importante:</strong> este enlace es válido hasta el
            {{ $consentimiento->token_expires_at?->format('d/m/Y \a \l\a\s H:i') }}.
            Si el enlace expiró, comuníquese con la institución para recibir uno nuevo.
        </div>

        <p>Si no solicitó este documento o cree que lo recibió por error, puede ignorar este correo.</p>
    </div>
    <div class="footer">
        Este mensaje fue generado automáticamente. Por favor no responda a este correo.
    </div>
</div>
</body>
</html>
