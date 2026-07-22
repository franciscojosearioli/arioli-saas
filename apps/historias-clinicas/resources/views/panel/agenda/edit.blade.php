@extends('layouts.panel')
@section('title', 'Editar Cita')

@push('styles')
<style>
.cit-wrap { max-width: 820px; margin: 0 auto; padding: 0 4px; animation: citFadeUp .3s ease both; }
@@keyframes citFadeUp {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.cit-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; flex-wrap: wrap; gap: 10px; }
.cit-header h1 { font-size: 22px; font-weight: 700; color: var(--text-primary, #0f172a); letter-spacing: -.02em; margin: 0; }
.cit-header p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

.cit-card { background: var(--card-bg, #fff); border: 1px solid var(--card-border, #e8edf2); border-radius: var(--card-radius, 14px); box-shadow: var(--card-shadow); margin-bottom: 18px; overflow: hidden; }
.cit-card-hdr { display: flex; align-items: center; gap: 8px; padding: 14px 20px; border-bottom: 1px solid var(--card-border, #e8edf2); font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a); }
.cit-card-hdr svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); flex-shrink: 0; }
.cit-card-body { padding: 20px; }

.cit-label { display: block; font-size: 10px; font-weight: 600; color: var(--text-muted, #94a3b8); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 5px; }
.cit-label .req { color: #dc2626; margin-left: 2px; }
.cit-input, .cit-select { width: 100%; padding: 8px 12px; border: 1px solid var(--card-border, #e8edf2); border-radius: 8px; font-size: 13px; font-family: var(--font-sans, inherit); color: var(--text-primary, #0f172a); background: var(--body-bg, #f8fafc); outline: none; transition: border-color .15s; }
.cit-input:focus, .cit-select:focus { border-color: var(--accent, #1d4ed8); background: var(--card-bg, #fff); }
.cit-input.is-invalid, .cit-select.is-invalid { border-color: #dc2626 !important; }
.cit-error { font-size: 11px; color: #dc2626; margin-top: 4px; }
.cit-row { display: flex; gap: 14px; }
.cit-row > * { flex: 1; min-width: 0; }
.cit-field { margin-bottom: 16px; }
.cit-field:last-child { margin-bottom: 0; }

.cit-toggle-group { display: flex; gap: 8px; }
.cit-toggle-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border: 2px solid var(--card-border, #e8edf2); border-radius: 10px; background: var(--body-bg, #f8fafc); cursor: pointer; transition: all .15s; color: var(--text-secondary, #64748b); font-size: 13px; font-weight: 600; text-align: center; }
.cit-toggle-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
.cit-toggle-btn:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); }
.cit-toggle-btn.active { border-color: var(--accent, #1d4ed8); background: var(--accent, #1d4ed8); color: #fff; }

/* Estado toggle */
.estado-toggle-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 10px; border: 2px solid var(--card-border, #e8edf2); border-radius: 10px; background: var(--body-bg, #f8fafc); cursor: pointer; transition: all .15s; color: var(--text-secondary, #64748b); font-size: 12px; font-weight: 600; text-align: center; }
.estado-toggle-btn:hover { opacity: .85; }
.estado-toggle-btn.active-pendiente  { border-color: #f59e0b; background: #f59e0b; color: #fff; }
.estado-toggle-btn.active-confirmado { border-color: #16a34a; background: #16a34a; color: #fff; }
.estado-toggle-btn.active-cancelado  { border-color: #dc2626; background: #dc2626; color: #fff; }
.estado-toggle-btn.active-realizado  { border-color: #64748b; background: #64748b; color: #fff; }

.cit-actions { display: flex; align-items: center; justify-content: space-between; padding: 4px 0 24px; flex-wrap: wrap; gap: 10px; }
.btn-cit-back { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 10px; border: 1px solid var(--card-border, #e8edf2); background: var(--card-bg, #fff); color: var(--text-secondary, #64748b); font-size: 13px; font-weight: 600; text-decoration: none; transition: all .15s; }
.btn-cit-back:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); text-decoration: none; }
.btn-cit-save { display: inline-flex; align-items: center; gap: 7px; padding: 9px 22px; border-radius: 10px; background: var(--accent, #1d4ed8); color: #fff; border: none; font-size: 13px; font-weight: 600; cursor: pointer; font-family: var(--font-sans, inherit); box-shadow: 0 2px 8px rgba(29,78,216,.25); transition: background .15s, transform .15s; }
.btn-cit-save:hover { background: var(--accent-hover, #1e40af); transform: translateY(-1px); }
.btn-cit-save svg { width: 15px; height: 15px; }
.btn-cit-del { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 10px; border: 1px solid #fca5a5; background: var(--card-bg, #fff); color: #dc2626; font-size: 13px; font-weight: 600; cursor: pointer; font-family: var(--font-sans, inherit); transition: all .15s; }
.btn-cit-del:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
.btn-cit-del svg { width: 14px; height: 14px; }
.cit-input[type="datetime-local"]::-webkit-calendar-picker-indicator { opacity: .5; cursor: pointer; }
</style>
@endpush

@section('content')
<div class="cit-wrap">

    <div class="cit-header">
        <div>
            <h1>
                <svg style="width:22px;height:22px;vertical-align:-3px;color:var(--accent);margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar Cita
            </h1>
            <p>Modificá los datos de la cita — {{ $agenda->paciente->apellido }}, {{ $agenda->paciente->nombre }}</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('panel.agenda.show', $agenda->id) }}" class="btn-cit-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-12 0c0 5.523 4.477 10 10 10S22 17.523 22 12 17.523 2 12 2 2 6.477 2 12z"/></svg>
                Ver detalle
            </a>
            <a href="{{ route('panel.agenda.index') }}" class="btn-cit-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendario
            </a>
        </div>
    </div>

    {{-- ── MAIN FORM (sin form anidado) ── --}}
    <form action="{{ route('panel.agenda.update', $agenda->id) }}" method="POST" id="form-agenda">
        @csrf
        @method('PUT')
        @if(request('from_paciente'))
        <input type="hidden" name="from_paciente" value="{{ request('from_paciente') }}">
        @endif

        {{-- ── Paciente y contexto ── --}}
        <div class="cit-card">
            <div class="cit-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Paciente y contexto
            </div>
            <div class="cit-card-body">
                <div class="cit-row">
                    <div class="cit-field">
                        <label class="cit-label" for="paciente_id">Paciente <span class="req">*</span></label>
                        <select class="cit-select select2" name="paciente_id" id="paciente_id" required>
                            <option value="">— Seleccionar paciente —</option>
                            @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" {{ old('paciente_id', $agenda->paciente_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->apellido }}, {{ $p->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('paciente_id')<div class="cit-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="cit-field">
                        <label class="cit-label" for="especialidad_id">Especialidad <span style="font-weight:400;text-transform:none;font-size:10px;">(opcional)</span></label>
                        <select class="cit-select select2" name="especialidad_id" id="especialidad_id">
                            <option value="">— Sin especialidad —</option>
                            @foreach($especialidades as $e)
                            <option value="{{ $e->id }}" {{ old('especialidad_id', $agenda->especialidad_id) == $e->id ? 'selected' : '' }}>
                                {{ $e->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('especialidad_id')<div class="cit-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="cit-field" style="margin-bottom:0;">
                    <label class="cit-label">Modalidad <span class="req">*</span></label>
                    <div class="cit-toggle-group" id="modalidad-group">
                        <button type="button" class="cit-toggle-btn {{ old('modalidad', $agenda->modalidad) === 'presencial' ? 'active' : '' }}" data-value="presencial">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Presencial
                        </button>
                        <button type="button" class="cit-toggle-btn {{ old('modalidad', $agenda->modalidad) === 'virtual' ? 'active' : '' }}" data-value="virtual">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.867v6.266a1 1 0 01-1.447.902L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Virtual
                        </button>
                    </div>
                    <input type="hidden" name="modalidad" id="modalidad-input" value="{{ old('modalidad', $agenda->modalidad) }}">
                    @error('modalidad')<div class="cit-error">{{ $message }}</div>@enderror

                    <div id="row-link-virtual" style="{{ old('modalidad', $agenda->modalidad) === 'virtual' ? 'margin-top:14px;' : 'display:none;margin-top:14px;' }}">
                        <label class="cit-label" for="link_virtual">Enlace de reunión virtual</label>
                        <input type="url" class="cit-input @error('link_virtual') is-invalid @enderror"
                               name="link_virtual" id="link_virtual"
                               placeholder="https://meet.google.com/... o https://zoom.us/..."
                               value="{{ old('link_virtual', $agenda->link_virtual) }}">
                        @error('link_virtual')<div class="cit-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Estado ── --}}
        <div class="cit-card">
            <div class="cit-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Estado de la cita
            </div>
            <div class="cit-card-body">
                <div class="cit-field" style="margin-bottom:0;">
                    <div class="cit-toggle-group" id="estado-group">
                        @php $estadoActual = old('estado', $agenda->estado); @endphp
                        <button type="button" class="estado-toggle-btn {{ $estadoActual === 'pendiente' ? 'active-pendiente' : '' }}" data-value="pendiente" style="{{ $estadoActual !== 'pendiente' ? 'border-color:#f59e0b;color:#f59e0b;' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pendiente
                        </button>
                        <button type="button" class="estado-toggle-btn {{ $estadoActual === 'confirmado' ? 'active-confirmado' : '' }}" data-value="confirmado" style="{{ $estadoActual !== 'confirmado' ? 'border-color:#16a34a;color:#16a34a;' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Confirmado
                        </button>
                        <button type="button" class="estado-toggle-btn {{ $estadoActual === 'cancelado' ? 'active-cancelado' : '' }}" data-value="cancelado" style="{{ $estadoActual !== 'cancelado' ? 'border-color:#dc2626;color:#dc2626;' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancelado
                        </button>
                        <button type="button" class="estado-toggle-btn {{ $estadoActual === 'realizado' ? 'active-realizado' : '' }}" data-value="realizado" style="{{ $estadoActual !== 'realizado' ? 'border-color:#64748b;color:#64748b;' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Realizado
                        </button>
                    </div>
                    <input type="hidden" name="estado" id="estado-input" value="{{ $estadoActual }}">
                    @error('estado')<div class="cit-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- ── Profesional ── --}}
        <div class="cit-card">
            <div class="cit-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Profesional <span class="req" style="margin-left:2px;">*</span>
            </div>
            <div class="cit-card-body">
                @if($profesionales->count() > 1)
                {{-- Admin: puede elegir tipo y cualquier profesional --}}
                <div class="cit-field">
                    <label class="cit-label">Tipo de profesional</label>
                    <div class="cit-toggle-group" id="prof-tipo-group">
                        <button type="button" class="cit-toggle-btn {{ old('profesional_tipo', $agenda->profesional_tipo) === 'registrado' ? 'active' : '' }}" data-value="registrado">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1"/></svg>
                            Registrado en el sistema
                        </button>
                        <button type="button" class="cit-toggle-btn {{ old('profesional_tipo', $agenda->profesional_tipo) === 'externo' ? 'active' : '' }}" data-value="externo">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Externo
                        </button>
                    </div>
                    <input type="hidden" name="profesional_tipo" id="prof-tipo-input" value="{{ old('profesional_tipo', $agenda->profesional_tipo) }}">
                </div>

                <div id="row-prof-registrado" class="cit-field" style="{{ old('profesional_tipo', $agenda->profesional_tipo) === 'registrado' ? 'margin-bottom:0;' : 'display:none;margin-bottom:0;' }}">
                    <label class="cit-label" for="profesional_id">Seleccionar profesional</label>
                    <select class="cit-select select2" name="profesional_id" id="profesional_id">
                        <option value="">— Seleccionar —</option>
                        @foreach($profesionales as $u)
                        <option value="{{ $u->id }}" {{ old('profesional_id', $agenda->profesional_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('profesional_id')<div class="cit-error">{{ $message }}</div>@enderror
                </div>

                <div id="row-prof-externo" style="{{ old('profesional_tipo', $agenda->profesional_tipo) === 'externo' ? '' : 'display:none;' }}">
                    <div class="cit-row">
                        <div class="cit-field" style="margin-bottom:0;">
                            <label class="cit-label" for="profesional_externo_nombre">Nombre del profesional</label>
                            <input type="text" class="cit-input @error('profesional_externo_nombre') is-invalid @enderror"
                                   name="profesional_externo_nombre" id="profesional_externo_nombre"
                                   placeholder="Nombre completo"
                                   value="{{ old('profesional_externo_nombre', $agenda->profesional_externo_nombre) }}">
                            @error('profesional_externo_nombre')<div class="cit-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="cit-field" style="margin-bottom:0;">
                            <label class="cit-label" for="profesional_externo_email">Email <span style="font-weight:400;text-transform:none;font-size:10px;">(para recordatorio)</span></label>
                            <input type="email" class="cit-input @error('profesional_externo_email') is-invalid @enderror"
                                   name="profesional_externo_email" id="profesional_externo_email"
                                   placeholder="profesional@ejemplo.com"
                                   value="{{ old('profesional_externo_email', $agenda->profesional_externo_email) }}">
                            @error('profesional_externo_email')<div class="cit-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                @else
                {{-- Profesional: asignado fijo a sí mismo --}}
                <input type="hidden" name="profesional_tipo" value="registrado">
                <input type="hidden" name="profesional_id" value="{{ auth()->id() }}">
                <div class="cit-field" style="margin-bottom:0;">
                    <label class="cit-label">Profesional asignado</label>
                    <div class="cit-input" style="background:var(--body-bg);cursor:default;color:var(--text-secondary);">
                        {{ auth()->user()->name }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Horario ── --}}
        <div class="cit-card">
            <div class="cit-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Horario
            </div>
            <div class="cit-card-body">
                <div class="cit-row">
                    <div class="cit-field" style="margin-bottom:0;">
                        <label class="cit-label" for="fecha_hora_inicio">Inicio <span class="req">*</span></label>
                        <input type="datetime-local" name="fecha_hora_inicio" id="fecha_hora_inicio"
                               class="cit-input @error('fecha_hora_inicio') is-invalid @enderror"
                               value="{{ old('fecha_hora_inicio', $agenda->fecha_hora_inicio->format('Y-m-d\TH:i')) }}"
                               required>
                        @error('fecha_hora_inicio')<div class="cit-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="cit-field" style="margin-bottom:0;">
                        <label class="cit-label" for="fecha_hora_fin">Fin <span class="req">*</span></label>
                        <input type="datetime-local" name="fecha_hora_fin" id="fecha_hora_fin"
                               class="cit-input @error('fecha_hora_fin') is-invalid @enderror"
                               value="{{ old('fecha_hora_fin', $agenda->fecha_hora_fin->format('Y-m-d\TH:i')) }}"
                               required>
                        @error('fecha_hora_fin')<div class="cit-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Motivo y notas ── --}}
        <div class="cit-card">
            <div class="cit-card-hdr">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                Motivo y notas
            </div>
            <div class="cit-card-body">
                <div class="cit-field">
                    <label class="cit-label" for="motivo">Motivo de la cita <span class="req">*</span></label>
                    <textarea name="motivo" id="motivo" rows="3"
                              class="cit-input @error('motivo') is-invalid @enderror"
                              placeholder="Describa el motivo de la cita...">{{ old('motivo', $agenda->motivo) }}</textarea>
                    @error('motivo')<div class="cit-error">{{ $message }}</div>@enderror
                </div>
                <div class="cit-field" style="margin-bottom:0;">
                    <label class="cit-label" for="notas">Notas adicionales <span style="font-weight:400;text-transform:none;font-size:10px;">(opcional)</span></label>
                    <textarea name="notas" id="notas" rows="2"
                              class="cit-input @error('notas') is-invalid @enderror"
                              placeholder="Instrucciones especiales, documentación a traer, etc.">{{ old('notas', $agenda->notas) }}</textarea>
                    @error('notas')<div class="cit-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- ── Acciones (dentro del form, SIN form anidado) ── --}}
        <div class="cit-actions">
            @can('agenda_delete')
            <button type="button" class="btn-cit-del" id="btn-eliminar-cita">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Eliminar cita
            </button>
            @else
            <span></span>
            @endcan
            <div style="display:flex;gap:10px;">
                <a href="{{ route('panel.agenda.show', $agenda->id) }}" class="btn-cit-back">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Cancelar
                </a>
                <button type="submit" class="btn-cit-save">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar cambios
                </button>
            </div>
        </div>

    </form>

    {{-- Delete form FUERA del form principal (evita nested form bug) --}}
    @can('agenda_delete')
    <form id="form-eliminar" action="{{ route('panel.agenda.destroy', $agenda->id) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
    @endcan

</div>
@endsection

@push('scripts')
<script>
$(function () {
    // Fin no puede ser anterior al inicio
    $('#fecha_hora_inicio').on('change', function () {
        var val = $(this).val();
        if (val) $('#fecha_hora_fin').attr('min', val);
    });

    // Select2
    $('.select2').select2({ width: '100%', allowClear: true });

    // Modalidad toggle
    $('#modalidad-group .cit-toggle-btn').on('click', function () {
        $('#modalidad-group .cit-toggle-btn').removeClass('active');
        $(this).addClass('active');
        var val = $(this).data('value');
        $('#modalidad-input').val(val);
        if (val === 'virtual') {
            $('#row-link-virtual').show();
        } else {
            $('#row-link-virtual').hide();
            $('#link_virtual').val('');
        }
    });

    // Profesional tipo toggle
    $('#prof-tipo-group .cit-toggle-btn').on('click', function () {
        $('#prof-tipo-group .cit-toggle-btn').removeClass('active');
        $(this).addClass('active');
        var val = $(this).data('value');
        $('#prof-tipo-input').val(val);
        if (val === 'registrado') {
            $('#row-prof-registrado').show();
            $('#row-prof-externo').hide();
            $('#profesional_externo_nombre, #profesional_externo_email').val('');
        } else {
            $('#row-prof-registrado').hide();
            $('#row-prof-externo').show();
            $('#profesional_id').val(null).trigger('change');
        }
    });

    // Estado toggle
    var estadoColorMap = {
        'pendiente':  'active-pendiente',
        'confirmado': 'active-confirmado',
        'cancelado':  'active-cancelado',
        'realizado':  'active-realizado',
    };
    var estadoBorderMap = {
        'pendiente':  '#f59e0b',
        'confirmado': '#16a34a',
        'cancelado':  '#dc2626',
        'realizado':  '#64748b',
    };
    $('#estado-group .estado-toggle-btn').on('click', function () {
        var val = $(this).data('value');
        $('#estado-group .estado-toggle-btn').each(function () {
            var v = $(this).data('value');
            $(this).removeClass('active-pendiente active-confirmado active-cancelado active-realizado');
            $(this).css({ 'border-color': estadoBorderMap[v], color: estadoBorderMap[v], background: '' });
        });
        $(this).addClass(estadoColorMap[val]);
        $(this).css({ 'border-color': '', color: '', background: '' });
        $('#estado-input').val(val);
    });

    // Eliminar cita (form externo, no anidado)
    $('#btn-eliminar-cita').on('click', function () {
        if (confirm('¿Confirma que desea eliminar esta cita? Esta acción no se puede deshacer.')) {
            $('#form-eliminar').submit();
        }
    });
});
</script>
@endpush
