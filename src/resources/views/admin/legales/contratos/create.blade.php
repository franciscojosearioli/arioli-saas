<x-admin-layout title="Nuevo Contrato">

    <div style="margin-bottom:24px;">
        <a href="{{ route('legales.contratos.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Contratos</a>
    </div>

    <div class="card" style="max-width:640px; padding:28px;">
        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">Nuevo Contrato</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:24px;">
            Se crea como borrador con un firmante "Cliente" por defecto. Podés agregar más firmantes y enviarlo desde su detalle.
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

        <form method="POST" action="{{ route('legales.contratos.store') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label class="form-label">Título del contrato</label>
                <input type="text" name="title" class="form-input" value="{{ old('title') }}" placeholder="Contrato de licencia — Servis">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select">
                    @foreach(\App\Enums\ContractType::cases() as $t)
                        <option value="{{ $t->value }}" {{ old('type') === $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Plantilla (opcional)</label>
                <select name="contract_template_id" id="templateSelect" class="form-select">
                    <option value="">— Sin plantilla (contenido libre) —</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ old('contract_template_id') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }} ({{ $template->type->label() }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Asociar a (opcional)</label>
                <select name="contractable_ref" id="contractableSelect" class="form-select" onchange="splitContractable()">
                    <option value="">— Ninguno —</option>
                    <optgroup label="Órdenes">
                        @foreach($orders as $order)
                            <option value="order:{{ $order->id }}"
                                    data-tenant="{{ $order->tenant_id }}"
                                    data-name="{{ $order->customer_name }}"
                                    data-email="{{ $order->customer_email }}">
                                #{{ $order->id }} — {{ $order->customer_name }} — {{ $order->plan?->product?->name }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Licencias">
                        @foreach($licenses as $license)
                            <option value="license:{{ $license->id }}"
                                    data-tenant="{{ $license->tenant_id }}"
                                    data-name="{{ $license->tenant_id }}"
                                    data-email="">
                                #{{ $license->id }} — {{ $license->tenant_id }} — {{ $license->plan?->product?->name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                <input type="hidden" name="contractable_type" id="contractable_type">
                <input type="hidden" name="contractable_id" id="contractable_id">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Tenant</label>
                <input type="text" name="tenant_id" id="tenant_id" class="form-input" value="{{ old('tenant_id') }}" placeholder="ej. acme">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Nombre del cliente (firmante inicial)</label>
                <input type="text" name="customer_name" id="customer_name" class="form-input" value="{{ old('customer_name') }}">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Email del cliente</label>
                <input type="email" name="customer_email" id="customer_email" class="form-input" value="{{ old('customer_email') }}">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">CUIT del cliente (opcional)</label>
                <input type="text" name="customer_cuit" class="form-input" value="{{ old('customer_cuit') }}">
            </div>

            <div style="margin-bottom:24px;">
                <label class="form-label">Contenido (si no elegiste plantilla)</label>
                <textarea name="content" class="form-input" rows="6" placeholder="Podés usar placeholders como @{{cliente_nombre}}, @{{monto}}, @{{fecha_hoy}}...">{{ old('content') }}</textarea>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Crear borrador</button>
                <a href="{{ route('legales.contratos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
    function splitContractable() {
        const select = document.getElementById('contractableSelect');
        const opt = select.options[select.selectedIndex];
        if (!opt.value) {
            document.getElementById('contractable_type').value = '';
            document.getElementById('contractable_id').value = '';
            return;
        }
        const [type, id] = opt.value.split(':');
        document.getElementById('contractable_type').value = type;
        document.getElementById('contractable_id').value = id;
        document.getElementById('tenant_id').value = opt.dataset.tenant || '';
        document.getElementById('customer_name').value = opt.dataset.name || '';
        document.getElementById('customer_email').value = opt.dataset.email || '';
    }
    </script>

</x-admin-layout>
