@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('cruds.informe.title_singular') }} {{ $Informe->tipo->name ?? '' }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.informe.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>
        
        <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>{{ trans('cruds.informe.fields.paciente') }}</th>
                            <td>{{ $Informe->paciente->nombre ?? '' }} {{ $Informe->paciente->apellido ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans('cruds.informe.fields.fecha') }}</th>
                            <td>{{ $Informe->fecha ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>

        @if($attachedFiles)
            @foreach($attachedFiles as $file)
                <div class="mb-3">
                    <iframe src="{{ asset('storage/uploads/' . $Informe->paciente->id . '/' . $Informe->tipo->id . '/' . $file) }}" width="100%" height="600px" style="border: none;"></iframe>
                </div>
            @endforeach
        @else
            <p>{{ trans('global.no_files_attached') }}</p>
        @endif

        <div class="form-group">
            <a class="btn btn-default" href="{{ route('admin.informe.index') }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>
    </div>
</div>
@endsection