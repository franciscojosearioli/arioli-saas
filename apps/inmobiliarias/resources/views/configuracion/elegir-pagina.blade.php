<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Elegir página de Facebook</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <p class="text-sm text-gray-500">
                Tu cuenta administra más de una página. Elegí cuál conectar — si tiene una cuenta de
                Instagram vinculada, se conecta también.
            </p>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-100">
                @foreach ($paginas as $pagina)
                    <form method="POST" action="{{ route('configuracion.facebook.elegir-pagina') }}" class="p-4 flex items-center justify-between">
                        @csrf
                        <input type="hidden" name="estado" value="{{ $estado }}">
                        <input type="hidden" name="pagina_id" value="{{ $pagina['id'] }}">
                        <span class="font-medium text-gray-900">{{ $pagina['name'] }}</span>
                        <x-primary-button type="submit">Conectar esta página</x-primary-button>
                    </form>
                @endforeach
            </div>

            <a href="{{ route('configuracion') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Cancelar</a>
        </div>
    </div>
</x-app-layout>
