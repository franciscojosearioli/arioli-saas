@php
    $estadoBadge = fn (string $estado) => match ($estado) {
        'pendiente' => 'bg-yellow-100 text-yellow-800',
        'liquidada' => 'bg-green-100 text-green-800',
    };
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Comisiones</h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <select wire:model.live="filtroEstado" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="liquidada">Liquidada</option>
            </select>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 uppercase text-xs tracking-wider">
                            <th class="py-2 pr-4">Operación</th>
                            <th class="py-2 pr-4">Agente</th>
                            <th class="py-2 pr-4">%</th>
                            <th class="py-2 pr-4">Monto</th>
                            <th class="py-2 pr-4">Estado</th>
                            <th class="py-2 pr-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($comisiones as $comision)
                            <tr wire:key="comision-{{ $comision->id }}">
                                <td class="py-3 pr-4">
                                    <a href="{{ route('operaciones.show', $comision->operacion) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">
                                        {{ $comision->operacion->propiedad->titulo }}
                                    </a>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $comision->agente->name }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $comision->porcentaje }}%</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $comision->moneda }} {{ number_format($comision->monto, 0, ',', '.') }}</td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $estadoBadge($comision->estado) }}">
                                        {{ ucfirst($comision->estado) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-right">
                                    @can('update', $comision)
                                        @if ($comision->estado === 'pendiente')
                                            <button wire:click="liquidar({{ $comision->id }})" wire:confirm="¿Marcar esta comisión como liquidada?" class="text-indigo-600 hover:text-indigo-900">Liquidar</button>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">No hay comisiones todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $comisiones->links() }}
        </div>
    </div>
</div>
