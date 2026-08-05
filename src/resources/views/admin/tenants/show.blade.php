<x-admin-layout title="Detalle del Cliente">

    <div class="mb-6">
        <a href="{{ route('tenants.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Volver al listado
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Datos del tenant --}}
        <div class="bg-white rounded shadow p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                Información del Cliente
            </h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">ID</dt>
                    <dd class="font-mono text-gray-700">{{ $tenant->id }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Nombre</dt>
                    <dd class="font-medium">{{ $tenant->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email</dt>
                    <dd>{{ $tenant->email ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Dominio</dt>
                    <dd class="text-blue-600">{{ $domain->domain ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Creado</dt>
                    <dd>{{ $tenant->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('tenants.edit', $tenant->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    Editar
                </a>
                <form method="POST" action="{{ route('tenants.destroy', $tenant->id) }}"
                      onsubmit="return confirm('¿Eliminar este tenant? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>

        {{-- Base de datos --}}
        <div class="bg-white rounded shadow p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                Base de Datos
            </h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Nombre BD</dt>
                    <dd class="font-mono text-gray-700">tenant{{ $tenant->id }}</dd>
                </div>
            </dl>
        </div>

    </div>

</x-admin-layout>