<x-admin-layout title="Nuevo artículo — Centro de Ayuda">

    <div style="margin-bottom:24px;">
        <a href="{{ route('centro-de-ayuda.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Centro de Ayuda</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0 0 20px;">Nuevo artículo</h1>

    @include('admin.centro-de-ayuda.articulos._form', [
        'action' => route('centro-de-ayuda.articulos.store'),
        'article' => null,
        'categories' => $categories,
    ])

</x-admin-layout>
