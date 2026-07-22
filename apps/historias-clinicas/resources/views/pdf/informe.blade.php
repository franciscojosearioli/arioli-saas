<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $tipo ?? 'Informe' }} — {{ $paciente ?? '' }}</title>
    <style>
        @page { margin: 0; }

        body {
            margin: 0;
            padding-top: 75px;
            padding-bottom: 55px;
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #111;
            line-height: 1.5;
        }

        .hdr, .ftr { position: fixed; left: 0; right: 0; text-align: center; }
        .hdr { top: 0; height: 65px; border-bottom: 2px solid #1a3561; }
        .ftr { bottom: 0; height: 45px; border-top: 1px solid #ccc; font-size: 8px; color: #666; padding-top: 8px; }

        .content { margin: 0 36px; padding-top: 8px; }

        /* ── Document heading ── */
        .doc-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #1a3561;
            border-bottom: 2px solid #1a3561;
            padding: 10px 0 7px;
            margin-bottom: 4px;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-bottom: 12px;
        }

        /* ── Section headers ── */
        .sec-title {
            background: #1a3561;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 8px;
            margin: 10px 0 0;
        }

        /* ── Label-value grid ── */
        .dg { width: 100%; border-collapse: collapse; margin: 0; }
        .dg td { padding: 4px 7px; border: 1px solid #ccc; font-size: 10px; vertical-align: top; }
        .dg .lbl { font-weight: bold; background: #f0f3f8; color: #1a3561; white-space: nowrap; width: 30%; }
        .dg .val { color: #111; }

        /* ── Rich text content block ── */
        .inf-body {
            border: 1px solid #ccc;
            padding: 12px 14px;
            font-size: 11px;
            line-height: 1.7;
            color: #111;
            background: #fff;
            min-height: 160px;
        }
        .inf-body p  { margin: 0 0 8px; }
        .inf-body h1 { font-size: 14px; color: #1a3561; margin: 10px 0 5px; }
        .inf-body h2 { font-size: 12px; color: #1a3561; margin: 10px 0 5px; }
        .inf-body h3 { font-size: 11px; color: #1a3561; margin: 8px 0 4px; }
        .inf-body ul, .inf-body ol { margin: 6px 0 8px 16px; padding: 0; }
        .inf-body li { margin-bottom: 3px; }
        .inf-body strong, .inf-body b { font-weight: bold; }
        .inf-body em, .inf-body i { font-style: italic; }

        /* ── Signature block ── */
        .sig-table { width: 100%; border-collapse: collapse; margin-top: 50px; }
        .sig-table td { border: none; padding: 0; text-align: center; vertical-align: bottom; }
        .sig-line { border-top: 1px solid #444; width: 75%; margin: 30px auto 4px; }
        .sig-label { font-size: 8.5px; color: #555; }

        /* ── Watermark-style place-date ── */
        .place-date {
            text-align: right;
            font-size: 10px;
            color: #444;
            padding: 4px 0 10px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 10px;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

@include('pdf.header')

<div class="content">

    <div class="doc-title">{{ $tipo ?? 'Informe Clínico' }}</div>
    <div class="doc-subtitle">Documento de carácter confidencial — uso exclusivo del equipo profesional</div>

    <div class="place-date">
        {{ $fecha ?? \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}
        &nbsp;—&nbsp; Concepción del Uruguay, Entre Ríos
    </div>

    {{-- Datos del Paciente --}}
    <div class="sec-title">Datos del Paciente</div>
    <table class="dg">
        <tr>
            <td class="lbl">Apellido y Nombre</td>
            <td class="val" colspan="3">{{ $paciente ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">DNI</td>
            <td class="val">{{ $pacienteDNI ?? '—' }}</td>
            @if(!empty($codigo_cie10))
            <td class="lbl" style="width:22%;">Código CIE-10 / DSM-5</td>
            <td class="val" style="width:25%;">{{ $codigo_cie10 }}</td>
            @else
            <td class="lbl" style="width:22%;">Fecha del informe</td>
            <td class="val" style="width:25%;">{{ $fecha ?? '—' }}</td>
            @endif
        </tr>
        @if(!empty($diagnostico))
        <tr>
            <td class="lbl">Diagnóstico / Motivo</td>
            <td class="val" colspan="3">{{ $diagnostico }}</td>
        </tr>
        @endif
        @if(!empty($profesional))
        <tr>
            <td class="lbl">Profesional responsable</td>
            <td class="val" colspan="3">{{ $profesional }}</td>
        </tr>
        @endif
    </table>

    {{-- Contenido del Informe --}}
    <div class="sec-title">Contenido del Informe</div>
    <div class="inf-body">
        {!! $informe ?? '' !!}
    </div>

    {{-- Firma --}}
    @if(!empty($firmaData) && $firmaData['nombre'])
    <table class="sig-table" style="margin-top:40px;">
        <tr>
            <td style="text-align:center; vertical-align:bottom; width:50%;">
                @if(!empty($firmaData['base64']))
                <img src="data:image/png;base64,{{ $firmaData['base64'] }}"
                     style="max-height:70px; max-width:220px; margin-bottom:4px; display:block; margin:0 auto 4px;">
                @endif
                <div style="border-top:1px solid #444; width:80%; margin:6px auto 4px;"></div>
                <div style="font-size:9px; font-weight:bold; color:#1a3561;">{{ $firmaData['nombre'] }}</div>
                <div style="font-size:8px; color:#555;">DNI: {{ $firmaData['dni'] ?? '—' }}</div>
                @if(!empty($firmaData['matricula']))
                <div style="font-size:8px; color:#555;">M.P. {{ $firmaData['matricula'] }}</div>
                @endif
                @if(!empty($firmaData['especialidad']))
                <div style="font-size:8px; color:#888;">{{ $firmaData['especialidad'] }}</div>
                @endif
                <div style="font-size:7.5px; color:#aaa; margin-top:4px;">Firmado digitalmente</div>
            </td>
            <td style="width:50%;"></td>
        </tr>
    </table>
    @else
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-line">&#160;</div>
                <div class="sig-label">Firma y sello profesional</div>
            </td>
            <td>
                <div class="sig-line">&#160;</div>
                <div class="sig-label">Matrícula / Especialidad</div>
            </td>
            <td>
                <div class="sig-line">&#160;</div>
                <div class="sig-label">Aclaración</div>
            </td>
        </tr>
    </table>
    @endif

</div>

@include('pdf.footer')
</body>
</html>
