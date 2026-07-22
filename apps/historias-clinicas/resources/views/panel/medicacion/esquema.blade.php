@extends('layouts.panel')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Esquema de Prescripciones de {{ $medicacion->paciente->nombre ?? '' }} {{ $medicacion->paciente->apellido ?? '' }} ({{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }})
                </div>

                <div class="card-body">
                    @if($medicaciones->isEmpty())
                        <p>No hay prescripciones registradas para esta fecha.</p>
                    @else
                        @foreach($medicaciones as $horario => $medicacionesPorHorario)
                            <h4>{{ $horario }}</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Medicamento</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($medicacionesPorHorario as $medicacion)
                                            <tr>
                                                <td>{{ $medicacion->medicamento ?? '' }}</td>
                                                <td>{{ $medicacion->unidad ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endif

                    <a href="{{ route('panel.medicacion.show', $medicacion->id) }}" class="btn btn-secondary mt-3">Volver al esquema actual</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection