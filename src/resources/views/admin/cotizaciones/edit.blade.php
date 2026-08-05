@php
    $existingItemsData = $quote->items->map(fn ($item) => [
        'description'   => $item->description,
        'item_type'      => $item->item_type->value,
        'service_type'   => $item->service_type?->value,
        'billing_cycle'  => $item->billing_cycle?->value ?? 'unico',
        'quantity'       => (float) $item->quantity,
        'unit_label'     => $item->unit_label,
        'currency'       => $item->currency->value,
        'unit_price'     => (float) $item->unit_price,
    ]);
@endphp
<x-admin-layout title="Editar Propuesta">

    <div style="margin-bottom:24px;">
        <a href="{{ route('cotizaciones.show', $quote) }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a la propuesta</a>
    </div>

    <div class="card" style="max-width:820px; padding:28px;">
        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:24px;">Editar Propuesta</h2>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px;">
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('cotizaciones.update', $quote) }}" id="quoteForm">
            @csrf
            @method('PUT')

            {{-- Información general --}}
            <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:12px;">
                Información general
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label class="form-label">Cliente</label>
                    <select name="client_id" class="form-select" required>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ (string) old('client_id', $quote->client_id) === (string) $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Título</label>
                    <input type="text" name="title" class="form-input" value="{{ old('title', $quote->title) }}" required>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Resumen (opcional)</label>
                <p style="font-size:12px; color:var(--text-muted); margin:2px 0 8px;">Una línea corta que se muestra debajo del título en el PDF.</p>
                <input type="text" name="summary" class="form-input" value="{{ old('summary', $quote->summary) }}">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
                <div>
                    <label class="form-label">Válida hasta (opcional)</label>
                    <input type="date" name="valid_until" class="form-input" value="{{ old('valid_until', $quote->valid_until?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label">Cobro inicial (opcional)</label>
                    <input type="number" step="0.01" name="initial_charge_amount" class="form-input" value="{{ old('initial_charge_amount', $quote->initial_charge_amount) }}">
                </div>
            </div>

            {{-- Contenido de la propuesta --}}
            <div style="border-top:1px solid #f3f4f6; padding-top:16px; margin-bottom:16px;">
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px;">
                    Contenido de la propuesta
                </div>
                <p style="font-size:12px; color:var(--text-muted); margin:0 0 14px;">
                    Un ítem por línea se muestra como lista con viñetas. Dejá una línea en blanco para separar párrafos. Cada sección se oculta sola si queda vacía.
                </p>

                <div style="margin-bottom:14px;">
                    <label class="form-label">Introducción</label>
                    <textarea name="introduction" class="form-input" rows="3">{{ old('introduction', $quote->introduction) }}</textarea>
                </div>
                <div style="margin-bottom:14px;">
                    <label class="form-label">Alcance del proyecto</label>
                    <textarea name="project_scope" class="form-input" rows="5">{{ old('project_scope', $quote->project_scope) }}</textarea>
                </div>
                <div style="margin-bottom:14px;">
                    <label class="form-label">Beneficios</label>
                    <textarea name="benefits" class="form-input" rows="4">{{ old('benefits', $quote->benefits) }}</textarea>
                </div>
                <div style="margin-bottom:14px;">
                    <label class="form-label">No incluye</label>
                    <textarea name="exclusions" class="form-input" rows="4">{{ old('exclusions', $quote->exclusions) }}</textarea>
                </div>
                <div style="margin-bottom:4px;">
                    <label class="form-label">Garantía</label>
                    <textarea name="warranty" class="form-input" rows="2">{{ old('warranty', $quote->warranty) }}</textarea>
                </div>
            </div>

            {{-- Condiciones comerciales --}}
            <div style="border-top:1px solid #f3f4f6; padding-top:16px; margin-bottom:16px;">
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:12px;">
                    Condiciones comerciales
                </div>

                <div style="margin-bottom:14px;">
                    <label class="form-label">Forma de pago</label>
                    <textarea name="payment_terms" class="form-input" rows="3">{{ old('payment_terms', $quote->payment_terms) }}</textarea>
                </div>
                <div style="margin-bottom:14px;">
                    <label class="form-label">Tiempo estimado</label>
                    <input type="text" name="timeline_estimate" class="form-input" placeholder="Ej. 15 a 20 días hábiles" value="{{ old('timeline_estimate', $quote->timeline_estimate) }}">
                </div>
                <div style="margin-bottom:4px;">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observations" class="form-input" rows="2">{{ old('observations', $quote->observations) }}</textarea>
                </div>
            </div>

            {{-- Proyecto al aceptarse --}}
            <div style="border-top:1px solid #f3f4f6; padding-top:16px; margin-bottom:16px;">
                <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; margin-bottom:12px;">
                    <input type="checkbox" name="creates_project" id="createsProject" value="1" {{ $quote->creates_project ? 'checked' : '' }} onchange="document.getElementById('projectFields').style.display = this.checked ? 'grid' : 'none'">
                    Esta propuesta genera un proyecto nuevo al aceptarse
                </label>
                <div id="projectFields" style="display:{{ $quote->creates_project ? 'grid' : 'none' }}; grid-template-columns:1fr 1fr; gap:16px;">
                    <input type="text" name="project_name" class="form-input" placeholder="Nombre del proyecto" value="{{ old('project_name', $quote->project_name) }}">
                    <select name="project_type" class="form-select">
                        @foreach(\App\Enums\ProjectType::cases() as $t)
                            <option value="{{ $t->value }}" {{ old('project_type', $quote->project_type) === $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Cotización --}}
            <div style="border-top:1px solid #f3f4f6; padding-top:16px; margin-bottom:20px;">
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:12px;">
                    Cotización — ítems
                </div>
                <div id="itemsWrap"></div>
                <button type="button" class="btn btn-secondary" onclick="addItemRow()" style="margin-top:4px;">+ Agregar ítem</button>

                <div id="totalsByCurrency" style="margin-top:16px; padding-top:12px; border-top:1px solid #f3f4f6; font-size:14px;"></div>
            </div>

            {{-- Mensaje final --}}
            <div style="border-top:1px solid #f3f4f6; padding-top:16px; margin-bottom:20px;">
                <label class="form-label">Mensaje final</label>
                <textarea name="final_message" class="form-input" rows="2">{{ old('final_message', $quote->final_message) }}</textarea>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('cotizaciones.show', $quote) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <template id="itemRowTemplate">
        <div class="quote-item-row" style="border:1px solid var(--card-border); border-radius:10px; padding:12px; margin-bottom:10px;">
            <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:8px; margin-bottom:8px;">
                <input type="text" name="items[__i__][description]" class="form-input" placeholder="Descripción" required>
                <select name="items[__i__][item_type]" class="form-select" onchange="toggleServiceFields(this)">
                    @foreach(\App\Enums\QuoteItemType::cases() as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
                <select name="items[__i__][service_type]" class="form-select service-only">
                    @foreach(\App\Enums\ServiceType::cases() as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
                <select name="items[__i__][billing_cycle]" class="form-select">
                    @foreach(\App\Enums\BillingCycle::cases() as $b)
                        <option value="{{ $b->value }}">{{ $b->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1.2fr 0.8fr 1fr 1.2fr auto; gap:8px; align-items:center;">
                <input type="number" step="0.01" name="items[__i__][quantity]" class="form-input item-qty" value="1" placeholder="Cant." oninput="recalcTotals()">
                <input type="text" name="items[__i__][unit_label]" class="form-input" placeholder="Unidad (año, mes, hs., único...)">
                <select name="items[__i__][currency]" class="form-select item-currency" onchange="recalcTotals()">
                    @foreach(\App\Enums\Currency::cases() as $cur)
                        <option value="{{ $cur->value }}">{{ $cur->value }}</option>
                    @endforeach
                </select>
                <input type="number" step="0.01" name="items[__i__][unit_price]" class="form-input item-price" placeholder="Precio unit." oninput="recalcTotals()">
                <div class="item-subtotal" style="font-size:13px; font-weight:600; color:var(--text-secondary); text-align:right;"></div>
                <button type="button" class="btn btn-secondary" onclick="this.closest('.quote-item-row').remove(); recalcTotals();">×</button>
            </div>
        </div>
    </template>

    <script>
        let itemIndex = 0;

        function addItemRow(prefill) {
            const template = document.getElementById('itemRowTemplate').innerHTML.replaceAll('__i__', itemIndex);
            const wrap = document.getElementById('itemsWrap');
            const div = document.createElement('div');
            div.innerHTML = template;
            const row = div.firstElementChild;
            wrap.appendChild(row);

            if (prefill) {
                row.querySelector('[name$="[description]"]').value = prefill.description ?? '';
                row.querySelector('[name$="[item_type]"]').value = prefill.item_type ?? 'service';
                row.querySelector('[name$="[service_type]"]').value = prefill.service_type ?? '';
                row.querySelector('[name$="[billing_cycle]"]').value = prefill.billing_cycle ?? 'unico';
                row.querySelector('.item-qty').value = prefill.quantity ?? 1;
                row.querySelector('[name$="[unit_label]"]').value = prefill.unit_label ?? '';
                row.querySelector('.item-currency').value = prefill.currency ?? 'ARS';
                row.querySelector('.item-price').value = prefill.unit_price ?? '';
                toggleServiceFields(row.querySelector('[name$="[item_type]"]'));
            }

            itemIndex++;
            recalcTotals();
        }

        function toggleServiceFields(select) {
            const row = select.closest('.quote-item-row');
            const isService = select.value === 'service';
            row.querySelectorAll('.service-only').forEach(el => el.style.display = isService ? '' : 'none');
        }

        function formatMoney(n) {
            return n.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function recalcTotals() {
            const totals = {};

            document.querySelectorAll('.quote-item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
                const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
                const currency = row.querySelector('.item-currency')?.value || 'ARS';
                const subtotal = qty * price;

                const subtotalEl = row.querySelector('.item-subtotal');
                if (subtotalEl) subtotalEl.textContent = subtotal > 0 ? (currency + ' ' + formatMoney(subtotal)) : '';

                totals[currency] = (totals[currency] || 0) + subtotal;
            });

            const wrap = document.getElementById('totalsByCurrency');
            const entries = Object.entries(totals).filter(([, amt]) => amt > 0);

            wrap.innerHTML = entries.length
                ? entries.map(([cur, amt]) => `<span style="margin-right:20px;">Total <strong>${cur}</strong> ${formatMoney(amt)}</span>`).join('')
                : '<span style="color:var(--text-muted);">Agregá ítems para ver el total.</span>';
        }

        const existingItems = @json($existingItemsData);

        document.addEventListener('DOMContentLoaded', () => {
            if (existingItems.length) {
                existingItems.forEach(item => addItemRow(item));
            } else {
                addItemRow();
            }
        });
    </script>

</x-admin-layout>
