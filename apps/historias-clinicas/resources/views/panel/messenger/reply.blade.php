@extends('panel.messenger.template')

@section('title', trans('global.reply') . ': ' . $topic->subject)

@section('messenger-content')
<style>
    .reply-context {
        background: var(--body-bg, #f8fafc);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: 10px; padding: 10px 14px; margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px;
    }
    html.dark .reply-context { background: #0f172a; }
    .reply-context svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); flex-shrink: 0; }
    .reply-context-text { font-size: 12px; color: var(--text-secondary, #64748b); }
    .reply-context-text strong { color: var(--text-primary, #0f172a); }

    .msg-form { display: flex; flex-direction: column; gap: 18px; max-width: 680px; }
    .msg-field { display: flex; flex-direction: column; gap: 5px; }
    .msg-label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .06em; color: var(--text-muted, #94a3b8);
    }
    .msg-textarea {
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: 8px; padding: 9px 12px;
        font-size: 13px; color: var(--text-primary, #0f172a);
        background: var(--body-bg, #f8fafc);
        font-family: var(--font-sans, inherit);
        width: 100%; outline: none; transition: border-color .15s;
        resize: vertical; min-height: 120px; line-height: 1.6;
    }
    .msg-textarea:focus {
        border-color: var(--accent, #1d4ed8);
        box-shadow: 0 0 0 3px rgba(29,78,216,.08);
    }
    html.dark .msg-textarea { background: #0f172a; color: #f1f5f9; border-color: #1e293b; }

    .msg-form-actions { display: flex; align-items: center; justify-content: flex-end; gap: 10px; }

    .msg-btn-cancel {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px;
        border: 1px solid var(--card-border, #e8edf2);
        background: var(--card-bg, #fff); color: var(--text-secondary, #64748b);
        font-size: 13px; font-weight: 500; text-decoration: none;
        transition: background .12s;
    }
    .msg-btn-cancel:hover { background: var(--body-bg, #f8fafc); color: var(--text-primary, #0f172a); text-decoration: none; }
    .msg-btn-cancel svg { width: 13px; height: 13px; }

    .msg-btn-send {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 8px;
        background: var(--accent, #1d4ed8); color: #fff;
        font-size: 13px; font-weight: 600; border: none; cursor: pointer;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
        transition: background .15s, transform .15s;
    }
    .msg-btn-send:hover { background: var(--accent-hover, #1e40af); transform: translateY(-1px); }
    .msg-btn-send svg { width: 14px; height: 14px; }
</style>

<div class="reply-context">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
    </svg>
    <span class="reply-context-text">
        Respondiendo a <strong>{{ optional($topic->receiverOrCreator())->name ?? '(usuario)' }}</strong>
        &mdash; <em>{{ $topic->subject }}</em>
    </span>
</div>

<form action="{{ route('panel.messenger.reply', $topic->id) }}" method="POST" class="msg-form">
    @csrf

    <div class="msg-field">
        <label class="msg-label" for="content">{{ trans('global.content') }}</label>
        <textarea name="content" id="content" class="msg-textarea"
                  placeholder="Escriba su respuesta..." required></textarea>
    </div>

    <div class="msg-form-actions">
        <a href="{{ route('panel.messenger.showMessages', $topic->id) }}" class="msg-btn-cancel">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Volver
        </a>
        <button type="submit" class="msg-btn-send">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
            {{ trans('global.reply') }}
        </button>
    </div>

</form>
@endsection
