@forelse($tenants as $tenant)
    <tr style="border-bottom:1px solid var(--card-border); transition:background .12s;"
        onmouseover="this.style.background='var(--body-bg)'"
        onmouseout="this.style.background='transparent'">
        <td style="padding:13px 20px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <span style="font-weight:600; color:var(--text-primary); font-size:14px;">{{ $tenant->name ?? '-' }}</span>
                @if($tenant->is_custom)
                    <span style="font-size:10px; background:#fffbeb; color:#d97706; border:1px solid #fde68a; padding:1px 7px; border-radius:20px; font-weight:600; white-space:nowrap;">Personalizado</span>
                @endif
            </div>
            <div style="font-family:var(--font-mono); font-size:11px; color:var(--text-muted); margin-top:2px;">{{ $tenant->id }}</div>
        </td>
        <td style="padding:13px 20px;">
            <span style="display:inline-flex; align-items:center; gap:5px; background:var(--accent-light); color:var(--accent); padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; font-family:var(--font-mono);">
                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 1 0 20M12 2a14.5 14.5 0 0 0 0 20M2 12h20"/>
                </svg>
                {{ $domains->get($tenant->id)?->first()?->domain ?? '-' }}
            </span>
        </td>
        <td style="padding:13px 20px; color:var(--text-secondary); font-size:13.5px;">{{ $tenant->email ?? '-' }}</td>
        <td style="padding:13px 20px; color:var(--text-muted); font-size:12px;">{{ $tenant->created_at->format('d/m/Y') }}</td>
        <td style="padding:13px 20px;">
            <div style="display:flex; gap:6px; align-items:center;">
                <a href="{{ route('tenants.show', $tenant->id) }}" class="action-btn action-view">Ver</a>
                <a href="{{ route('tenants.edit', $tenant->id) }}" class="action-btn action-edit">Editar</a>
                <form method="POST" action="{{ route('tenants.destroy', $tenant->id) }}"
                      onsubmit="return confirm('¿Eliminar este cliente?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn action-delete">Eliminar</button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
                <strong>
                    @if(!empty($search))
                        No se encontraron clientes para "{{ $search }}"
                    @else
                        No hay clientes registrados
                    @endif
                </strong>
                <p>
                    @if(empty($search))
                        Creá tu primer cliente con el botón de arriba.
                    @endif
                </p>
            </div>
        </td>
    </tr>
@endforelse