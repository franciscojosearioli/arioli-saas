@extends('admin.messenger.template')

@section('title', trans('global.new_message'))

@section('messenger-content')
<style>
    .msg-form { display: flex; flex-direction: column; gap: 18px; max-width: 680px; }
    .msg-field { display: flex; flex-direction: column; gap: 5px; }
    .msg-label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .06em; color: var(--text-muted, #94a3b8);
    }
    .msg-input, .msg-textarea, .msg-select {
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: 8px; padding: 9px 12px;
        font-size: 13px; color: var(--text-primary, #0f172a);
        background: var(--body-bg, #f8fafc);
        font-family: var(--font-sans, inherit);
        width: 100%; outline: none; transition: border-color .15s;
    }
    .msg-input:focus, .msg-textarea:focus, .msg-select:focus {
        border-color: var(--accent, #1d4ed8);
        box-shadow: 0 0 0 3px rgba(29,78,216,.08);
    }
    html.dark .msg-input, html.dark .msg-textarea, html.dark .msg-select {
        background: #0f172a; color: #f1f5f9; border-color: #1e293b;
    }
    .msg-textarea { resize: vertical; min-height: 120px; line-height: 1.6; }
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

<form action="{{ route('admin.messenger.storeTopic') }}" method="POST" class="msg-form">
    @csrf

    <div class="msg-field">
        <label class="msg-label" for="recipient">{{ trans('global.recipient') }}</label>
        <select name="recipient" id="recipient" class="msg-select select2" required>
            <option value="">— Seleccionar destinatario —</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->email }}</option>
            @endforeach
        </select>
    </div>

    <div class="msg-field">
        <label class="msg-label" for="subject">{{ trans('global.subject') }}</label>
        <input type="text" name="subject" id="subject" class="msg-input"
               placeholder="Asunto del mensaje..." required>
    </div>

    <div class="msg-field">
        <label class="msg-label" for="content">{{ trans('global.content') }}</label>
        <textarea name="content" id="content" class="msg-textarea"
                  placeholder="Escriba su mensaje aquí..." required></textarea>
    </div>

    <div class="msg-form-actions">
        <a href="{{ route('admin.messenger.index') }}" class="msg-btn-cancel">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Cancelar
        </a>
        <button type="submit" class="msg-btn-send">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            {{ trans('global.submit') }}
        </button>
    </div>

</form>
@endsection

@push('scripts')
<script>
$(function () {
    $('#recipient').select2({ width: '100%', placeholder: '— Seleccionar destinatario —' });
});
</script>
@endpush
