@extends('layouts.app')
@section('content')
<div class="pt-wrap" style="max-width:640px;margin:0 auto;padding:28px 16px;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('panel.paciente.show', $paciente->id) }}#consentimientos" style="font-size:13px;color:#64748b;">
            ← Volver a {{ $paciente->apellido }}, {{ $paciente->nombre }}
        </a>
        <h1 style="font-size:20px;font-weight:700;margin-top:8px;">Nuevo consentimiento</h1>
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
        <div style="padding:18px 24px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
            <p style="margin:0;font-size:13px;color:#64748b;">
                Paciente: <strong style="color:#0f172a;">{{ $paciente->apellido }}, {{ $paciente->nombre }}</strong>
                @if($paciente->dni) · DNI {{ $paciente->dni }} @endif
            </p>
        </div>
        <form method="POST" action="{{ route('panel.consentimiento.store', $paciente->id) }}" style="padding:24px;">
            @csrf
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;margin-bottom:8px;">
                    Tipo de consentimiento
                </label>
                @foreach($tipos as $tipo)
                <label style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:8px;cursor:pointer;transition:border-color .12s;"
                       class="tipo-option">
                    <input type="radio" name="tipo_id" value="{{ $tipo->id }}" required style="margin-top:2px;flex-shrink:0;">
                    <div>
                        <strong style="font-size:14px;color:#0f172a;">{{ $tipo->nombre }}</strong>
                        @if($tipo->descripcion)
                        <p style="margin:2px 0 0;font-size:12px;color:#64748b;">{{ $tipo->descripcion }}</p>
                        @endif
                        @if($tipo->requiere_firma_profesional)
                        <span style="display:inline-flex;align-items:center;gap:4px;margin-top:5px;font-size:11px;font-weight:600;color:#1d4ed8;background:#eff6ff;border-radius:5px;padding:2px 8px;">
                            Requiere firma profesional
                        </span>
                        @endif
                    </div>
                </label>
                @endforeach

                @if($tipos->isEmpty())
                <p style="color:#94a3b8;font-size:13px;padding:16px;text-align:center;">
                    No hay tipos de consentimiento configurados.
                    <a href="{{ route('admin.tipos-consentimiento.create') }}">Crear uno</a>
                </p>
                @endif
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('panel.paciente.show', $paciente->id) }}#consentimientos"
                   style="padding:10px 20px;border:1px solid #e2e8f0;border-radius:10px;text-decoration:none;color:#64748b;font-size:13px;">
                    Cancelar
                </a>
                <button type="submit"
                   style="padding:10px 24px;background:#1d4ed8;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;">
                    Crear consentimiento
                </button>
            </div>
        </form>
    </div>
</div>
<style>
.tipo-option:has(input:checked) { border-color:#1d4ed8;background:#eff6ff; }
</style>
@endsection
