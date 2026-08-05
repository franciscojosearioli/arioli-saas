<a href="{{ route('landing.caso-exito', $case) }}" class="client-card">
    @if($case->logo_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($case->logo_path) }}" alt="{{ $case->name }}" class="client-logo">
    @else
        <div class="client-logo client-logo-placeholder">{{ mb_substr($case->name, 0, 1) }}</div>
    @endif

    <div class="client-name">{{ $case->name }}</div>

    @if($case->category)
        <div class="client-category">{{ $case->category }}</div>
    @endif
</a>
