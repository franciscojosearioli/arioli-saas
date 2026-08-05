<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Licencia Expirada — Arioli.dev</title></head>
<body style="font-family:sans-serif;text-align:center;padding:80px 20px;background:#080d1a;color:#f1f5f9;">
    <h1 style="color:#f87171;font-size:28px;">Licencia Expirada</h1>
    <p style="color:#94a3b8;margin-top:12px;">Tu licencia expiró el <strong>{{ $expired_at->format('d/m/Y') }}</strong>.<br>Contactá al administrador para renovarla.</p>
    @if($plan)
    <p style="color:#94a3b8;margin-top:8px;">Plan: {{ $plan->name }} — ${{ number_format($plan->price, 2) }}/mes</p>
    @endif
    <a href="mailto:soporte@arioli.dev" style="color:#3b82f6;margin-top:24px;display:inline-block;">Contactar soporte</a>
</body>
</html>