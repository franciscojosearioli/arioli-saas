{{-- Contactos --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Contactos</h3>
        <x-admin.modal id="add-contact" title="Agregar contacto" trigger-label="+ Agregar" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
            <form method="POST" action="{{ route('clients.contacts.store', $client) }}">
                @csrf
                <input type="text" name="name" class="form-input" placeholder="Nombre" style="margin-bottom:8px;" required>
                <input type="email" name="email" class="form-input" placeholder="Email" style="margin-bottom:8px;">
                <select name="role" class="form-select" style="margin-bottom:12px;">
                    @foreach(\App\Enums\ContactRole::cases() as $r)
                        <option value="{{ $r->value }}">{{ $r->label() }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary" style="width:100%;">Agregar contacto</button>
            </form>
        </x-admin.modal>
    </div>
    @foreach($client->contacts as $contact)
        <div style="display:flex; justify-content:space-between; align-items:start; padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div>
                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $contact->name }} @if($contact->is_primary) <span style="font-size:10px; color:var(--accent);">★</span> @endif</div>
                <div style="font-size:11.5px; color:var(--text-muted);">{{ $contact->role->label() }} — {{ $contact->email }} {{ $contact->phone }}</div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <x-admin.modal id="edit-contact-{{ $contact->id }}" title="Editar contacto" trigger-label="✎" trigger-style="font-size:12px;">
                    <form method="POST" action="{{ route('clients.contacts.update', [$client, $contact]) }}">
                        @csrf @method('PATCH')
                        <input type="text" name="name" class="form-input" placeholder="Nombre" style="margin-bottom:8px;" value="{{ $contact->name }}" required>
                        <input type="email" name="email" class="form-input" placeholder="Email" style="margin-bottom:8px;" value="{{ $contact->email }}">
                        <input type="text" name="phone" class="form-input" placeholder="Teléfono" style="margin-bottom:8px;" value="{{ $contact->phone }}">
                        <select name="role" class="form-select" style="margin-bottom:8px;">
                            @foreach(\App\Enums\ContactRole::cases() as $r)
                                <option value="{{ $r->value }}" {{ $contact->role === $r ? 'selected' : '' }}>{{ $r->label() }}</option>
                            @endforeach
                        </select>
                        <label style="font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:5px; margin-bottom:12px;">
                            <input type="checkbox" name="is_primary" value="1" {{ $contact->is_primary ? 'checked' : '' }}> Contacto principal
                        </label>
                        <button type="submit" class="btn btn-primary" style="width:100%;">Guardar cambios</button>
                    </form>
                </x-admin.modal>
                <form method="POST" action="{{ route('clients.contacts.destroy', [$client, $contact]) }}" onsubmit="return confirm('¿Eliminar contacto?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-secondary" style="padding:2px 8px; font-size:11px;">×</button>
                </form>
            </div>
        </div>
    @endforeach

    <div style="margin-top:16px; padding-top:16px; border-top:1px solid #f3f4f6;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <h4 style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin:0;">Acceso al Portal</h4>
            <x-admin.modal id="add-portal-user" title="Dar acceso al portal" trigger-label="{{ $client->portalUsers->isEmpty() ? '+ Crear acceso' : '+ Agregar usuario' }}" trigger-class="btn btn-secondary" trigger-style="font-size:11px; padding:4px 8px;">
                <form method="POST" action="{{ route('clients.portal-user.store', $client) }}">
                    @csrf
                    @php $primaryContact = $client->contacts->firstWhere('is_primary', true) ?? $client->contacts->first(); @endphp
                    <input type="text" name="name" class="form-input" style="margin-bottom:8px;" placeholder="Nombre" value="{{ $client->portalUsers->isEmpty() ? ($primaryContact->name ?? '') : '' }}" required>
                    <input type="email" name="email" class="form-input" style="margin-bottom:12px;" placeholder="Email" value="{{ $client->portalUsers->isEmpty() ? ($primaryContact->email ?? '') : '' }}" required>
                    <button type="submit" class="btn btn-primary" style="width:100%;">{{ $client->portalUsers->isEmpty() ? 'Crear acceso al portal' : 'Agregar usuario' }}</button>
                </form>
            </x-admin.modal>
        </div>
        @foreach($client->portalUsers as $portalUser)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:6px 0; font-size:12.5px;">
                <div>
                    <div style="color:var(--text-primary); font-weight:600;">{{ $portalUser->name }}</div>
                    <div style="color:var(--text-muted); font-size:11px;">{{ $portalUser->email }}</div>
                </div>
                <div style="display:flex; gap:6px;">
                    <form method="POST" action="{{ route('clients.portal-user.resend', [$client, $portalUser]) }}">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="font-size:11px; padding:4px 8px;">Reenviar acceso</button>
                    </form>
                    <form method="POST" action="{{ route('clients.portal-user.destroy', [$client, $portalUser]) }}" onsubmit="return confirm('¿Quitar el acceso al portal de {{ $portalUser->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-secondary" style="font-size:11px; padding:4px 8px; color:#dc2626;">Quitar acceso</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
