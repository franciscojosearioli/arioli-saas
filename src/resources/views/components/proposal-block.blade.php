@props(['text'])
@php
    $paragraphs = $text ? preg_split('/\n\s*\n/', trim($text)) : [];
@endphp
@foreach($paragraphs as $paragraph)
    @php
        $lines = array_values(array_filter(array_map('trim', explode("\n", $paragraph)), fn ($l) => $l !== ''));
    @endphp
    @if(count($lines) === 1)
        <p>{{ $lines[0] }}</p>
    @elseif(count($lines) > 1)
        <ul>
            @foreach($lines as $line)
                <li>{{ $line }}</li>
            @endforeach
        </ul>
    @endif
@endforeach
