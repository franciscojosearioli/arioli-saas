@php
    $config = \App\Models\ConfiguracionSistema::instancia();
@endphp
<!DOCTYPE html>
<html>
<head>
    <style>
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 90px;
            text-align: center;
            line-height: 35px;
            border-top: 1px solid #000;
        }
        p {
            line-height: normal;
            margin: 0;
            font-size: 11px;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="footer">
        @if($config->pie_pdf)
            <p>{!! nl2br(e($config->pie_pdf)) !!}</p>
        @else
            @php
                $linea1 = trim(implode(', ', array_filter([
                    $config->direccion,
                    $config->localidad,
                    $config->provincia,
                ])));
                $linea2 = trim(implode(' | ', array_filter([
                    $config->telefono ? 'Tel: ' . $config->telefono : null,
                    $config->email_contacto,
                    $config->website,
                ])));
            @endphp
            @if($linea1 || $linea2)
                @if($linea1)<p>{{ $linea1 }}</p>@endif
                @if($linea2)<p>{{ $linea2 }}</p>@endif
            @else
                <p>{{ $config->nombre_sistema }}</p>
            @endif
        @endif
    </div>
</body>
</html>
