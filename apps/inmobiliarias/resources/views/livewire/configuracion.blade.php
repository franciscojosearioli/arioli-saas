<div class="py-12" x-data="{ guardado: false }" x-on:configuracion-guardada.window="guardado = true; setTimeout(() => guardado = false, 2500)">
    <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración</h2>

        @if (session('status'))
            <div class="p-3 rounded-md bg-green-50 border border-green-200 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="p-3 rounded-md bg-red-50 border border-red-200 text-sm text-red-800">{{ session('error') }}</div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
            <div>
                <h3 class="font-medium text-gray-900">Cuentas conectadas (§09/§14)</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Conectá la Página de Facebook de la inmobiliaria para publicar ahí — si tiene una cuenta profesional de Instagram vinculada, se conecta sola en el mismo paso.
                </p>
            </div>

            @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram'] as $canal => $etiqueta)
                @php $cuenta = $cuentasConectadas->get($canal); @endphp

                <div class="flex items-center justify-between gap-3 border border-gray-200 rounded-md p-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $etiqueta }}</p>

                        @if ($cuenta)
                            <p class="text-xs text-gray-500">{{ $cuenta->external_account_name ?? $cuenta->external_account_id }}</p>

                            @if ($cuenta->requiereReconexion())
                                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">Requiere reconexión</span>
                            @else
                                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Conectada</span>
                            @endif
                        @else
                            <p class="text-xs text-gray-500">
                                {{ $canal === 'instagram' ? 'Se conecta junto con Facebook si la Página tiene una cuenta profesional vinculada.' : 'No conectada.' }}
                            </p>
                        @endif
                    </div>

                    @if ($cuenta)
                        <form method="POST" action="{{ route('configuracion.cuentas-conectadas.desconectar', $cuenta) }}" onsubmit="return confirm('¿Desconectar {{ $etiqueta }}? Se van a dejar de publicar cambios ahí.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">Desconectar</button>
                        </form>
                    @elseif ($canal === 'facebook')
                        <a href="{{ route('configuracion.facebook.conectar') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-800 text-white text-xs font-medium rounded-md hover:bg-gray-700">
                            Conectar Facebook
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <form wire:submit="guardar" class="space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">Storefront público (§08)</h3>

                <div>
                    <x-input-label for="nombre_comercial" value="Nombre comercial" />
                    <x-text-input id="nombre_comercial" type="text" class="mt-1 block w-full" wire:model="nombre_comercial" placeholder="ej. Edisur Inmobiliaria" />
                    <x-input-error :messages="$errors->get('nombre_comercial')" class="mt-1" />
                    <p class="text-xs text-gray-500 mt-1">
                        Se muestra en la home pública de tu subdominio y en el título de la página.
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
