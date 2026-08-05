{{-- Requiere: $credentialable (modelo con relación credentials cargada), $credentialableType (string: client|domain|hosting|project) --}}
@php
    $ownerColors = [
        'cliente' => ['bg' => '#d1fae5', 'fg' => '#065f46'],
        'arioli' => ['bg' => '#e0e7ff', 'fg' => '#3730a3'],
        'proveedor_externo' => ['bg' => '#fef3c7', 'fg' => '#92400e'],
    ];
@endphp
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Credenciales</h3>
        <x-admin.modal id="add-credential-{{ $credentialableType }}-{{ $credentialable->id }}" title="Agregar credencial" trigger-label="+ Agregar credencial" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
            <form method="POST" action="{{ route('credentials.store') }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                @csrf
                <input type="hidden" name="credentialable_type" value="{{ $credentialableType }}">
                <input type="hidden" name="credentialable_id" value="{{ $credentialable->id }}">
                <select name="type" class="form-select">
                    @foreach(\App\Enums\CredentialType::cases() as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
                <select name="owner" class="form-select">
                    @foreach(\App\Enums\CredentialOwner::cases() as $o)
                        <option value="{{ $o->value }}">{{ $o->label() }}</option>
                    @endforeach
                </select>
                <input type="text" name="label" class="form-input" placeholder="Etiqueta (opcional)">
                <input type="text" name="username" class="form-input" placeholder="Usuario">
                <input type="text" name="url" class="form-input" style="grid-column:span 2;" placeholder="URL de acceso (opcional)">
                <input type="password" name="secret" class="form-input" style="grid-column:span 2;" placeholder="Contraseña / secreto" required>
                <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Agregar credencial</button>
            </form>
        </x-admin.modal>
    </div>

    @forelse($credentialable->credentials as $credential)
        @php $oc = $ownerColors[$credential->owner->value] ?? $ownerColors['cliente']; @endphp
        <details style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <summary style="cursor:pointer; display:flex; justify-content:space-between; align-items:center; list-style:none;">
                <div>
                    <span style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $credential->type->label() }}</span>
                    @if($credential->label) <span style="font-size:11.5px; color:var(--text-muted);">— {{ $credential->label }}</span> @endif
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $oc['bg'] }}; color:{{ $oc['fg'] }};">{{ $credential->owner->label() }}</span>
                    <span style="font-size:10.5px; color:var(--text-muted);">
                        {{ $credential->last_verified_at ? 'Verificada ' . $credential->last_verified_at->format('d/m/Y') : 'Sin verificar' }}
                    </span>
                </div>
            </summary>

            <div style="margin-top:12px; padding-left:4px;">
                <div style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">
                    @if($credential->username) Usuario: <strong>{{ $credential->username }}</strong><br> @endif
                    @if($credential->url) URL: <a href="{{ $credential->url }}" target="_blank">{{ $credential->url }}</a><br> @endif
                    Contraseña:
                    <code data-secret-value>••••••••</code>
                    <button type="button" class="reveal-secret-btn" data-reveal-url="{{ route('credentials.reveal', $credential) }}" style="border:none; background:none; color:var(--accent); cursor:pointer; font-size:11px; text-decoration:underline;">Ver</button>
                </div>
                <div style="display:flex; gap:6px; margin-bottom:10px;">
                    <form method="POST" action="{{ route('credentials.mark-verified', $credential) }}">
                        @csrf
                        <button class="btn btn-secondary" style="padding:4px 10px; font-size:11px;">Marcar verificada</button>
                    </form>
                    <form method="POST" action="{{ route('credentials.destroy', $credential) }}" onsubmit="return confirm('¿Eliminar esta credencial?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-secondary" style="padding:4px 10px; font-size:11px; color:#dc2626;">Eliminar</button>
                    </form>
                </div>

                <x-admin.modal id="edit-credential-{{ $credential->id }}" title="Editar credencial" trigger-label="✎ Editar">
                    <form method="POST" action="{{ route('credentials.update', $credential) }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                        @csrf @method('PATCH')
                        <select name="type" class="form-select">
                            @foreach(\App\Enums\CredentialType::cases() as $t)
                                <option value="{{ $t->value }}" {{ $credential->type === $t ? 'selected' : '' }}>{{ $t->label() }}</option>
                            @endforeach
                        </select>
                        <select name="owner" class="form-select">
                            @foreach(\App\Enums\CredentialOwner::cases() as $o)
                                <option value="{{ $o->value }}" {{ $credential->owner === $o ? 'selected' : '' }}>{{ $o->label() }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="label" class="form-input" value="{{ $credential->label }}" placeholder="Etiqueta">
                        <input type="text" name="username" class="form-input" value="{{ $credential->username }}" placeholder="Usuario">
                        <input type="text" name="url" class="form-input" style="grid-column:span 2;" value="{{ $credential->url }}" placeholder="URL de acceso">
                        <input type="password" name="secret" class="form-input" style="grid-column:span 2;" placeholder="Dejar en blanco para no cambiar la contraseña">
                        <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Guardar cambios</button>
                    </form>
                </x-admin.modal>
            </div>
        </details>
    @empty
        <p style="font-size:12.5px; color:var(--text-muted);">Sin credenciales.</p>
    @endforelse
</div>

@once
    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.reveal-secret-btn');
            if (!btn) return;

            const code = btn.previousElementSibling;
            if (btn.dataset.revealed === '1') return;

            fetch(btn.dataset.revealUrl, { headers: { 'Accept': 'application/json' } })
                .then((r) => r.json())
                .then((data) => {
                    code.textContent = data.secret;
                    btn.dataset.revealed = '1';
                    btn.textContent = 'Ocultar en 15s';
                    setTimeout(() => {
                        code.textContent = '••••••••';
                        btn.dataset.revealed = '0';
                        btn.textContent = 'Ver';
                    }, 15000);
                });
        });
    </script>
@endonce
