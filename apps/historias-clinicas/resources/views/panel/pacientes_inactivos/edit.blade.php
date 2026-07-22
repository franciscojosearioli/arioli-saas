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
                </div>
            </div>
    </div>
            <div class="mb-4">
                <b>EDITAR PACIENTE</b>
            </div>

        <form method="POST" action="{{ route("panel.paciente.update", [$Paciente->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.paciente.ficha_paciente') }}
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="fecha_ingreso">{{ trans('cruds.paciente.fields.fecha_ingreso') }}</label>
                                <input class="form-control {{ $errors->has('fecha_ingreso') ? 'is-invalid' : '' }}" type="date" name="fecha_ingreso" id="fecha_ingreso" value="{{ old('fecha_ingreso', $Paciente->ficha_admision->fecha_ingreso ) }}" required>
                                @if($errors->has('fecha_ingreso'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('fecha_ingreso') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.paciente.fields.fecha_ingreso_helper') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="modalidad">{{ trans('cruds.paciente.fields.modalidad') }}</label>
                                <select class="form-control select2 {{ $errors->has('modalidad') ? 'is-invalid' : '' }}" name="modalidad" id="modalidad">
                                    <option selected disabled>Seleccione una modalidad</option>
                                    <option value="Internacion" {{ old('modalidad', $Paciente->ficha_admision->modalidad) == 'Internacion' ? 'selected' : '' }}>Internacion</option>
                                </select>
                                @if($errors->has('modalidad'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('modalidad') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.paciente.fields.modalidad_helper') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_egreso">{{ trans('cruds.paciente.fields.fecha_egreso') }}</label>
                                <input class="form-control {{ $errors->has('fecha_egreso') ? 'is-invalid' : '' }}" type="date" name="fecha_egreso" id="fecha_egreso" value="{{ old('fecha_egreso', $Paciente->ficha_admision->fecha_egreso) }}">
                                @if($errors->has('fecha_egreso'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('fecha_egreso') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.paciente.fields.fecha_egreso_helper') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_egreso">{{ trans('cruds.paciente.fields.tipo_egreso') }}</label>
                                <select class="form-control select2 {{ $errors->has('tipo_egreso') ? 'is-invalid' : '' }}" name="tipo_egreso" id="tipo_egreso">
                                    <option selected disabled>Seleccione un tipo de egreso</option>
                                    <option value="Alta" {{ old('tipo_egreso', $Paciente->ficha_admision->tipo_egreso) == 'Alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="Abandono" {{ old('tipo_egreso', $Paciente->ficha_admision->tipo_egreso) == 'Abandono' ? 'selected' : '' }}>Abandono</option>
                                    <option value="Expulsion" {{ old('tipo_egreso', $Paciente->ficha_admision->tipo_egreso) == 'Expulsion' ? 'selected' : '' }}>Expulsion</option>
                                </select>
                                @if($errors->has('tipo_egreso'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('tipo_egreso') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.paciente.fields.tipo_egreso_helper') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<div class="card mt-5">
    <div class="card-header">
        Reingresos del Paciente
    </div>
    <div class="card-body">
        <!-- Select ¿Hubo reingreso? -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="hubo_reingreso">¿Hubo reingresos?</label>
                    <select class="form-control select2 {{ $errors->has('hubo_reingreso') ? 'is-invalid' : '' }}" name="hubo_reingreso" id="hubo_reingreso">
                        <option value="0" {{ $Paciente->reingreso->isEmpty() ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $Paciente->reingreso->isNotEmpty() ? 'selected' : '' }}>Sí</option>
                    </select>
                    @if($errors->has('hubo_reingreso'))
                        <div class="invalid-feedback">
                            {{ $errors->first('hubo_reingreso') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Tabla dinámica para reingresos -->
        <div id="reingresos-container" style="display: {{ $Paciente->reingreso->isNotEmpty() ? 'block' : 'none' }};">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Fecha de Reingreso</th>
                        <th class="text-center">Modalidad de Reingreso</th>
                        <th class="text-center">Fecha de Egreso</th>
                        <th class="text-center">Tipo de Egreso</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody id="table-body-reingresos">
                    @foreach($Paciente->reingreso as $reingreso)
                        <tr>
                            <td class="text-center"><input class="form-control" type="date" name="fecha_reingreso[]" value="{{ $reingreso->fecha_reingreso }}"></td>
                            <td class="text-center">
                                <select class="form-control select2" name="modalidad_reingreso[]">
                                    <option value="Internacion" {{ $reingreso->modalidad == 'Internacion' ? 'selected' : '' }}>Internacion</option>
                                </select>
                            </td>
                            <td class="text-center"><input class="form-control" type="date" name="fecha_egreso_reingreso[]" value="{{ $reingreso->fecha_egreso }}"></td>
                            <td class="text-center">
                                <select class="form-control select2" name="tipo_egreso_reingreso[]">
                                    <option {{ $reingreso->tipo_egreso == '' ? 'selected' : '' }}>Sin egreso</option>
                                    <option value="Alta" {{ $reingreso->tipo_egreso == 'Alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="Abandono" {{ $reingreso->tipo_egreso == 'Abandono' ? 'selected' : '' }}>Abandono</option>
                                    <option value="Expulsion" {{ $reingreso->tipo_egreso == 'Expulsion' ? 'selected' : '' }}>Expulsion</option>
                                </select>
                            </td>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-primary" id="add-row-reingresos">Agregar Reingreso</button>
        </div>
    </div>
</div>

            <div class="card mt-5">
                    <div class="card-header">
                        {{ trans('cruds.paciente.datos_paciente') }}
                    </div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="nombre">{{ trans('cruds.paciente.fields.nombre') }}</label>
                                    <input class="form-control" type="text" name="nombre" id="nombre" value="{{ old('nombre', $Paciente->nombre) }}" required>
                                    @if($errors->has('nombre'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('nombre') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.nombre_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="apellido">{{ trans('cruds.paciente.fields.apellido') }}</label>
                                    <input class="form-control" type="text" name="apellido" id="apellido" value="{{ old('apellido', $Paciente->apellido) }}" required>
                                    @if($errors->has('apellido'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('apellido') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.apellido_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required" for="dni">{{ trans('cruds.paciente.fields.dni') }}</label>
                                    <input class="form-control" type="text" name="dni" id="dni" value="{{ old('dni', $Paciente->dni) }}" required>
                                    @if($errors->has('dni'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('dni') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.dni_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required" for="fecha_nac">{{ trans('cruds.paciente.fields.fecha_nac') }}</label>
                                    <input class="form-control" type="date" name="fecha_nac" id="fecha_nac" value="{{ old('fecha_nac', $Paciente->fecha_nac) }}" required>
                                    @if($errors->has('fecha_nac'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('fecha_nac') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.fecha_nac_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required" for="edad">{{ trans('cruds.paciente.fields.edad') }}</label>
                                    <input class="form-control" type="number" name="edad" id="edad" value="{{ old('edad', $Paciente->edad) }}" required>
                                    @if($errors->has('edad'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('edad') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.edad_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="sexo">{{ trans('cruds.paciente.fields.sexo') }}</label>
                                    <select class="form-control select2 {{ $errors->has('sexo') ? 'is-invalid' : '' }}" name="sexo" id="sexo" required>
                                    <option value="M" {{ old('sexo', $Paciente->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('sexo', $Paciente->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                                    </select>
                                    @if($errors->has('sexo'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('sexo') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.sexo_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="estado_civil">{{ trans('cruds.paciente.fields.estado_civil') }}</label>
                                    <select class="form-control select2 {{ $errors->has('estado_civil') ? 'is-invalid' : '' }}" name="estado_civil" id="estado_civil" required>
                                    <option value="Soltero" {{ old('estado_civil', $Paciente->estado_civil) == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                                    <option value="Casado" {{ old('estado_civil', $Paciente->estado_civil) == 'Casado' ? 'selected' : '' }}>Casado</option>
                                    </select>
                                    @if($errors->has('estado_civil'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('estado_civil') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.estado_civil_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="obra_social">{{ trans('cruds.paciente.fields.obra_social') }}</label>
                                    <input class="form-control" type="text" name="obra_social" id="obra_social" value="{{ old('obra_social', $Paciente->obra_social) }}">
                                   @if($errors->has('obra_social'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('obra_social') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.obra_social_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="n_afiliado">{{ trans('cruds.paciente.fields.n_afiliado') }}</label>
                                    <input class="form-control" type="text" name="n_afiliado" id="n_afiliado" value="{{ old('n_afiliado', $Paciente->n_afiliado) }}">
                                   @if($errors->has('n_afiliado'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('n_afiliado') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.n_afiliado_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="provincia">{{ trans('cruds.paciente.fields.provincia') }}</label>
                                    <input class="form-control" type="text" name="provincia" id="provincia" value="{{ old('provincia', $Paciente->provincia) }}" required>
                                    @if($errors->has('provincia'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('provincia') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.provincia_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="localidad">{{ trans('cruds.paciente.fields.localidad') }}</label>
                                    <input class="form-control" type="text" name="localidad" id="localidad" value="{{ old('localidad', $Paciente->localidad) }}" required>
                                    @if($errors->has('localidad'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('localidad') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.localidad_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required" for="calle">{{ trans('cruds.paciente.fields.calle') }}</label>
                                    <input class="form-control" type="text" name="calle" id="calle" value="{{ old('calle', $Paciente->calle) }}" required>
                                    @if($errors->has('calle'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('calle') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.calle_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required" for="calle_numero">{{ trans('cruds.paciente.fields.calle_numero') }}</label>
                                    <input class="form-control" type="text" name="calle_numero" id="calle_numero" value="{{ old('calle_numero', $Paciente->calle_numero) }}" required>
                                    @if($errors->has('calle_numero'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('calle_numero') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.calle_numero_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="calle_piso">{{ trans('cruds.paciente.fields.calle_piso') }}</label>
                                    <input class="form-control" type="text" name="calle_piso" id="calle_piso" value="{{ old('calle_piso', $Paciente->calle_piso) }}">
                                    @if($errors->has('calle_piso'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('calle_piso') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.calle_piso_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="calle_dpto">{{ trans('cruds.paciente.fields.calle_dpto') }}</label>
                                    <input class="form-control" type="text" name="calle_dpto" id="calle_dpto" value="{{ old('calle_dpto', $Paciente->calle_dpto) }}">
                                    @if($errors->has('calle_dpto'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('calle_dpto') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.calle_dpto_helper') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($Paciente->estado_civil == 'Casado')
                <div class="card mt-5">
                    <div class="card-header">
                        {{ trans('cruds.paciente.datos_conyugue') }}
                    </div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="conyugue_nombre">{{ trans('cruds.paciente.fields.conyugue_nombre') }}</label>
                                    <input class="form-control" type="text" name="conyugue_nombre" id="conyugue_nombre" value="{{ old('conyugue_nombre', $Paciente->conyugue->conyugue_nombre) }}">
                                    @if($errors->has('conyugue_nombre'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('conyugue_nombre') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.conyugue_nombre_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="conyugue_apellido">{{ trans('cruds.paciente.fields.conyugue_apellido') }}</label>
                                    <input class="form-control" type="text" name="conyugue_apellido" id="conyugue_apellido" value="{{ old('conyugue_apellido', $Paciente->conyugue->conyugue_apellido) }}">
                                    @if($errors->has('conyugue_apellido'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('conyugue_apellido') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.conyugue_apellido_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="conyugue_edad">{{ trans('cruds.paciente.fields.conyugue_edad') }}</label>
                                    <input class="form-control" type="number" name="conyugue_edad" id="conyugue_edad" value="{{ old('conyugue_edad', $Paciente->conyugue->conyugue_edad) }}">
                                    @if($errors->has('conyugue_edad'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('conyugue_edad') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.conyugue_edad_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="conyugue_domicilio">{{ trans('cruds.paciente.fields.conyugue_domicilio') }}</label>
                                    <input class="form-control" type="text" name="conyugue_domicilio" id="conyugue_domicilio" value="{{ old('conyugue_domicilio', $Paciente->conyugue->conyugue_domicilio) }}">
                                    @if($errors->has('conyugue_domicilio'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('conyugue_domicilio') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.conyugue_domicilio_helper') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card mt-5">
                    <div class="card-header">
                        {{ trans('cruds.paciente.datos_responsables') }}
                    </div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-12">


                            <div class="form-group">

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:10%"></th>
                                                <th style="width:55%">Nombre completo</th>
                                                <th style="width:30%">Telefono</th>
                                                <th style="width:5%">Responsable</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">Padre</td>
                                                <td class="text-center"><input class="form-control" type="text" name="padre_nombre" value="{{ old('padre_nombre', $Paciente->padres_tutores->padre_nombre) }}"></td>
                                                <td class="text-center"><input class="form-control" type="number" name="padre_telefono" value="{{ old('padre_telefono', $Paciente->padres_tutores->padre_telefono) }}"></td>
                                                <td class="text-center">
                                                    <select class="form-control select2 {{ $errors->has('padre_responsable') ? 'is-invalid' : '' }}" name="padre_responsable" id="padre_responsable">
                                                        <option value="Si" {{ old('padre_responsable', $Paciente->padres_tutores->padre_responsable) == 'Si' ? 'selected' : '' }}>Si</option>
                                                        <option value="No" {{ old('padre_responsable', $Paciente->padres_tutores->padre_responsable) == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </td>      
                                            </tr>
                                            <tr>
                                                <td class="text-center">Madre</td>
                                                <td class="text-center"><input class="form-control" type="text" name="madre_nombre" value="{{ old('madre_nombre', $Paciente->padres_tutores->madre_nombre) }}"></td>
                                                <td class="text-center"><input class="form-control" type="number" name="madre_telefono" value="{{ old('madre_telefono', $Paciente->padres_tutores->madre_telefono) }}"></td>
                                                <td class="text-center">
                                                    <select class="form-control select2 {{ $errors->has('madre_responsable') ? 'is-invalid' : '' }}" name="madre_responsable" id="madre_responsable">
                                                        <option value="Si" {{ old('madre_responsable', $Paciente->padres_tutores->madre_responsable) == 'Si' ? 'selected' : '' }}>Si</option>
                                                        <option value="No" {{ old('madre_responsable', $Paciente->padres_tutores->madre_responsable) == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </td>      
                                            </tr>
                                            <tr>
                                                <td class="text-center">Tutor</td>
                                                <td class="text-center"><input class="form-control" type="text" name="tutor_nombre" value="{{ old('tutor_nombre', $Paciente->padres_tutores->tutor_nombre) }}"></td>
                                                <td class="text-center"><input class="form-control" type="number" name="tutor_telefono" value="{{ old('tutor_telefono', $Paciente->padres_tutores->tutor_telefono) }}"></td>
                                                <td class="text-center">
                                                    <select class="form-control select2 {{ $errors->has('tutor_responsable') ? 'is-invalid' : '' }}" name="tutor_responsable" id="tutor_responsable">
                                                        <option value="Si" {{ old('tutor_responsable', $Paciente->padres_tutores->tutor_responsable) == 'Si' ? 'selected' : '' }}>Si</option>
                                                        <option value="No" {{ old('tutor_responsable', $Paciente->padres_tutores->tutor_responsable) == 'No' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </td>           
                                            </tr>
                                        </tbody>
                                    </table>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-5">
                    <div class="card-header">
                        {{ trans('cruds.paciente.datos_familiares') }}
                    </div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                            <div class="form-group">
                                    <label class="required" for="familiar_hijos">{{ trans('cruds.paciente.fields.familiar_hijos') }}</label>
                                    <select class="form-control select2 {{ $errors->has('familiar_hijos') ? 'is-invalid' : '' }}" name="familiar_hijos" id="familiar_hijos" required>
                                                        <option value="Si" {{ old('familiar_hijos', $Paciente->familiar_hijos) == 'Si' ? 'selected' : '' }}>Si</option>
                                                        <option value="No" {{ old('familiar_hijos', $Paciente->familiar_hijos) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @if($errors->has('familiar_hijos'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('familiar_hijos') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.familiar_hijos_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        @if($Paciente->familiar_hijos == 'Si')
                        <div class="row justify-content-center" id="table-container-hijos" >
                            <div class="col-md-12">
                                <div class="form-group">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width:50%">Nombre</th>
                                                <th style="width:30%">Edad</th>
                                                <th style="width:10%">Tutor</th>
                                                <th style="width:10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body-hijos">
                                            @foreach($Paciente->hijos as $hijo)
                                                <tr>
                                                    <td class="text-center"><input class="form-control" type="text" name="hijos_nombre[]" value="{{ old('hijos_nombre.' . $loop->index, $hijo->hijos_nombre) }}"></td>
                                                    <td class="text-center"><input class="form-control" type="number" name="hijos_edad[]" value="{{ old('hijos_edad.' . $loop->index, $hijo->hijos_edad) }}"></td>
                                                    <td class="text-center">
                                                        <select class="form-control select2" name="hijos_tutor[]">
                                                            <option value="Si" {{ old('hijos_tutor.' . $loop->index, $hijo->hijos_tutor) == 'Si' ? 'selected' : '' }}>Si</option>
                                                            <option value="No" {{ old('hijos_tutor.' . $loop->index, $hijo->hijos_tutor) == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary" id="add-row-hijos">Agregar fila</button>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="required" for="familiar_hermanos">{{ trans('cruds.paciente.fields.familiar_hermanos') }}</label>
                                    <select class="form-control select2 {{ $errors->has('familiar_hermanos') ? 'is-invalid' : '' }}" name="familiar_hermanos" id="familiar_hermanos" required>
                                                        <option value="Si" {{ old('familiar_hermanos', $Paciente->familiar_hermanos) == 'Si' ? 'selected' : '' }}>Si</option>
                                                        <option value="No" {{ old('familiar_hermanos', $Paciente->familiar_hermanos) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @if($errors->has('familiar_hermanos'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('familiar_hermanos') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.familiar_hermanos_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        @if($Paciente->familiar_hermanos == 'Si')
                        <div class="row justify-content-center" id="table-container-hermanos">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width:50%">Nombre</th>
                                                <th style="width:30%">Edad</th>
                                                <th style="width:10%">Convive</th>
                                                <th style="width:10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body-hermanos">
                                            @foreach($Paciente->hermanos as $hermano)
                                                <tr>
                                                    <td class="text-center"><input class="form-control" type="text" name="hermanos_nombre[]" value="{{ old('hermanos_nombre.' . $loop->index, $hermano->hermanos_nombre) }}"></td>
                                                    <td class="text-center"><input class="form-control" type="number" name="hermanos_edad[]" value="{{ old('hermanos_edad.' . $loop->index, $hermano->hermanos_edad) }}"></td>
                                                    <td class="text-center">
                                                        <select class="form-control select2" name="hermanos_convive[]">
                                                            <option value="Si" {{ old('hermanos_convive.' . $loop->index, $hermano->hermanos_convive) == 'Si' ? 'selected' : '' }}>Si</option>
                                                            <option value="No" {{ old('hermanos_convive.' . $loop->index, $hermano->hermanos_convive) == 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary" id="add-row-hermanos">Agregar fila</button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card mt-5">
    <div class="card-header">
        {{ trans('cruds.paciente.datos_educacion') }}
    </div>

    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="form-group">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Completa</th>
                                <th>Incompleta</th>
                                <th>Último año cursado</th>
                                <th>Expulsado</th>
                                <th>Interrumpido</th>
                                <th>Cambios</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Primaria -->
                            <tr>
                                <td>{{ trans('cruds.paciente.fields.primaria') }}</td>
                                <td class="text-center">
                                    <select class="form-control select2" name="primaria_completa">
                                        <option value="Si" {{ $Paciente->educacion->primaria_completa == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->primaria_completa == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="primaria_incompleta">
                                        <option value="Si" {{ $Paciente->educacion->primaria_incompleta == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->primaria_incompleta == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input class="form-control" type="number" name="primaria_ultimo_anio" value="{{ $Paciente->educacion->primaria_ultimo_anio }}">
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="primaria_expulsado">
                                        <option value="Si" {{ $Paciente->educacion->primaria_expulsado == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->primaria_expulsado == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="primaria_interrumpido">
                                        <option value="Si" {{ $Paciente->educacion->primaria_interrumpido == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->primaria_interrumpido == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="primaria_cambios">
                                        <option value="Si" {{ $Paciente->educacion->primaria_cambios == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->primaria_cambios == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                            </tr>

                            <!-- Secundaria -->
                            <tr>
                                <td>{{ trans('cruds.paciente.fields.secundaria') }}</td>
                                <td class="text-center">
                                    <select class="form-control select2" name="secundaria_completa">
                                        <option value="Si" {{ $Paciente->educacion->secundaria_completa == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->secundaria_completa == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="secundaria_incompleta">
                                        <option value="Si" {{ $Paciente->educacion->secundaria_incompleta == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->secundaria_incompleta == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input class="form-control" type="number" name="secundaria_ultimo_anio" value="{{ $Paciente->educacion->secundaria_ultimo_anio }}">
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="secundaria_expulsado">
                                        <option value="Si" {{ $Paciente->educacion->secundaria_expulsado == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->secundaria_expulsado == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="secundaria_interrumpido">
                                        <option value="Si" {{ $Paciente->educacion->secundaria_interrumpido == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->secundaria_interrumpido == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="secundaria_cambios">
                                        <option value="Si" {{ $Paciente->educacion->secundaria_cambios == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->secundaria_cambios == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                            </tr>

                            <!-- Terciaria -->
                            <tr>
                                <td>{{ trans('cruds.paciente.fields.terciaria') }}</td>
                                <td class="text-center">
                                    <select class="form-control select2" name="terciaria_completa">
                                        <option value="Si" {{ $Paciente->educacion->terciaria_completa == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->terciaria_completa == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="terciaria_incompleta">
                                        <option value="Si" {{ $Paciente->educacion->terciaria_incompleta == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->terciaria_incompleta == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input class="form-control" type="number" name="terciaria_ultimo_anio" value="{{ $Paciente->educacion->terciaria_ultimo_anio }}">
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="terciaria_expulsado">
                                        <option value="Si" {{ $Paciente->educacion->terciaria_expulsado == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->terciaria_expulsado == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="terciaria_interrumpido">
                                        <option value="Si" {{ $Paciente->educacion->terciaria_interrumpido == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->terciaria_interrumpido == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="terciaria_cambios">
                                        <option value="Si" {{ $Paciente->educacion->terciaria_cambios == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->terciaria_cambios == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                            </tr>

                            <!-- Facultad -->
                            <tr>
                                <td>{{ trans('cruds.paciente.fields.facultad') }}</td>
                                <td class="text-center">
                                    <select class="form-control select2" name="facultad_completa">
                                        <option value="Si" {{ $Paciente->educacion->facultad_completa == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->facultad_completa == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="facultad_incompleta">
                                        <option value="Si" {{ $Paciente->educacion->facultad_incompleta == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->facultad_incompleta == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input class="form-control" type="number" name="facultad_ultimo_anio" value="{{ $Paciente->educacion->facultad_ultimo_anio }}">
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="facultad_expulsado">
                                        <option value="Si" {{ $Paciente->educacion->facultad_expulsado == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->facultad_expulsado == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="facultad_interrumpido">
                                        <option value="Si" {{ $Paciente->educacion->facultad_interrumpido == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->facultad_interrumpido == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <select class="form-control select2" name="facultad_cambios">
                                        <option value="Si" {{ $Paciente->educacion->facultad_cambios == 'Si' ? 'selected' : '' }}>Sí</option>
                                        <option value="No" {{ $Paciente->educacion->facultad_cambios == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-5">
    <div class="card-header">
        {{ trans('cruds.paciente.datos_laborales') }}
    </div>

    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="required" for="actividad_laboral">{{ trans('cruds.paciente.fields.actividad_laboral') }}</label>
                    <select class="form-control select2 {{ $errors->has('actividad_laboral') ? 'is-invalid' : '' }}" name="actividad_laboral" id="actividad_laboral" required>
                        <option value="" disabled>Seleccione una opción</option>
                        <option value="Si" {{ $Paciente->laboral->actividad_laboral == 'Si' ? 'selected' : '' }}>Sí</option>
                        <option value="No" {{ $Paciente->laboral->actividad_laboral == 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('actividad_laboral'))
                        <div class="invalid-feedback">
                            {{ $errors->first('actividad_laboral') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.actividad_laboral_helper') }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="empresa_laboral">{{ trans('cruds.paciente.fields.empresa_laboral') }}</label>
                    <input class="form-control" type="text" name="empresa_laboral" id="empresa_laboral" value="{{ old('empresa_laboral', $Paciente->laboral->empresa_laboral) }}">
                    @if($errors->has('empresa_laboral'))
                        <div class="invalid-feedback">
                            {{ $errors->first('empresa_laboral') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.empresa_laboral_helper') }}</span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cargo_laboral">{{ trans('cruds.paciente.fields.cargo_laboral') }}</label>
                    <input class="form-control" type="text" name="cargo_laboral" id="cargo_laboral" value="{{ old('cargo_laboral', $Paciente->laboral->cargo_laboral) }}">
                    @if($errors->has('cargo_laboral'))
                        <div class="invalid-feedback">
                            {{ $errors->first('cargo_laboral') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.cargo_laboral_helper') }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="antiguedad_laboral">{{ trans('cruds.paciente.fields.antiguedad_laboral') }}</label>
                    <input class="form-control" type="text" name="antiguedad_laboral" id="antiguedad_laboral" value="{{ old('antiguedad_laboral', $Paciente->laboral->antiguedad_laboral) }}">
                    @if($errors->has('antiguedad_laboral'))
                        <div class="invalid-feedback">
                            {{ $errors->first('antiguedad_laboral') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.antiguedad_laboral_helper') }}</span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="antecedente_laboral">{{ trans('cruds.paciente.fields.antecedente_laboral') }}</label>
                    <textarea class="form-control {{ $errors->has('antecedente_laboral') ? 'is-invalid' : '' }}" name="antecedente_laboral" id="antecedente_laboral">{{ old('antecedente_laboral', $Paciente->laboral->antecedente_laboral) }}</textarea>
                    @if($errors->has('antecedente_laboral'))
                        <div class="invalid-feedback">
                            {{ $errors->first('antecedente_laboral') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.antecedente_laboral_helper') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-5">
    <div class="card-header">
        {{ trans('cruds.paciente.datos_historial') }}
    </div>

    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="required" for="historial_tratamiento">{{ trans('cruds.paciente.fields.historial_tratamiento') }}</label>
                    <select class="form-control select2 {{ $errors->has('historial_tratamiento') ? 'is-invalid' : '' }}" name="historial_tratamiento" id="historial_tratamiento" required>
                        <option value="" disabled>Seleccione una opción</option>
                        <option value="Si" {{ $Paciente->historial_tratamiento == 'Si' ? 'selected' : '' }}>Sí</option>
                        <option value="No" {{ $Paciente->historial_tratamiento == 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('historial_tratamiento'))
                        <div class="invalid-feedback">
                            {{ $errors->first('historial_tratamiento') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.historial_tratamiento_helper') }}</span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center" id="table-container-historial_tratamiento" style="{{ $Paciente->historial_tratamiento == 'Si' ? 'display: block;' : 'display: none;' }}">
            <div class="col-md-12">
                <div class="form-group">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:60%">Lugar</th>
                                <th style="width:30%">Duración</th>
                                <th style="width:10%"></th>
                            </tr>
                        </thead>
                        <tbody id="table-body-historial_tratamiento">
                            @foreach($Paciente->historial_tratamientos as $tratamiento)
                            <tr>
                                <td class="text-center"><input class="form-control" type="text" name="lugar[]" value="{{ $tratamiento->lugar }}"></td>
                                <td class="text-center"><input class="form-control" type="text" name="duracion[]" value="{{ $tratamiento->duracion }}"></td>
                                <td class="text-center"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" id="add-row-historial_tratamiento">Agregar fila</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-5">
    <div class="card-header">
        {{ trans('cruds.paciente.datos_problematica') }}
    </div>

    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="problematica">{{ trans('cruds.paciente.fields.problematica') }}</label>
                    <input class="form-control" type="text" name="problematica" id="problematica" value="{{ old('problematica', $Paciente->problematica->problematica ?? '') }}">
                    @if($errors->has('problematica'))
                        <div class="invalid-feedback">
                            {{ $errors->first('problematica') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.problematica_helper') }}</span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="problematica_detalles">{{ trans('cruds.paciente.fields.problematica_detalles') }}</label>
                    <textarea class="form-control {{ $errors->has('problematica_detalles') ? 'is-invalid' : '' }}" name="problematica_detalles" id="problematica_detalles">{{ old('problematica_detalles', $Paciente->problematica->problematica_detalles ?? '') }}</textarea>
                    @if($errors->has('problematica_detalles'))
                        <div class="invalid-feedback">
                            {{ $errors->first('problematica_detalles') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.problematica_detalles_helper') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-5">
    <div class="card-header">
        {{ trans('cruds.paciente.datos_adicionales') }}
    </div>

    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="required" for="abuso_sexual">{{ trans('cruds.paciente.fields.abuso_sexual') }}</label>
                    <select class="form-control select2 {{ $errors->has('abuso_sexual') ? 'is-invalid' : '' }}" name="abuso_sexual" id="abuso_sexual" required>
                        <option selected disabled>Seleccione una opción</option>
                        <option value="Si" {{ old('abuso_sexual', $Paciente->datos_adicionales->abuso_sexual ?? '') === 'Si' ? 'selected' : '' }}>Si</option>
                        <option value="No" {{ old('abuso_sexual', $Paciente->datos_adicionales->abuso_sexual ?? '') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('abuso_sexual'))
                        <div class="invalid-feedback">
                            {{ $errors->first('abuso_sexual') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.abuso_sexual_helper') }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="required" for="sobredosis">{{ trans('cruds.paciente.fields.sobredosis') }}</label>
                    <select class="form-control select2 {{ $errors->has('sobredosis') ? 'is-invalid' : '' }}" name="sobredosis" id="sobredosis" required>
                        <option selected disabled>Seleccione una opción</option>
                        <option value="Si" {{ old('sobredosis', $Paciente->datos_adicionales->sobredosis ?? '') === 'Si' ? 'selected' : '' }}>Si</option>
                        <option value="No" {{ old('sobredosis', $Paciente->datos_adicionales->sobredosis ?? '') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('sobredosis'))
                        <div class="invalid-feedback">
                            {{ $errors->first('sobredosis') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.sobredosis_helper') }}</span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="required" for="antecedentes_legales">{{ trans('cruds.paciente.fields.antecedentes_legales') }}</label>
                    <select class="form-control select2 {{ $errors->has('antecedentes_legales') ? 'is-invalid' : '' }}" name="antecedentes_legales" id="antecedentes_legales" required>
                        <option selected disabled>Seleccione una opción</option>
                        <option value="Si" {{ old('antecedentes_legales', $Paciente->datos_adicionales->antecedentes_legales ?? '') === 'Si' ? 'selected' : '' }}>Si</option>
                        <option value="No" {{ old('antecedentes_legales', $Paciente->datos_adicionales->antecedentes_legales ?? '') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('antecedentes_legales'))
                        <div class="invalid-feedback">
                            {{ $errors->first('antecedentes_legales') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.antecedentes_legales_helper') }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="required" for="analfabeto">{{ trans('cruds.paciente.fields.analfabeto') }}</label>
                    <select class="form-control select2 {{ $errors->has('analfabeto') ? 'is-invalid' : '' }}" name="analfabeto" id="analfabeto" required>
                        <option selected disabled>Seleccione una opción</option>
                        <option value="Si" {{ old('analfabeto', $Paciente->datos_adicionales->analfabeto ?? '') === 'Si' ? 'selected' : '' }}>Si</option>
                        <option value="No" {{ old('analfabeto', $Paciente->datos_adicionales->analfabeto ?? '') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('analfabeto'))
                        <div class="invalid-feedback">
                            {{ $errors->first('analfabeto') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.analfabeto_helper') }}</span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="form-group">
                    <label class="required" for="padres_separados">{{ trans('cruds.paciente.fields.padres_separados') }}</label>
                    <select class="form-control select2 {{ $errors->has('padres_separados') ? 'is-invalid' : '' }}" name="padres_separados" id="padres_separados" required>
                        <option selected disabled>Seleccione una opción</option>
                        <option value="Si" {{ old('padres_separados', $Paciente->datos_adicionales->padres_separados ?? '') === 'Si' ? 'selected' : '' }}>Si</option>
                        <option value="No" {{ old('padres_separados', $Paciente->datos_adicionales->padres_separados ?? '') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('padres_separados'))
                        <div class="invalid-feedback">
                            {{ $errors->first('padres_separados') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.padres_separados_helper') }}</span>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="required" for="privado_libertad">{{ trans('cruds.paciente.fields.privado_libertad') }}</label>
                    <select class="form-control select2 {{ $errors->has('privado_libertad') ? 'is-invalid' : '' }}" name="privado_libertad" id="privado_libertad" required>
                        <option selected disabled>Seleccione una opción</option>
                        <option value="Si" {{ old('privado_libertad', $Paciente->datos_adicionales->privado_libertad ?? '') === 'Si' ? 'selected' : '' }}>Si</option>
                        <option value="No" {{ old('privado_libertad', $Paciente->datos_adicionales->privado_libertad ?? '') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('privado_libertad'))
                        <div class="invalid-feedback">
                            {{ $errors->first('privado_libertad') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.privado_libertad_helper') }}</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="tiempo_privado_libertad">{{ trans('cruds.paciente.fields.tiempo_privado_libertad') }}</label>
                    <input class="form-control" type="text" name="tiempo_privado_libertad" id="tiempo_privado_libertad" value="{{ old('tiempo_privado_libertad', $Paciente->datos_adicionales->tiempo_privado_libertad ?? '') }}">
                    @if($errors->has('tiempo_privado_libertad'))
                        <div class="invalid-feedback">
                            {{ $errors->first('tiempo_privado_libertad') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.tiempo_privado_libertad_helper') }}</span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="required" for="enfermedad_cronica">{{ trans('cruds.paciente.fields.enfermedad_cronica') }}</label>
                    <select class="form-control select2 {{ $errors->has('enfermedad_cronica') ? 'is-invalid' : '' }}" name="enfermedad_cronica" id="enfermedad_cronica" required>
                        <option selected disabled>Seleccione una opción</option>
                        <option value="Si" {{ old('enfermedad_cronica', $Paciente->datos_adicionales->enfermedad_cronica ?? '') === 'Si' ? 'selected' : '' }}>Si</option>
                        <option value="No" {{ old('enfermedad_cronica', $Paciente->datos_adicionales->enfermedad_cronica ?? '') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('enfermedad_cronica'))
                        <div class="invalid-feedback">
                            {{ $errors->first('enfermedad_cronica') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.enfermedad_cronica_helper') }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="enfermedad_cronica_detalle">{{ trans('cruds.paciente.fields.enfermedad_cronica_detalles') }}</label>
                    <textarea class="form-control {{ $errors->has('enfermedad_cronica_detalle') ? 'is-invalid' : '' }}" name="enfermedad_cronica_detalle" id="enfermedad_cronica_detalle">{{ old('enfermedad_cronica_detalle', $Paciente->datos_adicionales->enfermedad_cronica_detalle ?? '') }}</textarea>
                    @if($errors->has('enfermedad_cronica_detalle'))
                        <div class="invalid-feedback">
                            {{ $errors->first('enfermedad_cronica_detalle') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.enfermedad_cronica_detalles_helper') }}</span>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="required" for="alergia">{{ trans('cruds.paciente.fields.alergia') }}</label>
                    <select class="form-control select2 {{ $errors->has('alergia') ? 'is-invalid' : '' }}" name="alergia" id="alergia" required>
                        <option selected disabled>Seleccione una opción</option>
                        <option value="Si" {{ old('alergia', $Paciente->datos_adicionales->alergia ?? '') === 'Si' ? 'selected' : '' }}>Si</option>
                        <option value="No" {{ old('alergia', $Paciente->datos_adicionales->alergia ?? '') === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                    @if($errors->has('alergia'))
                        <div class="invalid-feedback">
                            {{ $errors->first('alergia') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.alergia_helper') }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="alergia_detalle">{{ trans('cruds.paciente.fields.alergia_detalles') }}</label>
                    <textarea class="form-control {{ $errors->has('alergia_detalle') ? 'is-invalid' : '' }}" name="alergia_detalle" id="alergia_detalle">{{ old('alergia_detalle', $Paciente->datos_adicionales->alergia_detalle ?? '') }}</textarea>
                    @if($errors->has('alergia_detalle'))
                        <div class="invalid-feedback">
                            {{ $errors->first('alergia_detalle') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.paciente.fields.alergia_detalles_helper') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="row">
                <div class="col-md-12">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
                </div>
            </div>
    </div>
        </form>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
$(document).ready(function() {
// Mostrar/ocultar tabla según selección de hubo_reingreso
    $('#hubo_reingreso').change(function() {
        if ($(this).val() === '1') {
            $('#reingresos-container').show();
        } else {
            $('#reingresos-container').hide();
            $('#table-body-reingresos').empty(); // Limpiar filas al seleccionar "No"
        }
    });

    // Agregar nueva fila
    $('#add-row-reingresos').click(function() {
        var newRow = `
            <tr>
                <td class="text-center"><input class="form-control" type="date" name="fecha_reingreso[]"></td>
                <td class="text-center">
                    <select class="form-control select2" name="modalidad_reingreso[]">
                        <option value="Internacion">Internacion</option>
                    </select>
                </td>
                <td class="text-center"><input class="form-control" type="date" name="fecha_egreso_reingreso[]"></td>
                <td class="text-center">
                    <select class="form-control select2" name="tipo_egreso_reingreso[]">
                        <option selected disabled>Seleccione un tipo de egreso</option>
                        <option value="Alta">Alta</option>
                        <option value="Abandono">Abandono</option>
                        <option value="Expulsion">Expulsion</option>
                    </select>
                </td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button></td>
            </tr>
        `;
        $('#table-body-reingresos').append(newRow);
        // Re-inicializar Select2 en los nuevos elementos
        $('.select2').select2();
    });
    $('#add-row-hijos').click(function() {
        var newRow = `
            <tr>
                <td class="text-center"><input class="form-control" type="text" name="hijos_nombre[]"></td>
                <td class="text-center"><input class="form-control" type="number" name="hijos_edad[]"></td>
                <td class="text-center"><select class="form-control select2" name="hijos_tutor[]">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button></td>
            </tr>
        `;
        $('#table-body-hijos').append(newRow);
    });

    $('#add-row-hermanos').click(function() {
        var newRow = `
            <tr>
                                                                <td class="text-center"><input class="form-control" type="text" name="hermanos_nombre[]"></td>
                                                <td class="text-center"><input class="form-control" type="number" name="hermanos_edad[]"></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="hermanos_convive[]">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button></td>
            </tr>
        `;
        $('#table-body-hermanos').append(newRow);
    });

    $('#add-row-historial_tratamiento').click(function() {
        var newRow = `
            <tr>
                <td class="text-center"><input class="form-control" type="text" name="lugar[]"></td>
                <td class="text-center"><input class="form-control" type="text" name="duracion[]"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button></td>
            </tr>
        `;
        $('#table-body-historial_tratamiento').append(newRow);
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endsection