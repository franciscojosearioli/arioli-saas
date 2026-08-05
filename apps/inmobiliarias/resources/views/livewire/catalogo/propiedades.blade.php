@php
    $tipoLabel = fn (string $tipo) => match ($tipo) {
        'loteo' => 'Loteo', 'casa' => 'Casa', 'departamento' => 'Departamento', 'local' => 'Local',
        'oficina' => 'Oficina', 'galpon' => 'Galpón', 'campo' => 'Campo', 'cochera' => 'Cochera',
    };
    $estadoBadge = fn (string $estado) => match ($estado) {
        'disponible' => 'bg-green-100 text-green-800',
        'reservado' => 'bg-yellow-100 text-yellow-800',
        'vendido' => 'bg-gray-200 text-gray-700',
        'alquilado' => 'bg-blue-100 text-blue-800',
    };
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Propiedades</h2>

            @can('create', \App\Models\Propiedad::class)
                <x-primary-button wire:click="nueva">Nueva propiedad</x-primary-button>
            @endcan
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <div class="flex flex-wrap gap-3">
                <x-text-input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar por título..." class="w-full sm:w-64" />

                <select wire:model.live="filtroTipo" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <option value="">Todos los tipos</option>
                    <option value="loteo">Loteo</option>
                    <option value="casa">Casa</option>
                    <option value="departamento">Departamento</option>
                    <option value="local">Local</option>
                    <option value="oficina">Oficina</option>
                    <option value="galpon">Galpón</option>
                    <option value="campo">Campo</option>
                    <option value="cochera">Cochera</option>
                </select>

                <select wire:model.live="filtroEstado" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <option value="">Todos los estados</option>
                    <option value="disponible">Disponible</option>
                    <option value="reservado">Reservado</option>
                    <option value="vendido">Vendido</option>
                    <option value="alquilado">Alquilado</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase text-xs tracking-wider">
                            <th class="py-2 pr-4">Título</th>
                            <th class="py-2 pr-4">Tipo</th>
                            <th class="py-2 pr-4">Estado</th>
                            <th class="py-2 pr-4">Precio</th>
                            <th class="py-2 pr-4">Desarrollo</th>
                            <th class="py-2 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($propiedades as $propiedad)
                            <tr wire:key="propiedad-{{ $propiedad->id }}">
                                <td class="py-3 pr-4">
                                    <div class="font-medium text-gray-900">{{ $propiedad->titulo }}</div>
                                    <div class="text-gray-500 text-xs">
                                        {{ collect([$propiedad->barrio, $propiedad->ciudad, $propiedad->provincia])->filter()->implode(', ') ?: $propiedad->direccion }}
                                    </div>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $tipoLabel($propiedad->tipo) }}</td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $estadoBadge($propiedad->estado) }}">
                                        {{ ucfirst($propiedad->estado) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">
                                    @if ($propiedad->precio)
                                        {{ $propiedad->moneda }} {{ number_format($propiedad->precio, 0, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $propiedad->desarrollo?->nombre ?? '—' }}</td>
                                <td class="py-3 pr-4 text-right space-x-3">
                                    @can('update', $propiedad)
                                        <button wire:click="editar({{ $propiedad->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                    @endcan
                                    @can('delete', $propiedad)
                                        <button wire:click="eliminar({{ $propiedad->id }})" wire:confirm="¿Eliminar esta propiedad?" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">No hay propiedades cargadas todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $propiedades->links() }}
        </div>
    </div>

    <x-modal name="form-propiedad" :show="$modalAbierto" maxWidth="2xl" focusable>
        <form wire:submit="guardar" class="p-6 max-h-[80vh] overflow-y-auto">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editandoId ? 'Editar propiedad' : 'Nueva propiedad' }}
            </h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="titulo" value="Título" />
                    <x-text-input id="titulo" type="text" class="mt-1 block w-full" wire:model="titulo" />
                    <x-input-error :messages="$errors->get('titulo')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="tipo" value="Tipo" />
                        <select id="tipo" wire:model="tipo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="loteo">Loteo</option>
                            <option value="casa">Casa</option>
                            <option value="departamento">Departamento</option>
                            <option value="local">Local</option>
                            <option value="oficina">Oficina</option>
                            <option value="galpon">Galpón</option>
                            <option value="campo">Campo</option>
                            <option value="cochera">Cochera</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" wire:model="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="disponible">Disponible</option>
                            <option value="reservado">Reservado</option>
                            <option value="vendido">Vendido</option>
                            <option value="alquilado">Alquilado</option>
                        </select>
                        <x-input-error :messages="$errors->get('estado')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="moneda" value="Moneda" />
                        <select id="moneda" wire:model="moneda" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="ARS">ARS</option>
                            <option value="USD">USD</option>
                        </select>
                        <x-input-error :messages="$errors->get('moneda')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="precio" value="Precio" />
                        <x-text-input id="precio" type="number" step="0.01" class="mt-1 block w-full" wire:model="precio" />
                        <x-input-error :messages="$errors->get('precio')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="desarrollo_id" value="Desarrollo" />
                        <select id="desarrollo_id" wire:model="desarrollo_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Sin asignar</option>
                            @foreach ($desarrollos as $desarrollo)
                                <option value="{{ $desarrollo->id }}">{{ $desarrollo->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('desarrollo_id')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="propietario_id" value="Propietario" />
                    <select id="propietario_id" wire:model="propietario_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Sin asignar</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('propietario_id')" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <x-input-label for="ambientes" value="Ambientes" />
                        <x-text-input id="ambientes" type="number" class="mt-1 block w-full" wire:model="ambientes" />
                    </div>
                    <div>
                        <x-input-label for="dormitorios" value="Dormitorios" />
                        <x-text-input id="dormitorios" type="number" class="mt-1 block w-full" wire:model="dormitorios" />
                    </div>
                    <div>
                        <x-input-label for="banos" value="Baños" />
                        <x-text-input id="banos" type="number" class="mt-1 block w-full" wire:model="banos" />
                    </div>
                    <div>
                        <x-input-label for="cocheras" value="Cocheras" />
                        <x-text-input id="cocheras" type="number" class="mt-1 block w-full" wire:model="cocheras" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="superficie_cubierta" value="Superficie cubierta (m²)" />
                        <x-text-input id="superficie_cubierta" type="number" step="0.01" class="mt-1 block w-full" wire:model="superficie_cubierta" />
                    </div>
                    <div>
                        <x-input-label for="superficie_total" value="Superficie total (m²)" />
                        <x-text-input id="superficie_total" type="number" step="0.01" class="mt-1 block w-full" wire:model="superficie_total" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="manzana" value="Manzana" />
                        <x-text-input id="manzana" type="text" class="mt-1 block w-full" wire:model="manzana" />
                        <x-input-error :messages="$errors->get('manzana')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="numero_lote" value="Número de lote" />
                        <x-text-input id="numero_lote" type="text" class="mt-1 block w-full" wire:model="numero_lote" />
                    </div>
                </div>

                <div>
                    <x-input-label for="direccion" value="Dirección" />
                    <x-text-input id="direccion" type="text" class="mt-1 block w-full" wire:model="direccion" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                    <div>
                        <x-input-label for="barrio" value="Barrio" />
                        <x-text-input id="barrio" type="text" class="mt-1 block w-full" wire:model="barrio" />
                    </div>
                </div>

                <div>
                    <x-input-label for="descripcion" value="Descripción" />
                    <textarea id="descripcion" wire:model="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                </div>

                <div>
                    <x-input-label for="servicios" value="Servicios (separados por coma)" />
                    <x-text-input id="servicios" type="text" list="servicios-sugeridos" class="mt-1 block w-full" wire:model="servicios" placeholder="agua corriente, gas natural, ..." />
                    <datalist id="servicios-sugeridos">
                        @foreach (config('argentina.servicios') as $servicio)
                            <option value="{{ $servicio }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <x-input-label for="caracteristicas_destacadas" value="Características destacadas (separadas por coma)" />
                    <x-text-input id="caracteristicas_destacadas" type="text" class="mt-1 block w-full" wire:model="caracteristicas_destacadas" placeholder="a estrenar, doble ingreso, ..." />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="cerrarModal">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Guardar</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
