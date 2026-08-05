@extends('layouts.admin')
@section('content')

@push('styles')
<style>
    .usr-wrap {
        display: flex; flex-direction: column; gap: 20px;
        animation: usrFadeUp .35s ease both;
    }
    @@keyframes usrFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── header ── */
    .usr-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .usr-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .usr-header-left p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

    .usr-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px;
        font-size: 13px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: background .15s, transform .15s;
    }
    .usr-btn:hover { transform: translateY(-1px); text-decoration: none; }
    .usr-btn svg  { width: 15px; height: 15px; }
    .usr-btn.primary {
        background: var(--accent, #1d4ed8); color: #fff;
        box-shadow: 0 2px 8px rgba(29,78,216,.25);
    }
    .usr-btn.primary:hover { background: var(--accent-hover, #1e40af); color: #fff; }

    /* ── table card ── */
    .usr-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow); overflow: hidden;
    }
    .usr-table-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-wrap: wrap; gap: 8px;
    }
    .usr-table-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .usr-table-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
    .usr-table-body { padding: 16px 20px; }

    /* ── role badge ── */
    .usr-role {
        display: inline-flex; align-items: center;
        padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600;
        background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8);
        margin: 1px 2px;
    }

    /* ── 2FA badge ── */
    .usr-2fa {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600;
    }
    .usr-2fa.on  { background: #f0fdf4; color: #16a34a; }
    .usr-2fa.off { background: var(--body-bg, #f8fafc); color: var(--text-muted, #94a3b8); }
    .usr-2fa svg { width: 11px; height: 11px; }

    /* ── action btn ── */
    .usr-action-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity .15s; font-family: var(--font-sans, inherit);
    }
    .usr-action-btn:hover { opacity: .82; text-decoration: none; }
    .usr-action-btn svg  { width: 12px; height: 12px; }
    .usr-action-btn.view   { background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8); }
    .usr-action-btn.edit   { background: #f0fdf4; color: #16a34a; }
    .usr-action-btn.danger { background: #fff1f2; color: #e11d48; }
</style>
@endpush

<div class="usr-wrap">

    {{-- ── Header ── --}}
    <div class="usr-header">
        <div class="usr-header-left">
            <h1>{{ trans('cruds.user.title') }}</h1>
            <p>Gestión de usuarios y roles del sistema</p>
        </div>
        @can('user_create')
        <a href="{{ route('admin.users.create') }}" class="usr-btn primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
        </a>
        @endcan
    </div>

    {{-- ── Tabla ── --}}
    <div class="usr-table-card">
        <div class="usr-table-head">
            <div class="usr-table-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ trans('cruds.user.title_singular') }} {{ trans('global.list') }}
            </div>
        </div>

        <div class="usr-table-body">
            <div class="table-responsive">
                <table class="table datatable datatable-User" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.user.fields.id') }}</th>
                            <th>{{ trans('cruds.user.fields.name') }}</th>
                            <th>{{ trans('cruds.user.fields.email') }}</th>
                            <th>{{ trans('cruds.user.fields.email_verified_at') }}</th>
                            <th>{{ trans('cruds.user.fields.two_factor') }}</th>
                            <th>{{ trans('cruds.user.fields.roles') }}</th>
                            <th style="width:160px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr data-entry-id="{{ $user->id }}">
                            <td></td>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                {{ $user->email_verified_at
                                    ? \Carbon\Carbon::parse($user->email_verified_at)->format('d/m/Y')
                                    : '—' }}
                            </td>
                            <td>
                                <span style="display:none">{{ $user->two_factor ? 1 : 0 }}</span>
                                @if($user->two_factor)
                                    <span class="usr-2fa on">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Activo
                                    </span>
                                @else
                                    <span class="usr-2fa off">—</span>
                                @endif
                            </td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="usr-role">{{ $role->title }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    @can('user_show')
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="usr-action-btn view">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-12 0c0 5.523 4.477 10 10 10S22 17.523 22 12 17.523 2 12 2 2 6.477 2 12z"/>
                                        </svg>
                                        {{ trans('global.view') }}
                                    </a>
                                    @endcan
                                    @can('user_edit')
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="usr-action-btn edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        {{ trans('global.edit') }}
                                    </a>
                                    @endcan
                                    @can('user_delete')
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}')"
                                          style="display:inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="usr-action-btn danger">
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

    @can('user_delete')
    dtButtons.push({
        text: '{{ trans('global.datatables.delete') }}',
        url:  '{{ route('admin.users.massDestroy') }}',
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

    $('.datatable-User:not(.ajaxTable)').DataTable({ buttons: dtButtons });

    $('a[data-toggle="tab"]').on('shown.bs.tab click', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
});
</script>
@endpush
