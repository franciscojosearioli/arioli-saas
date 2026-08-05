<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VerifyLicenseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = tenant('id');

        if (!$tenantId) {
            abort(403);
        }

        // Buscar licencia directamente en BD central sin usar Eloquent
        $license = DB::connection('central')
            ->table('licenses')
            ->where('tenant_id', $tenantId)
            ->where('active', true)
            ->first();

        if (!$license) {
            return response()->view('errors.no-license', [], 403);
        }

        $plan = DB::connection('central')
            ->table('plans')
            ->where('id', $license->plan_id)
            ->first();

        if (!$plan || !$plan->active) {
            return response()->view('errors.plan-inactive', [], 403);
        }

        $now = now();

        if ($license->starts_at > $now) {
            return response()->view('errors.license-not-started', [
                'starts_at' => \Carbon\Carbon::parse($license->starts_at),
            ], 403);
        }

        if ($license->expires_at < $now) {
            return response()->view('errors.license-expired', [
                'expired_at' => \Carbon\Carbon::parse($license->expires_at),
                'plan'       => $plan,
            ], 403);
        }

        return $next($request);
    }
}