<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Constructoras</h2>

            @can('create', \App\Models\Constructora::class)
                <x-primary-button wire:click="nueva">Nueva constructora</x-primary-button>
            @endcan
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <x-text-input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre..." class="w-full sm:w-80" />

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase text-xs tracking-wider">
                            <th class="py-2 pr-4">Nombre</th>
                            <th class="py-2 pr-4">Contacto</th>
                            <th class="py-2 pr-4">Desarrollos</th>
                            <th class="py-2 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($constructoras as $constructora)
                            <tr wire:key="constructora-{{ $constructora->id }}">
                                <td class="py-3 pr-4">
                                    <div class="font-medium text-gray-900">{{ $constructora->nombre }}</div>
                                    @if ($constructora->descripcion)
                                        <div class="text-gray-500 text-xs">{{ Str::limit($constructora->descripcion, 80) }}</div>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-gray-600">
                                    {{ $constructora->email }}
                                    @if ($constructora->email && $constructora->telefono) · @endif
                                    {{ $constructora->telefono }}
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $constructora->desarrollos_count }}</td>
                                <td class="py-3 pr-4 text-right space-x-3">
                                    @can('update', $constructora)
                                        <button wire:click="editar({{ $constructora->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                    @endcan
                                    @can('delete', $constructora)
                                        <button wire:click="eliminar({{ $constructora->id }})" wire:confirm="¿Eliminar esta constructora?" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-500">No hay constructoras cargadas todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $constructoras->links() }}
        </div>
    </div>

    <x-modal name="form-constructora" :show="$modalAbierto" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editandoId ? 'Editar constructora' : 'Nueva constructora' }}
            </h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="nombre" value="Nombre" />
                    <x-text-input id="nombre" type="text" class="mt-1 block w-full" wire:model="nombre" />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="descripcion" value="Descripción" />
                    <textarea id="descripcion" wire:model="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    <x-input-error :messages="$errors->get('descripcion')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" type="email" class="mt-1 block w-full" wire:model="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="telefono" value="Teléfono" />
                        <x-text-input id="telefono" type="text" class="mt-1 block w-full" wire:model="telefono" />
                        <x-input-error :messages="$errors->get('telefono')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="logo" value="Logo (URL)" />
                    <x-text-input id="logo" type="text" class="mt-1 block w-full" wire:model="logo" />
                    <x-input-error :messages="$errors->get('logo')" class="mt-1" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="cerrarModal">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Guardar</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
