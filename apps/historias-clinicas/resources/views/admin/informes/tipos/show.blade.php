@extends('layouts.admin')
@section('content')

<div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.informes.tipos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.informe_tipo.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.informe_tipo.fields.id') }}
                        </th>
                        <td>
                            {{ $InformeTipo->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.informe_tipo.fields.name') }}
                        </th>
                        <td>
                            {{ $InformeTipo->name }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection