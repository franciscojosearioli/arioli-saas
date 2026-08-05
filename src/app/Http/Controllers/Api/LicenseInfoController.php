<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use App\Models\License;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LicenseInfoController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $tenantId    = $request->query('tenant');
        $productSlug = $request->query('product');

        if (!$tenantId || !$productSlug) {
            return response()->json(['error' => 'missing_parameters'], 422);
        }

        $cacheKey = "license_data_{$tenantId}_{$productSlug}";
        $cached   = Cache::get($cacheKey);

        if (!$cached) {
            $license = License::where('tenant_id', $tenantId)
                ->where('active', true)
                ->whereHas('plan.product', fn($q) => $q->where('slug', $productSlug))
                ->with(['plan.product'])
                ->latest()
                ->first();

            if (!$license) {
                return response()->json(['error' => 'license_not_found'], 404);
            }

            $latestVersion = Cache::remember(
                "latest_version_{$productSlug}",
                300,
                fn() => AppVersion::whereHas('product', fn($q) => $q->where('slug', $productSlug))
                    ->where('is_current', true)
                    ->value('version')
            );

            $publicDomain = $license->plan->product->public_domain ?? $productSlug;
            $domain = $request->header('X-Tenant-Host')
                ?? optional($license->domain)->domain
                ?? "{$tenantId}.{$publicDomain}." . config('app.tenant_domain');

            $cached = [
                'product'           => $license->plan->product->name ?? $productSlug,
                'plan'              => $license->plan->name,
                'license_type'      => $license->license_type ?? 'commercial',
                'active'            => $license->active,
                'starts_at'         => $license->starts_at?->toDateString(),
                'expires_at'        => $license->expires_at?->toDateString(),
                'days_remaining'    => $license->daysRemaining(),
                'domain'            => $domain,
                'installed_version' => $license->installed_version,
                'latest_version'    => $latestVersion,
            ];

            Cache::put($cacheKey, $cached, 300);
        }

        return response()->json(array_merge($cached, [
            'last_validated_at' => now()->toIso8601String(),
        ]));
    }
}
