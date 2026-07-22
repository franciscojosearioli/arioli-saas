@extends('layouts.admin')
@section('content')
<div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pacientes.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <div class="mb-4">
                {{ trans('global.add') }} {{ trans('cruds.paciente.title_singular') }}
            </div>

            <form method="POST" action="{{ route("admin.paciente.store") }}" enctype="multipart/form-data">
                @method('POST')
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
                                    <input class="form-control" type="date" name="fecha_ingreso" id="fecha_ingreso" value="{{ old('fecha_ingreso', '') }}" required>
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
                                        <option value="internacion">Internacion</option>
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
                                    <label for="first_name">{{ trans('cruds.paciente.fields.fecha_egreso') }}</label>
                                    <input class="form-control" type="date" name="first_name" id="first_name" value="{{ old('first_name', '') }}">
                                    @if($errors->has('first_name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('first_name') }}
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
                                        <option value="Alta">Alta</option>
                                        <option value="Retiro">Retiro</option>
                                        <option value="Fuga">Fuga</option>
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
                        {{ trans('cruds.paciente.datos_paciente') }}
                    </div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="nombre">{{ trans('cruds.paciente.fields.nombre') }}</label>
                                    <input class="form-control" type="text" name="nombre" id="nombre" value="{{ old('nombre', '') }}" required>
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
                                    <input class="form-control" type="text" name="apellido" id="apellido" value="{{ old('apellido', '') }}" required>
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
                                    <input class="form-control" type="text" name="dni" id="dni" value="{{ old('dni', '') }}" required>
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
                                    <input class="form-control" type="date" name="fecha_nac" id="fecha_nac" value="{{ old('fecha_nac', '') }}" required>
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
                                    <input class="form-control" type="number" name="edad" id="edad" value="{{ old('edad', '') }}" required>
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
                                        <option selected disabled>Seleccione un sexo</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
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
                                        <option selected disabled>Seleccione un estado civil</option>
                                        <option value="Soltero">Soltero</option>
                                        <option value="Casado">Casado</option>
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
                                    <input class="form-control" type="text" name="obra_social" id="obra_social" value="{{ old('obra_social', '') }}">
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
                                    <input class="form-control" type="text" name="n_afiliado" id="n_afiliado" value="{{ old('n_afiliado', '') }}">
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
                                    <input class="form-control" type="text" name="provincia" id="provincia" value="{{ old('provincia', '') }}" required>
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
                                    <input class="form-control" type="text" name="localidad" id="localidad" value="{{ old('localidad', '') }}" required>
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
                                    <input class="form-control" type="text" name="calle" id="calle" value="{{ old('calle', '') }}" required>
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
                                    <input class="form-control" type="text" name="calle_numero" id="calle_numero" value="{{ old('calle_numero', '') }}" required>
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
                                    <input class="form-control" type="text" name="calle_piso" id="calle_piso" value="{{ old('calle_piso', '') }}">
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
                                    <input class="form-control" type="text" name="calle_dpto" id="calle_dpto" value="{{ old('calle_dpto', '') }}">
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

                <div class="card mt-5" id="conyuge-form" style="display: none;">
                    <div class="card-header">
                        {{ trans('cruds.paciente.datos_conyugue') }}
                    </div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="conyugue_nombre">{{ trans('cruds.paciente.fields.conyugue_nombre') }}</label>
                                    <input class="form-control" type="text" name="conyugue_nombre" id="conyugue_nombre" value="{{ old('conyugue_nombre', '') }}">
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
                                    <input class="form-control" type="text" name="conyugue_apellido" id="conyugue_apellido" value="{{ old('conyugue_apellido', '') }}">
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
                                    <input class="form-control" type="number" name="conyugue_edad" id="conyugue_edad" value="{{ old('conyugue_edad', '') }}">
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
                                    <input class="form-control" type="text" name="conyugue_domicilio" id="conyugue_domicilio" value="{{ old('conyugue_domicilio', '') }}">
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
                                                <td class="text-center"><input class="form-control" type="text" name="padre_nombre"></td>
                                                <td class="text-center"><input class="form-control" type="number" name="padre_telefono"></td>
                                                <td class="text-center">
                                                    <select class="form-control select2 {{ $errors->has('padre_responsable') ? 'is-invalid' : '' }}" name="padre_responsable" id="padre_responsable">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>      
                                            </tr>
                                            <tr>
                                                <td class="text-center">Madre</td>
                                                <td class="text-center"><input class="form-control" type="text" name="madre_nombre"></td>
                                                <td class="text-center"><input class="form-control" type="number" name="madre_telefono"></td>
                                                <td class="text-center">
                                                    <select class="form-control select2 {{ $errors->has('madre_responsable') ? 'is-invalid' : '' }}" name="madre_responsable" id="madre_responsable">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>      
                                            </tr>
                                            <tr>
                                                <td class="text-center">Tutor</td>
                                                <td class="text-center"><input class="form-control" type="text" name="tutor_nombre"></td>
                                                <td class="text-center"><input class="form-control" type="number" name="tutor_telefono"></td>
                                                <td class="text-center">
                                                    <select class="form-control select2 {{ $errors->has('tutor_responsable') ? 'is-invalid' : '' }}" name="tutor_responsable" id="tutor_responsable">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                        <div class="row justify-content-center" id="table-container-hijos" style="display: none;">
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
                                            <tr>
                                                <td class="text-center"><input class="form-control" type="text" name="hijos_nombre[]"></td>
                                                <td class="text-center"><input class="form-control" type="number" name="hijos_edad[]"></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="hijos_tutor[]">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary" id="add-row-hijos">Agregar fila</button>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="required" for="familiar_hermanos">{{ trans('cruds.paciente.fields.familiar_hermanos') }}</label>
                                    <select class="form-control select2 {{ $errors->has('familiar_hermanos') ? 'is-invalid' : '' }}" name="familiar_hermanos" id="familiar_hermanos" required>
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                        <div class="row justify-content-center" id="table-container-hermanos" style="display: none;">
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
                                            <tr>
                                                <td class="text-center"><input class="form-control" type="text" name="hermanos_nombre[]"></td>
                                                <td class="text-center"><input class="form-control" type="number" name="hermanos_edad[]"></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="hermanos_convive[]">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary" id="add-row-hermanos">Agregar fila</button>
                                </div>
                            </div>
                        </div>
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
                                                <th>Ultimo año cursado</th>
                                                <th>Expulsado</th>
                                                <th>Interrumpido</th>
                                                <th>Cambios</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ trans('cruds.paciente.fields.primaria') }}</td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="primaria_completa">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="primaria_incompleta">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center"><input class="form-control" type="number" name="primaria_ultimo_anio"></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="primaria_expulsado">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="primaria_interrumpido">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="primaria_cambios">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                                            </tr>
                                            <tr>
                                                <td>{{ trans('cruds.paciente.fields.secundaria') }}</td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="secundaria_completa">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="secundaria_incompleta">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center"><input class="form-control" type="number" name="secundaria_ultimo_anio"></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="secundaria_expulsado">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="secundaria_interrumpido">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="secundaria_cambios">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                                            </tr>
                                            <tr>
                                                <td>{{ trans('cruds.paciente.fields.terciaria') }}</td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="terciaria_completa">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="terciaria_incompleta">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center"><input class="form-control" type="number" name="terciaria_ultimo_anio"></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="terciaria_expulsado">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="terciaria_interrumpido">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="terciaria_cambios">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                                            </tr>
                                            <tr>
                                                <td>{{ trans('cruds.paciente.fields.facultad') }}</td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="facultad_completa">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="facultad_incompleta">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center"><input class="form-control" type="number" name="facultad_ultimo_anio"></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="facultad_expulsado">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="facultad_interrumpido">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                                                <td class="text-center">
                                                <select class="form-control select2" name="facultad_cambios">
                                                        <option value="Si">Si</option>
                                                        <option value="No" selected>No</option>
                                                    </select></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observaciones">{{ trans('cruds.paciente.fields.observaciones') }}</label>
                                    <textarea class="form-control {{ $errors->has('observaciones') ? 'is-invalid' : '' }}" name="observaciones" id="observaciones">{{ old('observaciones') }}</textarea>
                                    @if($errors->has('observaciones'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('observaciones') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.observaciones_helper') }}</span>
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                                    <input class="form-control" type="text" name="empresa_laboral" id="empresa_laboral" value="{{ old('empresa_laboral', '') }}">
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
                                    <input class="form-control" type="text" name="cargo_laboral" id="cargo_laboral" value="{{ old('cargo_laboral', '') }}">
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
                                    <input class="form-control" type="text" name="antiguedad_laboral" id="antiguedad_laboral" value="{{ old('antiguedad_laboral', '') }}">
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
                                    <textarea class="form-control {{ $errors->has('antecedente_laboral') ? 'is-invalid' : '' }}" name="antecedente_laboral" id="antecedente_laboral">{{ old('antecedente_laboral') }}</textarea>
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                        <div class="row justify-content-center" id="table-container-historial_tratamiento" style="display: none;">
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
                                            <tr>
                                                <td class="text-center"><input class="form-control" type="text" name="lugar[]"></td>
                                                <td class="text-center"><input class="form-control" type="text" name="duracion[]"></td>
                                                <td class="text-center"></td>
                                            </tr>
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
                                    <input class="form-control" type="text" name="problematica" id="problematica" value="{{ old('problematica', '') }}">
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
                                    <textarea class="form-control {{ $errors->has('problematica_detalles') ? 'is-invalid' : '' }}" name="problematica_detalles" id="problematica_detalles">{{ old('problematica_detalles') }}</textarea>
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
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
                                    <input class="form-control" type="text" name="tiempo_privado_libertad" id="tiempo_privado_libertad" value="{{ old('tiempo_privado_libertad', '') }}">
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
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
                                    </select>
                                    @if($errors->has('enfermedad_cronica'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('enfermedad_cronica') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.enfermedad_cronica_helper') }}</span>
                                </div>
                                <div class="form-group" id="form-enfermedad_cronica_detalles" style="display: none;">
                                    <label for="enfermedad_cronica_detalles">{{ trans('cruds.paciente.fields.enfermedad_cronica_detalles') }}</label>
                                    <textarea class="form-control {{ $errors->has('enfermedad_cronica_detalles') ? 'is-invalid' : '' }}" name="enfermedad_cronica_detalles" id="enfermedad_cronica_detalles">{{ old('enfermedad_cronica_detalles') }}</textarea>
                                    @if($errors->has('enfermedad_cronica_detalles'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('enfermedad_cronica_detalles') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.enfermedad_cronica_detalles_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="alergia">{{ trans('cruds.paciente.fields.alergia') }}</label>
                                    <select class="form-control select2 {{ $errors->has('alergia') ? 'is-invalid' : '' }}" name="alergia" id="alergia" required>
                                        <option selected disabled>Seleccione una opcion</option>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
                                    </select>
                                    @if($errors->has('alergia'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('alergia') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.alergia_helper') }}</span>
                                </div>
                                <div class="form-group" id="form-alergia_detalles" style="display: none;">
                                    <label for="alergia_detalles">{{ trans('cruds.paciente.fields.alergia_detalles') }}</label>
                                    <textarea class="form-control {{ $errors->has('alergia_detalles') ? 'is-invalid' : '' }}" name="alergia_detalles" id="alergia_detalles">{{ old('alergia_detalles') }}</textarea>
                                    @if($errors->has('alergia_detalles'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('alergia_detalles') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.paciente.fields.alergia_detalles_helper') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            <div class="form-group mt-5">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
            </form>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#estado_civil').change(function() {
        if ($(this).val() === 'Casado') {
            $('#conyuge-form').show();
        } else {
            $('#conyuge-form').hide();
        }
    });
    $('#historial_tratamiento').change(function() {
        if ($(this).val() === 'Si') {
            $('#table-container-historial_tratamiento').show();
        } else {
            $('#table-container-historial_tratamiento').hide();
        }
    });
    $('#familiar_hijos').change(function() {
        if ($(this).val() === 'Si') {
            $('#table-container-hijos').show();
        } else {
            $('#table-container-hijos').hide();
        }
    });
    $('#familiar_hermanos').change(function() {
        if ($(this).val() === 'Si') {
            $('#table-container-hermanos').show();
        } else {
            $('#table-container-hermanos').hide();
        }
    });
    
    $('#alergia').change(function() {
        if ($(this).val() === 'Si') {
            $('#form-alergia_detalles').show();
        } else {
            $('#form-alergia_detalles').hide();
        }
    });
    
    $('#enfermedad_cronica').change(function() {
        if ($(this).val() === 'Si') {
            $('#form-enfermedad_cronica_detalles').show();
        } else {
            $('#form-enfermedad_cronica_detalles').hide();
        }
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