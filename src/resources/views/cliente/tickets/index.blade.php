<x-cliente-layout title="Mis Tickets de Soporte">

    <div class="page-header">
        <div>
            <h1 class="page-title">Mis Tickets de Soporte</h1>
            <p class="page-subtitle">Gestiona tus solicitudes de soporte técnico</p>
        </div>
        <a href="{{ route('cliente.tickets.create') }}" class="btn btn-primary">
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

    @if(session('error'))
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total de Tickets</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tickets Abiertos</div>
            <div class="stat-value" style="color: var(--warning);">{{ $stats['abiertos'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tickets Resueltos</div>
            <div class="stat-value" style="color: var(--success);">{{ $stats['resueltos'] }}</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body">
            <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 16px; align-items: end;">

                <div>
                    <label class="form-label">Buscar</label>
                    <div class="search-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="ID o título del ticket..." class="search-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="abierto" {{ request('status') === 'abierto' ? 'selected' : '' }}>Abierto</option>
                        <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="esperando_cliente" {{ request('status') === 'esperando_cliente' ? 'selected' : '' }}>Esperando mi Respuesta</option>
                        <option value="resuelto" {{ request('status') === 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                        <option value="cerrado" {{ request('status') === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Categoría</label>
                    <select name="category" class="form-select">
                        <option value="">Todas las categorías</option>
                        <option value="tecnico" {{ request('category') === 'tecnico' ? 'selected' : '' }}>Técnico</option>
                        <option value="facturacion" {{ request('category') === 'facturacion' ? 'selected' : '' }}>Facturación</option>
                        <option value="configuracion" {{ request('category') === 'configuracion' ? 'selected' : '' }}>Configuración</option>
                        <option value="otro" {{ request('category') === 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('cliente.tickets.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de tickets -->
    @if($tickets->count() > 0)
        <div style="display: grid; gap: 16px;">
            @foreach($tickets as $ticket)
                <div class="card">
                    <div class="card-body" style="padding: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                    <h3 style="font-size: 16px; font-weight: 600; margin: 0; color: var(--text-primary);">
                                        <a href="{{ route('cliente.tickets.show', $ticket) }}" style="text-decoration: none; color: inherit;">
                                            {{ $ticket->title }}
                                        </a>
                                    </h3>
                                    <span class="mono" style="font-size: 12px; color: var(--text-muted);">#{{ $ticket->id }}</span>
                                </div>

                                <p style="color: var(--text-secondary); margin: 0; line-height: 1.5;">
                                    {{ Str::limit($ticket->description, 120) }}
                                </p>

                                <div style="display: flex; gap: 8px; margin-top: 12px;">
                                    <span class="badge {{ $ticket->status_badge }}">{{ $ticket->status_label }}</span>
                                    <span class="badge {{ $ticket->priority_badge }}">{{ $ticket->priority_label }}</span>
                                    <span class="badge badge-blue">{{ $ticket->category_label }}</span>
                                </div>
                            </div>

                            <div style="display: flex; flex-direction: column; align-items: end; gap: 8px; margin-left: 24px;">
                                <div style="font-size: 13px; color: var(--text-muted); text-align: right;">
                                    {{ $ticket->created_at->format('d/m/Y') }}
                                    <br>
                                    <span style="font-size: 12px;">{{ $ticket->created_at->format('H:i') }}</span>
                                </div>

                                @if($ticket->assignedTo)
                                    <div style="font-size: 12px; color: var(--text-muted); text-align: right;">
                                        Asignado a:<br>
                                        <strong>{{ $ticket->assignedTo->name }}</strong>
                                    </div>
                                @endif

                                <div style="display: flex; gap: 6px;">
                                    <a href="{{ route('cliente.tickets.show', $ticket) }}" class="action-btn action-view">Ver</a>

                                    @if(in_array($ticket->status, ['abierto', 'esperando_cliente']))
                                        <a href="{{ route('cliente.tickets.edit', $ticket) }}" class="action-btn action-edit">Editar</a>
                                    @endif

                                    @if(!in_array($ticket->status, ['resuelto', 'cerrado']))
                                        <form method="POST" action="{{ route('cliente.tickets.close', $ticket) }}"
                                              style="display: inline;"
                                              onsubmit="return confirm('¿Estás seguro de cerrar este ticket?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="action-btn action-delete">Cerrar</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($ticket->updated_at != $ticket->created_at)
                            <div style="border-top: 1px solid var(--card-border); padding-top: 12px; margin-top: 16px;">
                                <div style="font-size: 12px; color: var(--text-muted);">
                                    Última actualización: {{ $ticket->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($tickets->hasPages())
            <div style="margin-top: 32px;">
                {{ $tickets->links() }}
            </div>
        @endif
    @else
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 60px 40px;">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto 20px; color: #d1d5db;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">No hay tickets de soporte</h3>
                <p style="color: var(--text-secondary); margin-bottom: 24px;">
                    @if(request()->hasAny(['search', 'status', 'category']))
                        No se encontraron tickets que coincidan con los filtros aplicados.
                    @else
                        Aún no has creado ningún ticket de soporte. Cuando tengas algún problema o pregunta, no dudes en contactarnos.
                    @endif
                </p>
                @if(!request()->hasAny(['search', 'status', 'category']))
                    <a href="{{ route('cliente.tickets.create') }}" class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Crear mi Primer Ticket
                    </a>
                @endif
            </div>
        </div>
    @endif

</x-cliente-layout>