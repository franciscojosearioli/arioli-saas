@extends('admin.messenger.template')

@section('title', $title)

@section('messenger-content')
<style>
    .msg-thread-list { display: flex; flex-direction: column; gap: 2px; }

    .msg-thread-row {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 12px; border-radius: 10px;
        border: 1px solid transparent; transition: background .12s;
    }
    .msg-thread-row:hover { background: var(--body-bg, #f8fafc); border-color: var(--card-border, #e8edf2); }
    html.dark .msg-thread-row:hover { background: #0f172a; }

    .msg-avatar {
        width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700; color: #fff;
        background: var(--accent, #1d4ed8);
    }
    .msg-avatar.read { background: var(--text-muted, #94a3b8); }

    .msg-thread-body { flex: 1; min-width: 0; }
    .msg-thread-top {
        display: flex; align-items: baseline; justify-content: space-between;
        gap: 8px; margin-bottom: 2px;
    }
    .msg-thread-name {
        font-size: 13px; font-weight: 600;
        color: var(--text-primary, #0f172a);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        text-decoration: none;
    }
    .msg-thread-name:hover { text-decoration: none; color: var(--accent, #1d4ed8); }
    .msg-thread-name.read { font-weight: 500; color: var(--text-secondary, #64748b); }
    .msg-thread-time { font-size: 11px; color: var(--text-muted, #94a3b8); white-space: nowrap; flex-shrink: 0; }
    .msg-thread-subject {
        font-size: 12px; color: var(--text-secondary, #64748b);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        text-decoration: none; display: block;
    }
    .msg-thread-subject.unread { font-weight: 600; color: var(--text-primary, #0f172a); }

    .msg-unread-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--accent, #1d4ed8); flex-shrink: 0;
    }

    .msg-del-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border-radius: 8px;
        background: transparent; border: 1px solid transparent;
        color: var(--text-muted, #94a3b8); cursor: pointer;
        transition: all .12s; flex-shrink: 0; padding: 0;
    }
    .msg-del-btn:hover { background: #fff1f2; border-color: #fecaca; color: #e11d48; }
    .msg-del-btn svg { width: 13px; height: 13px; }

    .msg-empty {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 60px 20px;
        color: var(--text-muted, #94a3b8); text-align: center; gap: 12px;
    }
    .msg-empty svg { width: 40px; height: 40px; }
    .msg-empty p { font-size: 13px; margin: 0; }
</style>

<div class="msg-thread-list">
    @forelse($topics as $topic)
        @php($other = $topic->receiverOrCreator())
        @php($unread = $topic->hasUnreads())
        <div class="msg-thread-row">

            <a href="{{ route('admin.messenger.showMessages', $topic->id) }}"
               style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;text-decoration:none;">

                <div class="msg-avatar {{ !$unread ? 'read' : '' }}">
                    {{ $other ? strtoupper(substr($other->email, 0, 1)) : '?' }}
                </div>

                <div class="msg-thread-body">
                    <div class="msg-thread-top">
                        <span class="msg-thread-name {{ !$unread ? 'read' : '' }}">
                            {{ $other ? $other->email : '(usuario eliminado)' }}
                        </span>
                        <span class="msg-thread-time">{{ $topic->created_at->diffForHumans() }}</span>
                    </div>
                    <span class="msg-thread-subject {{ $unread ? 'unread' : '' }}">
                        {{ $topic->subject }}
                    </span>
                </div>

                @if($unread)
                    <div class="msg-unread-dot"></div>
                @endif

            </a>

            <form action="{{ route('admin.messenger.destroyTopic', $topic->id) }}" method="POST"
                  onsubmit="return confirm('{{ trans('global.areYouSure') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="msg-del-btn" title="{{ trans('global.delete') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>

        </div>
    @empty
        <div class="msg-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p>{{ trans('global.you_have_no_messages') }}</p>
            <a href="{{ route('admin.messenger.createTopic') }}" class="msg-btn-new">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo mensaje
            </a>
        </div>
    @endforelse
</div>
@endsection
