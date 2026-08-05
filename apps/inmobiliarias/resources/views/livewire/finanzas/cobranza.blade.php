@php
    $estadoBadge = fn (string $estado) => match ($estado) {
        'pendiente' => 'bg-gray-100 text-gray-700',
        'parcial' => 'bg-yellow-100 text-yellow-800',
        'pagada' => 'bg-green-100 text-green-800',
        'vencida' => 'bg-red-100 text-red-800',
    };
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cobranza</h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <select wire:model.live="filtroEstado" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="parcial">Parcial</option>
                <option value="pagada">Pagada</option>
                <option value="vencida">Vencida</option>
            </select>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase text-xs tracking-wider">
                            <th class="py-2 pr-4">Operación</th>
                            <th class="py-2 pr-4">Cuota</th>
                            <th class="py-2 pr-4">Vencimiento</th>
                            <th class="py-2 pr-4">Monto</th>
                            <th class="py-2 pr-4">Estado</th>
                            <th class="py-2 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($cuotas as $cuota)
                            <tr wire:key="cuota-{{ $cuota->id }}">
                                <td class="py-3 pr-4">
                                    <a href="{{ route('operaciones.show', $cuota->operacion) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">
                                        {{ $cuota->operacion->propiedad->titulo }}
                                    </a>
                                    <div class="text-gray-500 text-xs">Agente: {{ $cuota->operacion->agente?->name ?? '—' }}</div>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">#{{ $cuota->numero }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $cuota->moneda }} {{ number_format($cuota->monto, 0, ',', '.') }}</td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $estadoBadge($cuota->estado) }}">
                                        {{ ucfirst($cuota->estado) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-right">
                                    @if ($cuota->estado !== 'pagada')
                                        @can('create', \App\Models\Pago::class)
                                            <button wire:click="registrarPago({{ $cuota->id }})" class="text-indigo-600 hover:text-indigo-900">Registrar pago</button>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">No hay cuotas para este filtro.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $cuotas->links() }}
        </div>
    </div>

    <x-modal name="form-pago" :show="$modalAbierto" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Registrar pago</h2>
            @if ($cuota_referencia)
                <p class="text-sm text-gray-500 mt-1">{{ $cuota_referencia }}</p>
            @endif

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="monto" value="Monto" />
                    <x-text-input id="monto" type="number" step="0.01" class="mt-1 block w-full" wire:model="monto" />
                    <x-input-error :messages="$errors->get('monto')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="fecha" value="Fecha" />
                    <x-text-input id="fecha" type="date" class="mt-1 block w-full" wire:model="fecha" />
                    <x-input-error :messages="$errors->get('fecha')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="medio_pago" value="Medio de pago" />
                    <select id="medio_pago" wire:model="medio_pago" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="cheque">Cheque</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="otro">Otro</option>
                    </select>
                    <x-input-error :messages="$errors->get('medio_pago')" class="mt-1" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="cerrarModal">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Registrar</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
