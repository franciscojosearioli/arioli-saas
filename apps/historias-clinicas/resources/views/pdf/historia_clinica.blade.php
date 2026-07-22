<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Historia Clínica — {{ $paciente->apellido }}, {{ $paciente->nombre }}</title>
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

        /* ── Fixed header/footer ── */
        .hdr, .ftr {
            position: fixed;
            left: 0; right: 0;
            text-align: center;
        }
        .hdr { top: 0; height: 65px; border-bottom: 2px solid #1a3561; }
        .ftr {
            bottom: 0; height: 45px;
            border-top: 1px solid #ccc;
            font-size: 8px;
            color: #666;
            padding-top: 8px;
        }

        /* ── Content wrapper ── */
        .content { margin: 0 36px; padding-top: 6px; }

        /* ── Document title ── */
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
        .doc-subtitle {
            text-align: center;
            font-size: 10px;
            color: #555;
            margin-top: -6px;
            margin-bottom: 10px;
        }

        /* ── Section titles ── */
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

        /* ── Data grid (label | value) ── */
        .dg {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .dg td {
            padding: 4px 7px;
            border: 1px solid #ccc;
            font-size: 10px;
            vertical-align: top;
        }
        .dg .lbl {
            font-weight: bold;
            background: #f0f3f8;
            color: #1a3561;
            white-space: nowrap;
            width: 38%;
        }
        .dg .val { color: #111; }
        .dg .lbl-sm { font-weight: bold; background: #f0f3f8; color: #1a3561; width: 28%; }

        /* ── Standard bordered table ── */
        .tbl {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 10px;
        }
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
        .tbl td {
            padding: 4px 7px;
            border: 1px solid #ccc;
            font-size: 10px;
            vertical-align: top;
        }
        .tbl tbody tr:nth-child(even) td { background: #f9fafc; }

        /* ── Inline note (sin datos) ── */
        .no-data {
            font-size: 9px;
            color: #888;
            font-style: italic;
            padding: 4px 7px;
            border: 1px solid #ddd;
            background: #fafafa;
        }

        /* ── Page break ── */
        .page-break { page-break-before: always; }
        .tc { text-align: center; }
        .tr { text-align: right; }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════
     PÁGINA 1 — ADMISIÓN · DATOS PERSONALES · RESPONSABLES
     ═══════════════════════════════════════════════════════ --}}

@include('pdf.header')

<div class="content">

    <div class="doc-title">Historia Clínica</div>
    <div class="doc-subtitle">
        Paciente: <strong>{{ $paciente->apellido }}, {{ $paciente->nombre }}</strong>
        &nbsp;·&nbsp; DNI: <strong>{{ $paciente->dni }}</strong>
    </div>

    {{-- FICHA DE ADMISIÓN --}}
    <div class="sec-title">Ficha de Admisión</div>
    <table class="dg">
        <tr>
            <td class="lbl">Fecha de Ingreso</td>
            <td class="val">{{ $paciente->ficha_admision?->fecha_ingreso ? \Carbon\Carbon::parse($paciente->ficha_admision->fecha_ingreso)->format('d/m/Y') : '—' }}</td>
            <td class="lbl">Modalidad</td>
            <td class="val">{{ $paciente->ficha_admision?->modalidad ? ucfirst($paciente->ficha_admision->modalidad) : '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Fecha de Egreso</td>
            <td class="val">{{ $paciente->ficha_admision?->fecha_egreso ? \Carbon\Carbon::parse($paciente->ficha_admision->fecha_egreso)->format('d/m/Y') : '—' }}</td>
            <td class="lbl">Tipo de Egreso</td>
            <td class="val">{{ $paciente->ficha_admision?->tipo_egreso ?? '—' }}</td>
        </tr>
    </table>

    {{-- DATOS PERSONALES --}}
    <div class="sec-title">Datos Personales</div>
    <table class="dg">
        <tr>
            <td class="lbl">Apellido y Nombre</td>
            <td class="val" colspan="3">{{ $paciente->apellido }}, {{ $paciente->nombre }}</td>
        </tr>
        <tr>
            <td class="lbl">DNI</td>
            <td class="val">{{ $paciente->dni }}</td>
            <td class="lbl">Fecha de Nacimiento</td>
            <td class="val">{{ $paciente->fecha_nac ? \Carbon\Carbon::parse($paciente->fecha_nac)->format('d/m/Y') : '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Edad</td>
            <td class="val">{{ $paciente->edad ?? '—' }}</td>
            <td class="lbl">Sexo</td>
            <td class="val">
                @if($paciente->sexo == 'F') Femenino
                @elseif($paciente->sexo == 'M') Masculino
                @else {{ $paciente->sexo ?? '—' }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="lbl">Estado Civil</td>
            <td class="val">{{ $paciente->estado_civil ?? '—' }}</td>
            <td class="lbl">Obra Social</td>
            <td class="val">{{ $paciente->obra_social ?? 'No tiene' }}</td>
        </tr>
        <tr>
            <td class="lbl">N° de Afiliado</td>
            <td class="val">{{ $paciente->n_afiliado ?? '—' }}</td>
            <td class="lbl">Provincia</td>
            <td class="val">{{ $paciente->provincia ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Localidad</td>
            <td class="val">{{ $paciente->localidad ?? '—' }}</td>
            <td class="lbl">Domicilio</td>
            <td class="val">{{ trim(($paciente->calle ?? '') . ' ' . ($paciente->calle_numero ?? '') . ' ' . ($paciente->piso ?? '') . ' ' . ($paciente->dpto ?? '')) ?: '—' }}</td>
        </tr>
    </table>

    {{-- DATOS DE RESPONSABLES --}}
    <div class="sec-title">Datos de Responsables</div>
    @php
        $tieneResponsables = $paciente->padres_tutores &&
            ($paciente->padres_tutores->padre_responsable == 'Si' ||
             $paciente->padres_tutores->madre_responsable == 'Si' ||
             $paciente->padres_tutores->tutor_responsable == 'Si');
    @endphp
    @if($tieneResponsables)
    <table class="tbl">
        <thead>
            <tr><th style="width:20%">Vínculo</th><th>Nombre y Apellido</th><th style="width:25%">Teléfono</th></tr>
        </thead>
        <tbody>
            @if($paciente->padres_tutores->padre_responsable == 'Si')
            <tr>
                <td><strong>Padre</strong></td>
                <td>{{ $paciente->padres_tutores->padre_nombre ?? '—' }}</td>
                <td>{{ $paciente->padres_tutores->padre_telefono ?? '—' }}</td>
            </tr>
            @endif
            @if($paciente->padres_tutores->madre_responsable == 'Si')
            <tr>
                <td><strong>Madre</strong></td>
                <td>{{ $paciente->padres_tutores->madre_nombre ?? '—' }}</td>
                <td>{{ $paciente->padres_tutores->madre_telefono ?? '—' }}</td>
            </tr>
            @endif
            @if($paciente->padres_tutores->tutor_responsable == 'Si')
            <tr>
                <td><strong>Tutor</strong></td>
                <td>{{ $paciente->padres_tutores->tutor_nombre ?? '—' }}</td>
                <td>{{ $paciente->padres_tutores->tutor_telefono ?? '—' }}</td>
            </tr>
            @endif
        </tbody>
    </table>
    @else
    <div class="no-data">Sin responsables registrados.</div>
    @endif

</div>
@include('pdf.footer')

{{-- ═══════════════════════════════════════════════════════
     PÁGINA 2 — DATOS FAMILIARES
     ═══════════════════════════════════════════════════════ --}}
<div class="page-break"></div>
@include('pdf.header')

<div class="content">

    <div class="doc-title">Historia Clínica — Datos Familiares</div>
    <div class="doc-subtitle">{{ $paciente->apellido }}, {{ $paciente->nombre }} &nbsp;·&nbsp; DNI: {{ $paciente->dni }}</div>

    {{-- CÓNYUGE --}}
    @if($paciente->estado_civil == 'Casado')
    <div class="sec-title">Cónyuge</div>
    @if($paciente->conyugue)
    <table class="dg">
        <tr>
            <td class="lbl">Apellido y Nombre</td>
            <td class="val">{{ ($paciente->conyugue->conyugue_apellido ?? '') . ', ' . ($paciente->conyugue->conyugue_nombre ?? '') }}</td>
            <td class="lbl">Edad</td>
            <td class="val">{{ $paciente->conyugue->conyugue_edad ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Domicilio</td>
            <td class="val" colspan="3">{{ $paciente->conyugue->conyugue_domicilio ?? '—' }}</td>
        </tr>
    </table>
    @else
    <div class="no-data">Sin datos de cónyuge registrados.</div>
    @endif
    @endif

    {{-- HIJOS --}}
    <div class="sec-title">Hijos</div>
    @if($paciente->familiar_hijos == 'Si' && $paciente->hijos && $paciente->hijos->count() > 0)
    <table class="tbl">
        <thead>
            <tr><th>Nombre</th><th style="width:15%">Edad</th><th style="width:30%">Tutor / Responsable</th></tr>
        </thead>
        <tbody>
            @foreach($paciente->hijos as $hijo)
            <tr>
                <td>{{ $hijo->hijos_nombre ?? '—' }}</td>
                <td>{{ $hijo->hijos_edad ?? '—' }}</td>
                <td>{{ $hijo->hijos_tutor ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">Sin hijos registrados.</div>
    @endif

    {{-- HERMANOS --}}
    <div class="sec-title">Hermanos</div>
    @if($paciente->familiar_hermanos == 'Si' && $paciente->hermanos && $paciente->hermanos->count() > 0)
    <table class="tbl">
        <thead>
            <tr><th>Nombre</th><th style="width:15%">Edad</th><th style="width:20%">Convive</th></tr>
        </thead>
        <tbody>
            @foreach($paciente->hermanos as $hermano)
            <tr>
                <td>{{ $hermano->hermanos_nombre ?? '—' }}</td>
                <td>{{ $hermano->hermanos_edad ?? '—' }}</td>
                <td>{{ $hermano->hermanos_convive ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">Sin hermanos registrados.</div>
    @endif

</div>
@include('pdf.footer')

{{-- ═══════════════════════════════════════════════════════
     PÁGINA 3 — EDUCACIÓN · TRABAJO · HISTORIAL
     ═══════════════════════════════════════════════════════ --}}
<div class="page-break"></div>
@include('pdf.header')

<div class="content">

    <div class="doc-title">Historia Clínica — Educación y Situación Laboral</div>
    <div class="doc-subtitle">{{ $paciente->apellido }}, {{ $paciente->nombre }} &nbsp;·&nbsp; DNI: {{ $paciente->dni }}</div>

    {{-- NIVEL DE INSTRUCCIÓN --}}
    <div class="sec-title">Nivel de Instrucción</div>
    @if($paciente->educacion)
    <table class="tbl">
        <thead>
            <tr>
                <th style="width:18%">Nivel</th>
                <th>Completa</th>
                <th>Incompleta</th>
                <th>Último año</th>
                <th>Expulsado</th>
                <th>Interrumpido</th>
                <th>Cambios</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['primaria', 'secundaria', 'terciaria', 'facultad'] as $nivel)
            <tr>
                <td><strong>{{ trans('cruds.paciente.fields.' . $nivel) }}</strong></td>
                <td class="tc">{{ $paciente->educacion->{$nivel . '_completa'} ?? '—' }}</td>
                <td class="tc">{{ $paciente->educacion->{$nivel . '_incompleta'} ?? '—' }}</td>
                <td class="tc">{{ $paciente->educacion->{$nivel . '_ultimo_anio'} ?? '—' }}</td>
                <td class="tc">{{ $paciente->educacion->{$nivel . '_expulsado'} ?? '—' }}</td>
                <td class="tc">{{ $paciente->educacion->{$nivel . '_interrumpido'} ?? '—' }}</td>
                <td class="tc">{{ $paciente->educacion->{$nivel . '_cambios'} ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($paciente->educacion->observaciones)
    <table class="dg" style="margin-top:4px;">
        <tr>
            <td class="lbl" style="width:20%">Observaciones</td>
            <td class="val">{{ $paciente->educacion->observaciones }}</td>
        </tr>
    </table>
    @endif
    @else
    <div class="no-data">Sin datos de instrucción registrados.</div>
    @endif

    {{-- DATOS LABORALES --}}
    <div class="sec-title">Situación Laboral</div>
    @if($paciente->laboral)
    <table class="dg">
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.actividad_laboral') }}</td>
            <td class="val">{{ $paciente->laboral->actividad_laboral ?? '—' }}</td>
            <td class="lbl">Antigüedad</td>
            <td class="val">{{ $paciente->laboral->actividad_laboral != 'No' ? ($paciente->laboral->antiguedad_laboral ?? '—') : '—' }}</td>
        </tr>
        @if($paciente->laboral->actividad_laboral != 'No')
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.empresa_laboral') }}</td>
            <td class="val">{{ $paciente->laboral->empresa_laboral ?? '—' }}</td>
            <td class="lbl">{{ trans('cruds.paciente.fields.cargo_laboral') }}</td>
            <td class="val">{{ $paciente->laboral->cargo_laboral ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.antecedente_laboral') }}</td>
            <td class="val" colspan="3">{{ $paciente->laboral->antecedente_laboral ?? '—' }}</td>
        </tr>
        @endif
    </table>
    @else
    <div class="no-data">Sin datos laborales registrados.</div>
    @endif

    {{-- HISTORIAL DE TRATAMIENTOS --}}
    <div class="sec-title">Historial de Tratamientos</div>
    <table class="dg" style="margin-bottom:4px;">
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.historial_tratamiento') }}</td>
            <td class="val">{{ $paciente->historial_tratamiento ?? '—' }}</td>
        </tr>
    </table>
    @if($paciente->historial_tratamiento == 'Si' && $paciente->historial_tratamientos && $paciente->historial_tratamientos->count() > 0)
    <table class="tbl">
        <thead>
            <tr><th>Lugar / Institución</th><th style="width:30%">Duración</th></tr>
        </thead>
        <tbody>
            @foreach($paciente->historial_tratamientos as $historial)
            <tr>
                <td>{{ $historial->lugar ?? '—' }}</td>
                <td>{{ $historial->duracion ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

</div>
@include('pdf.footer')

{{-- ═══════════════════════════════════════════════════════
     PÁGINA 4 — PROBLEMÁTICA · DATOS ADICIONALES
     ═══════════════════════════════════════════════════════ --}}
<div class="page-break"></div>
@include('pdf.header')

<div class="content">

    <div class="doc-title">Historia Clínica — Problemática y Datos Adicionales</div>
    <div class="doc-subtitle">{{ $paciente->apellido }}, {{ $paciente->nombre }} &nbsp;·&nbsp; DNI: {{ $paciente->dni }}</div>

    {{-- PROBLEMÁTICA --}}
    <div class="sec-title">Problemática</div>
    @if($paciente->problematica)
    <table class="dg">
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.problematica') }}</td>
            <td class="val">{{ $paciente->problematica->problematica ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.problematica_detalles') }}</td>
            <td class="val">{{ $paciente->problematica->problematica_detalles ?? '—' }}</td>
        </tr>
    </table>
    @else
    <div class="no-data">Sin datos de problemática registrados.</div>
    @endif

    {{-- DATOS ADICIONALES --}}
    <div class="sec-title">Datos Adicionales</div>
    @if($paciente->datos_adicionales)
    @php $da = $paciente->datos_adicionales; @endphp
    <table class="dg">
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.abuso_sexual') }}</td>
            <td class="val">{{ $da->abuso_sexual ?? '—' }}</td>
            <td class="lbl">{{ trans('cruds.paciente.fields.sobredosis') }}</td>
            <td class="val">{{ $da->sobredosis ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.antecedentes_legales') }}</td>
            <td class="val">{{ $da->antecedentes_legales ?? '—' }}</td>
            <td class="lbl">{{ trans('cruds.paciente.fields.analfabeto') }}</td>
            <td class="val">{{ $da->analfabeto ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.padres_separados') }}</td>
            <td class="val">{{ $da->padres_separados ?? '—' }}</td>
            <td class="lbl">{{ trans('cruds.paciente.fields.privado_libertad') }}</td>
            <td class="val">{{ $da->privado_libertad ?? '—' }}</td>
        </tr>
        @if(($da->privado_libertad ?? '') != 'No' && $da->privado_libertad)
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.tiempo_privado_libertad') }}</td>
            <td class="val" colspan="3">{{ $da->tiempo_privado_libertad ?? '—' }}</td>
        </tr>
        @endif
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.enfermedad_cronica') }}</td>
            <td class="val">{{ $da->enfermedad_cronica ?? '—' }}</td>
            <td class="lbl">{{ trans('cruds.paciente.fields.alergia') }}</td>
            <td class="val">{{ $da->alergia ?? '—' }}</td>
        </tr>
        @if(($da->enfermedad_cronica ?? '') != 'No' && $da->enfermedad_cronica)
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.enfermedad_cronica_detalles') }}</td>
            <td class="val" colspan="3">{{ $da->enfermedad_cronica_detalles ?? '—' }}</td>
        </tr>
        @endif
        @if(($da->alergia ?? '') != 'No' && $da->alergia)
        <tr>
            <td class="lbl">{{ trans('cruds.paciente.fields.alergia_detalles') }}</td>
            <td class="val" colspan="3">{{ $da->alergia_detalles ?? '—' }}</td>
        </tr>
        @endif
    </table>
    @else
    <div class="no-data">Sin datos adicionales registrados.</div>
    @endif


</div>
@include('pdf.footer')

</body>
</html>
