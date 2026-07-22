<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ficha del Paciente</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding-top: 60px;
            /* Espacio para el encabezado */
            padding-bottom: 60px;
            /* Espacio para el pie de página */
            font-family: 'Arial', sans-serif; /* Puedes cambiar 'Arial' por cualquier otra fuente estándar */
        }

        .header,
        .footer {
            position: fixed;
            left: 0;
            right: 0;
            text-align: center;
            line-height: 35px;
        }

        .header {
            top: 0;
            height: 50px;
            border-bottom: 1px solid #000;
            padding-bottom: 30px;
        }

        .footer {
            bottom: 0;
            height: 50px;
            border-top: 1px solid #000;
            padding-bottom: 5px;
        }

        .content {
            margin: 60px;
            /* Espacio para contenido principal */
        }

        .page-break {
            page-break-before: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 11px;
            /* Ajusta el tamaño de fuente si es necesario */
        }

        th {
            background-color: #f4f4f4;
        }

        .table-header {
            font-weight: bold;
        }
        td span{            
            font-size: 16px;
        }
    </style>
</head>

<body>
    @include('pdf.header')
    <div class="content">
        <h2>Ficha del Paciente</h2>
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <td colspan="3">Nombre completo<br><span>{{ $paciente->nombre }} {{ $paciente->apellido }}</span></td>
                </tr>
                <tr>
                    <td>DNI<br><span>{{ $paciente->dni }}</span></td>
                    <td>Fecha de Ingreso<br><span>{{ $paciente->ficha_admision?->fecha_ingreso ? \Carbon\Carbon::parse($paciente->ficha_admision->fecha_ingreso)->format('d/m/Y') : '' }}</span></td>
                    <td>Modalidad<br><span>{{ $paciente->ficha_admision?->modalidad ? ucfirst($paciente->ficha_admision->modalidad) : '' }}</span></td>
                </tr>
                <tr>
                    <td colspan="3">Responsable del Paciente</td>
                </tr>
                @if($paciente->padres_tutores?->padre_responsable == 'Si')
                <tr>
                    <td colspan="2">Padre<br><span>{{ $paciente->padres_tutores->padre_nombre }}</span></td>
                    <td>Telefono<br><span>{{ $paciente->padres_tutores->padre_telefono }}</span></td>
                </tr>
                @endif
                @if($paciente->padres_tutores?->madre_responsable == 'Si')
                <tr>
                    <td colspan="2">Madre<br><span>{{ $paciente->padres_tutores->madre_nombre }}</span></td>
                    <td>Telefono<br><span>{{ $paciente->padres_tutores->madre_telefono }}</span></td>
                </tr>
                @endif
                @if($paciente->padres_tutores?->tutor_responsable == 'Si')
                <tr>
                    <td colspan="2">Tutor<br><span>{{ $paciente->padres_tutores->tutor_nombre }}</span></td>
                    <td>Telefono<br><span>{{ $paciente->padres_tutores->tutor_telefono }}</span></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @include('pdf.footer')
</body>

</html>