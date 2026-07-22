<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Esquema de Prescripción — Pacientes Activos</title>
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
            margin-bottom: 4px;
        }
        .doc-subtitle { text-align: center; font-size: 9px; color: #666; margin-bottom: 10px; }

        .paciente-header {
            background: #1a3561;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            padding: 5px 8px;
            margin-top: 12px;
        }
        .paciente-header span { font-weight: normal; font-size: 9px; opacity: .85; }

        .tbl { width: 100%; border-collapse: collapse; margin: 0; font-size: 10px; }
        .tbl th {
            background: #dde3ee;
            color: #1a3561;
            font-weight: bold;
            padding: 4px 7px;
            border: 1px solid #bbb;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tbl td { padding: 4px 7px; border: 1px solid #ccc; font-size: 10px; vertical-align: middle; }
        .tbl tbody tr:nth-child(even) td { background: #f9fafc; }
        .tbl .horario-cell {
            background: #eef1f8;
            font-weight: bold;
            color: #1a3561;
            text-transform: capitalize;
            vertical-align: middle;
            width: 16%;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

@include('pdf.header')

<div class="content">

    <div class="doc-title">Esquema de Prescripción</div>
    <div class="doc-subtitle">
        Pacientes Activos &nbsp;·&nbsp; Fecha de emisión: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

    @php
        $ordenHorarios = ['mañana' => 1, 'mediodía' => 2, 'tarde' => 3, 'noche' => 4];
        $medicacionesPorPaciente = $medicaciones->groupBy('paciente_id');
        $contador = 0;
    @endphp

    @foreach($medicacionesPorPaciente as $pacienteId => $medsPaciente)
        @php
            $pac = $medsPaciente->first()->paciente;
            $medsOrdenadas = $medsPaciente->groupBy('horario')
                ->sortBy(fn($items, $key) => $ordenHorarios[$key] ?? 99);
        @endphp

        @if($contador > 0)
        <div class="page-break"></div>
        @endif

        <div class="paciente-header">
            {{ $pac->apellido }}, {{ $pac->nombre }}
            <span>&nbsp;·&nbsp; DNI: {{ $pac->dni }}{{ $pac->obra_social ? ' &nbsp;·&nbsp; O.S.: ' . $pac->obra_social : '' }}</span>
        </div>

        <table class="tbl">
            <thead>
                <tr>
                    <th style="width:16%">Horario</th>
                    <th>Medicamento</th>
                    <th style="width:25%">Dosis / Unidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medsOrdenadas as $horario => $medsHorario)
                    @foreach($medsHorario as $i => $med)
                    <tr>
                        @if($i == 0)
                        <td class="horario-cell" rowspan="{{ $medsHorario->count() }}">{{ ucfirst($horario) }}</td>
                        @endif
                        <td>{{ $med->medicamento }}</td>
                        <td>{{ $med->cantidad }} {{ $med->unidad }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        @php $contador++; @endphp
    @endforeach

    @if($medicacionesPorPaciente->count() === 0)
    <div style="padding:12px 7px; border:1px solid #ddd; color:#888; font-style:italic; font-size:9px; margin-top:10px;">
        No hay medicaciones activas registradas.
    </div>
    @endif

</div>

@include('pdf.footer')
</body>
</html>
