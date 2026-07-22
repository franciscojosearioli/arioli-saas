@extends('layouts.panel')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Documentos del Informe</h4>
    </div>

    <div class="card-body">

        @forelse ($files as $file)

            <iframe
                src="{{ asset('storage/uploads/' . $informe->paciente->id . '/' . $informe->tipo->id . '/' . $file) }}#toolbar=0&navpanes=0&scrollbar=0"
                style="width:100%;height:600px;border:none;margin-bottom:30px"
            ></iframe>

        @empty
            <p>No hay documentos disponibles.</p>
        @endforelse

    </div>
</div>
@endsection
