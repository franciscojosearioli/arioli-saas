<x-layouts.admin title="Tickets de Soporte">

    <div class="page-header">
        <div>
            <h1 class="page-title">Tickets de Soporte</h1>
            <p class="page-subtitle">Gestiona todos los tickets de soporte técnico de tus clientes</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Crear Ticket
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Estadísticas rápidas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px;">
        <div class="card" style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: var(--text-primary);">{{ $stats['total'] }}</div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">Total de Tickets</div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #f59e0b;">{{ $stats['abiertos'] }}</div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">Tickets Abiertos</div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fffbeb; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #10b981;">{{ $stats['resueltos'] }}</div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">Tickets Resueltos</div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #f0fdf4; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" fill="none" stroke="#10b981" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #ef4444;">{{ $stats['criticos'] }}</div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">Tickets Críticos</div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef2f2; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" fill="none" stroke="#ef4444" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card" style="padding: 24px; margin-bottom: 24px;">
        <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 16px; align-items: end;">

            <div>
                <label class="form-label">Buscar</label>
                <div class="search-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="ID, título o descripción..." class="search-input">
                </div>
            </div>

            <div>
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="abierto" {{ request('status') === 'abierto' ? 'selected' : '' }}>Abierto</option>
                    <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                    <option value="esperando_cliente" {{ request('status') === 'esperando_cliente' ? 'selected' : '' }}>Esperando Cliente</option>
                    <option value="resuelto" {{ request('status') === 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                    <option value="cerrado" {{ request('status') === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                </select>
            </div>

            <div>
                <label class="form-label">Prioridad</label>
                <select name="priority" class="form-select">
                    <option value="">Todas las prioridades</option>
                    <option value="baja" {{ request('priority') === 'baja' ? 'selected' : '' }}>Baja</option>
                    <option value="media" {{ request('priority') === 'media' ? 'selected' : '' }}>Media</option>
                    <option value="alta" {{ request('priority') === 'alta' ? 'selected' : '' }}>Alta</option>
                    <option value="critica" {{ request('priority') === 'critica' ? 'selected' : '' }}>Crítica</option>
                </select>
            </div>

            <div>
                <label class="form-label">Cliente</label>
                <select name="tenant_id" class="form-select">
                    <option value="">Todos los clientes</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" {{ request('tenant_id') === $tenant->id ? 'selected' : '' }}>
                            {{ $tenant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Lista de tickets -->
    <div class="card">
        @if($tickets->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Categoría</th>
                        <th>Asignado a</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td>
                                <span class="mono">#{{ $ticket->id }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $ticket->tenant->name }}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">{{ $ticket->user->name }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $ticket->title }}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">
                                    {{ Str::limit($ticket->description, 60) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $ticket->status_badge }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $ticket->priority_badge }}">
                                    {{ $ticket->priority_label }}
                                </span>
                            </td>
                            <td>{{ $ticket->category_label }}</td>
                            <td>
                                @if($ticket->assignedTo)
                                    <div style="font-size: 13px; font-weight: 500;">{{ $ticket->assignedTo->name }}</div>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $ticket->created_at->format('d/m/Y') }}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">
                                    {{ $ticket->created_at->format('H:i') }}
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 4px;">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="action-btn action-view">Ver</a>
                                    <a href="{{ route('tickets.edit', $ticket) }}" class="action-btn action-edit">Editar</a>
                                    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
                                          style="display: inline;"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este ticket?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-delete">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($tickets->hasPages())
                <div style="padding: 20px; border-top: 1px solid var(--card-border);">
                    {{ $tickets->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <strong>No hay tickets de soporte</strong>
                <p>
                    @if(request()->hasAny(['search', 'status', 'priority', 'tenant_id']))
                        No se encontraron tickets que coincidan con los filtros aplicados.
                    @else
                        Aún no hay tickets de soporte registrados.
                    @endif
                </p>
            </div>
        @endif
    </div>

</x-layouts.admin>