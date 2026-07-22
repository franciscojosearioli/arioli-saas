@php
    $config = \App\Models\ConfiguracionSistema::instancia();
    $logoBase64 = null;
    if ($config->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($config->logo)) {
        $logoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($config->logo);
        $logoMime = mime_content_type($logoPath) ?: 'image/png';
        $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
    $institucion = $config->nombre_institucion ?: $config->nombre_sistema;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Consentimiento Informado</title>
    <style>
        @page { margin: 0; }
        html { font-family: 'Arial', sans-serif; }
        body {
            margin: 0;
            padding-top: 100px;
            padding-bottom: 100px;
            font-family: 'Arial', sans-serif;
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 70px;
            text-align: center;
            border-top: 1px solid #000;
            font-size: 10px;
            color: #555;
            padding-top: 8px;
        }
        .content {
            margin: 20px 60px;
            font-size: 12px;
            line-height: 1.6;
        }
        .titulo {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .subtitulo {
            font-size: 13px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 6px;
        }
        .datos-paciente {
            background: #f8f8f8;
            border: 1px solid #ddd;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 12px;
        }
        .datos-paciente table { width: 100%; border: none; }
        .datos-paciente td { border: none; padding: 3px 6px; font-size: 12px; }
        .firma-bloque {
            margin-top: 30px;
        }
        .firma-linea {
            display: inline-block;
            width: 200px;
            border-bottom: 1px solid #000;
            margin-top: 40px;
        }
        .firma-tabla { width: 100%; margin-top: 40px; }
        .firma-tabla td { border: none; text-align: center; font-size: 11px; padding-top: 6px; }
        .page-break { page-break-before: always; }
        ol li { margin-bottom: 6px; }
        p { margin: 6px 0; }
        .header-nombre { font-size: 18px; font-weight: bold; line-height: 60px; color: #1a1a1a; }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" style="max-height:60px;max-width:280px;">
        @else
            <span class="header-nombre">{{ $config->nombre_sistema }}</span>
        @endif
    </div>

    {{-- PIE DE PÁGINA --}}
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
            @if(count($partes) > 0)
                {{ implode(' | ', $partes) }}
            @else
                {{ $institucion }}
            @endif
        @endif
    </div>

    {{-- PÁGINA 1: CONSENTIMIENTO INFORMADO --}}
    <div class="content">
        <p class="titulo">Consentimiento Informado para Tratamiento</p>

        <div class="datos-paciente">
            <table>
                <tr>
                    <td><strong>Paciente:</strong> {{ $paciente->apellido }}, {{ $paciente->nombre }}</td>
                    <td><strong>DNI:</strong> {{ $paciente->dni ?? '—' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Institución:</strong> {{ $institucion }}</td>
                </tr>
            </table>
        </div>

        <p>Por medio del presente documento, yo <strong>{{ $paciente->nombre }} {{ $paciente->apellido }}</strong>, con DNI <strong>{{ $paciente->dni ?? '—' }}</strong>, manifiesto libre y voluntariamente mi consentimiento para recibir tratamiento en <strong>{{ $institucion }}</strong>.</p>

        <p class="subtitulo">Declaro haber sido informado/a sobre:</p>
        <ol>
            <li>La naturaleza del tratamiento propuesto, sus objetivos, metodología y duración estimada.</li>
            <li>Los posibles beneficios, riesgos y alternativas disponibles.</li>
            <li>Mi derecho a retirar este consentimiento en cualquier momento, sin que ello afecte la calidad de la atención que se me brinde.</li>
            <li>La confidencialidad de mis datos personales y la información clínica, en conformidad con la normativa vigente.</li>
            <li>El reglamento interno de la institución, cuyas normas me comprometo a conocer y respetar durante el período de tratamiento.</li>
        </ol>

        <p class="subtitulo">Compromisos del paciente:</p>
        <ol>
            <li>Participar activamente en el plan terapéutico indicado por el equipo profesional.</li>
            <li>Comunicar cualquier cambio en mi estado de salud o circunstancias que pudieran afectar el tratamiento.</li>
            <li>Respetar los horarios, normas de convivencia y disposiciones del equipo tratante.</li>
            <li>Abstenerse de conductas que pongan en riesgo la propia seguridad o la de otros miembros de la comunidad terapéutica.</li>
        </ol>

        @php $fechaFirma = $firmadoAt ?? ($paciente->consentimiento_firmado_at ? \Carbon\Carbon::parse($paciente->consentimiento_firmado_at) : null); @endphp
        @php
            $firmaImg = null;
            if (!empty($firmaBase64)) {
                $firmaImg = 'data:image/png;base64,' . $firmaBase64;
            } elseif ($paciente->consentimiento_firma_imagen && \Illuminate\Support\Facades\Storage::disk('public')->exists($paciente->consentimiento_firma_imagen)) {
                $firmaImg = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($paciente->consentimiento_firma_imagen));
            }
        @endphp
        <div class="firma-bloque">
            <table class="firma-tabla">
                <tr>
                    <td>
                        @if($firmaImg)
                        <img src="{{ $firmaImg }}" style="max-height:60px;max-width:180px;display:block;margin:0 auto 4px;"><br>
                        @else
                        <div class="firma-linea"></div><br>
                        @endif
                        Firma del Paciente<br>
                        <small>{{ $paciente->nombre }} {{ $paciente->apellido }}</small>
                    </td>
                    <td>
                        <div class="firma-linea"></div><br>
                        Aclaración / DNI<br>
                        <small>{{ $paciente->dni ?? '—' }}</small>
                    </td>
                    <td>
                        @if($fechaFirma)
                        <div style="margin-top:30px;font-size:11px;font-weight:bold;">{{ $fechaFirma->format('d/m/Y') }}</div>
                        <div style="font-size:9px;color:#555;">Firmado digitalmente</div>
                        @else
                        <div class="firma-linea"></div><br>
                        Fecha y lugar
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- PÁGINA 2: ACUSE DE RECIBO DEL REGLAMENTO --}}
    <div class="page-break"></div>

    <div class="content">
        <p class="titulo">Acuse de Recibo — Reglamento Interno</p>

        <div class="datos-paciente">
            <table>
                <tr>
                    <td><strong>Paciente:</strong> {{ $paciente->apellido }}, {{ $paciente->nombre }}</td>
                    <td><strong>DNI:</strong> {{ $paciente->dni ?? '—' }}</td>
                </tr>
            </table>
        </div>

        <p>Yo, <strong>{{ $paciente->nombre }} {{ $paciente->apellido }}</strong>, declaro haber recibido, leído y comprendido el Reglamento Interno de <strong>{{ $institucion }}</strong>.</p>

        <p>Me comprometo a cumplir con las normas establecidas en dicho reglamento durante todo el período que dure mi tratamiento en la institución.</p>

        <p>Entiendo que el incumplimiento de dichas normas puede derivar en consecuencias disciplinarias conforme lo establecido en el reglamento, las cuales han sido explicadas por el equipo profesional.</p>

        <p>Este acuse de recibo queda incorporado a mi legajo clínico como constancia de mi conocimiento y aceptación voluntaria.</p>

        <div class="firma-bloque">
            <table class="firma-tabla">
                <tr>
                    <td>
                        @if($firmaImg)
                        <img src="{{ $firmaImg }}" style="max-height:60px;max-width:180px;display:block;margin:0 auto 4px;"><br>
                        @else
                        <div class="firma-linea"></div><br>
                        @endif
                        Firma del Paciente<br>
                        <small>{{ $paciente->nombre }} {{ $paciente->apellido }}</small>
                    </td>
                    <td>
                        @if($fechaFirma)
                        <div style="margin-top:30px;font-size:11px;font-weight:bold;">{{ $fechaFirma->format('d/m/Y') }}</div>
                        <div style="font-size:9px;color:#555;">Firmado digitalmente</div>
                        @else
                        <div class="firma-linea"></div><br>
                        Fecha
                        @endif
                    </td>
                    <td>
                        <div class="firma-linea"></div><br>
                        Firma del Profesional<br>
                        <small>&nbsp;</small>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
