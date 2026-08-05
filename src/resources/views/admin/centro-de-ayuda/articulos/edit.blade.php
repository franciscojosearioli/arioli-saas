<x-admin-layout title="{{ $article->title }} — Centro de Ayuda">

    <div style="margin-bottom:24px;">
        <a href="{{ route('centro-de-ayuda.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Centro de Ayuda</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0;">{{ $article->title }}</h1>
        <form method="POST" action="{{ route('centro-de-ayuda.articulos.destroy', $article) }}" onsubmit="return confirm('¿Eliminar este artículo?')">
            @csrf @method('DELETE')
            <button class="btn btn-secondary" style="color:#dc2626;">Eliminar artículo</button>
        </form>
    </div>

    @include('admin.centro-de-ayuda.articulos._form', [
        'action' => route('centro-de-ayuda.articulos.update', $article),
        'article' => $article,
        'categories' => $categories,
    ])

</x-admin-layout>
