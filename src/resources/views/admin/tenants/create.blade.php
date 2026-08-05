<x-admin-layout title="Nuevo Cliente">

    <div class="mb-6">
        <a href="{{ route('tenants.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Volver al listado
        </a>
    </div>

    <div class="bg-white rounded shadow p-6 max-w-2xl">

        @if($errors->any())
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenants.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                <select name="client_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Cliente nuevo (detectar por email) —</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ (old('client_id', $selectedClient?->id)) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">
                    @if($selectedClient)
                        Este sistema se va a agregar a <strong>{{ $selectedClient->name }}</strong>.
                    @else
                        Si dejás "Cliente nuevo", se busca un cliente existente por el email de contacto o se crea uno — elegí uno de la lista para evitar duplicados.
                    @endif
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del cliente</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Empresa S.A.">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email de acceso al panel</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="contacto@empresa.com">
                <p class="text-xs text-gray-400 mt-1">El cliente usará este email para iniciar sesión en su panel.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Mínimo 8 caracteres">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Repetir contraseña">
                </div>
            </div>

            {{-- Plan (primero, porque determina el dominio) --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                <select name="plan_id" id="plan_id"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" data-domain="">Seleccioná un plan</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}"
                                data-domain="{{ $plan->product?->public_domain }}"
                                {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->product?->name }} — {{ $plan->period_label }} — ${{ number_format($plan->price, 0, ',', '.') }} ARS
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Subdominio con preview dinámico --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subdominio</label>
                <div class="flex items-center gap-0">
                    <input type="text" name="subdomain" id="subdomain" value="{{ old('subdomain') }}"
                           class="border border-gray-300 rounded-l px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-40"
                           placeholder="miempresa">
                    <span id="domain-preview"
                          class="inline-block border border-l-0 border-gray-300 rounded-r bg-gray-50 px-3 py-2 text-sm text-gray-500 whitespace-nowrap">
                        .{{ config('app.tenant_domain') }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Solo letras minúsculas, números y guiones.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded text-sm">
                    Crear Cliente
                </button>
                <a href="{{ route('tenants.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded text-sm">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

    <script>
        const baseDomain = '{{ config('app.tenant_domain') }}';
        const planSelect = document.getElementById('plan_id');
        const preview    = document.getElementById('domain-preview');

        function updatePreview() {
            const opt    = planSelect.selectedOptions[0];
            const pd     = opt?.dataset?.domain;
            preview.textContent = pd ? `.${pd}.${baseDomain}` : `.${baseDomain}`;
        }

        planSelect.addEventListener('change', updatePreview);
        updatePreview();
    </script>

</x-admin-layout>
