<x-admin-layout title="Nuevo Plan de Hosting">

    <div style="margin-bottom:24px;">
        <a href="{{ route('hosting-plans.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver al listado</a>
    </div>

    <div class="card" style="max-width:640px; padding:28px;">
        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:24px;">Nuevo Plan de Hosting</h2>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:20px;">
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('hosting-plans.store') }}">
            @csrf
            @include('admin.hosting-plans.partials.form')

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Crear Plan</button>
                <a href="{{ route('hosting-plans.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</x-admin-layout>
