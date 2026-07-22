@extends('panel.messenger.template')

@section('title', $topic->subject)

@section('msg-head-action')
@if($topic->receiverOrCreator() !== null && !$topic->receiverOrCreator()->trashed())
    <a href="{{ route('panel.messenger.showReply', $topic->id) }}" class="msg-btn-new">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
        </svg>
        {{ trans('global.reply') }}
    </a>
@endif
@endsection

@section('messenger-content')
<style>
    .chat-thread { display: flex; flex-direction: column; gap: 14px; }

    .chat-row { display: flex; align-items: flex-end; gap: 8px; }
    .chat-row.mine { flex-direction: row-reverse; }

    .chat-avatar {
        width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: #fff;
        background: var(--text-muted, #94a3b8);
    }
    .chat-row.mine .chat-avatar { background: var(--accent, #1d4ed8); }

    .chat-bubble-wrap { max-width: 70%; display: flex; flex-direction: column; gap: 4px; }
    .chat-row.mine .chat-bubble-wrap { align-items: flex-end; }

    .chat-bubble {
        padding: 10px 14px; border-radius: 14px;
        font-size: 13px; line-height: 1.55;
        color: var(--text-primary, #0f172a);
        background: var(--body-bg, #f1f5f9);
        border-bottom-left-radius: 4px;
        word-break: break-word;
    }
    html.dark .chat-bubble { background: #1e293b; }
    .chat-row.mine .chat-bubble {
        background: var(--accent, #1d4ed8); color: #fff;
        border-bottom-left-radius: 14px; border-bottom-right-radius: 4px;
    }

    .chat-meta {
        font-size: 10px; color: var(--text-muted, #94a3b8);
        padding: 0 4px;
    }

    .chat-divider {
        display: flex; align-items: center; gap: 12px;
        color: var(--text-muted, #94a3b8); font-size: 11px; margin: 6px 0;
    }
    .chat-divider::before, .chat-divider::after {
        content: ''; flex: 1; height: 1px; background: var(--card-border, #e8edf2);
    }
</style>

<div class="chat-thread" id="chatThread">
    @foreach($topic->messages->reverse() as $message)
        @php($isMine = $message->sender_id === Auth::id())
        <div class="chat-row {{ $isMine ? 'mine' : '' }}">

            <div class="chat-avatar">
                {{ strtoupper(substr(optional($message->sender)->name ?? '?', 0, 1)) }}
            </div>

            <div class="chat-bubble-wrap">
                <div class="chat-bubble">{{ $message->content }}</div>
                <span class="chat-meta">
                    {{ optional($message->sender)->name ?? '—' }}
                    &middot;
                    {{ $message->created_at->diffForHumans() }}
                </span>
            </div>

        </div>
    @endforeach
</div>

<script>
    // scroll to bottom of chat content area on load
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.querySelector('.msg-content');
        if (el) el.scrollTop = el.scrollHeight;
    });
</script>
@endsection
