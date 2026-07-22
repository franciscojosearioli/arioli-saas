@extends('layouts.panel')
@section('title', 'Odontograma')

@section('content')
<div style="max-width:640px; margin:30px auto; padding:0 20px;">
    <h1 style="font-size:18px; font-weight:700; margin-bottom:4px;">Odontograma — {{ $odontograma->fecha->format('d/m/Y') }}</h1>
    <p style="font-size:13px; color:var(--text-secondary,#64748b); margin-bottom:20px;">
        {{ $odontograma->paciente->apellido }}, {{ $odontograma->paciente->nombre }}
    </p>

    <table style="width:100%; border-collapse:collapse; font-size:13px;">
        <thead>
            <tr style="text-align:left; border-bottom:1px solid var(--card-border,#e2e8f0);">
                <th style="padding:6px 0;">Pieza</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($odontograma->piezas as $pieza)
            <tr style="border-bottom:1px solid var(--card-border,#e2e8f0);">
                <td style="padding:6px 0;">{{ $pieza->numero }}</td>
                <td>{{ \App\Modules\Odontologia\Models\PiezaDental::estadosLabels()[$pieza->estado] ?? $pieza->estado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
