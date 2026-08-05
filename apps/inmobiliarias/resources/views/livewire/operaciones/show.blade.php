@php
    $tipoLabel = fn (string $tipo) => match ($tipo) {
        'venta' => 'Venta', 'alquiler' => 'Alquiler', 'reserva' => 'Reserva',
    };
    $estadoBadge = fn (string $estado) => match ($estado) {
        'abierta' => 'bg-blue-100 text-blue-800',
        'cerrada', 'pagada', 'firmado' => 'bg-green-100 text-green-800',
        'cancelada' => 'bg-gray-200 text-gray-700',
        'parcial', 'reservado' => 'bg-yellow-100 text-yellow-800',
        'vencida' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-600',
    };
    $rolLabel = fn (string $rol) => match ($rol) {
        'comprador' => 'Comprador', 'vendedor' => 'Vendedor', 'locador' => 'Locador',
        'locatario' => 'Locatario', 'garante' => 'Garante',
    };
@endphp

<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div>
            <a href="{{ route('operaciones.index') }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-900">&larr; Operaciones</a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800">{{ $operacion->propiedad->titulo }}</h2>
                    <div class="mt-1 flex items-center gap-2 text-sm text-gray-600">
                        <span>{{ $tipoLabel($operacion->tipo) }}</span>
                        <span>·</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $estadoBadge($operacion->estado) }}">
                            {{ ucfirst($operacion->estado) }}
                        </span>
                        @if ($operacion->monto)
                            <span>· {{ $operacion->moneda }} {{ number_format($operacion->monto, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        Agente: {{ $operacion->agente?->name ?? '—' }} · Inicio: {{ $operacion->fecha_inicio->format('d/m/Y') }}
                        @if ($operacion->fecha_cierre)
                            · Cierre: {{ $operacion->fecha_cierre->format('d/m/Y') }}
                        @endif
                    </div>
                </div>

                @can('update', $operacion)
                    @if ($operacion->estado === 'abierta')
                        <div class="flex gap-2">
                            <x-secondary-button wire:click="cancelar" wire:confirm="¿Cancelar esta operación?">Cancelar</x-secondary-button>
                            <x-primary-button wire:click="cerrar" wire:confirm="¿Cerrar esta operación? Se generará la comisión del agente.">Cerrar operación</x-primary-button>
                        </div>
                    @endif
                @endcan
            </div>
        </div>

        {{-- Partes --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-medium text-gray-900">Partes</h3>
                @can('update', $operacion)
                    <x-secondary-button wire:click="abrirModalParte">Agregar parte</x-secondary-button>
                @endcan
            </div>

            @forelse ($operacion->partes as $parte)
                <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-3 first:border-t-0 first:pt-0">
                    <span class="text-gray-900">{{ $parte->nombre }}</span>
                    <span class="text-gray-500">{{ $rolLabel($parte->pivot->rol) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">Todavía no se asignaron partes.</p>
            @endforelse
        </div>

        {{-- Cuotas --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-medium text-gray-900">Plan de cuotas</h3>
                @can('update', $operacion)
                    @if ($operacion->cuotas->isEmpty())
                        <x-secondary-button wire:click="abrirModalPlan">Generar plan de cuotas</x-secondary-button>
                    @endif
                @endcan
            </div>

            @if ($operacion->cuotas->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 uppercase text-xs tracking-wider">
                                <th class="py-2 pr-4">#</th>
                                <th class="py-2 pr-4">Vencimiento</th>
                                <th class="py-2 pr-4">Monto</th>
                                <th class="py-2 pr-4">Pagado</th>
                                <th class="py-2 pr-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($operacion->cuotas->sortBy('numero') as $cuota)
                                <tr>
                                    <td class="py-2 pr-4 text-gray-600">{{ $cuota->numero }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $cuota->moneda }} {{ number_format($cuota->monto, 0, ',', '.') }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ number_format((float) $cuota->montoPagado(), 0, ',', '.') }}</td>
                                    <td class="py-2 pr-4">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $estadoBadge($cuota->estado) }}">
                                            {{ ucfirst($cuota->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-500">Registrar cobros se hace desde Cobranza, no acá.</p>
            @else
                <p class="text-sm text-gray-500">Todavía no se generó un plan de cuotas.</p>
            @endif
        </div>

        {{-- Contrato --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-medium text-gray-900">Contrato</h3>
                @can('update', $operacion)
                    @if (! $contratoActivo)
                        <x-secondary-button wire:click="abrirModalContrato">Generar contrato</x-secondary-button>
                    @elseif ($contratoActivo->estado === 'firmado')
                        <x-secondary-button wire:click="renovarContrato({{ $contratoActivo->id }})">Renovar</x-secondary-button>
                    @endif
                @endcan
            </div>

            @if ($contratoActivo)
                <div class="text-sm text-gray-600">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $estadoBadge($contratoActivo->estado) }}">
                        {{ ucfirst($contratoActivo->estado) }}
                    </span>
                    <span class="ml-2">
                        {{ $contratoActivo->fecha_inicio->format('d/m/Y') }}
                        @if ($contratoActivo->fecha_fin)
                            — {{ $contratoActivo->fecha_fin->format('d/m/Y') }}
                        @endif
                    </span>
                </div>
            @else
                <p class="text-sm text-gray-500">Todavía no hay contrato generado.</p>
            @endif
        </div>

        {{-- Comisión --}}
        @if ($operacion->comision)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-2">
                <h3 class="font-medium text-gray-900">Comisión</h3>
                <div class="text-sm text-gray-600">
                    {{ $operacion->comision->moneda }} {{ number_format($operacion->comision->monto, 0, ',', '.') }}
                    ({{ $operacion->comision->porcentaje }}%) —
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $estadoBadge($operacion->comision->estado) }}">
                        {{ ucfirst($operacion->comision->estado) }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: agregar parte --}}
    <x-modal name="form-parte" :show="$modalParteAbierto" focusable>
        <form wire:submit="asignarParte" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Agregar parte</h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="parte_cliente_id" value="Cliente" />
                    <select id="parte_cliente_id" wire:model="parte_cliente_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Seleccionar...</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('parte_cliente_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="parte_rol" value="Rol" />
                    <select id="parte_rol" wire:model="parte_rol" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="comprador">Comprador</option>
                        <option value="vendedor">Vendedor</option>
                        <option value="locador">Locador</option>
                        <option value="locatario">Locatario</option>
                        <option value="garante">Garante</option>
                    </select>
                    <x-input-error :messages="$errors->get('parte_rol')" class="mt-1" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="$set('modalParteAbierto', false)">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Agregar</x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal: plan de cuotas --}}
    <x-modal name="form-plan" :show="$modalPlanAbierto" focusable>
        <form wire:submit="generarPlan" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Generar plan de cuotas</h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="cantidad_cuotas" value="Cantidad de cuotas" />
                    <x-text-input id="cantidad_cuotas" type="number" class="mt-1 block w-full" wire:model="cantidad_cuotas" />
                    <x-input-error :messages="$errors->get('cantidad_cuotas')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="fecha_primer_vencimiento" value="Fecha del primer vencimiento" />
                    <x-text-input id="fecha_primer_vencimiento" type="date" class="mt-1 block w-full" wire:model="fecha_primer_vencimiento" />
                    <x-input-error :messages="$errors->get('fecha_primer_vencimiento')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="monto_por_cuota" value="Monto por cuota" />
                    <x-text-input id="monto_por_cuota" type="number" step="0.01" class="mt-1 block w-full" wire:model="monto_por_cuota" />
                    <x-input-error :messages="$errors->get('monto_por_cuota')" class="mt-1" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="$set('modalPlanAbierto', false)">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Generar</x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal: generar contrato --}}
    <x-modal name="form-contrato" :show="$modalContratoAbierto" focusable>
        <form wire:submit="crearContrato" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Generar contrato</h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="contrato_fecha_inicio" value="Fecha de inicio" />
                    <x-text-input id="contrato_fecha_inicio" type="date" class="mt-1 block w-full" wire:model="contrato_fecha_inicio" />
                    <x-input-error :messages="$errors->get('contrato_fecha_inicio')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="contrato_fecha_fin" value="Fecha de fin (opcional)" />
                    <x-text-input id="contrato_fecha_fin" type="date" class="mt-1 block w-full" wire:model="contrato_fecha_fin" />
                    <x-input-error :messages="$errors->get('contrato_fecha_fin')" class="mt-1" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button type="button" wire:click="$set('modalContratoAbierto', false)">Cancelar</x-secondary-button>
                <x-primary-button type="submit">Generar</x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
