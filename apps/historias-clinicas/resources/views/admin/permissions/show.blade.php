@extends('layouts.admin')
@section('title', trans('global.show') . ' ' . trans('cruds.permission.title_singular'))

@section('content')
<div style="max-width:560px; display:flex; flex-direction:column; gap:20px;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <a href="{{ route('admin.permissions.index') }}"
               style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; border:1px solid var(--border); color:var(--t2); text-decoration:none; flex-shrink:0;"
               onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 style="font-size:20px; font-weight:700; color:var(--t1); margin:0; letter-spacing:-.02em;">{{ $permission->title }}</h1>
                <p style="font-size:13px; color:var(--t2); margin:3px 0 0;">Permiso del sistema</p>
            </div>
        </div>
        @can('permission_edit')
        <a href="{{ route('admin.permissions.edit', $permission->id) }}"
           style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; font-size:13px; font-weight:600; background:var(--accent-lt); color:var(--accent); text-decoration:none;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            {{ trans('global.edit') }}
        </a>
        @endcan
    </div>

    {{-- Data card --}}
    <div style="background:var(--card); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow);">

        <div style="display:flex; padding:13px 20px; border-bottom:1px solid var(--border); background:var(--card);">
            <div style="width:160px; flex-shrink:0; font-size:13px; font-weight:500; color:var(--t2);">{{ trans('cruds.permission.fields.id') }}</div>
            <div style="font-size:13px; color:var(--t1); font-weight:600;">{{ $permission->id }}</div>
        </div>

        <div style="display:flex; padding:13px 20px; background:var(--bg);">
            <div style="width:160px; flex-shrink:0; font-size:13px; font-weight:500; color:var(--t2);">{{ trans('cruds.permission.fields.title') }}</div>
            <div style="font-size:13px; color:var(--t1); font-weight:600; font-family:var(--font-mono);">{{ $permission->title }}</div>
        </div>

    </div>

</div>
@endsection
