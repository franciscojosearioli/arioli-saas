<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Licencia inactiva</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-lg p-10 text-center">

            <div class="flex justify-center mb-6">
                <div class="bg-red-100 rounded-full p-4">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Licencia inactiva</h1>

            @if($reason === 'expired')
                <p class="text-gray-500 mb-6">Tu licencia ha vencido. Para continuar usando el sistema, renovála desde el portal de clientes.</p>
            @elseif($reason === 'not_found')
                <p class="text-gray-500 mb-6">No se encontró una licencia activa para este sistema.</p>
            @elseif($reason === 'not_started')
                <p class="text-gray-500 mb-6">Tu licencia aún no ha comenzado. Volvé en unos momentos.</p>
            @else
                <p class="text-gray-500 mb-6">El acceso a este sistema está temporalmente suspendido.</p>
            @endif

            <a href="https://arioli.dev/cliente/login"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                Ir al portal de clientes
            </a>

            <p class="text-xs text-gray-400 mt-6">
                ¿Necesitás ayuda? Contactá a
                <a href="mailto:soporte@arioli.dev" class="underline">soporte@arioli.dev</a>
            </p>
        </div>
    </div>
</body>
</html>