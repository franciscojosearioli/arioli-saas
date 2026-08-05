<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function validate(Request $request): JsonResponse
    {
        $tenantId    = $request->query('tenant');
        $productSlug = $request->query('product');

        if (!$tenantId || !$productSlug) {
            return response()->json([
                'valid'  => false,
                'reason' => 'missing_parameters',
            ], 422);
        }

        $license = License::where('tenant_id', $tenantId)
            ->where('active', true)
            ->whereHas('plan.product', fn($q) => $q->where('slug', $productSlug))
            ->with(['plan.product'])
            ->latest()
            ->first();

        if (!$license) {
            return response()->json([
                'valid'  => false,
                'reason' => 'not_found',
            ]);
        }

        if (!$license->plan->active) {
            return response()->json([
                'valid'  => false,
                'reason' => 'plan_inactive',
            ]);
        }

        if ($license->starts_at > now()) {
            return response()->json([
                'valid'      => false,
                'reason'     => 'not_started',
                'starts_at'  => $license->starts_at->toIso8601String(),
            ]);
        }

        if ($license->isExpired()) {
            return response()->json([
                'valid'      => false,
                'reason'     => 'expired',
                'expired_at' => $license->expires_at?->toIso8601String(),
            ]);
        }

        return response()->json([
            'valid'          => true,
            'tenant_id'      => $license->tenant_id,
            'product'        => $productSlug,
            'plan'           => $license->plan->name,
            'license_type'   => $license->license_type ?? 'commercial',
            'expires_at'     => $license->expires_at?->toIso8601String(),
            'days_remaining' => $license->daysRemaining(),
        ]);
    }
}
