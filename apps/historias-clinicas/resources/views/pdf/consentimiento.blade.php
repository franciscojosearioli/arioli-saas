@php
    $config      = \App\Models\ConfiguracionSistema::instancia();
    $logoBase64  = null;
    if ($config->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($config->logo)) {
        $logoPath   = \Illuminate\Support\Facades\Storage::disk('public')->path($config->logo);
        $logoMime   = mime_content_type($logoPath) ?: 'image/png';
        $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
    $institucion = $config->nombre_institucion ?: $config->nombre_sistema;

    // Use multi-page array; fall back to legacy p1/p2 columns
    $paginas = $tipo->contenido_paginas ?? null;
    if (empty($paginas)) {
        $paginas = array_filter([$tipo->contenido_pagina1 ?? '', $tipo->contenido_pagina2 ?? '']);
        if (empty($paginas)) $paginas = [''];
    }
    $paginas = array_values($paginas);
@endphp
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{{ $tipo->nombre }}</title>
<style>
@page { margin: 0; }
html  { font-family: 'Arial', sans-serif; }
body  { margin: 0; padding-top: 95px; padding-bottom: 90px; font-family: 'Arial', sans-serif; font-size: 12px; line-height: 1.65; }
.header { position: fixed; top: 0; left: 0; right: 0; height: 75px; text-align: center; border-bottom: 1px solid #000; padding-top: 8px; }
.footer { position: fixed; bottom: 0; left: 0; right: 0; height: 70px; text-align: center; border-top: 1px solid #000; font-size: 10px; color: #555; padding-top: 8px; }
.content { margin: 18px 55px; }
.titulo  { text-align: center; font-size: 15px; font-weight: bold; margin-bottom: 18px; text-transform: uppercase; letter-spacing: 1px; }
.datos-paciente { background: #f8f8f8; border: 1px solid #ddd; padding: 10px 14px; margin-bottom: 14px; }
.datos-paciente table { width: 100%; border: none; }
.datos-paciente td { border: none; padding: 3px 6px; font-size: 12px; }
.firma-bloque { margin-top: 28px; }
.firma-linea  { display: inline-block; width: 170px; border-bottom: 1px solid #000; margin-top: 36px; }
.firma-tabla  { width: 100%; margin-top: 36px; }
.firma-tabla td { border: none; text-align: center; font-size: 11px; padding-top: 5px; vertical-align: bottom; }
.page-break { page-break-before: always; }
ol li { margin-bottom: 5px; }
p { margin: 5px 0; }
.header-nombre { font-size: 17px; font-weight: bold; line-height: 60px; color: #1a1a1a; }
</style>
</head>
<body>

{{-- Fixed header --}}
<div class="header">
    @if($logoBase64)
        <img src="{{ $logoBase64 }}" style="max-height:58px;max-width:270px;">
    @else
        <span class="header-nombre">{{ $config->nombre_sistema }}</span>
    @endif
</div>

{{-- Fixed footer --}}
<div class="footer">
    @if($config->pie_pdf)
        {!! nl2br(e($config->pie_pdf)) !!}
    @else
        @php
            $partes = array_filter([
                trim(implode(', ', array_filter([$config->direccion, $config->localidad, $config->provincia]))),
                $config->telefono ? 'Tel: ' . $config->telefono : null,
                $config->email_contacto,
            ]);
        @endphp
        {{ count($partes) > 0 ? implode(' | ', $partes) : $institucion }}
    @endif
</div>

{{-- Pages --}}
@foreach($paginas as $pgIdx => $contenido)
    @if(!$loop->first)
    <div class="page-break"></div>
    @endif

    <div class="content">
        <p class="titulo">
            {{ $tipo->nombre }}
            @if(!$loop->first)— Página {{ $pgIdx + 1 }}@endif
        </p>

        {{-- Patient data: full on page 1, compact on subsequent --}}
        @if($loop->first)
        <div class="datos-paciente">
            <table>
                <tr>
                    <td><strong>Paciente:</strong> {{ $paciente->apellido }}, {{ $paciente->nombre }}</td>
                    <td><strong>DNI:</strong> {{ $paciente->dni ?? '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Institución:</strong> {{ $institucion }}</td>
                    <td><strong>Fecha:</strong> {{ now()->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
        @else
        <div class="datos-paciente">
            <table>
                <tr>
                    <td><strong>Paciente:</strong> {{ $paciente->apellido }}, {{ $paciente->nombre }}</td>
                    <td><strong>DNI:</strong> {{ $paciente->dni ?? '—' }}</td>
                </tr>
            </table>
        </div>
        @endif

        {{-- Page content --}}
        {!! $contenido !!}

        {{-- Signatures on every page --}}
        <div class="firma-bloque">
            <table class="firma-tabla">
                <tr>
                    <td>
                        @if($loop->first && $firmaImgPaciente)
                            <img src="{{ $firmaImgPaciente }}" style="max-height:55px;max-width:150px;display:block;margin:0 auto 4px;">
                        @else
                            <div class="firma-linea"></div>
                        @endif
                        <br>Firma del Paciente<br>
                        <small>{{ $paciente->nombre }} {{ $paciente->apellido }}</small>
                    </td>
                    <td>
                        <div class="firma-linea"></div><br>
                        Aclaración / DNI<br>
                        <small>{{ $paciente->dni ?? '—' }}</small>
                    </td>
                    <td>
                        @if($consentimiento->firmado_paciente && $consentimiento->firmado_paciente_at)
                            <div style="margin-top:28px;font-size:11px;font-weight:bold;">
                                {{ $consentimiento->firmado_paciente_at->format('d/m/Y') }}
                            </div>
                            <div style="font-size:9px;color:#555;">Firmado digitalmente</div>
                        @else
                            <div class="firma-linea"></div><br>Fecha y lugar
                        @endif
                    </td>
                    @if($tipo->requiere_firma_profesional)
                    <td>
                        @if($loop->first && $firmaImgProfesional)
                            <img src="{{ $firmaImgProfesional }}" style="max-height:55px;max-width:150px;display:block;margin:0 auto 4px;">
                        @else
                            <div class="firma-linea"></div>
                        @endif
                        <br>Firma del Profesional<br>
                        <small>{{ $firmadoPor?->name ?? '—' }}</small>
                    </td>
                    @endif
                </tr>
            </table>
        </div>
    </div>
@endforeach

</body>
</html>
