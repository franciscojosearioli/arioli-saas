@extends('layouts.admin')
@section('title', trans('global.create') . ' ' . trans('cruds.role.title_singular'))

@section('content')
<div style="max-width:680px; display:flex; flex-direction:column; gap:20px;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ route('admin.roles.index') }}"
           style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; border:1px solid var(--border); color:var(--t2); text-decoration:none; flex-shrink:0;"
           onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--t1); margin:0; letter-spacing:-.02em;">
                {{ trans('global.create') }} {{ trans('cruds.role.title_singular') }}
            </h1>
            <p style="font-size:13px; color:var(--t2); margin:3px 0 0;">Definí el nombre y los permisos del rol</p>
        </div>
    </div>

    {{-- Form card --}}
    <div style="background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:24px; box-shadow:var(--shadow);">
        <form method="POST" action="{{ route('admin.roles.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:flex; flex-direction:column; gap:18px;">

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--t1); margin-bottom:6px;">
                        {{ trans('cruds.role.fields.title') }} <span style="color:var(--err);">*</span>
                    </label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                           type="text" name="title" id="title" value="{{ old('title') }}" required
                           style="border-radius:8px;">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--t1); margin-bottom:6px;">
                        {{ trans('cruds.role.fields.permissions') }} <span style="color:var(--err);">*</span>
                    </label>
                    <select class="form-control select2 {{ $errors->has('permissions') ? 'is-invalid' : '' }}"
                            name="permissions[]" id="permissions" multiple required style="border-radius:8px;">
                        @foreach($permissions as $id => $permission)
                            <option value="{{ $id }}" {{ in_array($id, old('permissions', [])) ? 'selected' : '' }}>{{ $permission }}</option>
                        @endforeach
                    </select>
                    @error('permissions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div style="display:flex; gap:10px; padding-top:4px;">
                    <button type="submit"
                            style="display:inline-flex; align-items:center; gap:6px; padding:9px 20px; border-radius:9px; font-size:13px; font-weight:600; border:none; cursor:pointer; background:var(--accent); color:#fff;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ trans('global.save') }}
                    </button>
                    <a href="{{ route('admin.roles.index') }}"
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
