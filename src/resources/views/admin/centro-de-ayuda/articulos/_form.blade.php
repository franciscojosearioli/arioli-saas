<div class="card" style="padding:24px; max-width:720px;">
    <form method="POST" action="{{ $action }}">
        @csrf
        @if($article)
            @method('PATCH')
        @endif

        <div style="display:grid; grid-template-columns:2fr 1fr; gap:12px; margin-bottom:12px;">
            <input type="text" name="title" class="form-input" placeholder="Título" value="{{ $article->title ?? '' }}" required>
            <select name="help_category_id" class="form-select" required>
                <option value="">— Categoría —</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ ($article->help_category_id ?? null) === $c->id ? 'selected' : '' }}>
                        {{ $c->parent_id ? '— ' : '' }}{{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:12px;">
            <label class="form-label">Tipo de contenido</label>
            <select name="content_type" id="contentType" class="form-select">
                @foreach(\App\Enums\HelpArticleContentType::cases() as $t)
                    <option value="{{ $t->value }}" {{ ($article->content_type->value ?? 'articulo') === $t->value ? 'selected' : '' }}>{{ $t->icon() }} {{ $t->label() }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:12px;">
            <label class="form-label">Contenido</label>
            <textarea name="content" class="form-input" rows="10" placeholder="Texto del artículo o novedad...">{{ $article->content ?? '' }}</textarea>
        </div>

        <div id="videoField" style="margin-bottom:12px;">
            <label class="form-label">URL de video</label>
            <input type="text" name="video_url" class="form-input" placeholder="https://youtube.com/..." value="{{ $article->video_url ?? '' }}">
        </div>

        <div id="externalField" style="margin-bottom:12px;">
            <label class="form-label">Enlace externo (PDF u otro link)</label>
            <input type="text" name="external_url" class="form-input" placeholder="https://..." value="{{ $article->external_url ?? '' }}">
        </div>

        <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-secondary); margin-bottom:16px;">
            <input type="checkbox" name="published" value="1" {{ ($article->published ?? true) ? 'checked' : '' }}>
            Publicado (visible en el portal)
        </label>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>

<script>
    function toggleContentFields() {
        const type = document.getElementById('contentType').value;
        document.getElementById('videoField').style.display = type === 'video' ? 'block' : 'none';
        document.getElementById('externalField').style.display = (type === 'pdf' || type === 'enlace') ? 'block' : 'none';
    }
    document.getElementById('contentType').addEventListener('change', toggleContentFields);
    toggleContentFields();
</script>
