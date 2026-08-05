@php
    $tipoLabel = fn (string $tipo) => match ($tipo) {
        'venta' => 'Venta', 'alquiler' => 'Alquiler', 'reserva' => 'Reserva',
    };
    $estadoBadge = fn (string $estado) => match ($estado) {
        'abierta' => 'bg-blue-100 text-blue-800',
        'cerrada' => 'bg-green-100 text-green-800',
        'cancelada' => 'bg-gray-200 text-gray-700',
    };
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Operaciones</h2>

            @can('create', \App\Models\Operacion::class)
                <x-primary-button wire:click="nueva">Nueva operación</x-primary-button>
            @endcan
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <div class="flex flex-wrap gap-3">
                <select wire:model.live="filtroTipo" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <option value="">Todos los tipos</option>
                    <option value="venta">Venta</option>
                    <option value="alquiler">Alquiler</option>
                    <option value="reserva">Reserva</option>
                </select>

                <select wire:model.live="filtroEstado" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <option value="">Todos los estados</option>
                    <option value="abierta">Abierta</option>
                    <option value="cerrada">Cerrada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase text-xs tracking-wider">
                            <th class="py-2 pr-4">Propiedad</th>
                            <th class="py-2 pr-4">Tipo</th>
                            <th class="py-2 pr-4">Estado</th>
                            <th class="py-2 pr-4">Monto</th>
                            <th class="py-2 pr-4">Agente</th>
                            <th class="py-2 pr-4">Inicio</th>
                            <th class="py-2 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($operaciones as $operacion)
                            <tr wire:key="operacion-{{ $operacion->id }}">
                                <td class="py-3 pr-4 font-medium text-gray-900">{{ $operacion->propiedad->titulo }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $tipoLabel($operacion->tipo) }}</td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $estadoBadge($operacion->estado) }}">
                                        {{ ucfirst($operacion->estado) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">
                                    @if ($operacion->monto)
                                        {{ $operacion->moneda }} {{ number_format($operacion->monto, 0, ',', '.') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $operacion->agente?->name ?? '—' }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $operacion->fecha_inicio->format('d/m/Y') }}</td>
                                <td class="py-3 pr-4 text-right">
                                    <a href="{{ route('panel.operaciones.show', $operacion) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-gray-500">No hay operaciones cargadas todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $operaciones->links() }}
        </div>
    </div>

    <x-modal name="form-operacion" :show="$modalAbierto" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Nueva operación</h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="propiedad_id" value="Propiedad" />
                    <select id="propiedad_id" wire:model="propiedad_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Seleccionar...</option>
                        @foreach ($propiedades as $propiedad)
                            <option value="{{ $propiedad->id }}">{{ $propiedad->titulo }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('propiedad_id')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="tipo" value="Tipo" />
                        <select id="tipo" wire:model="tipo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="venta">Venta</option>
                            <option value="alquiler">Alquiler</option>
                            <option value="reserva">Reserva</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="fecha_inicio" value="Fecha de inicio" />
                        <x-text-input id="fecha_inicio" type="date" class="mt-1 block w-full" wire:model="fecha_inicio" />
                        <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="monto" value="Monto" />
                        <x-text-input id="monto" type="number" step="0.01" class="mt-1 block w-full" wire:model="monto" />
                        <x-input-error :messages="$errors->get('monto')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="moneda" value="Moneda" />
                        <select id="moneda" wire:model="moneda" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="ARS">ARS</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                </div>

                @if (auth()->user()->hasRole('admin'))
                    <div>
                        <x-input-label for="agente_id" value="Agente asignado" />
                        <select id="agente_id" wire:model="agente_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Sin asignar</option>
                            @foreach ($agentes as $agente)
                                <option value="{{ $agente->id }}">{{ $agente->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('agente_id')" class="mt-1" />
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="cerrarModal">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Crear</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
