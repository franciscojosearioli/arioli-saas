<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name'))</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 text-gray-900 antialiased">
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="{{ route('busqueda') }}" class="font-semibold text-lg text-indigo-700">{{ config('app.name') }}</a>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 py-8">
            @yield('content')
        </main>

        <footer class="max-w-6xl mx-auto px-4 py-8 text-sm text-gray-400">
            Marketplace inmobiliario — Arioli.
        </footer>

        @stack('scripts')
    </body>
</html>
