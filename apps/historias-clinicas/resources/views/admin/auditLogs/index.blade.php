@extends('layouts.admin')
@section('content')

@push('styles')
<style>
    .aud-wrap {
        display: flex; flex-direction: column; gap: 20px;
        animation: audFadeUp .35s ease both;
    }
    @@keyframes audFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── header ── */
    .aud-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .aud-header-left h1 {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .aud-header-left p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 3px 0 0; }

    /* ── table card ── */
    .aud-table-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow); overflow: hidden;
    }
    .aud-table-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid var(--card-border, #e8edf2);
        flex-wrap: wrap; gap: 8px;
    }
    .aud-table-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .aud-table-title svg { width: 16px; height: 16px; color: var(--accent, #1d4ed8); }
    .aud-table-body { padding: 16px 20px; }

    /* ── description badge ── */
    .aud-desc {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600;
    }
    .aud-desc.created { background: #f0fdf4; color: #16a34a; }
    .aud-desc.updated { background: #fffbeb; color: #b45309; }
    .aud-desc.deleted { background: #fff1f2; color: #e11d48; }
    .aud-desc.other   { background: var(--body-bg, #f1f5f9); color: var(--text-secondary, #64748b); }

    /* ── subject type chip ── */
    .aud-type {
        font-size: 11px; font-weight: 600; padding: 2px 7px;
        border-radius: 99px; font-family: var(--font-mono, monospace);
        background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8);
    }

    /* ── action btn ── */
    .aud-action-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity .15s; font-family: var(--font-sans, inherit);
        background: var(--accent-light, #eff6ff); color: var(--accent, #1d4ed8);
    }
    .aud-action-btn:hover { opacity: .82; text-decoration: none; }
    .aud-action-btn svg  { width: 12px; height: 12px; }
</style>
@endpush

<div class="aud-wrap">

    {{-- ── Header ── --}}
    <div class="aud-header">
        <div class="aud-header-left">
            <h1>{{ trans('cruds.auditLog.title') }}</h1>
            <p>Registro de actividad y cambios en el sistema</p>
        </div>
    </div>

    {{-- ── Tabla ── --}}
    <div class="aud-table-card">
        <div class="aud-table-head">
            <div class="aud-table-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                {{ trans('cruds.auditLog.title_singular') }} {{ trans('global.list') }}
            </div>
        </div>

        <div class="aud-table-body">
            <div class="table-responsive">
                <table class="table datatable datatable-AuditLog" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.auditLog.fields.id') }}</th>
                            <th>{{ trans('cruds.auditLog.fields.description') }}</th>
                            <th>{{ trans('cruds.auditLog.fields.subject_id') }}</th>
                            <th>{{ trans('cruds.auditLog.fields.subject_type') }}</th>
                            <th>{{ trans('cruds.auditLog.fields.user_id') }}</th>
                            <th>{{ trans('cruds.auditLog.fields.host') }}</th>
                            <th>{{ trans('cruds.auditLog.fields.created_at') }}</th>
                            <th style="width:80px;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditLogs as $auditLog)
                        <tr data-entry-id="{{ $auditLog->id }}">
                            <td></td>
                            <td>{{ $auditLog->id }}</td>
                            <td>
                                @php
                                    $desc  = strtolower($auditLog->description ?? '');
                                    $cls   = str_contains($desc, 'creat') ? 'created'
                                           : (str_contains($desc, 'updat') ? 'updated'
                                           : (str_contains($desc, 'delet') ? 'deleted' : 'other'));
                                @endphp
                                <span class="aud-desc {{ $cls }}">{{ $auditLog->description }}</span>
                            </td>
                            <td>{{ $auditLog->subject_id ?? '—' }}</td>
                            <td>
                                @if($auditLog->subject_type)
                                    <span class="aud-type">{{ class_basename($auditLog->subject_type) }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $auditLog->user_id ?? '—' }}</td>
                            <td>{{ $auditLog->host ?? '—' }}</td>
                            <td>
                                {{ $auditLog->created_at
                                    ? \Carbon\Carbon::parse($auditLog->created_at)->format('d/m/Y H:i')
                                    : '—' }}
                            </td>
                            <td>
                                @can('audit_log_show')
                                <a href="{{ route('admin.audit-logs.show', $auditLog->id) }}" class="aud-action-btn">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-12 0c0 5.523 4.477 10 10 10S22 17.523 22 12 17.523 2 12 2 2 6.477 2 12z"/>
                                    </svg>
                                    {{ trans('global.view') }}
                                </a>
                                @endcan
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

    $.extend(true, $.fn.dataTable.defaults, {
        orderCellsTop: true,
        order: [[ 1, 'desc' ]],
        pageLength: 100,
    });

    $('.datatable-AuditLog:not(.ajaxTable)').DataTable({ buttons: dtButtons });

    $('a[data-toggle="tab"]').on('shown.bs.tab click', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
});
</script>
@endpush
