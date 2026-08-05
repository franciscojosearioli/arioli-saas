<x-admin-layout title="Registrar dominio — {{ $client->name }}">

    <div style="margin-bottom:24px;">
        <a href="{{ route('clients.show', $client) }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a {{ $client->name }}</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0 0 20px;">Registrar dominio vía Porkbun</h1>

    <div class="card" style="padding:24px; max-width:520px;">
        <div style="margin-bottom:16px;">
            <label style="font-size:12.5px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Dominio</label>
            <div style="display:flex; gap:8px;">
                <input type="text" id="domainInput" class="form-input" placeholder="midominio.com" required>
                <button type="button" id="checkBtn" class="btn btn-secondary">Consultar disponibilidad</button>
            </div>
        </div>

        <div id="checkResult" style="font-size:13px; margin-bottom:16px;"></div>

        <form id="registerForm" method="POST" action="{{ route('clients.domain-registrations.store', $client) }}" style="display:none; border-top:1px solid #f3f4f6; padding-top:16px;">
            @csrf
            <input type="hidden" name="domain_name" id="hiddenDomain">
            <input type="hidden" name="price_ars" id="hiddenPriceArs">

            <div id="priceBreakdown" style="font-size:12.5px; color:var(--text-secondary); background:var(--body-bg); border-radius:8px; padding:10px 12px; margin-bottom:12px;"></div>

            <label style="font-size:12.5px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">
                Honorario de gestión (ARS, opcional)
            </label>
            <input type="number" step="0.01" min="0" name="management_fee" id="managementFeeInput" class="form-input" placeholder="0" style="margin-bottom:16px;">

            <p style="font-size:12.5px; color:var(--text-muted); margin-bottom:12px;">
                Esto genera un cobro por Mercado Pago en <strong>ARS</strong> — el dominio se registra automáticamente en Porkbun recién cuando el cliente lo pague. No se cobra nada del saldo de Porkbun hasta ese momento.
            </p>

            <label style="font-size:12.5px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">
                Escribí <code id="confirmDomainLabel"></code> para confirmar
            </label>
            <input type="text" name="confirm_domain" id="confirmDomainInput" class="form-input" style="margin-bottom:16px;" autocomplete="off">

            <button type="submit" id="submitBtn" class="btn btn-primary" disabled style="width:100%;">Generar cobro y preparar registro</button>
        </form>
    </div>

    <script>
        const domainInput = document.getElementById('domainInput');
        const checkBtn = document.getElementById('checkBtn');
        const checkResult = document.getElementById('checkResult');
        const registerForm = document.getElementById('registerForm');
        const hiddenDomain = document.getElementById('hiddenDomain');
        const hiddenPriceArs = document.getElementById('hiddenPriceArs');
        const priceBreakdown = document.getElementById('priceBreakdown');
        const managementFeeInput = document.getElementById('managementFeeInput');
        const confirmDomainLabel = document.getElementById('confirmDomainLabel');
        const confirmDomainInput = document.getElementById('confirmDomainInput');
        const submitBtn = document.getElementById('submitBtn');

        function fmt(n) {
            return new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n);
        }

        function updateBreakdown() {
            const fee = parseFloat(managementFeeInput.value) || 0;
            const base = parseFloat(hiddenPriceArs.dataset.base || '0');
            const total = base + fee;
            hiddenPriceArs.value = total.toFixed(2);
            priceBreakdown.innerHTML = `Costo Porkbun: <strong>$${fmt(base)} ARS</strong> (USD ${hiddenPriceArs.dataset.usd} · cotización $${hiddenPriceArs.dataset.rate})<br>
                Honorario de gestión: <strong>$${fmt(fee)} ARS</strong><br>
                <span style="color:var(--text-primary); font-weight:700;">Total a cobrar: $${fmt(total)} ARS</span>`;
        }

        checkBtn.addEventListener('click', async () => {
            const domain = domainInput.value.trim();
            if (!domain) return;

            registerForm.style.display = 'none';
            checkResult.textContent = 'Consultando...';
            checkBtn.disabled = true;

            try {
                const response = await fetch(@json(route('clients.domain-registrations.check', $client)), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ domain_name: domain }),
                });
                const data = await response.json();

                if (data.available && data.price_ars) {
                    checkResult.innerHTML = `<span style="color:#065f46; font-weight:600;">✓ Disponible</span> — ${data.message}`;
                    hiddenDomain.value = domain;
                    hiddenPriceArs.dataset.base = data.price_ars;
                    hiddenPriceArs.dataset.usd = data.price_usd;
                    hiddenPriceArs.dataset.rate = data.rate;
                    confirmDomainLabel.textContent = domain;
                    confirmDomainInput.value = '';
                    managementFeeInput.value = '';
                    submitBtn.disabled = true;
                    updateBreakdown();
                    registerForm.style.display = 'block';
                } else if (data.available) {
                    checkResult.innerHTML = `<span style="color:#991b1b; font-weight:600;">✗ Disponible pero sin precio</span> — no se pudo obtener el costo de Porkbun, probá de nuevo en unos segundos.`;
                } else {
                    checkResult.innerHTML = `<span style="color:#991b1b; font-weight:600;">✗ No disponible</span> — ${data.message}`;
                }
            } catch (e) {
                checkResult.innerHTML = '<span style="color:#991b1b;">Error al consultar. Intentá de nuevo.</span>';
            } finally {
                checkBtn.disabled = false;
            }
        });

        managementFeeInput.addEventListener('input', updateBreakdown);

        confirmDomainInput.addEventListener('input', () => {
            submitBtn.disabled = confirmDomainInput.value.trim() !== hiddenDomain.value;
        });

        registerForm.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Generando cobro...';
        });
    </script>

</x-admin-layout>
