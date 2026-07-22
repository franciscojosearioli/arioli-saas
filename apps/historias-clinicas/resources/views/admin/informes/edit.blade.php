@extends('layouts.admin')

@section('content')
<style>
    .file-upload {
        border: 2px dashed #ccc;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    .file-input-label {
        display: inline-block;
        cursor: pointer;
        padding: 10px 20px;
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .file-input-label i {
        margin-right: 5px;
    }

    .file-list {
        list-style-type: none;
        padding: 0;
    }

    .file-list li {
        margin-bottom: 5px;
    }

    .file-list li .file-info {
        display: inline-block;
        margin-right: 10px;
    }

    .file-list li .delete-file {
        cursor: pointer;
        color: red;
    }
</style>
<div class="form-group">
    <a class="btn btn-default" href="{{ route('admin.informe.index') }}">
        {{ trans('global.back_to_list') }}
    </a>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.informe.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.informe.update", $Informe->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="lock_version" value="{{ $Informe->lock_version }}">
            @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)<p style="margin:0">{{ $error }}</p>@endforeach
            </div>
            @endif
            <div class="form-group">
                <label class="required" for="paciente_id">{{ trans('cruds.informe.fields.paciente') }}</label>
                <select class="form-control select2 {{ $errors->has('paciente_id') ? 'is-invalid' : '' }}" name="paciente_id" id="paciente_id" required>
                    @foreach($pacientes as $id => $entry)
                    <option value="{{ $id }}" {{ old('paciente_id', $Informe->paciente_id) == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('paciente_id'))
                <div class="invalid-feedback">
                    {{ $errors->first('paciente_id') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.informe.fields.paciente_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="tipo_id">{{ trans('cruds.informe.fields.tipo') }}</label>
                <select class="form-control select2 {{ $errors->has('tipo_id') ? 'is-invalid' : '' }}" name="tipo_id" id="tipo_id" required>
                    @foreach($tipos as $id => $entry)
                    <option value="{{ $id }}" {{ old('tipo_id', $Informe->tipo_id) == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('tipo_id'))
                <div class="invalid-feedback">
                    {{ $errors->first('tipo_id') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.informe.fields.tipo_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="fecha">{{ trans('cruds.informe.fields.fecha') }}</label>
                <input class="form-control {{ $errors->has('fecha') ? 'is-invalid' : '' }}" type="date" name="fecha" id="fecha" value="{{ old('fecha', $Informe->fecha) }}" required>
                @if($errors->has('fecha'))
                <div class="invalid-feedback">
                    {{ $errors->first('fecha') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.informe.fields.fecha_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="document_file">{{ trans('cruds.informe.fields.document_file') }}</label>
                <div class="file-upload">
                    <input id="file-input" name="document_file[]" type="file" multiple>
                    <ul id="file-list" class="file-list">
                        @if(isset($attachedFiles))
                            @foreach($attachedFiles as $file)
                                <li>
                                    <span class="file-info">{{ basename($file) }}</span>
                                    <a href="#" class="delete-file" data-file="{{ $file }}">Delete</a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                @if($errors->has('document_file'))
                <div class="invalid-feedback">
                    {{ $errors->first('document_file') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.informe.fields.document_file_helper') }}</span>
            </div>
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
    document.addEventListener('DOMContentLoaded', function () {
        // Handle file removal
        document.querySelectorAll('.delete-file').forEach(function (element) {
            element.addEventListener('click', function (e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this file?')) {
                    // Remove the file from the list
                    this.parentElement.remove();
                }
            });
        });
    });
</script>
@endsection