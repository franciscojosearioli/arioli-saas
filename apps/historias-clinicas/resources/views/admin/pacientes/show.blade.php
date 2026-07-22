@extends('layouts.admin')
@section('content')

            
<div class="d-flex justify-content-between align-items-center">
            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-default" href="{{ route('admin.paciente.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
        <a href="{{ route('admin.paciente.consentimientoPaciente', $Paciente->id) }}" target="_blank">
            Consentimiento del Paciente
        </a>
                </div>
            </div>
    </div>

            <div class="row justify-content-center mt-2 mb-4">
                <div class="col-md-3">
                    <form action="{{ route('admin.paciente.historiaClinica', $Paciente->id) }}" method="GET" target="_blank">
                        @csrf
                        <button class="btn btn-primary btn-block" type="submit">Historia Clínica</button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('admin.paciente.fichaHistoriaClinica', $Paciente->id) }}" method="GET" target="_blank">
                        @csrf
                        <button class="btn btn-primary btn-block" type="submit">Ficha de Historia Clinica</button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('admin.paciente.fichaPaciente', $Paciente->id) }}" method="GET" target="_blank">
                        @csrf
                        <button class="btn btn-primary btn-block" type="submit">Ficha del Paciente</button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('admin.paciente.medicacionPaciente', $Paciente->id) }}" method="GET" target="_blank">
                        @csrf
                        <button class="btn btn-primary btn-block" type="submit">Prescripción del Paciente</button>
                    </form>
                </div>
            </div>

