@extends('layouts.admin')
@section('content')

@push('styles')
<style>
.ua-form-wrap { max-width: 640px; display: flex; flex-direction: column; gap: 20px; }

.ua-form-header {
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
}
.ua-form-header h1 { font-size: 22px; font-weight: 700; color: var(--t1); letter-spacing: -.02em; margin: 0; }
.ua-form-header p  { font-size: 13px; color: var(--t2); margin: 3px 0 0; }

.ua-back-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; border-radius: 10px;
    border: 1px solid var(--border); background: var(--card);
    font-size: 12px; font-weight: 600; color: var(--t2);
    text-decoration: none; transition: all .12s;
}
.ua-back-btn:hover { border-color: #1a3561; color: #1a3561; text-decoration: none; }
.ua-back-btn svg { width: 13px; height: 13px; }

.ua-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
}
.ua-card-head {
    display: flex; align-items: center; gap: 8px;
    padding: 16px 20px; border-bottom: 1px solid var(--border);
    font-size: 14px; font-weight: 600; color: var(--t1);
}
.ua-card-head svg { width: 16px; height: 16px; color: #1a3561; flex-shrink: 0; }
.ua-card-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; }

.ua-field label {
    display: block; font-size: 12px; font-weight: 600; color: var(--t2);
    margin-bottom: 6px; text-transform: uppercase; letter-spacing: .05em;
}
.ua-field label .req { color: #dc2626; margin-left: 2px; }
.ua-field input[type="text"],
.ua-field select {
    width: 100%; padding: 9px 12px; border-radius: 8px;
    border: 1.5px solid var(--border); background: var(--card);
    font-size: 13px; color: var(--t1); font-family: var(--font-sans);
    transition: border-color .12s;
    outline: none;
}
.ua-field input[type="text"]:focus,
.ua-field select:focus { border-color: #1a3561; }
.ua-field .help { font-size: 11px; color: var(--t3); margin-top: 4px; }
.ua-field .err-msg { font-size: 11px; color: #dc2626; margin-top: 4px; }

.ua-users-select-wrap { position: relative; }
.ua-select-actions { display: flex; gap: 6px; margin-bottom: 6px; }
.ua-select-actions button {
    padding: 3px 10px; border-radius: 6px; border: 1px solid var(--border);
    font-size: 11px; font-weight: 600; color: var(--t2); background: var(--card); cursor: pointer;
    transition: all .12s; font-family: var(--font-sans);
}
.ua-select-actions button:hover { border-color: #1a3561; color: #1a3561; }

.select2-container { width: 100% !important; }
.select2-container--default .select2-selection--multiple {
    border: 1.5px solid var(--border) !important; border-radius: 8px !important;
    min-height: 42px !important;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #1a3561 !important;
}

.ua-submit-row { display: flex; align-items: center; gap: 10px; }
.ua-submit-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 22px; border-radius: 10px;
    background: #1a3561; color: #fff;
    font-size: 13px; font-weight: 600; border: none; cursor: pointer;
    box-shadow: 0 2px 8px rgba(26,53,97,.2);
    font-family: var(--font-sans);
    transition: background .12s, transform .12s;
}
.ua-submit-btn:hover { background: #142a4f; transform: translateY(-1px); }
.ua-submit-btn svg { width: 15px; height: 15px; }
.ua-cancel-link {
    font-size: 13px; color: var(--t2); text-decoration: none; font-weight: 500;
}
.ua-cancel-link:hover { color: var(--t1); }
</style>
@endpush

<div class="ua-form-wrap">

    {{-- Header --}}
    <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
            <a href="{{ route('admin.user-alerts.index') }}" class="ua-back-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver
            </a>
        </div>
        <h1 style="font-size:22px;font-weight:700;color:var(--t1);margin:12px 0 3px;">Nueva Alerta</h1>
        <p style="font-size:13px;color:var(--t2);margin:0;">Enviá una notificación a uno o más usuarios.</p>
    </div>

    {{-- Form --}}
    <div class="ua-card">
        <div class="ua-card-head">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            Datos de la alerta
        </div>
        <div class="ua-card-body">
            <form method="POST" action="{{ route('admin.user-alerts.store') }}">
                @csrf

                <div style="display:flex;flex-direction:column;gap:16px;">
                    {{-- Mensaje --}}
                    <div class="ua-field">
                        <label for="alert_text">Mensaje <span class="req">*</span></label>
                        <input type="text" name="alert_text" id="alert_text"
                               value="{{ old('alert_text', '') }}"
                               placeholder="Escribí el texto de la notificación…"
                               class="{{ $errors->has('alert_text') ? 'is-invalid' : '' }}"
                               required>
                        @if($errors->has('alert_text'))
                            <div class="err-msg">{{ $errors->first('alert_text') }}</div>
                        @endif
                    </div>

                    {{-- Enlace --}}
                    <div class="ua-field">
                        <label for="alert_link">Enlace (opcional)</label>
                        <input type="text" name="alert_link" id="alert_link"
                               value="{{ old('alert_link', '') }}"
                               placeholder="https://…"
                               class="{{ $errors->has('alert_link') ? 'is-invalid' : '' }}">
                        <div class="help">URL a la que apunta la notificación.</div>
                        @if($errors->has('alert_link'))
                            <div class="err-msg">{{ $errors->first('alert_link') }}</div>
                        @endif
                    </div>

                    {{-- Usuarios --}}
                    <div class="ua-field">
                        <label for="users">Destinatarios</label>
                        <div class="ua-select-actions">
                            <button type="button" class="select-all-users">Seleccionar todos</button>
                            <button type="button" class="deselect-all-users">Deseleccionar todos</button>
                        </div>
                        <select class="form-control select2 {{ $errors->has('users') ? 'is-invalid' : '' }}"
                                name="users[]" id="users" multiple>
                            @foreach($users as $id => $user)
                                <option value="{{ $id }}" {{ in_array($id, old('users', [])) ? 'selected' : '' }}>{{ $user }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('users'))
                            <div class="err-msg">{{ $errors->first('users') }}</div>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <div class="ua-submit-row">
                        <button type="submit" class="ua-submit-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Enviar alerta
                        </button>
                        <a href="{{ route('admin.user-alerts.index') }}" class="ua-cancel-link">Cancelar</a>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
$(function() {
    $('#users').select2();
    $('.select-all-users').on('click', function() {
        $('#users option').prop('selected', true);
        $('#users').trigger('change');
    });
    $('.deselect-all-users').on('click', function() {
        $('#users option').prop('selected', false);
        $('#users').trigger('change');
    });
});
</script>
@endpush
