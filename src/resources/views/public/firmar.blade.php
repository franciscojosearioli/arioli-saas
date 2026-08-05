<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Firmar documento — {{ $contract->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; color: #111827; padding: 24px 16px; }
        .wrap { max-width: 680px; margin: 0 auto; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 28px; margin-bottom: 20px; }
        h1 { font-size: 19px; margin-bottom: 4px; }
        .sub { font-size: 13px; color: #6b7280; margin-bottom: 20px; }
        .content { white-space: pre-wrap; font-size: 13.5px; line-height: 1.7; background: #f9fafb; border-radius: 10px; padding: 18px; max-height: 420px; overflow-y: auto; border: 1px solid #eee; }
        label { display: block; font-size: 13px; font-weight: 600; margin: 16px 0 6px; }
        input[type=text] { width: 100%; padding: 11px 14px; border: 1.5px solid #d1d5db; border-radius: 10px; font-size: 14px; }
        .checkbox-row { display: flex; align-items: flex-start; gap: 10px; margin: 18px 0; }
        .checkbox-row input { width: 18px; height: 18px; margin-top: 2px; }
        .checkbox-row span { font-size: 13px; color: #374151; }
        .actions { display: flex; gap: 10px; margin-top: 20px; }
        button { flex: 1; padding: 13px; border: none; border-radius: 10px; font-size: 14.5px; font-weight: 700; cursor: pointer; }
        .btn-primary { background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; }
        .btn-danger { background: #fff; color: #dc2626; border: 1.5px solid #fca5a5; }
        .error { font-size: 12.5px; color: #dc2626; margin-top: 4px; }
        .meta { font-size: 12px; color: #9ca3af; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>{{ $contract->title }}</h1>
        <p class="sub">Te pedimos que revises el documento y confirmes tu firma como <strong>{{ $signer->role->label() }}</strong>.</p>
        <div class="content">{{ $contract->content }}</div>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="error" style="margin-bottom:12px;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('signature.sign', $signer->signing_token) }}">
            @csrf
            <label>Firmante</label>
            <input type="text" value="{{ $signer->name }} ({{ $signer->email }})" disabled style="background:#f3f4f6; color:#6b7280;">

            <label for="confirm_name">Escribí tu nombre completo para confirmar la firma</label>
            <input type="text" name="confirm_name" id="confirm_name" placeholder="{{ $signer->name }}" value="{{ old('confirm_name') }}">

            <div class="checkbox-row">
                <input type="checkbox" name="accept" id="accept" value="1">
                <label for="accept" style="margin:0;"><span>Leí el documento completo y acepto sus términos.</span></label>
            </div>

            <div class="actions">
                <button type="submit" class="btn-primary">Aceptar y firmar</button>
            </div>
        </form>

        <form method="POST" action="{{ route('signature.reject', $signer->signing_token) }}" onsubmit="return confirm('¿Rechazar este documento? Esta acción no se puede deshacer.')" style="margin-top:10px;">
            @csrf
            <button type="submit" class="btn-danger" style="width:100%;">Rechazar</button>
        </form>
    </div>

    <p class="meta">Link válido hasta el {{ $signer->signing_token_expires_at->format('d/m/Y') }}. Tu firma queda registrada con fecha, hora, IP y navegador como evidencia.</p>
</div>
</body>
</html>
