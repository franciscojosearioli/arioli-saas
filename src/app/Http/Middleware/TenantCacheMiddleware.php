<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\TenantCacheService;
use App\Services\CacheMonitoringService;
use App\Support\CacheKeyStrategy;
use Symfony\Component\HttpFoundation\Response;

class TenantCacheMiddleware
{
    /**
     * Enterprise middleware for automatic tenant cache optimization
     * Provides transparent caching and performance monitoring
     */

    private TenantCacheService $tenantCache;
    private CacheMonitoringService $monitoring;

    public function __construct()
    {
        $this->tenantCache = new TenantCacheService();
        $this->monitoring = new CacheMonitoringService();
    }

    /**
     * Handle an incoming request with cache optimization
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $tenantId = $this->getTenantId($request);
        $cacheKey = $this->generateCacheKey($request, $tenantId);

        // Skip caching for non-cacheable requests
        if (!$this->shouldCache($request)) {
            return $this->processWithoutCache($next, $request, $startTime);
        }

        // Try to serve from cache first
        $cachedResponse = $this->tenantCache->get($cacheKey);

        if ($cachedResponse !== null) {
            $this->logCacheHit($request, $cacheKey, $tenantId, $startTime);
            return $this->createResponseFromCache($cachedResponse);
        }

        // Process request and cache response
        $response = $next($request);

        if ($this->shouldCacheResponse($response)) {
            $this->cacheResponse($cacheKey, $response, $request);
        }

        $this->logCacheMiss($request, $cacheKey, $tenantId, $startTime);

        return $response;
    }

    /**
     * Determine if request should be cached
     */
    private function shouldCache(Request $request): bool
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return false;
        }

        // Skip admin routes (they change frequently)
        if ($request->is('admin/*')) {
            return false;
        }

        // Skip API routes with authentication required
        if ($request->is('api/*') && !$request->user()) {
            return false;
        }

        // Cache dashboard and statistics routes
        return $request->is([
            'dashboard',
            'dashboard/*',
            'cliente/tickets',
            'cliente/tickets/*',
            'api/dashboard/*',
            'api/statistics/*'
        ]);
    }

    /**
     * Determine if response should be cached
     */
    private function shouldCacheResponse(Response $response): bool
    {
        // Only cache successful responses
        if (!$response->isSuccessful()) {
            return false;
        }

        // Don't cache responses with errors
        if ($response->headers->get('X-Application-Error')) {
            return false;
        }

        // Don't cache very large responses (>1MB)
        if (strlen($response->getContent()) > 1024 * 1024) {
            return false;
        }

        return true;
    }

    /**
     * Generate cache key for request
     */
    private function generateCacheKey(Request $request, ?string $tenantId): string
    {
        $path = $request->path();
        $params = $request->query();
        $user = $request->user();

        // Base key components
        $keyComponents = [
            'middleware_cache',
            $path,
            md5(serialize($params)),
        ];

        // Add tenant context if available
        if ($tenantId) {
            array_unshift($keyComponents, "tenant_{$tenantId}");
        }

        // Add user context for personalized content
        if ($user) {
            $keyComponents[] = "user_{$user->id}";
        }

        return implode(':', $keyComponents);
    }

    /**
     * Get tenant ID from request context
     */
    private function getTenantId(Request $request): ?string
    {
        // Try from authenticated user
        $user = $request->user();
        if ($user && $user->tenant_id) {
            return $user->tenant_id;
        }

        // Try from route parameters
        if ($request->route('tenant')) {
            return $request->route('tenant');
        }

        // Try from tenancy context
        if (function_exists('tenant') && tenant()) {
            return tenant()->id;
        }

        return null;
    }

    /**
     * Process request without caching
     */
    private function processWithoutCache(Closure $next, Request $request, float $startTime): Response
    {
        $response = $next($request);

        $this->logNonCachedRequest($request, $startTime);

        return $response;
    }

    /**
     * Cache response for future requests
     */
    private function cacheResponse(string $cacheKey, Response $response, Request $request): void
    {
        try {
            $cacheData = [
                'content' => $response->getContent(),
                'headers' => $response->headers->all(),
                'status' => $response->getStatusCode(),
                'timestamp' => now(),
            ];

            $ttl = $this->getCacheTTL($request);
            $this->tenantCache->put($cacheKey, $cacheData, $ttl);

            Log::info('Response cached successfully', [
                'cache_key' => $cacheKey,
                'ttl' => $ttl,
                'response_size' => strlen($response->getContent()),
                'route' => $request->path(),
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cache response', [
                'cache_key' => $cacheKey,
                'route' => $request->path(),
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
        }
    }

    /**
     * Create HTTP response from cached data
     */
    private function createResponseFromCache(array $cachedData): Response
    {
        $response = new Response(
            $cachedData['content'],
            $cachedData['status']
        );

        // Restore headers
        foreach ($cachedData['headers'] as $name => $values) {
            $response->headers->set($name, $values);
        }

        // Add cache headers
        $response->headers->set('X-Cache-Status', 'HIT');
        $response->headers->set('X-Cache-Timestamp', $cachedData['timestamp']);

        return $response;
    }

    /**
     * Get appropriate TTL for request
     */
    private function getCacheTTL(Request $request): int
    {
        $path = $request->path();

        return match (true) {
            str_starts_with($path, 'dashboard') => CacheKeyStrategy::TTL_SHORT, // 5 minutes
            str_starts_with($path, 'api/dashboard') => CacheKeyStrategy::TTL_VERY_SHORT, // 1 minute
            str_starts_with($path, 'cliente/tickets') => CacheKeyStrategy::TTL_SHORT, // 5 minutes
            str_starts_with($path, 'api/statistics') => CacheKeyStrategy::TTL_MEDIUM, // 30 minutes
            default => CacheKeyStrategy::TTL_SHORT // 5 minutes default
        };
    }

    /**
     * Log cache hit for monitoring
     */
    private function logCacheHit(Request $request, string $cacheKey, ?string $tenantId, float $startTime): void
    {
        $executionTime = microtime(true) - $startTime;

        $this->monitoring->recordHit($cacheKey, $tenantId, $executionTime);

        Log::info('Cache hit via middleware', [
            'route' => $request->path(),
            'cache_key' => $cacheKey,
            'tenant_id' => $tenantId,
            'execution_time_ms' => round($executionTime * 1000, 2),
            'timestamp' => now()
        ]);
    }

    /**
     * Log cache miss for monitoring
     */
    private function logCacheMiss(Request $request, string $cacheKey, ?string $tenantId, float $startTime): void
    {
        $executionTime = microtime(true) - $startTime;

        $this->monitoring->recordMiss($cacheKey, $tenantId, $executionTime);

        Log::info('Cache miss via middleware', [
            'route' => $request->path(),
            'cache_key' => $cacheKey,
            'tenant_id' => $tenantId,
            'execution_time_ms' => round($executionTime * 1000, 2),
            'timestamp' => now()
        ]);
    }

    /**
     * Log non-cached request
     */
    private function logNonCachedRequest(Request $request, float $startTime): void
    {
        $executionTime = microtime(true) - $startTime;

        Log::debug('Request processed without caching', [
            'route' => $request->path(),
            'method' => $request->method(),
            'reason' => 'not_cacheable',
            'execution_time_ms' => round($executionTime * 1000, 2),
            'timestamp' => now()
        ]);
    }
}