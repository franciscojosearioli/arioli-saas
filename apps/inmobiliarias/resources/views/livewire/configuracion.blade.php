<div class="py-12" x-data="{ guardado: false }" x-on:configuracion-guardada.window="guardado = true; setTimeout(() => guardado = false, 2500)">
    <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración</h2>

        <form wire:submit="guardar" class="space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">Perfil público (§08 — marketplace)</h3>

                <div>
                    <x-input-label for="nombre_comercial" value="Nombre comercial" />
                    <x-text-input id="nombre_comercial" type="text" class="mt-1 block w-full" wire:model="nombre_comercial" placeholder="ej. Edisur Inmobiliaria" />
                    <x-input-error :messages="$errors->get('nombre_comercial')" class="mt-1" />
                    <p class="text-xs text-gray-500 mt-1">
                        Sin esto cargado, todavía no hay un perfil público en el marketplace para esta inmobiliaria.
                    </p>
                </div>

                <div>
                    <x-input-label for="descripcion" value="Descripción" />
                    <textarea id="descripcion" wire:model="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    <x-input-error :messages="$errors->get('descripcion')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="logo_url" value="Logo (URL)" />
                    <x-text-input id="logo_url" type="text" class="mt-1 block w-full" wire:model="logo_url" />
                    <x-input-error :messages="$errors->get('logo_url')" class="mt-1" />
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">Finanzas</h3>

                <div>
                    <x-input-label for="comision_porcentaje" value="Porcentaje de comisión" />
                    <x-text-input id="comision_porcentaje" type="number" step="0.01" class="mt-1 block w-full" wire:model="comision_porcentaje" placeholder="ej. 4.00" />
                    <x-input-error :messages="$errors->get('comision_porcentaje')" class="mt-1" />
                    <p class="text-xs text-gray-500 mt-1">
                        Se aplica a toda operación que se cierre con un agente asignado. Si queda vacío, cerrar una operación no genera comisión.
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">Sitio web propio (§09 — canal de publicación)</h3>

                <div>
                    <x-input-label for="sitio_web_url" value="URL del sitio" />
                    <x-text-input id="sitio_web_url" type="text" class="mt-1 block w-full" wire:model="sitio_web_url" placeholder="https://tuinmobiliaria.com.ar" />
                    <x-input-error :messages="$errors->get('sitio_web_url')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="sitio_web_api_key" value="API key" />
                    <x-text-input id="sitio_web_api_key" type="text" class="mt-1 block w-full" wire:model="sitio_web_api_key" />
                    <x-input-error :messages="$errors->get('sitio_web_api_key')" class="mt-1" />
                    <p class="text-xs text-gray-500 mt-1">
                        Sin esto configurado, el canal "Sitio web" no tiene a dónde publicar.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button type="submit">Guardar</x-primary-button>
                <span x-show="guardado" x-transition class="text-sm text-green-600">Guardado.</span>
            </div>
        </form>
    </div>
</div>
