<x-admin-layout title="Nuevo Cliente">

    <div style="margin-bottom:24px;">
        <a href="{{ route('clients.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Clientes</a>
    </div>

    <div class="card" style="max-width:640px; padding:28px;">
        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">Nuevo Cliente</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:24px;">
            Para clientes de mantenimiento, sitios web, tiendas online, etc. — sin licencia de ningún sistema.
            Los clientes que sí contratan una licencia se crean automáticamente al dar de alta el Tenant.
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

        <form method="POST" action="{{ route('clients.store') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Municipalidad de..., Ferretería López...">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label class="form-label">Estado comercial</label>
                    <select name="commercial_status" class="form-select">
                        @foreach(\App\Enums\CommercialStatus::cases() as $s)
                            <option value="{{ $s->value }}" {{ old('commercial_status', 'prospecto') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Prioridad</label>
                    <select name="priority" class="form-select">
                        @foreach(\App\Enums\Priority::cases() as $p)
                            <option value="{{ $p->value }}" {{ old('priority', 'media') === $p->value ? 'selected' : '' }}>{{ $p->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="border-top:1px solid #f3f4f6; padding-top:16px; margin-bottom:16px;">
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:12px;">
                    Contacto principal (opcional)
                </div>
                <div style="margin-bottom:12px;">
                    <input type="text" name="contact_name" class="form-input" value="{{ old('contact_name') }}" placeholder="Nombre">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <input type="email" name="contact_email" class="form-input" value="{{ old('contact_email') }}" placeholder="Email">
                    <input type="text" name="contact_phone" class="form-input" value="{{ old('contact_phone') }}" placeholder="Teléfono">
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Crear cliente</button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</x-admin-layout>
