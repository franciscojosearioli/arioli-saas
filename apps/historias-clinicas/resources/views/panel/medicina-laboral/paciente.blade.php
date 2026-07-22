@extends('layouts.panel')
@section('title', 'Medicina Laboral — ' . $paciente->apellido . ', ' . $paciente->nombre)

@section('content')
<div style="max-width:640px; margin:30px auto; padding:0 20px;">
    <h1 style="font-size:18px; font-weight:700; margin-bottom:4px;">Evaluaciones laborales</h1>
    <p style="font-size:13px; color:var(--text-secondary,#64748b); margin-bottom:20px;">{{ $paciente->apellido }}, {{ $paciente->nombre }}</p>

    @can('medicina_laboral_create')
    <form method="POST" action="{{ route('panel.medicina-laboral.crear', $paciente) }}" style="margin-bottom:24px; padding:16px; border:1px solid var(--card-border,#e2e8f0); border-radius:10px;">
        @csrf
        <div style="display:flex; gap:10px; margin-bottom:10px;">
            <select name="tipo" required style="flex:1; padding:6px;">
                <option value="">Tipo...</option>
                @foreach(\App\Modules\MedicinaLaboral\Models\EvaluacionLaboral::tiposLabels() as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="fecha" required style="flex:1; padding:6px;" value="{{ date('Y-m-d') }}">
            <select name="estado" required style="flex:1; padding:6px;">
                <option value="">Estado...</option>
                @foreach(\App\Modules\MedicinaLaboral\Models\EvaluacionLaboral::estadosLabels() as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <textarea name="observaciones" placeholder="Observaciones (opcional)" style="width:100%; padding:6px; margin-bottom:10px;"></textarea>
        <button type="submit" style="padding:8px 16px;border-radius:8px;background:#1d4ed8;color:#fff;border:none;font-size:13px;cursor:pointer;">
            Registrar evaluación
        </button>
    </form>
    @endcan

    @if($evaluaciones->isEmpty())
    <p style="font-size:13px; color:var(--text-muted,#94a3b8);">Sin evaluaciones registradas todavía.</p>
    @else
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
        <thead>
            <tr style="text-align:left; border-bottom:1px solid var(--card-border,#e2e8f0);">
                <th style="padding:6px 0;">Fecha</th>
                <th>Tipo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluaciones as $e)
            <tr style="border-bottom:1px solid var(--card-border,#e2e8f0);">
                <td style="padding:6px 0;">{{ $e->fecha->format('d/m/Y') }}</td>
                <td>{{ \App\Modules\MedicinaLaboral\Models\EvaluacionLaboral::tiposLabels()[$e->tipo] ?? $e->tipo }}</td>
                <td>{{ \App\Modules\MedicinaLaboral\Models\EvaluacionLaboral::estadosLabels()[$e->estado] ?? $e->estado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
