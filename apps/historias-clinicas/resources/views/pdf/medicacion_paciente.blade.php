<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Prescripción — {{ $paciente->apellido }}, {{ $paciente->nombre }}</title>
    <style>
        @page { margin: 0; }

        body {
            margin: 0;
            padding-top: 75px;
            padding-bottom: 55px;
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #111;
            line-height: 1.4;
        }

        .hdr, .ftr { position: fixed; left: 0; right: 0; text-align: center; }
        .hdr { top: 0; height: 65px; border-bottom: 2px solid #1a3561; }
        .ftr { bottom: 0; height: 45px; border-top: 1px solid #ccc; font-size: 8px; color: #666; padding-top: 8px; }

        .content { margin: 0 36px; padding-top: 6px; }

        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #1a3561;
            border-bottom: 2px solid #1a3561;
            padding: 8px 0 6px;
            margin-bottom: 10px;
        }
        .doc-subtitle { text-align: center; font-size: 10px; color: #555; margin-top: -6px; margin-bottom: 10px; }

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

        .dg { width: 100%; border-collapse: collapse; margin: 0; }
        .dg td { padding: 4px 7px; border: 1px solid #ccc; font-size: 10px; vertical-align: top; }
        .dg .lbl { font-weight: bold; background: #f0f3f8; color: #1a3561; white-space: nowrap; width: 35%; }
        .dg .val { color: #111; }

        .tbl { width: 100%; border-collapse: collapse; margin: 6px 0; font-size: 10px; }
        .tbl th {
            background: #dde3ee;
            color: #1a3561;
            font-weight: bold;
            padding: 5px 7px;
            border: 1px solid #bbb;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tbl td { padding: 5px 7px; border: 1px solid #ccc; font-size: 10px; vertical-align: middle; }
        .tbl tbody tr:nth-child(even) td { background: #f9fafc; }
        .tbl .horario-cell {
            background: #eef1f8;
            font-weight: bold;
            color: #1a3561;
            text-transform: capitalize;
            vertical-align: middle;
        }
    </style>
</head>
<body>

@include('pdf.header')

<div class="content">

    <div class="doc-title">Prescripción Médica</div>
    <div class="doc-subtitle">
        Paciente: <strong>{{ $paciente->apellido }}, {{ $paciente->nombre }}</strong>
        &nbsp;·&nbsp; DNI: <strong>{{ $paciente->dni }}</strong>
    </div>

    {{-- Datos del paciente --}}
    <div class="sec-title">Datos del Paciente</div>
    <table class="dg">
        <tr>
            <td class="lbl">Apellido y Nombre</td>
            <td class="val">{{ $paciente->apellido }}, {{ $paciente->nombre }}</td>
            <td class="lbl">DNI</td>
            <td class="val">{{ $paciente->dni }}</td>
        </tr>
        <tr>
            <td class="lbl">Fecha de Nacimiento</td>
            <td class="val">{{ $paciente->fecha_nac ? \Carbon\Carbon::parse($paciente->fecha_nac)->format('d/m/Y') : '—' }}</td>
            <td class="lbl">Obra Social / N° Afil.</td>
            <td class="val">{{ $paciente->obra_social ?? '—' }}{{ $paciente->n_afiliado ? ' / ' . $paciente->n_afiliado : '' }}</td>
        </tr>
        <tr>
            <td class="lbl">Fecha de Prescripción</td>
            <td class="val" colspan="3">
                <strong>{{ $fecha ? \Carbon\Carbon::parse($fecha)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}</strong>
            </td>
        </tr>
    </table>

    {{-- Medicamentos --}}
    <div class="sec-title">Esquema de Prescripción</div>

    @php
        $ordenHorarios = ['mañana' => 1, 'mediodía' => 2, 'tarde' => 3, 'noche' => 4];
        $medicacionesOrdenadas = $medicaciones->sortBy(function($m) use ($ordenHorarios) {
            return $ordenHorarios[$m->horario] ?? 99;
        })->groupBy('horario');
    @endphp

    @if($medicacionesOrdenadas->count() > 0)
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:18%">{{ trans('cruds.medicacion.fields.horario') }}</th>
                <th>{{ trans('cruds.medicacion.fields.medicamento') }}</th>
                <th style="width:25%">{{ trans('cruds.medicacion.fields.cantidad') }} / Unidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicacionesOrdenadas as $horario => $medsHorario)
                @foreach($medsHorario as $i => $medicacion)
                <tr>
                    @if($i == 0)
                    <td class="horario-cell" rowspan="{{ $medsHorario->count() }}">{{ ucfirst($horario) }}</td>
                    @endif
                    <td>{{ $medicacion->medicamento }}</td>
                    <td>{{ $medicacion->cantidad }} {{ $medicacion->unidad }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
    @else
    <div style="padding:8px 7px; border:1px solid #ddd; color:#888; font-style:italic; font-size:9px;">
        Sin medicaciones registradas para esta fecha.
    </div>
    @endif


</div>

@include('pdf.footer')
</body>
</html>
