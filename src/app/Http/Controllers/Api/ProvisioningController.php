<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProvisioningController extends Controller
{
    public function provisionLoteos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id'      => 'required|string|regex:/^[a-z0-9][a-z0-9-]*$/',
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email',
            'admin_password' => 'required|string|min:8',
        ]);

        Log::info('Starting Loteos provisioning via core', $validated);

        try {
            $exitCode = Artisan::call('provision:loteos-direct', [
                'tenant_id'      => $validated['tenant_id'],
                'admin_name'     => $validated['admin_name'],
                'admin_email'    => $validated['admin_email'],
                'admin_password' => $validated['admin_password'],
            ]);

            $output = Artisan::output();

            if ($exitCode === 0) {
                Log::info('Loteos provisioning successful', [
                    'tenant_id' => $validated['tenant_id'],
                    'output'    => $output,
                ]);

                return response()->json([
                    'success'   => true,
                    'tenant_id' => $validated['tenant_id'],
                    'database'  => 'loteos_' . $validated['tenant_id'],
                    'output'    => $output,
                ]);
            } else {
                Log::error('Loteos provisioning failed', [
                    'tenant_id' => $validated['tenant_id'],
                    'output'    => $output,
                ]);

                return response()->json([
                    'success' => false,
                    'error'   => 'Provisioning failed',
                    'output'  => $output,
                ], 500);
            }

        } catch (\Throwable $e) {
            Log::error('Loteos provisioning exception', [
                'tenant_id' => $validated['tenant_id'],
                'error'     => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}