<div class="py-12" x-data="{ guardado: false }" x-on:configuracion-guardada.window="guardado = true; setTimeout(() => guardado = false, 2500)">
    <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración</h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <form wire:submit="guardar" class="space-y-4">
                <div>
                    <x-input-label for="comision_porcentaje" value="Porcentaje de comisión" />
                    <x-text-input id="comision_porcentaje" type="number" step="0.01" class="mt-1 block w-full" wire:model="comision_porcentaje" placeholder="ej. 4.00" />
                    <x-input-error :messages="$errors->get('comision_porcentaje')" class="mt-1" />
                    <p class="text-xs text-gray-500 mt-1">
                        Se aplica a toda operación que se cierre con un agente asignado. Si queda vacío, cerrar una operación no genera comisión.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <x-primary-button type="submit">Guardar</x-primary-button>
                    <span x-show="guardado" x-transition class="text-sm text-green-600">Guardado.</span>
                </div>
            </form>
        </div>
    </div>
</div>
