<x-cliente-layout title="Ticket #{{ $ticket->id }}">

    <div class="page-header">
        <div>
            <h1 class="page-title">Ticket #{{ $ticket->id }}</h1>
            <p class="page-subtitle">{{ $ticket->title }}</p>
        </div>
        <div style="display: flex; gap: 8px;">
            @if(in_array($ticket->status, ['abierto', 'esperando_cliente']))
                <a href="{{ route('cliente.tickets.edit', $ticket) }}" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
            @endif
            <a href="{{ route('cliente.tickets.index') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Mis Tickets
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">

        <!-- Contenido principal del ticket -->
        <div class="card">
            <div class="card-body" style="padding: 32px;">

                <!-- Estado y metadatos -->
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--card-border);">
                    <div>
                        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                            <span class="badge {{ $ticket->status_badge }}">{{ $ticket->status_label }}</span>
                            <span class="badge {{ $ticket->priority_badge }}">{{ $ticket->priority_label }}</span>
                            <span class="badge badge-blue">{{ $ticket->category_label }}</span>
                            @if($ticket->related)
                                <span class="badge badge-gray">
                                    {{ method_exists($ticket->related, 'label') ? $ticket->related->label() : ('Servicio: ' . ($ticket->related->service_type->label() ?? '')) }}
                                </span>
                            @endif
                        </div>
                        <div style="font-size: 13px; color: var(--text-secondary);">
                            Creado {{ $ticket->created_at->diffForHumans() }}
                            @if($ticket->updated_at != $ticket->created_at)
                                • Actualizado {{ $ticket->updated_at->diffForHumans() }}
                            @endif
                            @if($ticket->resolved_at)
                                • Resuelto {{ $ticket->resolved_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>

                    @if(!in_array($ticket->status, ['resuelto', 'cerrado']))
                        <form method="POST" action="{{ route('cliente.tickets.close', $ticket) }}"
                              onsubmit="return confirm('¿Estás seguro de cerrar este ticket? No podrás reabrirlo después.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">
                                Cerrar Ticket
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Descripción del problema -->
                <div style="margin-bottom: 32px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">
                        🔍 Descripción del Problema
                    </h3>
                    <div style="background: var(--body-bg); padding: 24px; border-radius: 12px; line-height: 1.6; white-space: pre-wrap; font-size: 14px;">{{ $ticket->description }}</div>
                </div>

                <!-- Estado del ticket con explicaciones -->
                <div style="background: var(--body-bg); padding: 24px; border-radius: 12px; margin-bottom: 28px;">
                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);">
                        📊 Estado Actual
                    </h4>
                    <div style="font-size: 14px; line-height: 1.6;">
                        @switch($ticket->status)
                            @case('abierto')
                                <span class="badge badge-red">🆕 Abierto</span>
                                <div style="margin-top: 8px; color: var(--text-secondary);">
                                    Tu ticket ha sido recibido y está en nuestra cola de soporte. Pronto será asignado a un miembro de nuestro equipo.
                                </div>
                                @break
                            @case('en_progreso')
                                <span class="badge badge-blue">🔧 En Progreso</span>
                                <div style="margin-top: 8px; color: var(--text-secondary);">
                                    Nuestro equipo está trabajando activamente en resolver tu problema. Te contactaremos pronto con una actualización.
                                </div>
                                @break
                            @case('esperando_cliente')
                                <span class="badge badge-yellow">⏳ Esperando tu Respuesta</span>
                                <div style="margin-top: 8px; color: var(--text-secondary);">
                                    Necesitamos más información de tu parte para continuar. Por favor revisa si hay alguna consulta adicional en las comunicaciones.
                                </div>
                                @break
                            @case('resuelto')
                                <span class="badge badge-green">✅ Resuelto</span>
                                <div style="margin-top: 8px; color: var(--text-secondary);">
                                    ¡Excelente! Tu problema ha sido resuelto. Si tenés alguna duda adicional, no dudes en crear un nuevo ticket.
                                </div>
                                @break
                            @case('cerrado')
                                <span class="badge" style="background: #6b7280; color: #fff;">🔒 Cerrado</span>
                                <div style="margin-top: 8px; color: var(--text-secondary);">
                                    Este ticket ha sido cerrado. Si tenés algún problema relacionado, podés crear un nuevo ticket.
                                </div>
                                @break
                        @endswitch
                    </div>
                </div>

                <!-- Acciones disponibles -->
                @if(in_array($ticket->status, ['abierto', 'esperando_cliente']))
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 20px; border-radius: 10px; margin-bottom: 24px;">
                        <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--accent);">
                            🛠️ ¿Querés hacer cambios?
                        </h4>
                        <div style="display: flex; gap: 12px;">
                            <a href="{{ route('cliente.tickets.edit', $ticket) }}" class="btn btn-primary" style="font-size: 13px;">
                                Editar Ticket
                            </a>
                            <span style="font-size: 13px; color: var(--text-secondary); align-self: center;">
                                Podés modificar la descripción, prioridad o categoría mientras esté abierto.
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar con información adicional -->
        <div>
            <!-- Información del ticket -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-body" style="padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">
                        📋 Detalles del Ticket
                    </h3>

                    <div style="space-y: 14px;">
                        <div class="detail-row">
                            <span class="detail-label">ID del Ticket</span>
                            <span class="detail-value mono">#{{ $ticket->id }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Prioridad</span>
                            <span class="badge {{ $ticket->priority_badge }}">{{ $ticket->priority_label }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Categoría</span>
                            <span class="detail-value">{{ $ticket->category_label }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Creado</span>
                            <div>
                                <div class="detail-value">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $ticket->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        @if($ticket->updated_at != $ticket->created_at)
                            <div class="detail-row">
                                <span class="detail-label">Última Actualización</span>
                                <div>
                                    <div class="detail-value">{{ $ticket->updated_at->format('d/m/Y H:i') }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ $ticket->updated_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endif

                        @if($ticket->resolved_at)
                            <div class="detail-row">
                                <span class="detail-label">Resuelto</span>
                                <div>
                                    <div class="detail-value">{{ $ticket->resolved_at->format('d/m/Y H:i') }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ $ticket->resolved_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endif

                        @if($ticket->assignedTo)
                            <div class="detail-row">
                                <span class="detail-label">Asignado a</span>
                                <div class="detail-value" style="font-weight: 600;">{{ $ticket->assignedTo->name }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ayuda y contacto -->
            <div class="card">
                <div class="card-body" style="padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">
                        💬 ¿Necesitás Ayuda?
                    </h3>

                    <div style="font-size: 13px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 16px;">
                        Si tenés alguna duda sobre este ticket o necesitás reportar algo diferente, podés crear un nuevo ticket o contactarnos directamente.
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="{{ route('cliente.tickets.create') }}" class="btn btn-secondary" style="width: 100%; justify-content: center; font-size: 13px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Crear Nuevo Ticket
                        </a>

                        @if(config('app.support_email'))
                            <a href="mailto:{{ config('app.support_email') }}" class="btn btn-secondary" style="width: 100%; justify-content: center; font-size: 13px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Escribir Email
                            </a>
                        @endif
                    </div>

                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--card-border); font-size: 11px; color: var(--text-muted); line-height: 1.5;">
                        <strong>⏰ Horarios de Atención:</strong><br>
                        Lunes a Viernes, 9:00 - 18:00 ART<br>
                        Tiempo de respuesta: &lt; 24hs
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-cliente-layout>