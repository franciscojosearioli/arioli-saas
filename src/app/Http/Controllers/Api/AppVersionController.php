<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateAppInstance;
use App\Models\AppVersion;
use App\Models\License;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    // GET /api/app/version?product={slug}
    public function latest(Request $request): JsonResponse
    {
        $slug = $request->query('product');

        if (!$slug) {
            return response()->json(['error' => 'product param required'], 422);
        }

        $version = AppVersion::latestFor($slug);

        if (!$version) {
            return response()->json(['error' => 'No version available'], 404);
        }

        return response()->json([
            'version'         => $version->version,
            'type'            => $version->type,
            'changelog'       => $version->changelog,
            'released_at'     => $version->released_at?->toIso8601String(),
            'min_php_version' => $version->min_php_version,
        ]);
    }

    // POST /api/app/version/installed
    // Body: { tenant_id, product, version }
    public function reportInstalled(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'required|string|max:63',
            'product'   => 'required|string|max:63',
            'version'   => 'required|string|max:20',
        ]);

        License::whereHas('plan.product', fn($q) => $q->where('slug', $validated['product']))
            ->where('tenant_id', $validated['tenant_id'])
            ->where('active', true)
            ->update(['installed_version' => $validated['version']]);

        return response()->json(['ok' => true]);
    }

    // POST /api/app/update
    // Body: { tenant_id, product }
    public function requestUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'required|string|max:63',
            'product'   => 'required|string|max:63',
        ]);

        if (!Product::where('slug', $validated['product'])->where('active', true)->exists()) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $latest = AppVersion::latestFor($validated['product']);

        if (!$latest) {
            return response()->json(['error' => 'No version available for this product'], 404);
        }

        UpdateAppInstance::dispatch($validated['tenant_id'], $validated['product'], $latest->version);

        return response()->json([
            'ok'             => true,
            'queued_version' => $latest->version,
        ]);
    }
}
