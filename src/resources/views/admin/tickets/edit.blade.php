<x-layouts.admin title="Editar Ticket #{{ $ticket->id }}">

    <div class="page-header">
        <div>
            <h1 class="page-title">Editar Ticket #{{ $ticket->id }}</h1>
            <p class="page-subtitle">{{ $ticket->title }}</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Ver Ticket
            </a>
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Tickets
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <div>
                <strong>Se encontraron errores:</strong>
                <ul style="margin: 4px 0 0; padding-left: 20px;">
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
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" style="padding: 28px;">
                @csrf
                @method('PATCH')

                <div style="margin-bottom: 24px;">
                    <label for="title" class="form-label">Título del Ticket *</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title', $ticket->title) }}"
                           placeholder="Describe brevemente el problema..."
                           class="form-input" required maxlength="255">
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="description" class="form-label">Descripción *</label>
                    <textarea name="description" id="description" rows="6"
                              class="form-input"
                              placeholder="Proporciona una descripción detallada del problema o solicitud..."
                              required>{{ old('description', $ticket->description) }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label for="status" class="form-label">Estado *</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="abierto" {{ old('status', $ticket->status) === 'abierto' ? 'selected' : '' }}>Abierto</option>
                            <option value="en_progreso" {{ old('status', $ticket->status) === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                            <option value="esperando_cliente" {{ old('status', $ticket->status) === 'esperando_cliente' ? 'selected' : '' }}>Esperando Cliente</option>
                            <option value="resuelto" {{ old('status', $ticket->status) === 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                            <option value="cerrado" {{ old('status', $ticket->status) === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="form-label">Prioridad *</label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="baja" {{ old('priority', $ticket->priority) === 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ old('priority', $ticket->priority) === 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('priority', $ticket->priority) === 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="critica" {{ old('priority', $ticket->priority) === 'critica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                    </div>

                    <div>
                        <label for="category" class="form-label">Categoría *</label>
                        <select name="category" id="category" class="form-select" required>
                            <option value="tecnico" {{ old('category', $ticket->category) === 'tecnico' ? 'selected' : '' }}>Técnico</option>
                            <option value="facturacion" {{ old('category', $ticket->category) === 'facturacion' ? 'selected' : '' }}>Facturación</option>
                            <option value="configuracion" {{ old('category', $ticket->category) === 'configuracion' ? 'selected' : '' }}>Configuración</option>
                            <option value="otro" {{ old('category', $ticket->category) === 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>

                    <div>
                        <label for="assigned_to" class="form-label">Asignar a</label>
                        <select name="assigned_to" id="assigned_to" class="form-select">
                            <option value="">Sin asignar</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ old('assigned_to', $ticket->assigned_to) == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 32px;">
                    <label for="admin_notes" class="form-label">Notas Administrativas</label>
                    <textarea name="admin_notes" id="admin_notes" rows="4"
                              class="form-input"
                              placeholder="Notas internas para el equipo de soporte (no visibles para el cliente)...">{{ old('admin_notes', $ticket->admin_notes) }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid var(--card-border);">
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Actualizar Ticket
                    </button>
                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>

        <!-- Sidebar con información del cliente -->
        <div>
            <!-- Información del ticket -->
            <div class="card" style="margin-bottom: 20px;">
                <div style="padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline-block; margin-right: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Información del Ticket
                    </h3>

                    <div style="space-y: 12px;">
                        <div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">ID</div>
                            <div class="mono" style="font-weight: 600;">#{{ $ticket->id }}</div>
                        </div>

                        <div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Estado Actual</div>
                            <span class="badge {{ $ticket->status_badge }}">{{ $ticket->status_label }}</span>
                        </div>

                        <div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Creado</div>
                            <div style="font-size: 14px;">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $ticket->created_at->diffForHumans() }}</div>
                        </div>

                        <div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Última Actualización</div>
                            <div style="font-size: 14px;">{{ $ticket->updated_at->format('d/m/Y H:i') }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $ticket->updated_at->diffForHumans() }}</div>
                        </div>

                        @if($ticket->resolved_at)
                            <div>
                                <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Resuelto</div>
                                <div style="font-size: 14px;">{{ $ticket->resolved_at->format('d/m/Y H:i') }}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">{{ $ticket->resolved_at->diffForHumans() }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información del cliente -->
            <div class="card">
                <div style="padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline-block; margin-right: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Cliente
                    </h3>

                    <div style="space-y: 12px;">
                        <div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Empresa</div>
                            <div style="font-weight: 600;">{{ $ticket->tenant->name }}</div>
                        </div>

                        <div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Usuario</div>
                            <div style="font-weight: 500;">{{ $ticket->user->name }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $ticket->user->email }}</div>
                        </div>

                        <div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Tenant ID</div>
                            <div class="mono" style="font-size: 12px;">{{ $ticket->tenant_id }}</div>
                        </div>
                    </div>

                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--card-border);">
                        <a href="{{ route('tenants.show', $ticket->tenant) }}" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                            Ver Cliente Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>