<x-admin-layout title="Editar Plan">

    <div style="margin-bottom:24px;">
        <a href="{{ route('plans.index') }}"
           style="font-size:13px; color:var(--text-muted); text-decoration:none;">
            ← Volver al listado
        </a>
    </div>

    <div class="card" style="max-width:580px; padding:28px;">

        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:24px;">Editar Plan</h2>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px;">
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('plans.update', $plan->id) }}">
            @csrf
            @method('PATCH')

            <div style="margin-bottom:16px;">
                <label class="form-label">Sistema</label>
                <select name="product_id" class="form-select">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            {{ $plan->product_id == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Período</label>
                <select name="period" id="period" class="form-select" onchange="togglePerpetual(this)">
                    <option value="monthly"    {{ $plan->period == 'monthly'    ? 'selected' : '' }}>Mensual (1 mes)</option>
                    <option value="quarterly"  {{ $plan->period == 'quarterly'  ? 'selected' : '' }}>Trimestral (3 meses)</option>
                    <option value="semiannual" {{ $plan->period == 'semiannual' ? 'selected' : '' }}>Semestral (6 meses)</option>
                    <option value="annual"     {{ $plan->period == 'annual'     ? 'selected' : '' }}>Anual (12 meses)</option>
                    <option value="perpetual"  {{ $plan->period == 'perpetual'  ? 'selected' : '' }}>Licencia indefinida (sin vencimiento — pago único)</option>
                </select>
                <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                    "Licencia indefinida" no se publica ni se puede comprar desde el checkout — solo la puede asignar un admin al dar de alta o editar un cliente.
                </p>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label" id="priceLabel">Precio mensual base (ARS)</label>
                <input type="number" name="price" class="form-input"
                       value="{{ old('price', $plan->base_price) }}"
                       min="0" step="1">
            </div>

            <div style="margin-bottom:16px;" id="discountWrap">
                <label class="form-label">Descuento (%)</label>
                <select name="discount_percent" class="form-select">
                    <option value="0"  {{ $plan->discount_percent == 0  ? 'selected' : '' }}>Sin descuento</option>
                    <option value="10" {{ $plan->discount_percent == 10 ? 'selected' : '' }}>10%</option>
                    <option value="20" {{ $plan->discount_percent == 20 ? 'selected' : '' }}>20%</option>
                    <option value="30" {{ $plan->discount_percent == 30 ? 'selected' : '' }}>30%</option>
                </select>
            </div>

            <script>
                function togglePerpetual(select) {
                    const isPerpetual = select.value === 'perpetual';
                    document.getElementById('discountWrap').style.display = isPerpetual ? 'none' : '';
                    document.getElementById('priceLabel').textContent = isPerpetual ? 'Monto único (ARS)' : 'Precio mensual base (ARS)';
                }
                document.addEventListener('DOMContentLoaded', () => togglePerpetual(document.getElementById('period')));
            </script>

            <div style="margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="active" id="active" value="1"
                       {{ $plan->active ? 'checked' : '' }}
                       style="width:16px; height:16px; accent-color:var(--accent);">
                <label for="active" class="form-label" style="margin:0;">Plan activo</label>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('plans.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</x-admin-layout>