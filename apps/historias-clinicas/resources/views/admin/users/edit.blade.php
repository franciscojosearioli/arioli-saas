@extends('layouts.admin')
@section('title', trans('global.edit') . ' ' . trans('cruds.user.title_singular'))

@section('content')
<div style="max-width:680px; display:flex; flex-direction:column; gap:20px;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ route('admin.users.index') }}"
           style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; border:1px solid var(--border); color:var(--t2); text-decoration:none; flex-shrink:0; transition:background .15s;"
           onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--t1); margin:0; letter-spacing:-.02em;">
                {{ trans('global.edit') }} {{ trans('cruds.user.title_singular') }}
            </h1>
            <p style="font-size:13px; color:var(--t2); margin:3px 0 0;">{{ $user->name }}</p>
        </div>
    </div>

    {{-- Demo banner --}}
    @if(app(\App\Services\License\LicenseClientInterface::class)->isDemo())
    <div style="background:#fef3c7; border:1px solid #f59e0b; border-radius:10px; padding:12px 16px; font-size:13px; color:#92400e; display:flex; align-items:center; gap:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        <span><strong>Modo Demo:</strong> Los cambios no se guardarán.</span>
    </div>
    @endif

    {{-- Form card --}}
    <div style="background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:24px; box-shadow:var(--shadow);">
        <form method="POST" action="{{ route('admin.users.update', [$user->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div style="display:flex; flex-direction:column; gap:18px;">

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--t1); margin-bottom:6px;">
                        {{ trans('cruds.user.fields.name') }} <span style="color:var(--err);">*</span>
                    </label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           style="border-radius:8px;">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--t1); margin-bottom:6px;">
                        {{ trans('cruds.user.fields.email') }} <span style="color:var(--err);">*</span>
                    </label>
                    <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           style="border-radius:8px;">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--t1); margin-bottom:6px;">
                        {{ trans('cruds.user.fields.password') }}
                        <span style="font-size:12px; font-weight:400; color:var(--t2);">(dejar en blanco para no cambiar)</span>
                    </label>
                    <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           type="password" name="password" id="password"
                           style="border-radius:8px;">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--t1); margin-bottom:6px;">
                        {{ trans('cruds.user.fields.roles') }} <span style="color:var(--err);">*</span>
                    </label>
                    <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}"
                            name="roles[]" id="roles" multiple required style="border-radius:8px;">
                        @foreach($roles as $id => $role)
                            <option value="{{ $id }}" {{ (in_array($id, old('roles', [])) || $user->roles->contains($id)) ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('roles') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--t1); margin-bottom:6px;">
                        Especialidades <span style="font-size:12px; font-weight:400; color:var(--t2);">(opcional)</span>
                    </label>
                    <select class="form-control select2 {{ $errors->has('especialidades') ? 'is-invalid' : '' }}"
                            name="especialidades[]" id="especialidades" multiple style="border-radius:8px;">
                        @foreach($especialidades as $id => $nombre)
                            <option value="{{ $id }}"
                                {{ (in_array($id, old('especialidades', [])) || $user->especialidades->contains($id)) ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex; gap:10px; padding-top:4px;">
                    <button type="submit"
                            style="display:inline-flex; align-items:center; gap:6px; padding:9px 20px; border-radius:9px; font-size:13px; font-weight:600; border:none; cursor:pointer; background:var(--accent); color:#fff; transition:background .15s;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ trans('global.save') }}
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       style="display:inline-flex; align-items:center; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:500; border:1px solid var(--border); background:var(--bg); color:var(--t2); text-decoration:none;">
                        {{ trans('global.cancel') ?? 'Cancelar' }}
                    </a>
                </div>

            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(function () { $('.select2').select2(); });
</script>
@endpush
