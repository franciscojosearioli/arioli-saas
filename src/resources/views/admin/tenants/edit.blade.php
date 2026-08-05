<x-admin-layout title="Editar Cliente">

    <div class="mb-6">
        <a href="{{ route('tenants.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Volver al listado
        </a>
    </div>

    <div class="bg-white rounded shadow p-6 max-w-2xl">

        @if($errors->any())
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenants.update', $tenant->id) }}">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre del cliente
                </label>
                <input type="text" name="name"
                       value="{{ old('name', $tenant->name ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email de contacto
                </label>
                <input type="email" name="email"
                       value="{{ old('email', $tenant->email ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Subdominio
                </label>
                <input type="text"
                       value="{{ $domain->domain ?? '-' }}"
                       class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed"
                       disabled>
                <p class="text-xs text-gray-400 mt-1">El subdominio no puede modificarse.</p>
            </div>

            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_custom" value="1"
                           {{ old('is_custom', $tenant->is_custom) ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                    <div>
                        <span class="block text-sm font-semibold text-amber-800">Cliente con desarrollo personalizado</span>
                        <span class="block text-xs text-amber-700 mt-0.5">
                            Al activar esta opción, el cliente queda <strong>fuera del ciclo de actualizaciones automáticas</strong>.
                            Sus actualizaciones deben aplicarse manualmente para evitar conflictos con el código personalizado.
                        </span>
                    </div>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded text-sm">
                    Guardar cambios
                </button>
                <a href="{{ route('tenants.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded text-sm">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

</x-admin-layout>