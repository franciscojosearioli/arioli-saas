<h2 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0 0 8px;">Auditoría</h2>
<p style="font-size:13px; color:var(--text-muted); margin:0 0 20px;">
    Registro de cambios de configuración y otras acciones administrativas. Se completa
    automáticamente a medida que se agreguen más módulos auditados (Facturación, Legales).
</p>

@if($logs->isEmpty())
    <div style="text-align:center; padding:32px; color:var(--text-muted); font-size:13px;">
        Todavía no hay eventos registrados.
    </div>
@else
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12.5px;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #f3f4f6;">
                    <th style="padding:8px 10px; color:var(--text-muted); font-weight:600;">Fecha</th>
                    <th style="padding:8px 10px; color:var(--text-muted); font-weight:600;">Usuario</th>
                    <th style="padding:8px 10px; color:var(--text-muted); font-weight:600;">Acción</th>
                    <th style="padding:8px 10px; color:var(--text-muted); font-weight:600;">Entidad</th>
                    <th style="padding:8px 10px; color:var(--text-muted); font-weight:600;">IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr style="border-bottom:1px solid #f9fafb;">
                        <td style="padding:8px 10px; white-space:nowrap;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding:8px 10px;">{{ $log->user_email ?? '—' }}</td>
                        <td style="padding:8px 10px;">
                            <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:#f3f4f6; color:#374151;">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td style="padding:8px 10px; font-family:monospace; font-size:11.5px;">
                            {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                        </td>
                        <td style="padding:8px 10px; color:var(--text-muted);">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $logs->appends(['tab' => 'auditoria'])->links() }}
    </div>
@endif
