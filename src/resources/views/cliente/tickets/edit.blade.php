<x-cliente-layout title="Editar Ticket #{{ $ticket->id }}">

    <div class="page-header">
        <div>
            <h1 class="page-title">Editar Ticket #{{ $ticket->id }}</h1>
            <p class="page-subtitle">{{ $ticket->title }}</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('cliente.tickets.show', $ticket) }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Ver Ticket
            </a>
            <a href="{{ route('cliente.tickets.index') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Mis Tickets
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <div>
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul style="margin: 8px 0 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">

        <!-- Formulario principal -->
        <div class="card">
            <div class="card-body" style="padding: 32px;">
                <form method="POST" action="{{ route('cliente.tickets.update', $ticket) }}">
                    @csrf
                    @method('PATCH')

                    <div style="margin-bottom: 28px;">
                        <label for="title" class="form-label">¿Cuál es tu problema o consulta? *</label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title', $ticket->title) }}"
                               placeholder="Por ejemplo: No puedo acceder al sistema, Error al guardar datos, etc."
                               class="form-input" required maxlength="255">
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                            Escribí un título claro y descriptivo para tu problema o consulta.
                        </div>
                    </div>

                    <div style="margin-bottom: 28px;">
                        <label for="description" class="form-label">Descripción detallada *</label>
                        <textarea name="description" id="description" rows="8"
                                  class="form-input"
                                  placeholder="Contanos en detalle qué está pasando..."
                                  required>{{ old('description', $ticket->description) }}</textarea>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                            Actualizá la descripción si tenés más información que agregar o si el problema cambió.
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px;">
                        <div>
                            <label for="priority" class="form-label">¿Qué tan urgente es? *</label>
                            <select name="priority" id="priority" class="form-select" required>
                                <option value="baja" {{ old('priority', $ticket->priority) === 'baja' ? 'selected' : '' }}>
                                    🟢 Baja - No es urgente, puede esperar
                                </option>
                                <option value="media" {{ old('priority', $ticket->priority) === 'media' ? 'selected' : '' }}>
                                    🟡 Media - Es importante pero no bloquea mi trabajo
                                </option>
                                <option value="alta" {{ old('priority', $ticket->priority) === 'alta' ? 'selected' : '' }}>
                                    🟠 Alta - Necesito resolverlo pronto
                                </option>
                                <option value="critica" {{ old('priority', $ticket->priority) === 'critica' ? 'selected' : '' }}>
                                    🔴 Crítica - Urgente, no puedo trabajar
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="category" class="form-label">¿Qué tipo de problema es? *</label>
                            <select name="category" id="category" class="form-select" required>
                                <option value="tecnico" {{ old('category', $ticket->category) === 'tecnico' ? 'selected' : '' }}>
                                    🔧 Problema Técnico
                                </option>
                                <option value="configuracion" {{ old('category', $ticket->category) === 'configuracion' ? 'selected' : '' }}>
                                    ⚙️ Configuración o Setup
                                </option>
                                <option value="facturacion" {{ old('category', $ticket->category) === 'facturacion' ? 'selected' : '' }}>
                                    💰 Facturación o Pagos
                                </option>
                                <option value="otro" {{ old('category', $ticket->category) === 'otro' ? 'selected' : '' }}>
                                    📝 Consulta General
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Información sobre edición -->
                    <div style="background: #fff3cd; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 0 8px 8px 0; margin-bottom: 32px;">
                        <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #92400e;">
                            ⚠️ Recordá que:
                        </h4>
                        <div style="font-size: 13px; color: #92400e; line-height: 1.5;">
                            Solo podés editar tickets que estén abiertos o esperando tu respuesta. Una vez que nuestro equipo esté trabajando en el ticket, no podrás modificarlo, pero sí crear uno nuevo si es necesario.
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid var(--card-border);">
                        <button type="submit" class="btn btn-primary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Actualizar Ticket
                        </button>
                        <a href="{{ route('cliente.tickets.show', $ticket) }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar con información del ticket -->
        <div>
            <!-- Estado actual -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-body" style="padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">
                        📊 Estado Actual
                    </h3>

                    <div style="text-align: center; margin-bottom: 16px;">
                        <span class="badge {{ $ticket->status_badge }}" style="font-size: 13px; padding: 8px 16px;">
                            {{ $ticket->status_label }}
                        </span>
                    </div>

                    <div style="space-y: 12px; font-size: 13px;">
                        <div class="detail-row">
                            <span class="detail-label">ID</span>
                            <span class="detail-value mono">#{{ $ticket->id }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Creado</span>
                            <div class="detail-value">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        @if($ticket->updated_at != $ticket->created_at)
                            <div class="detail-row">
                                <span class="detail-label">Última actualización</span>
                                <div class="detail-value">{{ $ticket->updated_at->format('d/m/Y H:i') }}</div>
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

            <!-- Ayuda -->
            <div class="card">
                <div class="card-body" style="padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">
                        💡 Tips para Editar
                    </h3>

                    <div style="font-size: 13px; color: var(--text-secondary); line-height: 1.6;">
                        <ul style="margin: 0; padding-left: 20px;">
                            <li style="margin-bottom: 8px;">
                                <strong>Agregá más detalles</strong> si descubriste algo nuevo sobre el problema
                            </li>
                            <li style="margin-bottom: 8px;">
                                <strong>Cambiá la prioridad</strong> si la urgencia cambió
                            </li>
                            <li style="margin-bottom: 8px;">
                                <strong>Actualizá la categoría</strong> si te diste cuenta que el problema es diferente
                            </li>
                            <li>
                                <strong>Sé específico</strong> con los cambios para que nuestro equipo los entienda mejor
                            </li>
                        </ul>
                    </div>

                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--card-border); font-size: 12px; color: var(--text-muted);">
                        <strong>📝 Nota:</strong> Estos cambios serán registrados y nuestro equipo será notificado de las actualizaciones.
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-cliente-layout>