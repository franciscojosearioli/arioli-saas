<x-admin-layout title="Editar Licencia">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Editar Licencia #{{ $license->id }}</h1>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">{{ $license->tenant_id }}</p>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('licenses.show', $license) }}" class="btn" style="background:var(--bg-secondary); color:var(--text-primary); border:1px solid var(--border);">
                Ver detalle
            </a>
            <a href="{{ route('licenses.index') }}" class="btn" style="background:var(--bg-secondary); color:var(--text-primary); border:1px solid var(--border);">
                Cancelar
            </a>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="alert" style="background:#fee2e2; border-left:4px solid var(--danger); color:#991b1b; margin-bottom:20px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li style="font-size:13px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('licenses.update', $license) }}">
        @csrf
        @method('PUT')

        <div class="card" style="padding:28px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-muted); margin-bottom:6px;">
                        Tenant (no editable)
                    </label>
                    <input type="text" value="{{ $license->tenant_id }}" disabled
                        style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px; color:var(--text-muted); background:var(--bg-secondary); box-sizing:border-box;">
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Plan <span style="color:var(--danger);">*</span>
                    </label>
                    <select name="plan_id" id="plan_id" required onchange="toggleExpiresAt()"
                        style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px; color:var(--text-primary); background:var(--bg); outline:none;">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" data-perpetual="{{ $plan->is_perpetual ? '1' : '0' }}" {{ old('plan_id', $license->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Fecha de inicio <span style="color:var(--danger);">*</span>
                    </label>
                    <input type="date" name="starts_at"
                        value="{{ old('starts_at', $license->starts_at ? \Carbon\Carbon::parse($license->starts_at)->format('Y-m-d') : '') }}"
                        required
                        style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px; color:var(--text-primary); background:var(--bg); outline:none; box-sizing:border-box;">
                </div>

                <div id="expiresAtWrap">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;" id="expiresAtLabel">
                        Fecha de vencimiento <span style="color:var(--danger);">*</span>
                    </label>
                    <input type="date" name="expires_at" id="expires_at"
                        value="{{ old('expires_at', $license->expires_at ? \Carbon\Carbon::parse($license->expires_at)->format('Y-m-d') : '') }}"
                        required
                        style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px; color:var(--text-primary); background:var(--bg); outline:none; box-sizing:border-box;">
                    <p style="font-size:12px; color:var(--text-muted); margin-top:4px;" id="expiresAtHelp"></p>
                </div>

                <script>
                    function toggleExpiresAt() {
                        const select = document.getElementById('plan_id');
                        const opt = select.options[select.selectedIndex];
                        const isPerpetual = opt?.dataset.perpetual === '1';
                        const input = document.getElementById('expires_at');
                        const label = document.getElementById('expiresAtLabel');
                        const help = document.getElementById('expiresAtHelp');

                        input.required = !isPerpetual;
                        input.disabled = isPerpetual;
                        label.innerHTML = isPerpetual ? 'Fecha de vencimiento' : 'Fecha de vencimiento <span style="color:var(--danger);">*</span>';
                        help.textContent = isPerpetual ? 'No aplica — es una licencia indefinida, sin vencimiento.' : '';
                    }
                    document.addEventListener('DOMContentLoaded', toggleExpiresAt);
                </script>

                @if($license->installed_version)
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-muted); margin-bottom:6px;">
                            Versión instalada
                        </label>
                        <input type="text" value="{{ $license->installed_version }}" disabled
                            style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px; color:var(--text-muted); background:var(--bg-secondary); box-sizing:border-box;">
                    </div>
                @endif

                <div style="grid-column:1/-1;">
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" {{ old('active', $license->active) ? 'checked' : '' }}
                            style="width:16px; height:16px; accent-color:var(--accent);">
                        <span style="font-size:13px; font-weight:600; color:var(--text-primary);">Licencia activa</span>
                    </label>
                </div>

            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:24px; padding-top:20px; border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </div>

    </form>

</x-admin-layout>
