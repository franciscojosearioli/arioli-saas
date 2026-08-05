<x-layouts.admin title="Crear Ticket">

    <div class="page-header">
        <div>
            <h1 class="page-title">Crear Nuevo Ticket</h1>
            <p class="page-subtitle">Crea un ticket de soporte para un cliente específico</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Tickets
        </a>
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

    <div class="card">
        <form method="POST" action="{{ route('tickets.store') }}" style="padding: 28px;">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div>
                    <label for="tenant_id" class="form-label">Cliente *</label>
                    <select name="tenant_id" id="tenant_id" class="form-select" required>
                        <option value="">Seleccionar cliente...</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                {{ $tenant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assigned_to" class="form-label">Asignar a</label>
                    <select name="assigned_to" id="assigned_to" class="form-select">
                        <option value="">Sin asignar</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ old('assigned_to') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label for="title" class="form-label">Título del Ticket *</label>
                <input type="text" name="title" id="title"
                       value="{{ old('title') }}"
                       placeholder="Describe brevemente el problema..."
                       class="form-input" required maxlength="255">
            </div>

            <div style="margin-bottom: 24px;">
                <label for="description" class="form-label">Descripción *</label>
                <textarea name="description" id="description" rows="6"
                          class="form-input"
                          placeholder="Proporciona una descripción detallada del problema o solicitud..."
                          required>{{ old('description') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label for="status" class="form-label">Estado *</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="abierto" {{ old('status') === 'abierto' ? 'selected' : '' }}>Abierto</option>
                        <option value="en_progreso" {{ old('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="esperando_cliente" {{ old('status') === 'esperando_cliente' ? 'selected' : '' }}>Esperando Cliente</option>
                        <option value="resuelto" {{ old('status') === 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                        <option value="cerrado" {{ old('status') === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="form-label">Prioridad *</label>
                    <select name="priority" id="priority" class="form-select" required>
                        <option value="baja" {{ old('priority') === 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ old('priority', 'media') === 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ old('priority') === 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="critica" {{ old('priority') === 'critica' ? 'selected' : '' }}>Crítica</option>
                    </select>
                </div>

                <div>
                    <label for="category" class="form-label">Categoría *</label>
                    <select name="category" id="category" class="form-select" required>
                        <option value="tecnico" {{ old('category', 'tecnico') === 'tecnico' ? 'selected' : '' }}>Técnico</option>
                        <option value="facturacion" {{ old('category') === 'facturacion' ? 'selected' : '' }}>Facturación</option>
                        <option value="configuracion" {{ old('category') === 'configuracion' ? 'selected' : '' }}>Configuración</option>
                        <option value="otro" {{ old('category') === 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 32px;">
                <label for="admin_notes" class="form-label">Notas Administrativas</label>
                <textarea name="admin_notes" id="admin_notes" rows="4"
                          class="form-input"
                          placeholder="Notas internas para el equipo de soporte (no visibles para el cliente)...">{{ old('admin_notes') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid var(--card-border);">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Crear Ticket
                </button>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</x-layouts.admin>