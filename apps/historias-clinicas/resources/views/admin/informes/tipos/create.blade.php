@extends('layouts.admin')
@section('content')

@push('styles')
<style>
.it-form-wrap { max-width: 520px; display: flex; flex-direction: column; gap: 18px; }

.it-form-header { display: flex; align-items: center; gap: 10px; }
.it-form-header a {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; border-radius: 8px;
    border: 1.5px solid var(--border); background: var(--card);
    font-size: 12px; font-weight: 600; color: var(--t2); text-decoration: none;
    transition: all .12s;
}
.it-form-header a:hover { border-color: #1a3561; color: #1a3561; }
.it-form-header a svg  { width: 13px; height: 13px; }
.it-form-header h1 { font-size: 20px; font-weight: 700; color: var(--t1); margin: 0; }

.it-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
}
.it-card-hdr {
    background: #1a3561; color: #fff;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
    padding: 10px 18px; display: flex; align-items: center; gap: 7px;
}
.it-card-hdr svg { width: 14px; height: 14px; opacity: .85; }
.it-card-body { padding: 22px 20px; }

.it-field { margin-bottom: 18px; }
.it-label {
    display: block; font-size: 11px; font-weight: 700; color: #1a3561;
    text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px;
}
.it-label .req { color: #dc2626; margin-left: 2px; }
.it-input {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid var(--border); border-radius: 9px;
    font-size: 14px; color: var(--t1); background: var(--bg);
    outline: none; transition: border-color .15s, box-shadow .15s;
    font-family: var(--font-sans);
}
.it-input:focus { border-color: #1a3561; box-shadow: 0 0 0 3px rgba(26,53,97,.1); background: var(--card); }
.it-input.error { border-color: #dc2626; }
.it-error { font-size: 12px; color: #dc2626; margin-top: 5px; }
.it-hint  { font-size: 12px; color: var(--t2); margin-top: 5px; }

.it-actions { display: flex; align-items: center; justify-content: space-between; padding-top: 4px; }
.it-save {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 24px; border-radius: 9px;
    background: #1a3561; color: #fff; border: none;
    font-size: 13px; font-weight: 700; cursor: pointer;
    font-family: var(--font-sans); transition: background .15s;
}
.it-save:hover { background: #142a4f; }
.it-save svg { width: 14px; height: 14px; }
.it-cancel {
    font-size: 13px; color: var(--t2); text-decoration: none;
    transition: color .12s;
}
.it-cancel:hover { color: #dc2626; }
</style>
@endpush

<div class="it-form-wrap">

    <div class="it-form-header">
        <a href="{{ route('admin.informes.tipos.index') }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <h1>Nuevo tipo de informe</h1>
    </div>

    <div class="it-card">
        <div class="it-card-hdr">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Datos del tipo
        </div>
        <div class="it-card-body">
            <form method="POST" action="{{ route('admin.informes.tipos.store') }}">
                @csrf

                <div class="it-field">
                    <label class="it-label" for="name">
                        Nombre del tipo <span class="req">*</span>
                    </label>
                    <input class="it-input {{ $errors->has('name') ? 'error' : '' }}"
                           type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           placeholder="Ej: Informe Psicológico, Informe Psiquiátrico…"
                           autofocus required>
                    @error('name')
                        <div class="it-error">{{ $message }}</div>
                    @enderror
                    <div class="it-hint">
                        Este nombre aparecerá en el selector al cargar un nuevo informe.
                    </div>
                </div>

                <div class="it-actions">
                    <a href="{{ route('admin.informes.tipos.index') }}" class="it-cancel">Cancelar</a>
                    <button type="submit" class="it-save">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar tipo
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
