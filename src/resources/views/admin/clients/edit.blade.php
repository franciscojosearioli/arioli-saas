<x-admin-layout title="Editar Cliente">

    <div style="margin-bottom:24px;">
        <a href="{{ route('clients.show', $client) }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a {{ $client->name }}</a>
    </div>

    <div class="card" style="max-width:640px; padding:28px;">
        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:24px;">Editar Cliente</h2>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('clients.update', $client) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div style="margin-bottom:16px;">
                <label class="form-label">Nombre</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $client->name) }}">
            </div>

            <div class="card" style="background:var(--body-bg); padding:20px; margin-bottom:24px;">
                <h3 style="font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:16px;">Caso de éxito (Arioli.dev)</h3>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div>
                        <label class="form-label">Logo</label>
                        @if($client->logo_path)
                            <div style="margin-bottom:8px;"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($client->logo_path) }}" alt="Logo actual" style="height:48px; border-radius:6px;"></div>
                        @endif
                        <input type="file" name="logo" class="form-input" accept="image/*">
                    </div>
                    <div>
                        <label class="form-label">Imagen de portada (banner del caso)</label>
                        @if($client->cover_image)
                            <div style="margin-bottom:8px;"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($client->cover_image) }}" alt="Portada actual" style="height:48px; border-radius:6px;"></div>
                        @endif
                        <input type="file" name="cover_image_file" class="form-input" accept="image/*">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div>
                        <label class="form-label">Rubro / categoría</label>
                        <input type="text" name="category" class="form-input" value="{{ old('category', $client->category) }}" placeholder="Salud, Construcción, Comercio...">
                    </div>
                    <div>
                        <label class="form-label">Orden (menor = primero)</label>
                        <input type="number" name="display_order" class="form-input" value="{{ old('display_order', $client->display_order) }}">
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Descripción de la empresa</label>
                    <textarea name="short_description" class="form-input" rows="2">{{ old('short_description', $client->short_description) }}</textarea>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">¿Cuál era el problema?</label>
                    <textarea name="challenge" class="form-input" rows="2">{{ old('challenge', $client->challenge) }}</textarea>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">¿Qué solución desarrollamos?</label>
                    <textarea name="solution" class="form-input" rows="2">{{ old('solution', $client->solution) }}</textarea>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">¿Qué resultados obtuvo?</label>
                    <textarea name="results" class="form-input" rows="2">{{ old('results', $client->results) }}</textarea>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Testimonio (opcional)</label>
                    <textarea name="testimonial_quote" class="form-input" rows="2" placeholder="Frase textual del cliente...">{{ old('testimonial_quote', $client->testimonial_quote) }}</textarea>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:8px;">
                        <input type="text" name="testimonial_author" class="form-input" value="{{ old('testimonial_author', $client->testimonial_author) }}" placeholder="Nombre de quien lo dice">
                        <input type="text" name="testimonial_position" class="form-input" value="{{ old('testimonial_position', $client->testimonial_position) }}" placeholder="Cargo / institución">
                    </div>
                </div>

                <label style="display:flex; align-items:center; gap:8px; font-size:14px; color:var(--text-primary);">
                    <input type="checkbox" name="show_on_landing" value="1" {{ old('show_on_landing', $client->show_on_landing) ? 'checked' : '' }}>
                    Mostrar en la página principal de Arioli.dev
                </label>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label class="form-label">CUIT</label>
                    <input type="text" name="cuit" class="form-input" value="{{ old('cuit', $client->cuit) }}" placeholder="20-12345678-9">
                </div>
                <div>
                    <label class="form-label">Condición frente al IVA</label>
                    <input type="text" name="condicion_iva" class="form-input" value="{{ old('condicion_iva', $client->condicion_iva) }}" placeholder="Responsable Inscripto, Monotributista, Consumidor Final...">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label class="form-label">Estado comercial</label>
                    <select name="commercial_status" class="form-select">
                        @foreach(\App\Enums\CommercialStatus::cases() as $s)
                            <option value="{{ $s->value }}" {{ old('commercial_status', $client->commercial_status->value) === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Prioridad</label>
                    <select name="priority" class="form-select">
                        @foreach(\App\Enums\Priority::cases() as $p)
                            <option value="{{ $p->value }}" {{ old('priority', $client->priority->value) === $p->value ? 'selected' : '' }}>{{ $p->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</x-admin-layout>
