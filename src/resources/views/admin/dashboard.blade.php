<x-admin-layout title="Dashboard">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

<div class="mb-10">
    <h1 class="text-4xl font-bold">
        Dashboard
    </h1>

    <p class="text-gray-500 mt-2">
        Welcome to your SaaS administration panel.
    </p>
</div>

<div class="grid md:grid-cols-4 gap-6">

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <p class="text-gray-500">
            Active Tenants
        </p>

        <h2 class="text-4xl font-bold mt-4">
            12
        </h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <p class="text-gray-500">
            Licenses
        </p>

        <h2 class="text-4xl font-bold mt-4">
            18
        </h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <p class="text-gray-500">
            Revenue
        </p>

        <h2 class="text-4xl font-bold mt-4">
            $4,200
        </h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <p class="text-gray-500">
            Users
        </p>

        <h2 class="text-4xl font-bold mt-4">
            241
        </h2>
    </div>

</div>

</x-admin-layout>