@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.medicacion.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.medicacion.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                {{ trans('cruds.medicacion.title') }}
            </div>

            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.medicacion.fields.id') }}
                            </th>
                            <td>
                                {{ $Medicacion->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.medicacion.fields.paciente') }}
                            </th>
                            <td>
                                {{ $Medicacion->paciente->nombre ?? '' }} {{ $Medicacion->paciente->apellido ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.medicacion.fields.medicamento') }}
                            </th>
                            <td>
                                {{ $Medicacion->medicamento }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.medicacion.fields.cantidad') }}
                            </th>
                            <td>
                                {{ $Medicacion->cantidad }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.medicacion.fields.unidad') }}
                            </th>
                            <td>
                                {{ $Medicacion->unidad }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.medicacion.fields.horario') }}
                            </th>
                            <td>
                                {{ $Medicacion->horario }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.medicacion.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection