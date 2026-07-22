@php
    $config = \App\Models\ConfiguracionSistema::instancia();
    $logoBase64 = null;
    if ($config->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($config->logo)) {
        $logoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($config->logo);
        $logoMime = mime_content_type($logoPath) ?: 'image/png';
        $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <style>
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
            line-height: 35px;
            border-bottom: 1px solid #000;
        }
        .header-nombre {
            font-size: 18px;
            font-weight: bold;
            line-height: 80px;
            color: #1a1a1a;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" style="max-height:60px;max-width:280px;padding-top:10px">
        @else
            <span class="header-nombre">{{ $config->nombre_sistema }}</span>
        @endif
    </div>
</body>
</html>
