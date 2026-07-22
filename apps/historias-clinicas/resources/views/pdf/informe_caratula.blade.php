<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $tipoNombre }}</title>
    <style>
        @page { margin: 0; }

        body {
            margin: 0;
            padding-top: 80px;
            padding-bottom: 90px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .hdr, .ftr { position: fixed; left: 0; right: 0; text-align: center; }
        .hdr { top: 0; height: 65px; border-bottom: 2px solid #1a3561; }
        .ftr { bottom: 0; height: 45px; border-top: 1px solid #ccc; font-size: 8px; color: #666; padding-top: 8px; }

        /* ── Cover content centered in page ── */
        .cover {
            margin: 0 36px;
            padding-top: 110px;
            text-align: center;
        }

        .section-label {
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 18px;
        }

        .section-band {
            background: #1a3561;
            color: #fff;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 22px 36px;
            margin-bottom: 0;
        }

        .section-band-accent {
            background: #142a4f;
            height: 5px;
            margin-bottom: 28px;
        }

        .patient-block {
            margin-bottom: 24px;
        }
        .patient-name {
            font-size: 16px;
            font-weight: bold;
            color: #111;
            margin-bottom: 5px;
        }
        .patient-meta {
            font-size: 11px;
            color: #666;
        }

        .divider {
            width: 50px;
            height: 3px;
            background: #1a3561;
            margin: 18px auto;
        }

        .count-wrap {
            display: inline-block;
            background: #eef1f8;
            border: 1px solid #c8d0e0;
            padding: 6px 20px;
            font-size: 11px;
            font-weight: bold;
            color: #1a3561;
            letter-spacing: .5px;
            margin-bottom: 10px;
        }

        .date-range {
            font-size: 9.5px;
            color: #888;
        }
    </style>
</head>
<body>

@include('pdf.header')

<div class="cover">
    <div class="section-label">Historia Clínica &nbsp;·&nbsp; Sección de Informes</div>
    <div class="section-band">{{ strtoupper($tipoNombre) }}</div>
    <div class="section-band-accent"></div>

    <div class="patient-block">
        <div class="patient-name">{{ $paciente->apellido }}, {{ $paciente->nombre }}</div>
        <div class="patient-meta">DNI: {{ $paciente->dni }}</div>
    </div>

    <div class="divider"></div>

    <div class="count-wrap">
        {{ $count }} informe{{ $count != 1 ? 's' : '' }} incluido{{ $count != 1 ? 's' : '' }}
    </div>

    @if($fechaDesde && $fechaHasta)
    <div class="date-range">
        @if($fechaDesde === $fechaHasta)
            Fecha: {{ $fechaDesde }}
        @else
            Período: {{ $fechaDesde }} — {{ $fechaHasta }}
        @endif
    </div>
    @endif
</div>

@include('pdf.footer')
</body>
</html>
