<x-cliente-layout title="Centro de Ayuda">

    <div class="page-header">
        <div>
            <h1 class="page-title">Centro de Ayuda</h1>
            <p class="page-subtitle">Guías y novedades sobre tus servicios con Arioli.dev</p>
        </div>
    </div>

    <form method="GET" action="{{ route('cliente.help.index') }}" style="margin-bottom:24px;">
        <div class="search-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="q" class="search-input" placeholder="Buscar en el Centro de Ayuda..." value="{{ $query }}">
        </div>
    </form>

    @if($results !== null)
        <div class="card">
            <div class="card-body">
                <div class="card-title" style="margin-bottom:16px;">Resultados para "{{ $query }}"</div>
                @forelse($results as $article)
                    <a href="{{ route('cliente.help.show', $article) }}" style="display:block; text-decoration:none; padding:10px 0; border-bottom:1px solid var(--card-border);">
                        <div style="font-weight:600; color:var(--text-primary); font-size:13.5px;">{{ $article->content_type->icon() }} {{ $article->title }}</div>
                        <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">{{ $article->category->name }}</div>
                    </a>
                @empty
                    <p style="color:var(--text-secondary); font-size:13px;">No encontramos nada con esa búsqueda.</p>
                @endforelse
            </div>
        </div>
    @else
        @forelse($categories as $category)
            @if($category->articles->isNotEmpty() || $category->children->isNotEmpty())
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-body">
                        <div class="card-title" style="margin-bottom:16px;">{{ $category->icon }} {{ $category->name }}</div>
                        @foreach($category->articles as $article)
                            <a href="{{ route('cliente.help.show', $article) }}" style="display:block; text-decoration:none; padding:8px 0; border-bottom:1px solid var(--card-border);">
                                <span style="font-size:13px; color:var(--text-primary);">{{ $article->content_type->icon() }} {{ $article->title }}</span>
                            </a>
                        @endforeach

                        @foreach($category->children as $child)
                            @if($child->articles->isNotEmpty())
                                <div style="margin-top:12px; padding-left:12px; border-left:2px solid var(--card-border);">
                                    <div style="font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:6px;">{{ $child->icon }} {{ $child->name }}</div>
                                    @foreach($child->articles as $article)
                                        <a href="{{ route('cliente.help.show', $article) }}" style="display:block; text-decoration:none; padding:6px 0;">
                                            <span style="font-size:12.5px; color:var(--text-primary);">{{ $article->content_type->icon() }} {{ $article->title }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <p style="color:var(--text-secondary); font-size:13px;">Todavía no hay artículos publicados.</p>
        @endforelse
    @endif

</x-cliente-layout>
