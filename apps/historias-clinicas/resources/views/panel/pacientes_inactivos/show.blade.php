@extends('layouts.panel')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-default" href="{{ url()->previous() }}">
                        Volver
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('panel.paciente.edit', $Paciente->id) }}"  class="btn btn-primary btn-block">
                        Editar Paciente
                    </a>
                </div>
            </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
            <div class="row">
                <div class="col-md-12">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
        <a href="{{ route('panel.paciente.consentimientoPaciente', $Paciente->id) }}" target="_blank">
            Consentimiento del Paciente
        </a>
                </div>
            </div>
    </div>

            <div class="row justify-content-center mt-2 mb-4">
                <div class="col-md-3">
                    <form action="{{ route('panel.paciente.historiaClinica', $Paciente->id) }}" method="GET" target="_blank">
                        @csrf
                        <button class="btn btn-primary btn-block" type="submit">Historia Clínica</button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('panel.paciente.fichaHistoriaClinica', $Paciente->id) }}" method="GET" target="_blank">
                        @csrf
                        <button class="btn btn-primary btn-block" type="submit">Ficha de Historia Clinica</button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('panel.paciente.fichaPaciente', $Paciente->id) }}" method="GET" target="_blank">
                        @csrf
                        <button class="btn btn-primary btn-block" type="submit">Ficha del Paciente</button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('panel.paciente.medicacionPaciente', $Paciente->id) }}" method="GET" target="_blank">
                        @csrf
                        <button class="btn btn-primary btn-block" type="submit">Prescripción del Paciente</button>
                    </form>
                </div>
            </div>

        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.paciente.ficha_paciente') }}
            </div>
            <div class="card-body">
                <p class="mb-3"><b>INGRESO DEL PACIENTE</b></p>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.fecha_ingreso') }}</th>
                            <td>{{ \Carbon\Carbon::parse($Paciente->ficha_admision->fecha_ingreso)->format('d/m/Y') ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.modalidad') }}</th>
                            <td>{{ $Paciente->ficha_admision->modalidad }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.fecha_egreso') }}</th>
                            <td>{{ \Carbon\Carbon::parse($Paciente->ficha_admision->fecha_egreso)->format('d/m/Y') ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.paciente.fields.tipo_egreso') }}</th>
                            <td>{{ $Paciente->ficha_admision->tipo_egreso }}</td>
                        </tr>
                    </tbody>
                </table>
                @if($Paciente->reingreso && $Paciente->reingreso->count())
                    @foreach($Paciente->reingreso as $reingreso)
                                <p class="mt-4 mb-3"><b>REINGRESO DEL PACIENTE Nº {{ $loop->iteration }}</b></p>
                               
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <th>{{ trans('cruds.paciente.fields.fecha_ingreso') }}</th>
                                            <td>{{ \Carbon\Carbon::parse($reingreso->fecha_reingreso)->format('d/m/Y') ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans('cruds.paciente.fields.modalidad') }}</th>
                                            <td>{{ $reingreso->modalidad }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans('cruds.paciente.fields.fecha_egreso') }}</th>
                                            <td>{{ \Carbon\Carbon::parse($reingreso->fecha_egreso)->format('d/m/Y') ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans('cruds.paciente.fields.tipo_egreso') }}</th>
                                            <td>{{ $reingreso->tipo_egreso }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                    @endforeach
                @endif
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
                            <td>{{ \Carbon\Carbon::parse($Paciente->fecha_nac)->format('d/m/Y') ?? '' }}</td>
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
                <p><b>Hijos</b></p><br>
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
                <hr>
                <p><b>Hermanos</b></p><br>
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

        @php
            // Ordenar medicaciones por fecha descendente
            $medicacionesPorFecha = $Medicaciones->sortByDesc('fecha')->groupBy('fecha');

            $ordenHorarios = [
                'Mañana' => 1,
                'Mediodia' => 2,
                'Tarde' => 3,
                'Noche' => 4
            ];
        @endphp

        @forelse($medicacionesPorFecha as $fecha => $medsPorFecha)
            <div class="mb-3">
                <strong>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</strong>
                <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#modal-{{ $fecha }}">
                    Ver / Editar
                </button>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modal-{{ $fecha }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel-{{ $fecha }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel-{{ $fecha }}">
                                Esquema de Medicación del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                                <form action="{{ route('panel.paciente.medicacionPaciente', $Paciente->id) }}" method="GET" target="_blank">
                                    @csrf
                                    <input type="hidden" name="fecha" value="{{ $fecha }}">
                                    <button class="btn btn-primary btn-sm float-right" type="submit">Imprimir este esquema</button>
                                </form>
                            @php
                                // Agrupar por horario dentro de la fecha
                                $medsPorHorario = $medsPorFecha->sortBy(function($m) use ($ordenHorarios) {
                                    return $ordenHorarios[$m->horario] ?? 99;
                                })->groupBy('horario');
                            @endphp

                            @foreach($medsPorHorario as $horario => $meds)
                                <h5>{{ $horario }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Medicamento</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($meds as $m)
                                                <tr>
                                                    <td>{{ $m->medicamento ?? '' }}</td>
                                                    <td>{{ $m->unidad ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('panel.medicacion.edit', [$m->paciente_id, $fecha]) }}" class="btn btn-primary">
                                Editar
                            </a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>No hay esquemas registrados.</p>
        @endforelse
    </div>
</div>
        <!-- Incluye las demás secciones de manera similar -->

        
        <div class="card mb-4">
            <div class="card-header">
                {{ trans('cruds.informe.title') }}
            </div>
            <div class="card-body">
                    <table class="table table-bordered table-striped datatable">
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
                                <td>{{ \Carbon\Carbon::parse($Informe->fecha)->format('d/m/Y') ?? '' }}</td>
                                <td>{{ $Informe->tipo->name }}</td>
                                <td>
                                @if($Informe->document_files)
                                <a href="{{ route('panel.informe.show', $Informe->id) }}" target="_blank">
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
    </div>
</div>
</div>


@endsection



@section('scripts')
    {{-- jQuery + DataTables --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css"/>

    <script>
        $(document).ready(function () {
            $('.datatable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                pageLength: 20,
                lengthMenu: [20, 50, 100],
                responsive: true
            });
        });
    </script>
@endsection