@extends('layouts.admin')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-stethoscope mr-1"></i> Editar Especialidad</h5>
            <a href="{{ route('admin.especialidades.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.especialidades.update', $especialidad->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label for="nombre" class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $especialidad->nombre) }}" required>
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción <span class="text-muted small">(opcional)</span></label>
                        <input type="text" name="descripcion" id="descripcion"
                               class="form-control @error('descripcion') is-invalid @enderror"
                               value="{{ old('descripcion', $especialidad->descripcion) }}">
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.especialidades.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
