@forelse($plans as $plan)
    <tr style="border-bottom:1px solid var(--card-border); transition:background .12s;"
        onmouseover="this.style.background='var(--body-bg)'"
        onmouseout="this.style.background='transparent'">
        <td style="padding:13px 20px;">
            <div style="font-weight:600; color:var(--text-primary); font-size:14px;">
                {{ $plan->product->name ?? '-' }}
            </div>
        </td>
        <td style="padding:13px 20px;">
            <span class="badge badge-blue">{{ $plan->period_label }}</span>
        </td>
        <td style="padding:13px 20px;">
            <div style="font-size:15px; font-weight:700; color:var(--text-primary);">
                ${{ number_format($plan->price, 0, ',', '.') }}
            </div>
            @if($plan->discount_percent > 0)
                <div style="font-size:11px; color:var(--success); margin-top:2px;">
                    {{ $plan->discount_percent }}% descuento
                </div>
            @endif
        </td>
        <td style="padding:13px 20px; color:var(--text-muted); font-size:12px;">
            ${{ number_format($plan->base_price, 0, ',', '.') }}/mes
        </td>
        <td style="padding:13px 20px;">
            <span style="font-size:13px; font-weight:600; color:var(--accent);">
                {{ $plan->licenses_count }}
            </span>
        </td>
        <td style="padding:13px 20px;">
            @if($plan->active)
                <span class="badge badge-green">Activo</span>
            @else
                <span class="badge badge-red">Inactivo</span>
            @endif
        </td>
        <td style="padding:13px 20px;">
            <div style="display:flex; gap:6px; align-items:center;">
                <a href="{{ route('plans.show', $plan->id) }}" class="action-btn action-view">Ver</a>
                <a href="{{ route('plans.edit', $plan->id) }}" class="action-btn action-edit">Editar</a>
                <form method="POST" action="{{ route('plans.destroy', $plan->id) }}"
                      onsubmit="return confirm('¿Eliminar este plan?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn action-delete">Eliminar</button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <strong>
                    @if(!empty($search))
                        No se encontraron planes para "{{ $search }}"
                    @else
                        No hay planes registrados
                    @endif
                </strong>
            </div>
        </td>
    </tr>
@endforelse