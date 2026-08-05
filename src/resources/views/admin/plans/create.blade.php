<x-admin-layout title="Nuevo Plan">

    <div style="margin-bottom:24px;">
        <a href="{{ route('plans.index') }}"
           style="font-size:13px; color:var(--text-muted); text-decoration:none;">
            ← Volver al listado
        </a>
    </div>

    <div class="card" style="max-width:580px; padding:28px;">

        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:24px;">Nuevo Plan</h2>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px;">
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('plans.store') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label class="form-label">Sistema</label>
                <select name="product_id" class="form-select">
                    <option value="">Seleccioná un sistema</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Período</label>
                <select name="period" id="period" class="form-select" onchange="togglePerpetual(this)">
                    <option value="">Seleccioná un período</option>
                    <option value="monthly"    {{ old('period') == 'monthly'    ? 'selected' : '' }}>Mensual (1 mes)</option>
                    <option value="quarterly"  {{ old('period') == 'quarterly'  ? 'selected' : '' }}>Trimestral (3 meses)</option>
                    <option value="semiannual" {{ old('period') == 'semiannual' ? 'selected' : '' }}>Semestral (6 meses)</option>
                    <option value="annual"     {{ old('period') == 'annual'     ? 'selected' : '' }}>Anual (12 meses)</option>
                    <option value="perpetual"  {{ old('period') == 'perpetual'  ? 'selected' : '' }}>Licencia indefinida (sin vencimiento — pago único)</option>
                </select>
                <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                    "Licencia indefinida" no se publica ni se puede comprar desde el checkout — solo la puede asignar un admin al dar de alta o editar un cliente.
                </p>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label" id="priceLabel">Precio mensual base (ARS)</label>
                <input type="number" name="price" class="form-input"
                       value="{{ old('price') }}"
                       min="0" step="1"
                       placeholder="200000">
                <p style="font-size:12px; color:var(--text-muted); margin-top:4px;" id="priceHelp">
                    El precio total se calcula automáticamente según el período y descuento.
                </p>
            </div>

            <div style="margin-bottom:16px;" id="discountWrap">
                <label class="form-label">Descuento (%)</label>
                <select name="discount_percent" class="form-select">
                    <option value="0"  {{ old('discount_percent') == '0'  ? 'selected' : '' }}>Sin descuento (Mensual)</option>
                    <option value="10" {{ old('discount_percent') == '10' ? 'selected' : '' }}>10% (Trimestral)</option>
                    <option value="20" {{ old('discount_percent') == '20' ? 'selected' : '' }}>20% (Semestral)</option>
                    <option value="30" {{ old('discount_percent') == '30' ? 'selected' : '' }}>30% (Anual)</option>
                </select>
            </div>

            <script>
                function togglePerpetual(select) {
                    const isPerpetual = select.value === 'perpetual';
                    document.getElementById('discountWrap').style.display = isPerpetual ? 'none' : '';
                    document.getElementById('priceLabel').textContent = isPerpetual ? 'Monto único (ARS)' : 'Precio mensual base (ARS)';
                    document.getElementById('priceHelp').textContent = isPerpetual ? 'Es el monto final, sin multiplicar por período ni descuento.' : 'El precio total se calcula automáticamente según el período y descuento.';
                }
                document.addEventListener('DOMContentLoaded', () => togglePerpetual(document.getElementById('period')));
            </script>

            <div style="margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="active" id="active" value="1"
                       {{ old('active', '1') ? 'checked' : '' }}
                       style="width:16px; height:16px; accent-color:var(--accent);">
                <label for="active" class="form-label" style="margin:0;">Plan activo</label>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Crear Plan</button>
                <a href="{{ route('plans.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</x-admin-layout>