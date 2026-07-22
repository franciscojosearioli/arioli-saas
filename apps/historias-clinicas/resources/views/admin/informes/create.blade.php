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
                    {{ trans('global.create') }} {{ trans('cruds.informe.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("admin.informe.store") }}" enctype="multipart/form-data">
                        @csrf
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
                        <div class="form-group">
                            <label class="required" for="tipo_id">{{ trans('cruds.informe.fields.tipo') }}</label>
                            <select class="form-control select2 {{ $errors->has('tipo') ? 'is-invalid' : '' }}" name="tipo_id" id="tipo_id" required>
                                @foreach($tipos as $id => $entry)

                                @if($id == 1)
                                @can('informe_psicologico_create')
                                <option value="{{ $id }}" {{ old('tipo_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endcan
                                @endif
                                @if($id == 2)
                                @can('informe_psiquiatrico_create')
                                <option value="{{ $id }}" {{ old('tipo_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endcan
                                @endif
                                @if($id == 3)
                                @can('informe_operador_create')
                                <option value="{{ $id }}" {{ old('tipo_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endcan
                                @endif
                                @if($id == 4)
                                @can('informe_judicial_create')
                                <option value="{{ $id }}" {{ old('tipo_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endcan
                                @endif
                                @if($id == 5)
                                @can('informe_clinico_create')
                                <option value="{{ $id }}" {{ old('tipo_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endcan
                                @endif
                                @endforeach
                            </select>
                            @if($errors->has('tipo'))
                            <div class="invalid-feedback">
                                {{ $errors->first('tipo') }}
                            </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.informe.fields.tipo_helper') }}</span>
                        </div>
                                <div class="form-group">
                                    <label class="required" for="fecha">{{ trans('cruds.informe.fields.fecha') }}</label>
                                    <input class="form-control {{ $errors->has('fecha') ? 'is-invalid' : '' }}" type="date" name="fecha" id="fecha" value="{{ old('fecha', '') }}" required>
                                    @if($errors->has('fecha'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('fecha') }}
                                    </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.informe.fields.fecha_helper') }}</span>
                                </div>
                        <div class="form-group">
                            <label class="required" for="document_file">{{ trans('cruds.informe.fields.document_file') }}</label>
                            <div class="file-upload">
                    <input id="file-input" name="document_file[]" type="file" multiple>
                    <ul id="file-list" class="file-list"></ul>
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
    Dropzone.options.documentFileDropzone = {
        maxFilesize: 2, // MB
        maxFiles: 1,
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        params: {
            size: 2
        },
        success: function(file, response) {
            $('form').find('input[name="document_file"]').remove()
            $('form').append('<input type="hidden" name="document_file" value="' + response.name + '">')
        },
        removedfile: function(file) {
            file.previewElement.remove()
            if (file.status !== 'error') {
                $('form').find('input[name="document_file"]').remove()
                this.options.maxFiles = this.options.maxFiles + 1
            }
        },
        init: function() {
            @if(isset($Informe) && $Informe->document_file)
            var file = {
                !!json_encode($Informe->document_file) !!
            }
            this.options.addedfile.call(this, file)
            file.previewElement.classList.add('dz-complete')
            $('form').append('<input type="hidden" name="document_file" value="' + file.file_name + '">')
            this.options.maxFiles = this.options.maxFiles - 1
            @endif
        },
        error: function(file, response) {
            if ($.type(response) === 'string') {
                var message = response //dropzone sends it's own error messages in string
            } else {
                var message = response.errors.file
            }
            file.previewElement.classList.add('dz-error')
            _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
            _results = []
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                node = _ref[_i]
                _results.push(node.textContent = message)
            }

            return _results
        }
    }
</script>
@endsection