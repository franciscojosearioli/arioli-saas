<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-white flex flex-col">

        <div class="p-6 border-b border-gray-800">
            <h1 class="text-2xl font-bold">
                Arioli SaaS
            </h1>
        </div>

        <nav class="flex-1 p-4 space-y-2">

            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 rounded hover:bg-gray-800">
                Dashboard
            </a>

            <a href="{{ route('tenants.index') }}"
               class="block px-4 py-2 rounded hover:bg-gray-800">
                Clientes
            </a>

            <a href="{{ route('licenses.index') }}"
               class="block px-4 py-2 rounded hover:bg-gray-800">
                Licencias
            </a>

            <a href="#"
               class="block px-4 py-2 rounded hover:bg-gray-800">
                Planes
            </a>

            <a href="#"
               class="block px-4 py-2 rounded hover:bg-gray-800">
                Facturación
            </a>

            <a href="{{ route('profile.edit') }}"
               class="block px-4 py-2 rounded hover:bg-gray-800">
                Perfil
            </a>

        </nav>

        <div class="p-4 border-t border-gray-800">

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button class="w-full bg-red-600 hover:bg-red-700 px-4 py-2 rounded">
                    Cerrar sesión
                </button>
            </form>

        </div>

    </aside>

    {{-- Main --}}
    <main class="flex-1">

        {{-- Topbar --}}
        <header class="bg-white shadow px-8 py-4 flex justify-between items-center">

            <div>
                <h2 class="text-xl font-semibold">
                    {{ $title ?? 'Panel Administrativo' }}
                </h2>
            </div>

            <div class="text-sm text-gray-600">
                {{ auth()->user()->name }}
            </div>

        </header>

        {{-- Content --}}
        <section class="p-8">
            {{ $slot }}
        </section>

    </main>

</div>

</body>
</html>