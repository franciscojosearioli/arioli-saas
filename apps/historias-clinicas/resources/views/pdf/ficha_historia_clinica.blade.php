<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ficha de Historia Clínica</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding-top: 60px; /* Espacio para el encabezado */
            padding-bottom: 60px; /* Espacio para el pie de página */
            font-family: 'Arial', sans-serif; /* Puedes cambiar 'Arial' por cualquier otra fuente estándar */
        }
        .header, .footer {
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
            margin: 60px; /* Espacio para contenido principal */
        }
        .page-break {
            page-break-before: always;
        }
        .centered-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 120px); /* Ajusta para considerar encabezado y pie de página */
            text-align: center;
            padding: 60px; /* Ajusta el padding si es necesario */
        }
        .centered-content h1, .centered-content p {
            margin: 10px 0;
        }
        .centered-content p {
            margin: 5px 0;
            font-size: 24px;
        }
        .content p {
            margin: 0px;
            padding: 10px;
            font-size: 24px;
        }
        h1{
            font-size: 50px;
        }

    </style>
</head>
<body>
    <div class="centered-content" style="margin-bottom:0px;">
        <h1><u>HISTORIA CLINICA</u></h1>
        <h2>DATOS PACIENTE</h2>
    </div>
            
    <div class="content" style="margin-top:0px;margin-bottom:40px;">
            <p>Paciente: {{ $paciente->nombre }} {{ $paciente->apellido }}</p>
            <p>DNI: {{ $paciente->dni }}</p>
            <p>Fecha de Ingreso: {{ $paciente->ficha_admision?->fecha_ingreso ? \Carbon\Carbon::parse($paciente->ficha_admision->fecha_ingreso)->format('d/m/Y') : '' }}</p>
            <p>Modalidad: {{ $paciente->ficha_admision?->modalidad ? ucfirst($paciente->ficha_admision->modalidad) : '' }}</p>
            <p>Obra Social: 
            @if($paciente->obra_social != null)
            {{ $paciente->obra_social }}
            @else
            No tiene
            @endif</p>
            @if($paciente->n_afiliado != null)
            <p>N° de Afiliado: {{ $paciente->n_afiliado }}</p>
            @endif
            <p>Domicilio: {{ $paciente->calle }} {{ $paciente->calle_numero }} {{ $paciente->piso }} {{ $paciente->dpto }}
            @if($paciente->localidad != null)
            , {{ $paciente->localidad }}
            @endif
            @if($paciente->provincia != null)
            , {{ $paciente->provincia }}
            @endif
            </p>
            
        </div>
        <div style="text-align: center;">
            <h2>DATOS RESPONSABLE</h2>
            </div>
            <div class="content" style="margin-top:0px;">
            <hr>
            @if($paciente->padres_tutores?->padre_responsable == 'Si')
            <p>Nombre: {{ $paciente->padres_tutores->padre_nombre }}</p>
            <p>Teléfono: {{ $paciente->padres_tutores->padre_telefono }}</p>
            <hr>
            @endif

            @if($paciente->padres_tutores?->madre_responsable == 'Si')
            <p>Nombre: {{ $paciente->padres_tutores->madre_nombre }}</p>
            <p>Teléfono: {{ $paciente->padres_tutores->madre_telefono }}</p>
            <hr>
            @endif

            @if($paciente->padres_tutores?->tutor_responsable == 'Si')
            <p>Nombre: {{ $paciente->padres_tutores->tutor_nombre }}</p>
            <p>Teléfono: {{ $paciente->padres_tutores->tutor_telefono }}</p>
            <hr>
            @endif
        </div>
        
</body>
</html>