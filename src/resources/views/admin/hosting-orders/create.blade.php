<x-admin-layout title="Nueva orden de hosting">

    <div style="margin-bottom:24px;">
        <a href="{{ route('clients.show', $client) }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a {{ $client->name }}</a>
    </div>

    <div class="card" style="max-width:560px; padding:28px;">
        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Nueva orden de hosting</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:24px;">
            Para <strong>{{ $client->name }}</strong>. La cuenta se crea en HestiaCP de inmediato al confirmar este
            formulario — el link de pago de Mercado Pago se genera en paralelo, para cobrar, pero no bloquea el alta.
        </p>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px;">
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @if($plans->isEmpty())
            <div class="alert alert-error">Todavía no hay planes de hosting cargados — cargá al menos uno en <a href="{{ route('hosting-plans.create') }}">Planes de Hosting</a> antes de crear una orden.</div>
        @else
            <form method="POST" action="{{ route('clients.hosting-orders.store', $client) }}">
                @csrf

                <div style="margin-bottom:16px;">
                    <label class="form-label">Plan de hosting</label>
                    <select name="hosting_plan_id" class="form-select" required>
                        <option value="">Seleccioná un plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('hosting_plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} — ${{ number_format($plan->price, 0, ',', '.') }} {{ $plan->currency }} / {{ $plan->billing_cycle->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Dominio</label>
                    <input type="text" name="domain_name" class="form-input" value="{{ old('domain_name') }}" placeholder="cliente.com" required>
                </div>

                @if($contact)
                    <div class="mcard tint" style="margin-bottom:24px; padding:10px 14px; font-size:12.5px; color:var(--text-muted); border-radius:9px;">
                        La cuenta en HestiaCP se crea con el contacto ya cargado: <strong>{{ $contact->name }}</strong> ({{ $contact->email }}).
                    </div>
                @else
                    <div style="margin-bottom:16px;">
                        <label class="form-label">Nombre de contacto</label>
                        <input type="text" name="contact_name" class="form-input" value="{{ old('contact_name') }}" placeholder="Nombre del cliente" required>
                        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">Este cliente todavía no tiene un contacto cargado — hace falta un email para crear la cuenta en HestiaCP.</p>
                    </div>
                    <div style="margin-bottom:24px;">
                        <label class="form-label">Email de contacto</label>
                        <input type="email" name="contact_email" class="form-input" value="{{ old('contact_email') }}" placeholder="cliente@empresa.com" required>
                    </div>
                @endif

                <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-secondary); margin-bottom:20px;">
                    <input type="checkbox" name="already_paid" value="1" id="alreadyPaidCheckbox">
                    Ya está pagado (no generar link de pago)
                </label>

                <div style="display:flex; gap:10px;">
                    <button type="submit" id="submitBtn" class="btn btn-primary">Crear orden y generar link de pago</button>
                    <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        @endif
    </div>

    <script>
        const alreadyPaidCheckbox = document.getElementById('alreadyPaidCheckbox');
        const submitBtn = document.getElementById('submitBtn');
        if (alreadyPaidCheckbox && submitBtn) {
            alreadyPaidCheckbox.addEventListener('change', () => {
                submitBtn.textContent = alreadyPaidCheckbox.checked ? 'Crear orden (ya pagada)' : 'Crear orden y generar link de pago';
            });
        }
    </script>

</x-admin-layout>
