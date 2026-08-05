@if(session()->has('notify.message'))
@php
    $type    = session()->get('notify.type', 'success');
    $message = session()->get('notify.message');
    $title   = session()->get('notify.title');
    $timeout = config('notify.timeout', 5000);

    $cssClass = match($type) {
        'error'   => 'f-err',
        'warning' => 'f-war',
        default   => 'f-ok',
    };

    $iconPath = match($type) {
        'error'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
        default   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    };
@endphp

<div id="hc-notify-toast" class="hc-flash {{ $cssClass }}"
     style="position:fixed;top:20px;right:20px;z-index:9999;max-width:380px;min-width:280px;
            box-shadow:0 4px 24px rgba(0,0,0,.13);margin:0;
            animation:hcNotifyIn .25s ease;">

    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
         style="width:16px;height:16px;flex-shrink:0;margin-top:1px;">
        {!! $iconPath !!}
    </svg>

    <div style="flex:1;min-width:0;">
        @if($title)
            <div style="font-weight:700;margin-bottom:3px;font-size:13px;">{{ $title }}</div>
        @endif
        <div style="font-size:13px;">{{ $message }}</div>
    </div>

    <button onclick="document.getElementById('hc-notify-toast').remove()"
            style="background:none;border:none;cursor:pointer;padding:0 0 0 10px;
                   color:inherit;opacity:.55;flex-shrink:0;"
            aria-label="Cerrar">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

<style>
    @@keyframes hcNotifyIn {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    (function() {
        var el = document.getElementById('hc-notify-toast');
        if (!el) return;
        setTimeout(function() {
            el.style.transition = 'opacity .3s ease';
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); }, 320);
        }, {{ $timeout }});
    })();
</script>

{{ session()->forget('notify.message') }}
@endif
