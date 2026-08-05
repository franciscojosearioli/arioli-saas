<x-layouts.admin title="Ticket #{{ $ticket->id }}">

    <div class="page-header">
        <div>
            <h1 class="page-title">Ticket #{{ $ticket->id }}</h1>
            <p class="page-subtitle">{{ $ticket->title }}</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">

        <!-- Contenido principal del ticket -->
        <div class="card">
            <div style="padding: 24px;">

                <!-- Header con estado y metadatos -->
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--card-border);">
                    <div>
                        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                            <span class="badge {{ $ticket->status_badge }}">{{ $ticket->status_label }}</span>
                            <span class="badge {{ $ticket->priority_badge }}">{{ $ticket->priority_label }}</span>
                            <span class="badge badge-blue">{{ $ticket->category_label }}</span>
                        </div>
                        <div style="font-size: 13px; color: var(--text-secondary);">
                            Creado {{ $ticket->created_at->diffForHumans() }} •
                            Actualizado {{ $ticket->updated_at->diffForHumans() }}
                            @if($ticket->resolved_at)
                                • Resuelto {{ $ticket->resolved_at->diffForHumans() }}
                            @endif
                        </div>
                    </div>

                    <!-- Acciones rápidas -->
                    <div style="display: flex; gap: 4px;">
                        <select onchange="updateStatus({{ $ticket->id }}, this.value)" style="padding: 6px 10px; border-radius: 6px; border: 1px solid var(--card-border); font-size: 12px;">
                            <option value="abierto" {{ $ticket->status === 'abierto' ? 'selected' : '' }}>Abierto</option>
                            <option value="en_progreso" {{ $ticket->status === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                            <option value="esperando_cliente" {{ $ticket->status === 'esperando_cliente' ? 'selected' : '' }}>Esperando Cliente</option>
                            <option value="resuelto" {{ $ticket->status === 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                            <option value="cerrado" {{ $ticket->status === 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                    </div>
                </div>

                <!-- Descripción del ticket -->
                <div style="margin-bottom: 28px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);">Descripción</h3>
                    <div style="background: var(--body-bg); padding: 20px; border-radius: 10px; line-height: 1.6; white-space: pre-wrap;">{{ $ticket->description }}</div>
                </div>

                <!-- Notas administrativas -->
                @if($ticket->admin_notes)
                    <div style="margin-bottom: 28px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);">Notas Administrativas</h3>
                        <div style="background: #fff3cd; border: 1px solid #ffecb5; padding: 20px; border-radius: 10px; line-height: 1.6; white-space: pre-wrap;">{{ $ticket->admin_notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar con información del cliente -->
        <div>
            <!-- Información del cliente -->
            <div class="card" style="margin-bottom: 20px;">
                <div style="padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline-block; margin-right: 8px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Información del Cliente
                    </h3>

                    <div style="space-y: 12px;">
                        <div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Cliente</div>
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

                        @if($ticket->assignedTo)
                            <div>
                                <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">Asignado a</div>
                                <div style="font-weight: 500;">{{ $ticket->assignedTo->name }}</div>
                            </div>
                        @endif
                    </div>

                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--card-border);">
                        <a href="{{ route('tenants.show', $ticket->tenant) }}" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                            Ver Cliente Completo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Licencias activas del cliente -->
            @if($licenses->count() > 0)
                <div class="card">
                    <div style="padding: 20px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline-block; margin-right: 8px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Licencias Activas
                        </h3>

                        <div style="space-y: 12px;">
                            @foreach($licenses as $license)
                                <div style="padding: 12px; background: var(--body-bg); border-radius: 8px;">
                                    <div style="font-weight: 600; font-size: 13px;">{{ $license->plan->product->name }}</div>
                                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                        Plan: {{ $license->plan->name }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--text-muted);">
                                        Vence: {{ $license->expires_at->format('d/m/Y') }}
                                    </div>
                                    @if($license->active)
                                        <span class="badge badge-green" style="margin-top: 6px; font-size: 11px;">Activa</span>
                                    @else
                                        <span class="badge badge-red" style="margin-top: 6px; font-size: 11px;">Inactiva</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Scripts para acciones AJAX -->
    <script>
        async function updateStatus(ticketId, newStatus) {
            try {
                const response = await fetch(`/tickets/${ticketId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await response.json();

                if (data.success) {
                    // Mostrar mensaje de éxito
                    showNotification(data.message, 'success');

                    // Recargar la página para actualizar el badge de estado
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification('Error al actualizar el estado', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al actualizar el estado', 'error');
            }
        }

        function showNotification(message, type) {
            // Crear el elemento de notificación
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.innerHTML = `
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success'
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                    }
                </svg>
                ${message}
            `;

            document.body.appendChild(notification);

            // Remover después de 3 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }
    </script>

</x-layouts.admin>