@php
    $origenLabel = fn (string $origen) => match ($origen) {
        'storefront' => 'Storefront', 'formulario' => 'Formulario web', 'whatsapp' => 'WhatsApp',
        'referido' => 'Referido', 'otro' => 'Otro',
    };
    $estadoBadge = fn (string $estado) => match ($estado) {
        'nuevo' => 'bg-blue-100 text-blue-800',
        'contactado' => 'bg-yellow-100 text-yellow-800',
        'calificado' => 'bg-purple-100 text-purple-800',
        'convertido' => 'bg-green-100 text-green-800',
        'perdido' => 'bg-gray-200 text-gray-700',
    };
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leads</h2>

            @can('create', \App\Models\Lead::class)
                <x-primary-button wire:click="nuevo">Nuevo lead</x-primary-button>
            @endcan
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <select wire:model.live="filtroEstado" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                <option value="">Todos los estados</option>
                <option value="nuevo">Nuevo</option>
                <option value="contactado">Contactado</option>
                <option value="calificado">Calificado</option>
                <option value="convertido">Convertido</option>
                <option value="perdido">Perdido</option>
            </select>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase text-xs tracking-wider">
                            <th class="py-2 pr-4">Nombre</th>
                            <th class="py-2 pr-4">Origen</th>
                            <th class="py-2 pr-4">Estado</th>
                            <th class="py-2 pr-4">Agente</th>
                            <th class="py-2 pr-4">Propiedad de interés</th>
                            <th class="py-2 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($leads as $lead)
                            <tr wire:key="lead-{{ $lead->id }}">
                                <td class="py-3 pr-4">
                                    <div class="font-medium text-gray-900">{{ $lead->nombre }}</div>
                                    <div class="text-gray-500 text-xs">
                                        {{ $lead->email }}
                                        @if ($lead->email && $lead->telefono) · @endif
                                        {{ $lead->telefono }}
                                    </div>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $origenLabel($lead->origen) }}</td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $estadoBadge($lead->estado) }}">
                                        {{ ucfirst($lead->estado) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $lead->agente?->name ?? '—' }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $lead->propiedad?->titulo ?? '—' }}</td>
                                <td class="py-3 pr-4 text-right space-x-3">
                                    @can('update', $lead)
                                        <button wire:click="editar({{ $lead->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                    @endcan
                                    @can('delete', $lead)
                                        <button wire:click="eliminar({{ $lead->id }})" wire:confirm="¿Eliminar este lead?" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">No hay leads cargados todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $leads->links() }}
        </div>
    </div>

    <x-modal name="form-lead" :show="$modalAbierto" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editandoId ? 'Editar lead' : 'Nuevo lead' }}
            </h2>

            <div class="mt-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" type="text" class="mt-1 block w-full" wire:model="nombre" />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="origen" value="Origen" />
                        <select id="origen" wire:model="origen" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="storefront">Storefront</option>
                            <option value="formulario">Formulario web</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="referido">Referido</option>
                            <option value="otro">Otro</option>
                        </select>
                        <x-input-error :messages="$errors->get('origen')" class="mt-1" />
                    </div>
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" wire:model="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="nuevo">Nuevo</option>
                            <option value="contactado">Contactado</option>
                            <option value="calificado">Calificado</option>
                            <option value="convertido">Convertido</option>
                            <option value="perdido">Perdido</option>
                        </select>
                        <x-input-error :messages="$errors->get('estado')" class="mt-1" />
                    </div>
                    <div>
                        @if (auth()->user()->hasRole('admin'))
                            <x-input-label for="agente_id" value="Agente asignado" />
                            <select id="agente_id" wire:model="agente_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Sin asignar</option>
                                @foreach ($agentes as $agente)
                                    <option value="{{ $agente->id }}">{{ $agente->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('agente_id')" class="mt-1" />
                        @else
                            <x-input-label value="Agente asignado" />
                            <p class="mt-1 text-sm text-gray-500 py-2">Vos ({{ auth()->user()->name }})</p>
                        @endif
                    </div>
                </div>

                <div>
                    <x-input-label for="propiedad_id" value="Propiedad de interés" />
                    <select id="propiedad_id" wire:model="propiedad_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Sin especificar</option>
                        @foreach ($propiedades as $propiedad)
                            <option value="{{ $propiedad->id }}">{{ $propiedad->titulo }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('propiedad_id')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="cliente_id" value="Vincular a cliente existente" />
                    <select id="cliente_id" wire:model="cliente_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Sin vincular</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cliente_id')" class="mt-1" />
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
