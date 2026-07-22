@extends('layouts.admin')
@section('content')

@push('styles')
<style>
.it-wrap { display: flex; flex-direction: column; gap: 20px; }

.it-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
}
.it-header h1 { font-size: 22px; font-weight: 700; color: var(--t1); letter-spacing: -.02em; margin: 0; }
.it-header p  { font-size: 13px; color: var(--t2); margin: 3px 0 0; }

.it-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; border-radius: 10px;
    font-size: 13px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    transition: background .15s, transform .12s;
}
.it-btn:hover { transform: translateY(-1px); text-decoration: none; }
.it-btn svg   { width: 15px; height: 15px; }
.it-btn.primary { background: #1a3561; color: #fff; box-shadow: 0 2px 8px rgba(26,53,97,.2); }
.it-btn.primary:hover { background: #142a4f; color: #fff; }

.it-alert {
    padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 500;
    background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;
    display: flex; align-items: center; gap: 8px;
}

.it-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
}
.it-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--border);
}
.it-card-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 600; color: var(--t1);
}
.it-card-title svg { width: 16px; height: 16px; color: #1a3561; }

.it-count {
    display: inline-flex; align-items: center;
    padding: 2px 9px; border-radius: 99px; font-size: 11px; font-weight: 600;
    background: #eff6ff; color: #1a3561;
}

.it-table { width: 100%; border-collapse: collapse; }
.it-table th {
    font-size: 10px; font-weight: 700; color: #1a3561;
    text-transform: uppercase; letter-spacing: .06em;
    padding: 10px 16px; text-align: left;
    background: #f8fafc; border-bottom: 1px solid var(--border);
}
.it-table td {
    padding: 12px 16px; font-size: 13px; color: var(--t1);
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}
.it-table tbody tr:last-child td { border-bottom: none; }
.it-table tbody tr:hover td { background: #f8fafc; }

.it-id { font-size: 11px; color: var(--t2); font-family: monospace; }

.it-actions { display: flex; gap: 6px; }
.it-act-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 11px; border-radius: 7px; font-size: 11px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer; transition: all .12s;
}
.it-act-btn svg { width: 12px; height: 12px; }
.it-act-btn.edit   { background: #eff6ff; color: #1a3561; }
.it-act-btn.edit:hover { background: #1a3561; color: #fff; }
.it-act-btn.del    { background: #fef2f2; color: #dc2626; }
.it-act-btn.del:hover { background: #dc2626; color: #fff; }

.it-empty {
    padding: 40px 20px; text-align: center; color: var(--t2);
    font-size: 13px;
}
.it-empty svg { width: 36px; height: 36px; margin: 0 auto 8px; display: block; opacity: .35; }
</style>
@endpush

<div class="it-wrap">

    {{-- Header --}}
    <div class="it-header">
        <div>
            <h1>Tipos de Informe</h1>
            <p>Definí los tipos de informes clínicos disponibles para los profesionales.</p>
        </div>
        @can('informe_tipo_create')
        <a href="{{ route('admin.informes.tipos.create') }}" class="it-btn primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo tipo
        </a>
        @endcan
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="it-alert">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Tabla --}}
    <div class="it-card">
        <div class="it-card-head">
            <div class="it-card-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Tipos registrados
            </div>
            <span class="it-count">{{ $InformeTipos->count() }}</span>
        </div>

        @if($InformeTipos->isEmpty())
        <div class="it-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            No hay tipos de informe registrados. Creá el primero.
        </div>
        @else
        <table class="it-table">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Nombre del tipo</th>
                    <th style="width:160px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($InformeTipos as $tipo)
                <tr>
                    <td><span class="it-id">{{ $tipo->id }}</span></td>
                    <td><strong>{{ $tipo->name }}</strong></td>
                    <td>
                        <div class="it-actions">
                            @can('informe_tipo_edit')
                            <a href="{{ route('admin.informes.tipos.edit', $tipo->id) }}" class="it-act-btn edit">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Editar
                            </a>
                            @endcan
                            @can('informe_tipo_delete')
                            <form action="{{ route('admin.informes.tipos.destroy', $tipo->id) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminás el tipo «{{ $tipo->name }}»? Los informes existentes no se verán afectados.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="it-act-btn del">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

@endsection
