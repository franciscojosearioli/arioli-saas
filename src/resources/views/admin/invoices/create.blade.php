<x-admin-layout title="Nueva Factura">

    <div style="margin-bottom:24px;">
        <a href="{{ route('finanzas.facturacion.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">
            ← Volver a Facturación
        </a>
    </div>

    <div class="card" style="max-width:600px; padding:28px;">
        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">Nueva Factura</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:24px;">
            Se crea como borrador. Podés revisarla y emitirla desde su detalle.
        </p>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px;">
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('finanzas.facturacion.store') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label class="form-label">Orden relacionada (opcional)</label>
                <select id="orderSelect" name="order_id" class="form-select" onchange="fillFromOrder()">
                    <option value="">— Ingresar datos manualmente —</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}"
                                data-tenant="{{ $order->tenant_id }}"
                                data-name="{{ $order->customer_name }}"
                                data-amount="{{ $order->amount }}"
                                {{ old('order_id') == $order->id ? 'selected' : '' }}>
                            #{{ $order->id }} — {{ $order->customer_name }} — {{ $order->plan?->product?->name }} — ${{ number_format($order->amount, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Tenant</label>
                <input type="text" id="tenant_id" name="tenant_id" class="form-input" value="{{ old('tenant_id') }}" placeholder="ej. acme">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Nombre del cliente</label>
                <input type="text" id="customer_name" name="customer_name" class="form-input" value="{{ old('customer_name') }}">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">CUIT del cliente (opcional)</label>
                <input type="text" name="customer_cuit" class="form-input" value="{{ old('customer_cuit') }}" placeholder="20-12345678-9">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Monto</label>
                <input type="number" id="amount" name="amount" class="form-input" value="{{ old('amount') }}" min="0" step="0.01">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Moneda</label>
                <input type="text" name="currency" class="form-input" value="{{ old('currency', 'ARS') }}" maxlength="3">
            </div>

            <div style="margin-bottom:24px;">
                <label class="form-label">Notas internas (opcional)</label>
                <textarea name="notes" class="form-input" rows="3">{{ old('notes') }}</textarea>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Crear borrador</button>
                <a href="{{ route('finanzas.facturacion.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
    function fillFromOrder() {
        const select = document.getElementById('orderSelect');
        const opt = select.options[select.selectedIndex];
        if (!opt.value) return;
        document.getElementById('tenant_id').value = opt.dataset.tenant || '';
        document.getElementById('customer_name').value = opt.dataset.name || '';
        document.getElementById('amount').value = opt.dataset.amount || '';
    }
    </script>

</x-admin-layout>
