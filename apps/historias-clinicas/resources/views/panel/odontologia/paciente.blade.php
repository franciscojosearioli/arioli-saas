@extends('layouts.panel')
@section('title', 'Odontología — ' . $paciente->apellido . ', ' . $paciente->nombre)

@section('content')
<div style="max-width:640px; margin:30px auto; padding:0 20px;">
    <h1 style="font-size:18px; font-weight:700; margin-bottom:4px;">Ficha odontológica</h1>
    <p style="font-size:13px; color:var(--text-secondary,#64748b); margin-bottom:20px;">{{ $paciente->apellido }}, {{ $paciente->nombre }}</p>

    <form method="POST" action="{{ route('panel.odontologia.crear', $paciente) }}" style="margin-bottom:20px;">
        @csrf
        <button type="submit" class="btn-save" style="padding:8px 16px;border-radius:8px;background:#1d4ed8;color:#fff;border:none;font-size:13px;cursor:pointer;">
            + Nuevo odontograma
        </button>
    </form>

    @if($odontogramas->isEmpty())
    <p style="font-size:13px; color:var(--text-muted,#94a3b8);">Sin odontogramas registrados todavía.</p>
    @else
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
        <thead>
            <tr style="text-align:left; border-bottom:1px solid var(--card-border,#e2e8f0);">
                <th style="padding:6px 0;">Fecha</th>
                <th>Profesional</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($odontogramas as $o)
            <tr style="border-bottom:1px solid var(--card-border,#e2e8f0);">
                <td style="padding:6px 0;">{{ $o->fecha->format('d/m/Y') }}</td>
                <td>{{ $o->profesional?->name ?? '—' }}</td>
                <td><a href="{{ route('panel.odontologia.show', $o) }}">Ver</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
