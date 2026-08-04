<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ProvisionController extends Controller
{
    public function provision(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'required|string|regex:/^[a-z0-9][a-z0-9-]*$/',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:8',
            'domain' => 'nullable|string',
        ]);

        try {
            $exitCode = Artisan::call('tenant:provision', array_filter([
                'tenant_id' => $validated['tenant_id'],
                'admin_name' => $validated['admin_name'],
                'admin_email' => $validated['admin_email'],
                'admin_password' => $validated['admin_password'],
                '--domain' => $validated['domain'] ?? null,
            ]));

            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'tenant_id' => $validated['tenant_id'],
                    'database' => 'inmobiliarias_'.$validated['tenant_id'],
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => trim(Artisan::output()),
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
