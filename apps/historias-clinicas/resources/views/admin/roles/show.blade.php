@extends('layouts.admin')
@section('title', trans('global.show') . ' ' . trans('cruds.role.title_singular'))

@section('content')
<div style="max-width:720px; display:flex; flex-direction:column; gap:20px;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <a href="{{ route('admin.roles.index') }}"
               style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; border:1px solid var(--border); color:var(--t2); text-decoration:none; flex-shrink:0;"
               onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 style="font-size:20px; font-weight:700; color:var(--t1); margin:0; letter-spacing:-.02em;">{{ $role->title }}</h1>
                <p style="font-size:13px; color:var(--t2); margin:3px 0 0;">{{ $role->permissions->count() }} permisos asignados</p>
            </div>
        </div>
        @can('role_edit')
        <a href="{{ route('admin.roles.edit', $role->id) }}"
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
            <div style="width:180px; flex-shrink:0; font-size:13px; font-weight:500; color:var(--t2);">{{ trans('cruds.role.fields.id') }}</div>
            <div style="font-size:13px; color:var(--t1); font-weight:600;">{{ $role->id }}</div>
        </div>

        <div style="display:flex; padding:13px 20px; border-bottom:1px solid var(--border); background:var(--bg);">
            <div style="width:180px; flex-shrink:0; font-size:13px; font-weight:500; color:var(--t2);">{{ trans('cruds.role.fields.title') }}</div>
            <div style="font-size:13px; color:var(--t1); font-weight:600;">{{ $role->title }}</div>
        </div>

        <div style="display:flex; padding:13px 20px; align-items:flex-start; background:var(--card);">
            <div style="width:180px; flex-shrink:0; font-size:13px; font-weight:500; color:var(--t2); padding-top:2px;">{{ trans('cruds.role.fields.permissions') }}</div>
            <div style="display:flex; flex-wrap:wrap; gap:4px;">
                @forelse($role->permissions as $perm)
                    <span style="display:inline-block;padding:2px 9px;border-radius:99px;font-size:11px;font-weight:600;background:var(--bg);color:var(--t2);border:1px solid var(--border);">{{ $perm->title }}</span>
                @empty
                    <span style="font-size:13px;color:var(--t3);">Sin permisos</span>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
