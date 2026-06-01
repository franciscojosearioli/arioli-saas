<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Arioli SaaS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-950 text-white">

    <!-- Navbar -->
    <header class="border-b border-gray-800">

        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">

            <h1 class="text-2xl font-bold">
                Arioli SaaS
            </h1>

            <div class="space-x-4">

                <a href="{{ route('login') }}"
                   class="text-gray-300 hover:text-white">
                    Login
                </a>

                <a href="{{ route('register') }}"
                   class="bg-white text-black px-5 py-2 rounded-lg font-medium hover:bg-gray-200 transition">
                    Start Now
                </a>

            </div>

        </div>

    </header>

    <!-- Hero -->
    <section class="py-32">

        <div class="max-w-7xl mx-auto px-6 text-center">

            <h2 class="text-6xl font-bold leading-tight">
                Multi-Tenant SaaS Platform
            </h2>

            <p class="mt-6 text-xl text-gray-400 max-w-3xl mx-auto">
                Scalable ERP, CRM and management systems for modern businesses.
            </p>

            <div class="mt-10">

                <a href="{{ route('register') }}"
                   class="bg-white text-black px-8 py-4 rounded-xl text-lg font-semibold hover:bg-gray-200 transition">
                    Get Started
                </a>

            </div>

        </div>

    </section>

    <!-- Features -->
    <section class="py-24 border-t border-gray-800">

        <div class="max-w-7xl mx-auto px-6">

            <div class="grid md:grid-cols-3 gap-8">

                <div class="bg-gray-900 p-8 rounded-2xl border border-gray-800">
                    <h3 class="text-2xl font-bold mb-4">
                        Multi Tenant
                    </h3>

                    <p class="text-gray-400">
                        Isolated environments for every client with secure architecture.
                    </p>
                </div>

                <div class="bg-gray-900 p-8 rounded-2xl border border-gray-800">
                    <h3 class="text-2xl font-bold mb-4">
                        Cloud Ready
                    </h3>

                    <p class="text-gray-400">
                        Dockerized infrastructure optimized for VPS and cloud deployment.
                    </p>
                </div>

                <div class="bg-gray-900 p-8 rounded-2xl border border-gray-800">
                    <h3 class="text-2xl font-bold mb-4">
                        Analytics
                    </h3>

                    <p class="text-gray-400">
                        Real-time dashboards, reports and operational insights.
                    </p>
                </div>

            </div>

        </div>

    </section>

    <!-- Plans -->
    <section class="py-24">

        <div class="max-w-7xl mx-auto px-6">

            <h2 class="text-4xl font-bold text-center mb-16">
                Plans
            </h2>

            <div class="grid md:grid-cols-3 gap-8">

                <div class="bg-gray-900 border border-gray-800 p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold">
                        Starter
                    </h3>

                    <p class="mt-4 text-5xl font-bold">
                        $19
                    </p>

                    <p class="mt-2 text-gray-400">
                        Small businesses
                    </p>
                </div>

                <div class="bg-white text-black p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold">
                        Professional
                    </h3>

                    <p class="mt-4 text-5xl font-bold">
                        $49
                    </p>

                    <p class="mt-2 text-gray-600">
                        Growing companies
                    </p>
                </div>

                <div class="bg-gray-900 border border-gray-800 p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold">
                        Enterprise
                    </h3>

                    <p class="mt-4 text-5xl font-bold">
                        Custom
                    </p>

                    <p class="mt-2 text-gray-400">
                        Large organizations
                    </p>
                </div>

            </div>

        </div>

    </section>

</body>
</html>