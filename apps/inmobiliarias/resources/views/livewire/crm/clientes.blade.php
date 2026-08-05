<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clientes</h2>

            @can('create', \App\Models\Cliente::class)
                <x-primary-button wire:click="nuevo">Nuevo cliente</x-primary-button>
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
                            <th class="py-2 pr-4">Contacto</th>
                            <th class="py-2 pr-4">Propiedades</th>
                            <th class="py-2 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($clientes as $cliente)
                            <tr wire:key="cliente-{{ $cliente->id }}">
                                <td class="py-3 pr-4">
                                    <div class="font-medium text-gray-900">{{ $cliente->nombre }}</div>
                                    @if ($cliente->documento)
                                        <div class="text-gray-500 text-xs">{{ $cliente->documento }}</div>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $cliente->tipo_persona === 'juridica' ? 'Jurídica' : 'Física' }}</td>
                                <td class="py-3 pr-4 text-gray-600">
                                    {{ $cliente->email }}
                                    @if ($cliente->email && $cliente->telefono) · @endif
                                    {{ $cliente->telefono }}
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $cliente->propiedades_count }}</td>
                                <td class="py-3 pr-4 text-right space-x-3">
                                    @can('update', $cliente)
                                        <button wire:click="editar({{ $cliente->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                    @endcan
                                    @can('delete', $cliente)
                                        <button wire:click="eliminar({{ $cliente->id }})" wire:confirm="¿Eliminar este cliente?" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">No hay clientes cargados todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $clientes->links() }}
        </div>
    </div>

    <x-modal name="form-cliente" :show="$modalAbierto" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editandoId ? 'Editar cliente' : 'Nuevo cliente' }}
            </h2>

            <div class="mt-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" type="text" class="mt-1 block w-full" wire:model="nombre" />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="tipo_persona" value="Tipo de persona" />
                        <select id="tipo_persona" wire:model="tipo_persona" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="fisica">Física</option>
                            <option value="juridica">Jurídica</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo_persona')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="documento" value="Documento (DNI / CUIT)" />
                    <x-text-input id="documento" type="text" class="mt-1 block w-full" wire:model="documento" />
                    <x-input-error :messages="$errors->get('documento')" class="mt-1" />
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
                    <x-input-label for="direccion" value="Dirección" />
                    <x-text-input id="direccion" type="text" class="mt-1 block w-full" wire:model="direccion" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="provincia" value="Provincia" />
                        <select id="provincia" wire:model="provincia" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Sin especificar</option>
                            @foreach (config('argentina.provincias') as $provincia)
                                <option value="{{ $provincia }}">{{ $provincia }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="ciudad" value="Ciudad" />
                        <x-text-input id="ciudad" type="text" class="mt-1 block w-full" wire:model="ciudad" />
                    </div>
                </div>

                <div>
                    <x-input-label for="notas" value="Notas" />
                    <textarea id="notas" wire:model="notas" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="cerrarModal">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Guardar</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
