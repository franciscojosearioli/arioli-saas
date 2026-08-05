<x-admin-layout title="Notificaciones">

    <div class="page-header">
        <div>
            <h1 class="page-title">Notificaciones</h1>
            <p class="page-subtitle">Historial de actividad de la plataforma</p>
        </div>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $notification)
                    <tr style="border-bottom:1px solid var(--card-border); {{ !$notification->read ? 'background:rgba(59,130,246,.03)' : '' }}">
                        <td style="padding:13px 16px; width:12px;">
                            @php
                                $colors = ['green' => 'var(--success)', 'blue' => 'var(--accent)', 'red' => 'var(--danger)', 'yellow' => '#f59e0b'];
                                $color  = $colors[$notification->color] ?? 'var(--accent)';
                            @endphp
                            <div style="width:8px; height:8px; border-radius:50%; background:{{ $color }};"></div>
                        </td>
                        <td style="padding:13px 20px;">
                            <div style="font-weight:600; color:var(--text-primary); font-size:13.5px;">{{ $notification->title }}</div>
                        </td>
                        <td style="padding:13px 20px; color:var(--text-secondary); font-size:13px;">
                            {{ $notification->message }}
                        </td>
                        <td style="padding:13px 20px; color:var(--text-muted); font-size:12px; white-space:nowrap;">
                            {{ $notification->created_at->format('d/m/Y H:i') }}
                            <div style="font-size:11px; margin-top:2px;">{{ $notification->created_at->diffForHumans() }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <strong>Sin notificaciones</strong>
                                <p>Las notificaciones aparecerán aquí automáticamente.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($notifications->hasPages())
            <div style="padding:14px 20px; border-top:1px solid var(--card-border);">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</x-admin-layout>