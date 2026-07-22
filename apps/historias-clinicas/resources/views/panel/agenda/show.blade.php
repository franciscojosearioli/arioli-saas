@extends('layouts.panel')
@section('title', 'Detalle de Cita')

@push('styles')
<style>
.cit-wrap { max-width: 900px; margin: 0 auto; padding: 0 4px; animation: citFadeUp .3s ease both; }
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

.cit-dl { display: grid; grid-template-columns: 130px 1fr; gap: 0; }
.cit-dt { font-size: 10px; font-weight: 600; color: var(--text-muted, #94a3b8); text-transform: uppercase; letter-spacing: .07em; padding: 10px 0; border-bottom: 1px solid var(--card-border, #e8edf2); display: flex; align-items: center; }
.cit-dd { font-size: 13px; color: var(--text-primary, #0f172a); padding: 10px 0 10px 12px; border-bottom: 1px solid var(--card-border, #e8edf2); display: flex; align-items: center; }
.cit-dl .cit-dt:last-of-type, .cit-dl .cit-dd:last-of-type { border-bottom: none; }

.estado-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; }
.estado-badge.pendiente  { background: #fef3c7; color: #f59e0b; }
.estado-badge.confirmado { background: #dcfce7; color: #16a34a; }
.estado-badge.cancelado  { background: #fee2e2; color: #dc2626; }
.estado-badge.realizado  { background: #f1f5f9; color: #64748b; }

.btn-cit-back { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 10px; border: 1px solid var(--card-border, #e8edf2); background: var(--card-bg, #fff); color: var(--text-secondary, #64748b); font-size: 13px; font-weight: 600; text-decoration: none; transition: all .15s; }
.btn-cit-back:hover { border-color: var(--accent, #1d4ed8); color: var(--accent, #1d4ed8); text-decoration: none; }
.btn-cit-edit { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 10px; background: var(--accent, #1d4ed8); color: #fff; border: none; font-size: 13px; font-weight: 600; text-decoration: none; transition: background .15s; box-shadow: 0 2px 8px rgba(29,78,216,.2); }
.btn-cit-edit:hover { background: var(--accent-hover, #1e40af); color: #fff; text-decoration: none; }

/* Estado change buttons */
.estado-change-btns { display: flex; flex-direction: column; gap: 8px; }
.btn-estado { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 10px; border: 2px solid; background: var(--body-bg, #f8fafc); font-size: 13px; font-weight: 600; cursor: pointer; transition: all .18s; font-family: var(--font-sans, inherit); width: 100%; }
.btn-estado svg { width: 15px; height: 15px; flex-shrink: 0; }
.btn-estado.pendiente  { border-color: #f59e0b; color: #f59e0b; }
.btn-estado.confirmado { border-color: #16a34a; color: #16a34a; }
.btn-estado.cancelado  { border-color: #dc2626; color: #dc2626; }
.btn-estado.realizado  { border-color: #64748b; color: #64748b; }
.btn-estado:hover { color: #fff; }
.btn-estado.pendiente:hover  { background: #f59e0b; }
.btn-estado.confirmado:hover { background: #16a34a; }
.btn-estado.cancelado:hover  { background: #dc2626; }
.btn-estado.realizado:hover  { background: #64748b; }
.btn-estado:disabled { opacity: .35; cursor: not-allowed; }

.cit-two-col { display: grid; grid-template-columns: 1fr 300px; gap: 18px; align-items: start; }
@@media (max-width: 768px) { .cit-two-col { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $estadoMap = [
        'pendiente'  => 'pendiente',
        'confirmado' => 'confirmado',
        'cancelado'  => 'cancelado',
        'realizado'  => 'realizado',
    ];
    $estadoClass = $estadoMap[$agenda->estado] ?? 'pendiente';
    $iconMap = [
        'pendiente'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'confirmado' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        'cancelado'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        'realizado'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ];
@endphp

<div class="cit-wrap">

    <div class="cit-header">
        <div>
            <h1>
                <svg style="width:22px;height:22px;vertical-align:-3px;color:var(--accent);margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Detalle de Cita
                <span class="estado-badge {{ $estadoClass }}" style="vertical-align:2px;margin-left:8px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;">{!! $iconMap[$agenda->estado] ?? $iconMap['pendiente'] !!}</svg>
                    {{ \App\Models\Agenda::estadosLabels()[$agenda->estado] }}
                </span>
            </h1>
            <p>{{ $agenda->paciente->apellido }}, {{ $agenda->paciente->nombre }} — {{ $agenda->fecha_hora_inicio->format('d/m/Y H:i') }} hs</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @can('agenda_edit')
            @if(!$agenda->creado_por || $agenda->creado_por == auth()->id() || $agenda->profesional_id == auth()->id())
            <a href="{{ route('panel.agenda.edit', $agenda->id) }}" class="btn-cit-edit">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </a>
            @endif
            @endcan
            <a href="{{ route('panel.agenda.index') }}" class="btn-cit-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendario
            </a>
        </div>
    </div>

    <div class="cit-two-col">

        {{-- ── Columna principal ── --}}
        <div>

            {{-- Paciente --}}
            <div class="cit-card">
                <div class="cit-card-hdr">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Paciente
                </div>
                <div class="cit-card-body" style="padding:16px 20px;">
                    <a href="{{ route('panel.paciente.show', $agenda->paciente_id) }}"
                       style="font-size:16px;font-weight:700;color:var(--text-primary);text-decoration:none;">
                        {{ $agenda->paciente->nombre }} {{ $agenda->paciente->apellido }}
                    </a>
                    @if($agenda->paciente->dni)
                    <div style="font-size:12px;color:var(--text-muted);margin-top:3px;">DNI: {{ $agenda->paciente->dni }}</div>
                    @endif
                </div>
            </div>

            {{-- Datos de la cita --}}
            <div class="cit-card">
                <div class="cit-card-hdr">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Datos de la Cita
                </div>
                <div class="cit-card-body" style="padding:0 20px;">
                    <dl class="cit-dl">
                        <dt class="cit-dt">Inicio</dt>
                        <dd class="cit-dd">
                            <strong>{{ $agenda->fecha_hora_inicio->format('d/m/Y') }}</strong>
                            &nbsp;{{ $agenda->fecha_hora_inicio->format('H:i') }} hs
                        </dd>
                        <dt class="cit-dt">Fin</dt>
                        <dd class="cit-dd">
                            <strong>{{ $agenda->fecha_hora_fin->format('d/m/Y') }}</strong>
                            &nbsp;{{ $agenda->fecha_hora_fin->format('H:i') }} hs
                            <span style="margin-left:8px;font-size:11px;color:var(--text-muted);">
                                ({{ $agenda->fecha_hora_inicio->diffInMinutes($agenda->fecha_hora_fin) }} min)
                            </span>
                        </dd>
                        <dt class="cit-dt">Profesional</dt>
                        <dd class="cit-dd">
                            {{ $agenda->getNombreProfesional() }}
                            <span style="margin-left:6px;font-size:10px;font-weight:600;padding:2px 7px;border-radius:99px;background:var(--body-bg);border:1px solid var(--card-border);color:var(--text-muted);">
                                {{ $agenda->profesional_tipo === 'externo' ? 'Externo' : 'Sistema' }}
                            </span>
                        </dd>
                        @if($agenda->profesional_tipo === 'externo' && $agenda->profesional_externo_email)
                        <dt class="cit-dt">Email prof.</dt>
                        <dd class="cit-dd"><a href="mailto:{{ $agenda->profesional_externo_email }}" style="color:var(--accent);">{{ $agenda->profesional_externo_email }}</a></dd>
                        @endif
                        <dt class="cit-dt">Modalidad</dt>
                        <dd class="cit-dd">
                            @if($agenda->modalidad === 'virtual')
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:#e0f2fe;color:#0891b2;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.867v6.266a1 1 0 01-1.447.902L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    Virtual
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;background:var(--body-bg);color:var(--text-secondary);border:1px solid var(--card-border);">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Presencial
                                </span>
                            @endif
                        </dd>
                        <dt class="cit-dt">Recordatorio</dt>
                        <dd class="cit-dd">
                            @if($agenda->recordatorio_enviado)
                                <span style="color:#16a34a;font-size:12px;font-weight:600;">✓ Enviado</span>
                            @else
                                <span style="color:var(--text-muted);font-size:12px;">Pendiente</span>
                            @endif
                        </dd>
                        <dt class="cit-dt">Creado por</dt>
                        <dd class="cit-dd" style="font-size:12px;color:var(--text-muted);">{{ $agenda->creadoPor->name ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            @if($agenda->modalidad === 'virtual' && $agenda->link_virtual)
            <div class="cit-card">
                <div class="cit-card-hdr">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    Enlace de reunión
                </div>
                <div class="cit-card-body">
                    <a href="{{ $agenda->link_virtual }}" target="_blank" rel="noopener"
                       style="color:var(--accent);font-size:13px;word-break:break-all;">
                        {{ $agenda->link_virtual }}
                    </a>
                </div>
            </div>
            @endif

            {{-- Motivo --}}
            <div class="cit-card">
                <div class="cit-card-hdr">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    Motivo de la Cita
                </div>
                <div class="cit-card-body">
                    <p style="margin:0;font-size:13px;line-height:1.6;color:var(--text-primary);">{{ $agenda->motivo }}</p>
                </div>
            </div>

            @if($agenda->notas)
            <div class="cit-card">
                <div class="cit-card-hdr">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Notas adicionales
                </div>
                <div class="cit-card-body">
                    <p style="margin:0;font-size:13px;line-height:1.6;color:var(--text-primary);">{{ $agenda->notas }}</p>
                </div>
            </div>
            @endif

        </div>

        {{-- ── Columna lateral ── --}}
        <div>

            {{-- Cambiar estado ── --}}
            @can('agenda_edit')
            <div class="cit-card">
                <div class="cit-card-hdr">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Cambiar Estado
                </div>
                <div class="cit-card-body">
                    <div class="estado-change-btns">
                        @foreach(\App\Models\Agenda::estadosLabels() as $key => $label)
                        @if($key !== $agenda->estado)
                        <button type="button" class="btn-estado {{ $key }} btn-cambiar-estado"
                                data-estado="{{ $key }}" data-id="{{ $agenda->id }}">
                            @if($key === 'pendiente')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($key === 'confirmado')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($key === 'cancelado')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @elseif($key === 'realizado')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                            {{ $label }}
                        </button>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endcan

            {{-- Acciones ── --}}
            <div class="cit-card">
                <div class="cit-card-hdr">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Acciones
                </div>
                <div class="cit-card-body" style="display:flex;flex-direction:column;gap:8px;">
                    @can('agenda_edit')
                    @if(!$agenda->creado_por || $agenda->creado_por == auth()->id() || $agenda->profesional_id == auth()->id())
                    <a href="{{ route('panel.agenda.edit', $agenda->id) }}" class="btn-cit-edit" style="justify-content:center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar cita
                    </a>
                    @endif
                    @endcan
                    <a href="{{ route('panel.agenda.create') }}?paciente_id={{ $agenda->paciente_id }}"
                       style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 18px;border-radius:10px;border:1px solid #bbf7d0;background:var(--card-bg);color:#16a34a;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;"
                       onmouseover="this.style.background='#16a34a';this.style.color='#fff';"
                       onmouseout="this.style.background='';this.style.color='#16a34a';">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva cita mismo paciente
                    </a>
                    @can('agenda_delete')
                    @if(!$agenda->creado_por || $agenda->creado_por == auth()->id() || $agenda->profesional_id == auth()->id())
                    <button type="button" id="btn-eliminar-show"
                       style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 18px;border-radius:10px;border:1px solid #fca5a5;background:var(--card-bg);color:#dc2626;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-sans,inherit);transition:all .15s;width:100%;"
                       onmouseover="this.style.background='#dc2626';this.style.color='#fff';"
                       onmouseout="this.style.background='';this.style.color='#dc2626';">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Eliminar cita
                    </button>
                    <form id="form-eliminar-show" action="{{ route('panel.agenda.destroy', $agenda->id) }}" method="POST" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                    @endif
                    @endcan
                </div>
            </div>

            {{-- Info ── --}}
            <div class="cit-card">
                <div class="cit-card-hdr">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Información
                </div>
                <div class="cit-card-body" style="padding:0 20px;">
                    <dl class="cit-dl">
                        <dt class="cit-dt">ID</dt>
                        <dd class="cit-dd">#{{ $agenda->id }}</dd>
                        <dt class="cit-dt">Creado</dt>
                        <dd class="cit-dd">{{ $agenda->created_at->format('d/m/Y H:i') }}</dd>
                        <dt class="cit-dt" style="border-bottom:none;">Modificado</dt>
                        <dd class="cit-dd" style="border-bottom:none;">{{ $agenda->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    // Cambiar estado via AJAX
    $('.btn-cambiar-estado').on('click', function () {
        var btn    = $(this);
        var id     = btn.data('id');
        var estado = btn.data('estado');

        btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("panel.agenda.cambiarEstado", "__ID__") }}'.replace('__ID__', id),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                estado: estado
            },
            success: function (res) {
                if (res.success) {
                    location.reload();
                } else {
                    btn.prop('disabled', false);
                    alert('Error al cambiar el estado.');
                }
            },
            error: function () {
                btn.prop('disabled', false);
                alert('Error al cambiar el estado. Intente nuevamente.');
            }
        });
    });

    // Eliminar cita
    $('#btn-eliminar-show').on('click', function () {
        if (confirm('¿Confirma que desea eliminar esta cita? Esta acción no se puede deshacer.')) {
            $('#form-eliminar-show').submit();
        }
    });

});
</script>
@endpush
