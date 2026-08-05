@extends('layouts.panel')
@section('title', 'Editar Esquema — ' . ($paciente->apellido ?? '') . ', ' . ($paciente->nombre ?? ''))

@push('styles')
<style>
    .rx-wrap {
        display: flex; flex-direction: column; gap: 24px;
        max-width: 900px; margin: 0 auto;
        animation: rxFade .3s ease both;
    }
    @@keyframes rxFade { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

    .rx-header {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    }
    .rx-header-left h1 { font-size: 22px; font-weight: 700; color: var(--text-primary); letter-spacing:-.02em; margin:0; }
    .rx-header-left p  { font-size: 13px; color: var(--text-secondary); margin: 4px 0 0; }

    .rx-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;
        text-decoration: none !important; border: none; cursor: pointer;
        transition: background .15s, transform .1s; font-family: var(--font-sans);
    }
    .rx-btn:hover { transform: translateY(-1px); }
    .rx-btn svg   { width: 15px; height: 15px; flex-shrink: 0; }
    .rx-btn.primary { background: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(29,78,216,.22); }
    .rx-btn.primary:hover { background: var(--accent-hover); color: #fff; }
    .rx-btn.ghost   { background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-secondary); }
    .rx-btn.ghost:hover { background: var(--body-bg); color: var(--text-primary); }
    .rx-btn.success { background: #16a34a; color: #fff; box-shadow: 0 2px 8px rgba(22,163,74,.22); }
    .rx-btn.success:hover { background: #15803d; color: #fff; }

    .rx-card {
        background: var(--card-bg); border: 1px solid var(--card-border);
        border-radius: 14px; box-shadow: var(--card-shadow); overflow: hidden;
    }
    .rx-card-header {
        padding: 16px 22px; border-bottom: 1px solid var(--card-border);
        display: flex; align-items: center; gap: 10px;
    }
    .rx-card-header svg { width: 17px; height: 17px; color: var(--accent); flex-shrink: 0; }
    .rx-card-title { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .rx-card-body  { padding: 22px; }

    .rx-field { margin-bottom: 18px; }
    .rx-label {
        display: block; font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .07em;
        color: var(--text-muted); margin-bottom: 6px;
    }
    .rx-input {
        width: 100%; padding: 9px 12px;
        border: 1px solid var(--card-border); border-radius: 8px;
        font-size: 13px; color: var(--text-primary);
        background: var(--body-bg); font-family: var(--font-sans);
        outline: none; transition: border-color .15s, box-shadow .15s;
        appearance: none;
    }
    .rx-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
    .rx-input[readonly], .rx-input[disabled] {
        opacity: .55; cursor: not-allowed; background: var(--card-border);
    }
    html.dark .rx-input { background: #0b1120; color: #f1f5f9; border-color: #1e293b; }
    html.dark .rx-input[readonly], html.dark .rx-input[disabled] { background: #1e293b; }

    .rx-row-item {
        display: grid; grid-template-columns: 160px 1fr 160px 44px;
        gap: 10px; align-items: end; padding: 12px 0;
        border-bottom: 1px solid var(--card-border);
    }
    .rx-row-item:first-child { padding-top: 0; }
    @@media (max-width: 640px) {
        .rx-row-item { grid-template-columns: 1fr; }
    }
    .rx-col-label {
        font-size: 10px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .07em; color: var(--text-muted); margin-bottom: 5px;
    }

    .rx-remove-btn {
        width: 36px; height: 36px; border-radius: 8px;
        background: #fff1f2; border: 1px solid #fecdd3;
        color: #e11d48; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s; flex-shrink: 0; padding: 0;
    }
    .rx-remove-btn:hover { background: #fecdd3; }
    .rx-remove-btn svg { width: 14px; height: 14px; }

    .rx-add-wrap { padding-top: 14px; display: flex; align-items: center; gap: 10px; }

    .rx-footer { display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-wrap: wrap; }

    .rx-readonly-note {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 11px; color: var(--text-muted);
        background: var(--body-bg); border: 1px solid var(--card-border);
        border-radius: 6px; padding: 3px 10px; margin-top: 4px;
    }
    .rx-readonly-note svg { width: 11px; height: 11px; }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('panel.medicacion.update', [$paciente->id, $fecha]) }}">
    @csrf
    @method('PUT')

<div class="rx-wrap">

    {{-- Header --}}
    <div class="rx-header">
        <div class="rx-header-left">
            <h1>Editar Esquema</h1>
            <p>{{ $paciente->apellido ?? '' }}, {{ $paciente->nombre ?? '' }} — {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ url()->previous() }}" class="rx-btn ghost">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Datos generales (readonly) --}}
    <div class="rx-card">
        <div class="rx-card-header">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="rx-card-title">Información general</span>
        </div>
        <div class="rx-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                <div class="rx-field">
                    <label class="rx-label" for="fecha">Fecha del esquema</label>
                    <input class="rx-input" type="date" name="fecha" id="fecha" value="{{ $fecha }}" readonly>
                    <span class="rx-readonly-note">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        La fecha no puede modificarse
                    </span>
                </div>
                <div class="rx-field">
                    <label class="rx-label" for="paciente_id">Paciente</label>
                    <select class="rx-input select2" name="paciente_id" id="paciente_id" disabled>
                        @foreach($pacientes as $id => $entry)
                        <option value="{{ $id }}" {{ $paciente->id == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    <span class="rx-readonly-note">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        El paciente no puede modificarse
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Medicamentos --}}
    <div class="rx-card">
        <div class="rx-card-header">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
            <span class="rx-card-title">Medicamentos</span>
        </div>
        <div class="rx-card-body">

            <div style="display:grid;grid-template-columns:160px 1fr 160px 44px;gap:10px;margin-bottom:6px;padding-bottom:6px;border-bottom:2px solid var(--card-border);">
                <span class="rx-col-label">Horario</span>
                <span class="rx-col-label">Medicamento</span>
                <span class="rx-col-label">Dosis / Unidad</span>
                <span></span>
            </div>

            <div id="rx-rows">
                @foreach($medicaciones as $medicacion)
                <div class="rx-row-item">
                    <div>
                        <select class="rx-input select2-inline" name="horario[]" required>
                            <option value="Mañana"   {{ $medicacion->horario == 'Mañana'   ? 'selected' : '' }}>Mañana</option>
                            <option value="Mediodia" {{ $medicacion->horario == 'Mediodia' ? 'selected' : '' }}>Mediodía</option>
                            <option value="Tarde"    {{ $medicacion->horario == 'Tarde'    ? 'selected' : '' }}>Tarde</option>
                            <option value="Noche"    {{ $medicacion->horario == 'Noche'    ? 'selected' : '' }}>Noche</option>
                        </select>
                    </div>
                    <div>
                        <input class="rx-input" type="text" name="medicamento[]"
                               value="{{ $medicacion->medicamento }}"
                               placeholder="Nombre del medicamento" required>
                    </div>
                    <div>
                        <input class="rx-input" type="text" name="unidad[]"
                               value="{{ $medicacion->unidad }}"
                               placeholder="Ej: 1 comprimido" required>
                    </div>
                    <div>
                        <button type="button" class="rx-remove-btn remove-row" title="Eliminar fila">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="rx-add-wrap">
                <button type="button" class="rx-btn ghost" id="add-row">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar medicamento
                </button>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="rx-footer">
        <a href="{{ url()->previous() }}" class="rx-btn ghost">Cancelar</a>
        <button type="submit" class="rx-btn success">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Guardar cambios
        </button>
    </div>

</div>
</form>
@endsection

@push('scripts')
<script>
$(function() {

    $('.select2-inline').select2({
        minimumResultsForSearch: Infinity,
        width: '100%',
        dropdownParent: $('body')
    });

    $('.select2').select2({
        width: '100%',
        dropdownParent: $('body')
    });

    const rowTemplate = () => `
        <div class="rx-row-item">
            <div>
                <select class="rx-input select2-inline" name="horario[]" required>
                    <option value="" disabled selected>Horario</option>
                    <option value="Mañana">Mañana</option>
                    <option value="Mediodia">Mediodía</option>
                    <option value="Tarde">Tarde</option>
                    <option value="Noche">Noche</option>
                </select>
            </div>
            <div>
                <input class="rx-input" type="text" name="medicamento[]"
                       placeholder="Nombre del medicamento" required>
            </div>
            <div>
                <input class="rx-input" type="text" name="unidad[]"
                       placeholder="Ej: 1 comprimido" required>
            </div>
            <div>
                <button type="button" class="rx-remove-btn remove-row" title="Eliminar fila">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

    $('#add-row').on('click', function() {
        const $row = $(rowTemplate());
        $('#rx-rows').append($row);
        $row.find('.select2-inline').select2({
            minimumResultsForSearch: Infinity,
            width: '100%',
            dropdownParent: $('body')
        });
    });

    $(document).on('click', '.remove-row', function() {
        if ($('#rx-rows .rx-row-item').length > 1) {
            $(this).closest('.rx-row-item').remove();
        } else {
            alert('El esquema debe tener al menos un medicamento.');
        }
    });

});
</script>
@endpush
