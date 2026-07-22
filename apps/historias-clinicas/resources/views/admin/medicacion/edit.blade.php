@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.medicacion.title_singular') }}
    </div>

    <div class="card-body">
    <form method="POST" action="{{ route("admin.medicacion.update", $Medicacion->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="required" for="paciente_id">{{ trans('cruds.informe.fields.paciente') }}</label>
                        <select class="form-control select2 {{ $errors->has('paciente_id') ? 'is-invalid' : '' }}" name="paciente_id" id="paciente_id" required>
                            @foreach($pacientes as $id => $entry)
                                <option value="{{ $id }}" {{ $Medicacion->paciente_id == $id ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('paciente_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('paciente_id') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.informe.fields.paciente_helper') }}</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <table class="table table-bordered" id="table-medicacion"> 
                    <thead>
                        <tr>
                            <th>{{ trans('cruds.medicacion.fields.medicamento') }}</th>
                            <th>{{ trans('cruds.medicacion.fields.cantidad') }}</th>
                            <th>{{ trans('cruds.medicacion.fields.unidad') }}</th>
                            <th>{{ trans('cruds.medicacion.fields.horario') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr>
                                <td>
                                    <input class="form-control {{ $errors->has('medicamento') ? 'is-invalid' : '' }}" type="text" name="medicamento" value="{{ $Medicacion->medicamento }}" required>
                                    @if($errors->has('medicamento'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('medicamento') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.medicacion.fields.medicamento_helper') }}</span>
                                </td>
                                <td>
                                    <input class="form-control {{ $errors->has('cantidad') ? 'is-invalid' : '' }}" type="text" name="cantidad" value="{{ $Medicacion->cantidad }}" required>
                                    @if($errors->has('cantidad'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('cantidad') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.medicacion.fields.cantidad_helper') }}</span>
                                </td>
                                <td>
                                    <select class="form-control select2 {{ $errors->has('unidad') ? 'is-invalid' : '' }}" name="unidad" required>
                                        <option value="g" {{ $Medicacion->unidad == 'g' ? 'selected' : '' }}>g</option>
                                        <option value="mg" {{ $Medicacion->unidad == 'mg' ? 'selected' : '' }}>mg</option>
                                    </select>
                                    @if($errors->has('unidad'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('unidad') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.medicacion.fields.unidad_helper') }}</span>
                                </td>
                                <td>
                                    <select class="form-control select2 {{ $errors->has('horario') ? 'is-invalid' : '' }}" name="horario" required>
                                        <option value="Mañana" {{ $Medicacion->horario == 'Mañana' ? 'selected' : '' }}>Mañana</option>
                                        <option value="Mediodia" {{ $Medicacion->horario == 'Mediodia' ? 'selected' : '' }}>Mediodia</option>
                                        <option value="Tarde" {{ $Medicacion->horario == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                                        <option value="Noche" {{ $Medicacion->horario == 'Noche' ? 'selected' : '' }}>Noche</option>
                                    </select>
                                    @if($errors->has('horario'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('horario') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.medicacion.fields.horario_helper') }}</span>
                                </td>
                            </tr>
                    </tbody>
                </table>
            </div>

            <br><br>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection