@extends('layouts.admin')
@section('content')

@push('styles')
<style>
    .rol-wrap {
        display: flex; flex-direction: column; gap: 20px;
        animation: rolFadeUp .35s ease both;
    }
    @@keyframes rolFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── header ── */
    .rol-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .rol-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .rol-header-left p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

    .rol-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px;
        font-size: 13px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: background .15s, transform .15s;
    }
    .rol-btn:hover { transform: translateY(-1px); text-decoration: none; }
    .rol-btn svg  { width: 15px; height: 15px; }
    .rol-btn.primary {
        background: var(--accent, #1d4ed8); color: #fff;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
    }
    .rol-btn.primary:hover { background: var(--accent-hover, #1e40af); color: #fff; }

    /* ── table card ── */
    .rol-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow); overflow: hidden;
    }
    .rol-table-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-wrap: wrap; gap: 8px;
    }
    .rol-table-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .rol-table-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
    .rol-table-body { padding: 16px 20px; }

    /* ── permission pill ── */
    .rol-perm {
        display: inline-flex; align-items: center;
        padding: 2px 7px; border-radius: 99px; font-size: 10px; font-weight: 600;
        background: var(--body-bg, #f1f5f9); color: var(--text-secondary, #64748b);
        border: 1px solid var(--card-border, #e8edf2);
        margin: 1px 2px;
    }
    .rol-perm-more {
        display: inline-flex; align-items: center;
        padding: 2px 7px; border-radius: 99px; font-size: 10px; font-weight: 600;
        background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8);
        margin: 1px 2px; cursor: default;
    }

    /* ── action btn ── */
    .rol-action-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity .15s; font-family: var(--font-sans, inherit);
    }
    .rol-action-btn:hover { opacity: .82; text-decoration: none; }
    .rol-action-btn svg  { width: 12px; height: 12px; }
    .rol-action-btn.view   { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); }
    .rol-action-btn.edit   { background: #f0fdf4; color: #16a34a; }
    .rol-action-btn.danger { background: #fff1f2; color: #e11d48; }
</style>
@endpush

<div class="rol-wrap">

    {{-- ── Header ── --}}
    <div class="rol-header">
        <div class="rol-header-left">
            <h1>{{ trans('cruds.role.title') }}</h1>
            <p>Roles y permisos de acceso al sistema</p>
        </div>
        @can('role_create')
        <a href="{{ route('admin.roles.create') }}" class="rol-btn primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            {{ trans('global.add') }} {{ trans('cruds.role.title_singular') }}
        </a>
        @endcan
    </div>

    {{-- ── Tabla ── --}}
    <div class="rol-table-card">
        <div class="rol-table-head">
            <div class="rol-table-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                {{ trans('cruds.role.title_singular') }} {{ trans('global.list') }}
            </div>
        </div>

        <div class="rol-table-body">
            <div class="table-responsive">
                <table class="table datatable datatable-Role" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.role.fields.id') }}</th>
                            <th>{{ trans('cruds.role.fields.title') }}</th>
                            <th>{{ trans('cruds.role.fields.permissions') }}</th>
                            <th style="width:160px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr data-entry-id="{{ $role->id }}">
                            <td></td>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->title }}</td>
                            <td>
                                @php($perms = $role->permissions)
                                @foreach($perms->take(5) as $perm)
                                    <span class="rol-perm">{{ $perm->title }}</span>
                                @endforeach
                                @if($perms->count() > 5)
                                    <span class="rol-perm-more">+{{ $perms->count() - 5 }} más</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    @can('role_show')
                                    <a href="{{ route('admin.roles.show', $role->id) }}" class="rol-action-btn view">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-12 0c0 5.523 4.477 10 10 10S22 17.523 22 12 17.523 2 12 2 2 6.477 2 12z"/>
                                        </svg>
                                        {{ trans('global.view') }}
                                    </a>
                                    @endcan
                                    @can('role_edit')
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="rol-action-btn edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        {{ trans('global.edit') }}
                                    </a>
                                    @endcan
                                    @can('role_delete')
                                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}')"
                                          style="display:inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rol-action-btn danger">
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

    @can('role_delete')
    dtButtons.push({
        text: '{{ trans('global.datatables.delete') }}',
        url:  '{{ route('admin.roles.massDestroy') }}',
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

    $('.datatable-Role:not(.ajaxTable)').DataTable({ buttons: dtButtons });

    $('a[data-toggle="tab"]').on('shown.bs.tab click', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
});
</script>
@endpush
