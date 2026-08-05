@forelse($licenses as $license)
    <tr style="border-bottom:1px solid var(--card-border); transition:background .12s;"
        onmouseover="this.style.background='var(--body-bg)'"
        onmouseout="this.style.background='transparent'">
        <td style="padding:13px 20px;">
            <div style="font-weight:600; color:var(--text-primary); font-size:14px;">
                {{ $license->tenant->name ?? $license->tenant_id }}
            </div>
            <div style="font-family:var(--font-mono); font-size:11px; color:var(--text-muted); margin-top:2px;">
                {{ $license->tenant_id }}
            </div>
        </td>
        <td style="padding:13px 20px;">
            <span style="font-size:13px; color:var(--text-secondary);">
                {{ $license->plan->name ?? '-' }}
            </span>
        </td>
        <td style="padding:13px 20px;">
            @if($license->license_type === 'demo')
                <span class="badge" style="background:#fef3c7; color:#92400e;">Demo</span>
            @elseif(in_array($license->license_type, ['comercial', 'commercial']))
                <span class="badge badge-green">Comercial</span>
            @else
                <span class="badge" style="background:var(--body-bg); color:var(--text-muted);">{{ $license->license_type ?? '—' }}</span>
            @endif
        </td>
        <td style="padding:13px 20px;">
            @if($license->isValid())
                <span class="badge badge-green">Activa</span>
            @elseif($license->isExpired())
                <span class="badge badge-red">Expirada</span>
            @else
                <span class="badge" style="background:var(--body-bg); color:var(--text-muted);">Inactiva</span>
            @endif
        </td>
        <td style="padding:13px 20px; color:var(--text-muted); font-size:12px;">
            {{ $license->starts_at ? $license->starts_at->format('d/m/Y') : '—' }}
        </td>
        <td style="padding:13px 20px; color:var(--text-muted); font-size:12px;">
            {{ $license->expires_at ? $license->expires_at->format('d/m/Y') : '∞' }}
        </td>
        <td style="padding:13px 20px;">
            @php $days = $license->daysRemaining(); @endphp
            @if($days === null)
                <span style="font-size:13px; font-weight:600; color:var(--success);">Sin vencimiento</span>
            @else
                <span style="font-size:13px; font-weight:600; color:{{ $days > 30 ? 'var(--success)' : ($days > 7 ? '#f59e0b' : 'var(--danger)') }};">
                    {{ $days > 0 ? $days . ' días' : 'Vencida' }}
                </span>
            @endif
        </td>
        <td style="padding:13px 20px;">
            <div style="display:flex; gap:6px; align-items:center;">
                <a href="{{ route('licenses.show', $license->id) }}" class="action-btn action-view">Ver</a>
                <a href="{{ route('licenses.edit', $license->id) }}" class="action-btn action-edit">Editar</a>
                <form method="POST" action="{{ route('licenses.destroy', $license->id) }}"
                      onsubmit="return confirm('¿Eliminar esta licencia?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn action-delete">Eliminar</button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <strong>
                    @if(!empty($search))
                        No se encontraron licencias para "{{ $search }}"
                    @else
                        No hay licencias registradas
                    @endif
                </strong>
                <p>
                    @if(empty($search))
                        Creá tu primera licencia con el botón de arriba.
                    @endif
                </p>
            </div>
        </td>
    </tr>
@endforelse
