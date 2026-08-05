<x-admin-layout title="Centro de Ayuda">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0;">Centro de Ayuda</h1>
        <a href="{{ route('centro-de-ayuda.articulos.create') }}" class="btn btn-primary">+ Nuevo artículo</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <div style="display:grid; grid-template-columns:1.4fr 1fr; gap:20px; align-items:start;">

        <div>
            @forelse($categories as $category)
                <div class="card" style="padding:24px; margin-bottom:20px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">
                            {{ $category->icon }} {{ $category->name }}
                        </h3>
                        <form method="POST" action="{{ route('centro-de-ayuda.categorias.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta categoría y sus subcategorías/artículos?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px; color:#dc2626;">Eliminar</button>
                        </form>
                    </div>

                    @foreach($category->articles as $article)
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #f3f4f6; font-size:12.5px;">
                            <div>
                                {{ $article->content_type->icon() }} {{ $article->title }}
                                @unless($article->published)
                                    <span class="badge badge-gray" style="margin-left:6px;">Borrador</span>
                                @endunless
                            </div>
                            <a href="{{ route('centro-de-ayuda.articulos.edit', $article) }}" style="font-size:11.5px; color:var(--accent); text-decoration:none;">Editar</a>
                        </div>
                    @endforeach

                    @foreach($category->children as $child)
                        <div style="margin-top:14px; padding-left:14px; border-left:2px solid #f3f4f6;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                                <div style="font-size:12.5px; font-weight:600; color:var(--text-primary);">{{ $child->icon }} {{ $child->name }}</div>
                                <form method="POST" action="{{ route('centro-de-ayuda.categorias.destroy', $child) }}" onsubmit="return confirm('¿Eliminar esta subcategoría?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-secondary" style="padding:2px 6px; font-size:10.5px; color:#dc2626;">×</button>
                                </form>
                            </div>
                            @foreach($child->articles as $article)
                                <div style="display:flex; justify-content:space-between; align-items:center; padding:6px 0; font-size:12px;">
                                    <div>
                                        {{ $article->content_type->icon() }} {{ $article->title }}
                                        @unless($article->published)
                                            <span class="badge badge-gray" style="margin-left:6px;">Borrador</span>
                                        @endunless
                                    </div>
                                    <a href="{{ route('centro-de-ayuda.articulos.edit', $article) }}" style="font-size:11px; color:var(--accent); text-decoration:none;">Editar</a>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @empty
                <p style="color:var(--text-muted); font-size:13px;">Sin categorías todavía — creá la primera al lado.</p>
            @endforelse
        </div>

        <div>
            <div class="card" style="padding:24px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Nueva categoría</h3>
                <form method="POST" action="{{ route('centro-de-ayuda.categorias.store') }}">
                    @csrf
                    <input type="text" name="name" class="form-input" placeholder="Nombre (ej. Hosting)" style="margin-bottom:8px;" required>
                    <input type="text" name="icon" class="form-input" placeholder="Ícono (emoji, opcional)" style="margin-bottom:8px;" maxlength="10">
                    <select name="parent_id" class="form-select" style="margin-bottom:12px;">
                        <option value="">— Categoría principal —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} (como subcategoría)</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary" style="width:100%;">Crear categoría</button>
                </form>
            </div>
        </div>

    </div>

</x-admin-layout>
