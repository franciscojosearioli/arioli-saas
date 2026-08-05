<x-cliente-layout title="{{ $article->title }}">

    <div style="margin-bottom:20px;">
        <a href="{{ route('cliente.help.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Centro de Ayuda</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $article->content_type->icon() }} {{ $article->title }}</h1>
            <p class="page-subtitle">{{ $article->category->name }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($article->content_type->value === 'video' && $article->video_url)
                <p style="margin-bottom:16px;"><a href="{{ $article->video_url }}" target="_blank" class="btn btn-primary">▶ Ver video</a></p>
            @endif

            @if(in_array($article->content_type->value, ['pdf', 'enlace']) && $article->external_url)
                <p style="margin-bottom:16px;"><a href="{{ $article->external_url }}" target="_blank" class="btn btn-primary">Abrir {{ $article->content_type->label() }}</a></p>
            @endif

            @if($article->content)
                <div style="font-size:14px; color:var(--text-primary); line-height:1.7; white-space:pre-line;">{{ $article->content }}</div>
            @endif
        </div>
    </div>

</x-cliente-layout>
