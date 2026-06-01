<x-admin-layout title="Tenants">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tenants
        </h2>
    </x-slot>

<h1 class="text-3xl font-bold mb-8">
    Tenants
</h1>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">

    <table class="w-full">

        <thead class="bg-gray-100">
            <tr>
                <th class="text-left p-4">Tenant</th>
                <th class="text-left p-4">Domain</th>
                <th class="text-left p-4">Status</th>
            </tr>
        </thead>

        <tbody>

            <tr class="border-t">
                <td class="p-4">Demo Company</td>
                <td class="p-4">demo.127.0.0.1.nip.io</td>
                <td class="p-4">
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                        Active
                    </span>
                </td>
            </tr>

        </tbody>

    </table>

</div>

</x-admin-layout>