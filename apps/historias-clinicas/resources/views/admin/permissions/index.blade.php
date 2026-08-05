@extends('layouts.admin')
@section('content')

@push('styles')
<style>
    .prm-wrap {
        display: flex; flex-direction: column; gap: 20px;
        animation: prmFadeUp .35s ease both;
    }
    @@keyframes prmFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── header ── */
    .prm-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .prm-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .prm-header-left p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

    .prm-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px;
        font-size: 13px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: background .15s, transform .15s;
    }
    .prm-btn:hover { transform: translateY(-1px); text-decoration: none; }
    .prm-btn svg  { width: 15px; height: 15px; }
    .prm-btn.primary {
        background: var(--accent, #1d4ed8); color: #fff;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
    }
    .prm-btn.primary:hover { background: var(--accent-hover, #1e40af); color: #fff; }

    /* ── table card ── */
    .prm-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow); overflow: hidden;
    }
    .prm-table-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-wrap: wrap; gap: 8px;
    }
    .prm-table-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .prm-table-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
    .prm-table-body { padding: 16px 20px; }

    /* ── action btn ── */
    .prm-action-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity .15s; font-family: var(--font-sans, inherit);
    }
    .prm-action-btn:hover { opacity: .82; text-decoration: none; }
    .prm-action-btn svg  { width: 12px; height: 12px; }
    .prm-action-btn.view   { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); }
    .prm-action-btn.edit   { background: #f0fdf4; color: #16a34a; }
    .prm-action-btn.danger { background: #fff1f2; color: #e11d48; }
</style>
@endpush

<div class="prm-wrap">

    {{-- ── Header ── --}}
    <div class="prm-header">
        <div class="prm-header-left">
            <h1>{{ trans('cruds.permission.title') }}</h1>
            <p>Permisos disponibles para asignar a roles</p>
        </div>
        @can('permission_create')
        <a href="{{ route('admin.permissions.create') }}" class="prm-btn primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            {{ trans('global.add') }} {{ trans('cruds.permission.title_singular') }}
        </a>
        @endcan
    </div>

    {{-- ── Tabla ── --}}
    <div class="prm-table-card">
        <div class="prm-table-head">
            <div class="prm-table-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                {{ trans('cruds.permission.title_singular') }} {{ trans('global.list') }}
            </div>
        </div>

        <div class="prm-table-body">
            <div class="table-responsive">
                <table class="table datatable datatable-Permission" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.permission.fields.id') }}</th>
                            <th>{{ trans('cruds.permission.fields.title') }}</th>
                            <th style="width:160px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                        <tr data-entry-id="{{ $permission->id }}">
                            <td></td>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->title }}</td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    @can('permission_show')
                                    <a href="{{ route('admin.permissions.show', $permission->id) }}" class="prm-action-btn view">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-12 0c0 5.523 4.477 10 10 10S22 17.523 22 12 17.523 2 12 2 2 6.477 2 12z"/>
                                        </svg>
                                        {{ trans('global.view') }}
                                    </a>
                                    @endcan
                                    @can('permission_edit')
                                    <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="prm-action-btn edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        {{ trans('global.edit') }}
                                    </a>
                                    @endcan
                                    @can('permission_delete')
                                    <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}')"
                                          style="display:inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="prm-action-btn danger">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            {{ trans('global.delete') }}
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
$(function () {
    var dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

    @can('permission_delete')
    dtButtons.push({
        text: '{{ trans('global.datatables.delete') }}',
        url:  '{{ route('admin.permissions.massDestroy') }}',
        className: 'btn-danger',
        action: function (e, dt, node, config) {
            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id');
            });
            if (ids.length === 0) {
                alert('{{ trans('global.datatables.zero_selected') }}');
                return;
            }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                    headers: { 'x-csrf-token': _token },
                    method: 'POST',
                    url: config.url,
                    data: { ids: ids, _method: 'DELETE' }
                }).done(function () { location.reload(); });
            }
        }
    });
    @endcan

    $.extend(true, $.fn.dataTable.defaults, {
        orderCellsTop: true,
        order: [[ 1, 'desc' ]],
        pageLength: 100,
    });

    $('.datatable-Permission:not(.ajaxTable)').DataTable({ buttons: dtButtons });

    $('a[data-toggle="tab"]').on('shown.bs.tab click', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
});
</script>
@endpush
