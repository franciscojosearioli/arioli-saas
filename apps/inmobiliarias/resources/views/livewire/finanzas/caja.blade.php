<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Caja</h2>

            @can('create', \App\Models\ArqueoCaja::class)
                <x-primary-button wire:click="nuevo">Cerrar caja del día</x-primary-button>
            @endcan
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase text-xs tracking-wider">
                            <th class="py-2 pr-4">Fecha</th>
                            <th class="py-2 pr-4">Esperado</th>
                            <th class="py-2 pr-4">Contado</th>
                            <th class="py-2 pr-4">Diferencia</th>
                            <th class="py-2 pr-4">Cerrado por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($arqueos as $arqueo)
                            <tr wire:key="arqueo-{{ $arqueo->id }}">
                                <td class="py-3 pr-4 font-medium text-gray-900">{{ $arqueo->fecha->format('d/m/Y') }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ number_format($arqueo->monto_esperado, 2, ',', '.') }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ number_format($arqueo->monto_contado, 2, ',', '.') }}</td>
                                <td class="py-3 pr-4 {{ $arqueo->diferencia() === '0.00' ? 'text-gray-600' : 'text-red-600 font-medium' }}">
                                    {{ number_format((float) $arqueo->diferencia(), 2, ',', '.') }}
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $arqueo->cerradoPor?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Todavía no se cerró ninguna caja.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $arqueos->links() }}
        </div>
    </div>

    <x-modal name="form-arqueo" :show="$modalAbierto" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Cerrar caja del día</h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="fecha" value="Fecha" />
                    <x-text-input id="fecha" type="date" class="mt-1 block w-full" wire:model="fecha" />
                    <x-input-error :messages="$errors->get('fecha')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="monto_contado" value="Monto contado" />
                    <x-text-input id="monto_contado" type="number" step="0.01" class="mt-1 block w-full" wire:model="monto_contado" />
                    <x-input-error :messages="$errors->get('monto_contado')" class="mt-1" />
                    <p class="text-xs text-gray-500 mt-1">Lo esperado se calcula solo con los pagos en efectivo de esa fecha.</p>
                </div>
                <div>
                    <x-input-label for="notas" value="Notas" />
                    <textarea id="notas" wire:model="notas" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="cerrarModal">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Cerrar caja</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
