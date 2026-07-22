@extends('layouts.admin')
@section('content')

<div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.medicacion.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    {{ trans('global.create') }} {{ trans('cruds.medicacion.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("admin.medicacion.store") }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="required" for="paciente_id">{{ trans('cruds.informe.fields.paciente') }}</label>
                                    <select class="form-control select2 {{ $errors->has('paciente') ? 'is-invalid' : '' }}" name="paciente_id" id="paciente_id" required>
                                        @foreach($pacientes as $id => $entry)
                                        <option value="{{ $id }}" {{ old('paciente_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('paciente'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('paciente') }}
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
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input class="form-control {{ $errors->has('medicamento') ? 'is-invalid' : '' }}" type="text" name="medicamento[]" id="medicamento" required>
                                @if($errors->has('medicamento'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('medicamento') }}
                                </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.medicacion.fields.medicamento_helper') }}</span>
                            </td>
                            <td>
                                <input class="form-control {{ $errors->has('cantidad') ? 'is-invalid' : '' }}" type="text" name="cantidad[]" id="cantidad" required>
                                @if($errors->has('cantidad'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('cantidad') }}
                                </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.medicacion.fields.cantidad_helper') }}</span>
                            </td>
                            <td>
                                <select class="form-control select2 {{ $errors->has('unidad') ? 'is-invalid' : '' }}" name="unidad[]" id="unidad" required>
                                    <option selected disabled>Seleccione</option>
                                    <option value="g">g</option>
                                    <option value="mg">mg</option>
                                </select>
                                @if($errors->has('unidad'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('unidad') }}
                                </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.medicacion.fields.unidad_helper') }}</span>
                            </td>
                            <td>
                                <select class="form-control select2 {{ $errors->has('horario') ? 'is-invalid' : '' }}" name="horario[]" id="horario" required>
                                    <option selected disabled>Seleccione</option>
                                    <option value="Mañana">Mañana</option>
                                    <option value="Mediodia">Mediodia</option>
                                    <option value="Tarde">Tarde</option>
                                    <option value="Noche">Noche</option>
                                </select>
                                @if($errors->has('horario'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('horario') }}
                                </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.medicacion.fields.horario_helper') }}</span>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary" id="add-medicacion">Agregar fila</button>
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





@section('scripts')
<script>
$(document).ready(function() {
$('#add-medicacion').click(function() {
        var newRow = `
            <tr>
                            <td>
                                <input class="form-control {{ $errors->has('medicamento') ? 'is-invalid' : '' }}" type="text" name="medicamento[]" id="medicamento">
                                @if($errors->has('medicamento'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('medicamento') }}
                                </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.medicacion.fields.medicamento_helper') }}</span>
                            </td>
                            <td>
                                <input class="form-control {{ $errors->has('cantidad') ? 'is-invalid' : '' }}" type="text" name="cantidad[]" id="cantidad">
                                @if($errors->has('cantidad'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('cantidad') }}
                                </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.medicacion.fields.cantidad_helper') }}</span>
                            </td>
                            <td>
                                <select class="form-control select2 {{ $errors->has('unidad') ? 'is-invalid' : '' }}" name="unidad[]" id="unidad" >
                                    <option selected disabled>Seleccione</option>
                                    <option value="g">g</option>
                                    <option value="mg">mg</option>
                                </select>
                                @if($errors->has('unidad'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('unidad') }}
                                </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.medicacion.fields.unidad_helper') }}</span>
                            </td>
                            <td>
                                <select class="form-control select2 {{ $errors->has('horario') ? 'is-invalid' : '' }}" name="horario[]" id="horario" required>
                                    <option selected disabled>Seleccione</option>
                                    <option value="Mañana">Mañana</option>
                                    <option value="Mediodia">Mediodia</option>
                                    <option value="Tarde">Tarde</option>
                                    <option value="Noche">Noche</option>
                                </select>
                                @if($errors->has('horario'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('horario') }}
                                </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.medicacion.fields.horario_helper') }}</span>
                            </td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">Eliminar</button></td>
            </tr>
        `;
        $('#table-medicacion').append(newRow);
    });
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endsection