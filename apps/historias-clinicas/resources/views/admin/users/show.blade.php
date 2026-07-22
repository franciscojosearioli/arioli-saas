@extends('layouts.admin')
@section('title', trans('global.show') . ' ' . trans('cruds.user.title_singular'))

@section('content')
<div style="max-width:720px; display:flex; flex-direction:column; gap:20px;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <a href="{{ route('admin.users.index') }}"
               style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; border:1px solid var(--border); color:var(--t2); text-decoration:none; flex-shrink:0;"
               onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 style="font-size:20px; font-weight:700; color:var(--t1); margin:0; letter-spacing:-.02em;">{{ $user->name }}</h1>
                <p style="font-size:13px; color:var(--t2); margin:3px 0 0;">{{ $user->email }}</p>
            </div>
        </div>
        @can('user_edit')
        <a href="{{ route('admin.users.edit', $user->id) }}"
           style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:9px; font-size:13px; font-weight:600; background:var(--accent-lt); color:var(--accent); text-decoration:none; border:1px solid transparent;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            {{ trans('global.edit') }}
        </a>
        @endcan
    </div>

    {{-- Data card --}}
    <div style="background:var(--card); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow);">
        @php
        $rows = [
            [trans('cruds.user.fields.id'),               $user->id],
            [trans('cruds.user.fields.name'),             $user->name],
            [trans('cruds.user.fields.email'),            $user->email],
            [trans('cruds.user.fields.email_verified_at'), $user->email_verified_at ? \Carbon\Carbon::parse($user->email_verified_at)->format('d/m/Y H:i') : '—'],
        ];
        @endphp
        @foreach($rows as $i => [$label, $value])
        <div style="display:flex; padding:13px 20px; border-bottom:1px solid var(--border); background:{{ $i % 2 === 0 ? 'var(--card)' : 'var(--bg)' }};">
            <div style="width:220px; flex-shrink:0; font-size:13px; font-weight:500; color:var(--t2);">{{ $label }}</div>
            <div style="font-size:13px; color:var(--t1); font-weight:600;">{{ $value }}</div>
        </div>
        @endforeach

        {{-- 2FA --}}
        <div style="display:flex; padding:13px 20px; border-bottom:1px solid var(--border); background:var(--card);">
            <div style="width:220px; flex-shrink:0; font-size:13px; font-weight:500; color:var(--t2);">{{ trans('cruds.user.fields.two_factor') }}</div>
            <div>
                @if($user->two_factor)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700;background:#f0fdf4;color:#16a34a;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Activo
                    </span>
                @else
                    <span style="font-size:13px;color:var(--t3);">—</span>
                @endif
            </div>
        </div>

        {{-- Roles --}}
        <div style="display:flex; padding:13px 20px; border-bottom:1px solid var(--border); background:var(--bg); align-items:flex-start;">
            <div style="width:220px; flex-shrink:0; font-size:13px; font-weight:500; color:var(--t2); padding-top:2px;">{{ trans('cruds.user.fields.roles') }}</div>
            <div style="display:flex; flex-wrap:wrap; gap:4px;">
                @forelse($user->roles as $role)
                    <span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:600;background:var(--accent-lt);color:var(--accent);">{{ $role->title }}</span>
                @empty
                    <span style="font-size:13px;color:var(--t3);">—</span>
                @endforelse
            </div>
        </div>

        {{-- Especialidades --}}
        <div style="display:flex; padding:13px 20px; align-items:flex-start; background:var(--card);">
            <div style="width:220px; flex-shrink:0; font-size:13px; font-weight:500; color:var(--t2); padding-top:2px;">Especialidades</div>
            <div style="display:flex; flex-wrap:wrap; gap:4px;">
                @forelse($user->especialidades ?? [] as $esp)
                    <span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:600;background:var(--bg);color:var(--t2);border:1px solid var(--border);">{{ $esp->nombre ?? $esp }}</span>
                @empty
                    <span style="font-size:13px;color:var(--t3);">—</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Alertas relacionadas --}}
    @if($user->userUserAlerts->count() > 0)
    <div style="background:var(--card); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow);">
        <div style="padding:14px 20px; border-bottom:1px solid var(--border); font-size:13px; font-weight:600; color:var(--t1);">
            {{ trans('cruds.userAlert.title') }}
        </div>
        @includeIf('admin.users.relationships.userUserAlerts', ['userAlerts' => $user->userUserAlerts])
    </div>
    @endif

</div>
@endsection