<div class="card">
            <div class="card-header">
                {{ trans('cruds.paciente.ficha_paciente') }}
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.fecha_ingreso') }}</th>
                            <td>{{ $Paciente->ficha_admision->fecha_ingreso }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.modalidad') }}</th>
                            <td>{{ $Paciente->ficha_admision->modalidad }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.fecha_egreso') }}</th>
                            <td>{{ $Paciente->ficha_admision->fecha_egreso }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.tipo_egreso') }}</th>
                            <td>{{ $Paciente->ficha_admision->tipo_egreso }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.datos_paciente') }}
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.nombre') }}</th>
                            <td>{{ $Paciente->nombre }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.apellido') }}</th>
                            <td>{{ $Paciente->apellido }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.dni') }}</th>
                            <td>{{ $Paciente->dni }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.fecha_nac') }}</th>
                            <td>{{ $Paciente->fecha_nac }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.edad') }}</th>
                            <td>{{ $Paciente->edad }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.sexo') }}</th>
                            <td>@if($Paciente->sexo == 'F')
                                    Femenino
                                @endif
                                @if($Paciente->sexo == 'M')
                                    Masculino
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.estado_civil') }}</th>
                            <td>{{ $Paciente->estado_civil }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.obra_social') }}</th>
                            <td>{{ $Paciente->obra_social }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.n_afiliado') }}</th>
                            <td>{{ $Paciente->n_afiliado }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.provincia') }}</th>
                            <td>{{ $Paciente->provincia }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.localidad') }}</th>
                            <td>{{ $Paciente->localidad }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.domicilio') }}</th>
                            <td>{{ $Paciente->calle }} {{ $Paciente->calle_numero }}, 
                                {{ $Paciente->calle_piso ? 'Piso ' . $Paciente->calle_piso . ',' : '' }} 
                                {{ $Paciente->calle_dpto ? 'Dpto ' . $Paciente->calle_dpto : '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($Paciente->estado_civil == 'Casado')
        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.datos_conyugue') }}
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.conyugue_nombre') }}</th>
                            <td>{{ $Paciente->conyugue->conyugue_nombre }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.conyugue_apellido') }}</th>
                            <td>{{ $Paciente->conyugue->conyugue_apellido }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.conyugue_edad') }}</th>
                            <td>{{ $Paciente->conyugue->conyugue_edad }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.conyugue_domicilio') }}</th>
                            <td>{{ $Paciente->conyugue->conyugue_domicilio }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.datos_responsables') }}
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Relación</th>
                            <th>Nombre completo</th>
                            <th>Teléfono</th>
                            <th>Responsable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($Paciente->padres_tutores)
                        @if($Paciente->padres_tutores->padre_nombre)
                        <tr>
                            <td>Padre</td>
                            <td>{{ $Paciente->padres_tutores->padre_nombre }}</td>
                            <td>{{ $Paciente->padres_tutores->padre_telefono }}</td>
                            <td>{{ $Paciente->padres_tutores->padre_responsable }}</td>
                        </tr>
                        @endif
                        @if($Paciente->padres_tutores->madre_nombre)
                        <tr>
                            <td>Madre</td>
                            <td>{{ $Paciente->padres_tutores->madre_nombre }}</td>
                            <td>{{ $Paciente->padres_tutores->madre_telefono }}</td>
                            <td>{{ $Paciente->padres_tutores->madre_responsable }}</td>
                        </tr>
                        @endif
                        @if($Paciente->padres_tutores->tutor_nombre)
                        <tr>
                            <td>Tutor</td>
                            <td>{{ $Paciente->padres_tutores->tutor_nombre }}</td>
                            <td>{{ $Paciente->padres_tutores->tutor_telefono }}</td>
                            <td>{{ $Paciente->padres_tutores->tutor_responsable }}</td>
                        </tr>
                        @endif
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Continúa con las demás secciones de manera similar -->

        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.datos_familiares') }}
            </div>
            <div class="card-body">
                <h5><b>Hijos</b></h5><br>
                @if($Paciente->familiar_hijos == 'Si' && $Paciente->hijos->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Edad</th>
                                <th>Tutor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Paciente->hijos as $hijo)
                            <tr>
                                <td>{{ $hijo->hijos_nombre }}</td>
                                <td>{{ $hijo->hijos_edad }}</td>
                                <td>{{ $hijo->hijos_tutor }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No tiene hijos</p>
                @endif

                <h5><b>Hermanos</b></h5><br>
                @if($Paciente->familiar_hermanos == 'Si' && $Paciente->hermanos->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Edad</th>
                                <th>Convive</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Paciente->hermanos as $hermano)
                            <tr>
                                <td>{{ $hermano->hermanos_nombre }}</td>
                                <td>{{ $hermano->hermanos_edad }}</td>
                                <td>{{ $hermano->hermanos_convive }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No tiene hermanos</p>
                @endif
            </div>
        </div>


        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.datos_educacion') }}
            </div>
            <div class="card-body">
            <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Completa</th>
                                                <th>Incompleta</th>
                                                <th>Ultimo año cursado</th>
                                                <th>Expulsado</th>
                                                <th>Interrumpido</th>
                                                <th>Cambios</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                @foreach(['primaria', 'secundaria', 'terciaria', 'facultad'] as $nivel)
                <tr>
                    <td>{{ trans('cruds.paciente.fields.' . $nivel) }}</td>
                    <td class="text-center">{{ $Paciente->educacion->{$nivel . '_completa'} }}</td>
                    <td class="text-center">{{ $Paciente->educacion->{$nivel . '_incompleta'} }}</td>
                    <td class="text-center">{{ $Paciente->educacion->{$nivel . '_ultimo_anio'} }}</td>
                    <td class="text-center">{{ $Paciente->educacion->{$nivel . '_expulsado'} }}</td>
                    <td class="text-center">{{ $Paciente->educacion->{$nivel . '_interrumpido'} }}</td>
                    <td class="text-center">{{ $Paciente->educacion->{$nivel . '_cambios'} }}</td>
                </tr>
                @endforeach
            </tbody>
                                    </table>
                                    <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.observaciones') }}</th>
                            <td>{{ $Paciente->educacion->observaciones }}</td>
                        </tr>
                    </tbody>
                </table>      
            </div>
        </div>

        
        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.datos_laborales') }}
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.actividad_laboral') }}</th>
                            <td>{{ $Paciente->laboral->actividad_laboral }}</td>
                        </tr>
                        @if($Paciente->laboral->actividad_laboral != 'No')
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.empresa_laboral') }}</th>
                            <td>{{ $Paciente->laboral->empresa_laboral }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.cargo_laboral') }}</th>
                            <td>{{ $Paciente->laboral->cargo_laboral }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.antiguedad_laboral') }}</th>
                            <td>{{ $Paciente->laboral->antiguedad_laboral }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.antecedente_laboral') }}</th>
                            <td>{{ $Paciente->laboral->antecedente_laboral }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>      
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.datos_historial') }}
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.historial_tratamiento') }}</th>
                            <td>{{ $Paciente->historial_tratamiento }}</td>
                        </tr>
                        @if($Paciente->historial_tratamiento != 'No')
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Lugar</th>
                                <th>Duracion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Paciente->historial_tratamientos as $historial)
                            <tr>
                                <td>{{ $historial->lugar }}</td>
                                <td>{{ $historial->duracion }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                        @endif
                    </tbody>
                </table>      
            </div>
        </div>
        <!-- Incluye las demás secciones de manera similar -->

        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.datos_problematica') }}
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.problematica') }}</th>
                            <td>{{ $Paciente->problematica->problematica }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.problematica_detalles') }}</th>
                            <td>{{ $Paciente->problematica->problematica_detalles }}</td>
                        </tr>
                    </tbody>
                </table>      
            </div>
        </div>
        <!-- Incluye las demás secciones de manera similar -->

        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.datos_adicionales') }}
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.abuso_sexual') }}</th>
                            <td>{{ $Paciente->datos_adicionales->abuso_sexual }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.sobredosis') }}</th>
                            <td>{{ $Paciente->datos_adicionales->sobredosis }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.antecedentes_legales') }}</th>
                            <td>{{ $Paciente->datos_adicionales->antecedentes_legales }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.analfabeto') }}</th>
                            <td>{{ $Paciente->datos_adicionales->analfabeto }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.padres_separados') }}</th>
                            <td>{{ $Paciente->datos_adicionales->padres_separados }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.privado_libertad') }}</th>
                            <td>{{ $Paciente->datos_adicionales->privado_libertad }}</td>
                        </tr>
                        @if($Paciente->datos_adicionales->privado_libertad != 'No')
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.tiempo_privado_libertad') }}</th>
                            <td>{{ $Paciente->datos_adicionales->tiempo_privado_libertad }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.enfermedad_cronica') }}</th>
                            <td>{{ $Paciente->datos_adicionales->enfermedad_cronica }}</td>
                        </tr>
                        @if($Paciente->datos_adicionales->enfermedad_cronica != 'No')
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.enfermedad_cronica_detalles') }}</th>
                            <td>{{ $Paciente->datos_adicionales->enfermedad_cronica_detalles }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.alergia') }}</th>
                            <td>{{ $Paciente->datos_adicionales->alergia }}</td>
                        </tr>
                        @if($Paciente->datos_adicionales->alergia != 'No')
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.alergia_detalles') }}</th>
                            <td>{{ $Paciente->datos_adicionales->alergia_detalles }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>      
            </div>
        </div>
        <!-- Incluye las demás secciones de manera similar -->

        
        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.medicacion.title_singular') }}
            </div>
            <div class="card-body">
                    <table class="table table-bordered table-striped">
                    <thead>
        <tr>
            <th>{{ trans('cruds.medicacion.fields.horario') }}</th>
            <th>{{ trans('cruds.medicacion.fields.medicamento') }}</th>
            <th>{{ trans('cruds.medicacion.fields.cantidad') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Definir el orden personalizado de los horarios
            $ordenHorarios = [
                'mañana' => 1,
                'mediodía' => 2,
                'tarde' => 3,
                'noche' => 4
            ];

            // Ordenar las medicaciones por horario usando el orden personalizado
            $medicacionesOrdenadas = $Medicaciones->sortBy(function($medicacion) use ($ordenHorarios) {
                return $ordenHorarios[$medicacion->horario] ?? 99; // Default a 99 si el horario no está definido
            })->groupBy('horario');
        @endphp

        @foreach($medicacionesOrdenadas as $horario => $medicacionesForHorario)
            <tr>
                <td rowspan="{{ $medicacionesForHorario->count() }}">
                    {{ $horario }}
                </td>
                <td>{{ $medicacionesForHorario->first()->medicamento }}</td>
                <td>{{ $medicacionesForHorario->first()->cantidad }} {{ $medicacionesForHorario->first()->unidad }}</td>
            </tr>

            @foreach($medicacionesForHorario->slice(1) as $medicacion)
                <tr>
                    <td>{{ $medicacion->medicamento }}</td>
                    <td>{{ $medicacion->cantidad }} {{ $medicacion->unidad }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>


                    </table>
            </div>
        </div>
        <!-- Incluye las demás secciones de manera similar -->

        
        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.informe.title') }}
            </div>
            <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Archivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Informes as $Informe)
                            <tr>
                                <td>{{ $Informe->fecha }}</td>
                                <td>{{ $Informe->tipo->name }}</td>
                                <td>
                                @if($Informe->document_files)
                                <a href="{{ route('admin.informe.show', $Informe->id) }}" target="_blank">
                                    Ver Informe
                                </a>
                                @endif
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
        </div>
        <!-- Incluye las demás secciones de manera similar -->

    </div>
</div>

@endsection