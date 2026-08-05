<x-admin-layout title="Nueva Plantilla">

    <div style="margin-bottom:24px;">
        <a href="{{ route('legales.plantillas.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Plantillas</a>
    </div>

    <div class="card" style="max-width:700px; padding:28px;">
        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:24px;">Nueva Plantilla</h2>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px;">
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('legales.plantillas.store') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Contrato de licencia estándar">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select">
                    @foreach(\App\Enums\ContractType::cases() as $t)
                        <option value="{{ $t->value }}" {{ old('type') === $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:8px;">
                <label class="form-label">Contenido</label>
                <textarea name="content" class="form-input" rows="14">{{ old('content') }}</textarea>
            </div>

            <div style="margin-bottom:16px; font-size:12px; color:var(--text-muted);">
                Placeholders disponibles (escribilos entre doble llave, ej. dos llaves + nombre + dos llaves):
                @foreach($availablePlaceholders as $key)
                    <code style="background:#f3f4f6; padding:2px 6px; border-radius:4px; margin-right:4px;">{{ $key }}</code>
                @endforeach
            </div>

            <div style="margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="active" id="active" value="1" checked style="width:16px; height:16px; accent-color:var(--accent);">
                <label for="active" class="form-label" style="margin:0;">Plantilla activa</label>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Crear plantilla</button>
                <a href="{{ route('legales.plantillas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</x-admin-layout>
