@php
    $tipoLabel = fn (string $tipo) => match ($tipo) {
        'loteo' => 'Loteo',
        'barrio_cerrado' => 'Barrio cerrado',
        'edificio' => 'Edificio',
        'emprendimiento' => 'Emprendimiento',
    };
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Desarrollos</h2>

            @can('create', \App\Models\Desarrollo::class)
                <x-primary-button wire:click="nuevo">Nuevo desarrollo</x-primary-button>
            @endcan
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <x-text-input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre..." class="w-full sm:w-80" />

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase text-xs tracking-wider">
                            <th class="py-2 pr-4">Nombre</th>
                            <th class="py-2 pr-4">Tipo</th>
                            <th class="py-2 pr-4">Constructora</th>
                            <th class="py-2 pr-4">Ubicación</th>
                            <th class="py-2 pr-4">Propiedades</th>
                            <th class="py-2 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($desarrollos as $desarrollo)
                            <tr wire:key="desarrollo-{{ $desarrollo->id }}">
                                <td class="py-3 pr-4 font-medium text-gray-900">{{ $desarrollo->nombre }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $tipoLabel($desarrollo->tipo) }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $desarrollo->constructora?->nombre ?? '—' }}</td>
                                <td class="py-3 pr-4 text-gray-600">
                                    {{ collect([$desarrollo->barrio, $desarrollo->ciudad, $desarrollo->provincia])->filter()->implode(', ') ?: '—' }}
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $desarrollo->propiedades_count }}</td>
                                <td class="py-3 pr-4 text-right space-x-3">
                                    @can('update', $desarrollo)
                                        <button wire:click="editar({{ $desarrollo->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                    @endcan
                                    @can('delete', $desarrollo)
                                        <button wire:click="eliminar({{ $desarrollo->id }})" wire:confirm="¿Eliminar este desarrollo?" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">No hay desarrollos cargados todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $desarrollos->links() }}
        </div>
    </div>

    <x-modal name="form-desarrollo" :show="$modalAbierto" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editandoId ? 'Editar desarrollo' : 'Nuevo desarrollo' }}
            </h2>

            <div class="mt-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" type="text" class="mt-1 block w-full" wire:model="nombre" />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="tipo" value="Tipo" />
                        <select id="tipo" wire:model="tipo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="loteo">Loteo</option>
                            <option value="barrio_cerrado">Barrio cerrado</option>
                            <option value="edificio">Edificio</option>
                            <option value="emprendimiento">Emprendimiento</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="constructora_id" value="Constructora" />
                    <select id="constructora_id" wire:model="constructora_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Sin asignar</option>
                        @foreach ($constructoras as $constructora)
                            <option value="{{ $constructora->id }}">{{ $constructora->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('constructora_id')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="descripcion" value="Descripción" />
                    <textarea id="descripcion" wire:model="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    <x-input-error :messages="$errors->get('descripcion')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="provincia" value="Provincia" />
                        <x-text-input id="provincia" type="text" class="mt-1 block w-full" wire:model="provincia" />
                        <x-input-error :messages="$errors->get('provincia')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="ciudad" value="Ciudad" />
                        <x-text-input id="ciudad" type="text" class="mt-1 block w-full" wire:model="ciudad" />
                        <x-input-error :messages="$errors->get('ciudad')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="barrio" value="Barrio" />
                        <x-text-input id="barrio" type="text" class="mt-1 block w-full" wire:model="barrio" />
                        <x-input-error :messages="$errors->get('barrio')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="plano_maestro" value="Plano maestro (URL)" />
                    <x-text-input id="plano_maestro" type="text" class="mt-1 block w-full" wire:model="plano_maestro" />
                    <x-input-error :messages="$errors->get('plano_maestro')" class="mt-1" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="cerrarModal">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Guardar</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
