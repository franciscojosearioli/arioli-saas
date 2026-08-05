<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\Repository;

class TenantCacheService
{
    /**
     * Enterprise tenant-aware cache service with complete namespace isolation
     * Provides performance optimization and security for multitenant SaaS
     */

    private string $tenantId;
    private string $namespace;
    private Repository $cache;
    private array $metrics = [];

    public function __construct(?string $tenantId = null)
    {
        $this->tenantId = $tenantId ?? $this->getCurrentTenantId();
        $this->namespace = $this->buildNamespace();
        $this->cache = Cache::store('tenant');

        // Log cache service initialization for enterprise monitoring
        Log::info('TenantCacheService initialized', [
            'tenant_id' => $this->tenantId,
            'namespace' => $this->namespace,
            'store' => 'tenant',
            'timestamp' => now()
        ]);
    }

    /**
     * Get cached value with tenant namespace isolation
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $namespacedKey = $this->getNamespacedKey($key);
        $startTime = microtime(true);

        try {
            $value = $this->cache->get($namespacedKey, $default);
            $hit = $value !== $default;

            $this->recordMetrics('get', $namespacedKey, $hit, $startTime);

            // Log cache access for enterprise audit
            Log::info('Tenant cache access', [
                'tenant_id' => $this->tenantId,
                'key' => $key,
                'namespaced_key' => $namespacedKey,
                'hit' => $hit,
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'timestamp' => now()
            ]);

            return $value;
        } catch (\Exception $e) {
            Log::error('Tenant cache get failed', [
                'tenant_id' => $this->tenantId,
                'key' => $key,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return $default;
        }
    }

    /**
     * Store value in cache with tenant namespace isolation
     */
    public function put(string $key, mixed $value, int $ttl = 3600): bool
    {
        $namespacedKey = $this->getNamespacedKey($key);
        $startTime = microtime(true);

        try {
            $result = $this->cache->put($namespacedKey, $value, $ttl);

            $this->recordMetrics('put', $namespacedKey, true, $startTime);

            // Log cache storage for enterprise audit
            Log::info('Tenant cache storage', [
                'tenant_id' => $this->tenantId,
                'key' => $key,
                'namespaced_key' => $namespacedKey,
                'ttl' => $ttl,
                'value_size' => strlen(serialize($value)),
                'success' => $result,
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'timestamp' => now()
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Tenant cache put failed', [
                'tenant_id' => $this->tenantId,
                'key' => $key,
                'ttl' => $ttl,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return false;
        }
    }

    /**
     * Remember pattern with tenant namespace isolation
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $namespacedKey = $this->getNamespacedKey($key);
        $startTime = microtime(true);

        try {
            // Check if value exists first for metrics
            $exists = $this->cache->has($namespacedKey);

            $value = $this->cache->remember($namespacedKey, $ttl, $callback);

            $this->recordMetrics('remember', $namespacedKey, $exists, $startTime);

            // Log cache remember operation
            Log::info('Tenant cache remember', [
                'tenant_id' => $this->tenantId,
                'key' => $key,
                'namespaced_key' => $namespacedKey,
                'ttl' => $ttl,
                'cache_hit' => $exists,
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'timestamp' => now()
            ]);

            return $value;
        } catch (\Exception $e) {
            Log::error('Tenant cache remember failed', [
                'tenant_id' => $this->tenantId,
                'key' => $key,
                'ttl' => $ttl,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            // Fallback to direct callback execution
            return $callback();
        }
    }

    /**
     * Forget cached value with tenant namespace isolation
     */
    public function forget(string $key): bool
    {
        $namespacedKey = $this->getNamespacedKey($key);
        $startTime = microtime(true);

        try {
            $result = $this->cache->forget($namespacedKey);

            $this->recordMetrics('forget', $namespacedKey, true, $startTime);

            // Log cache invalidation for enterprise audit
            Log::info('Tenant cache invalidation', [
                'tenant_id' => $this->tenantId,
                'key' => $key,
                'namespaced_key' => $namespacedKey,
                'success' => $result,
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'timestamp' => now()
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Tenant cache forget failed', [
                'tenant_id' => $this->tenantId,
                'key' => $key,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return false;
        }
    }

    /**
     * Flush all cache for the current tenant
     */
    public function flushTenant(): bool
    {
        $pattern = $this->namespace . '*';
        $startTime = microtime(true);

        try {
            // Get all keys matching tenant pattern
            $connection = $this->cache->getStore()->getRedis();
            $keys = $connection->keys($pattern);

            if (!empty($keys)) {
                $result = $connection->del($keys) > 0;
            } else {
                $result = true; // No keys to delete
            }

            // Log tenant cache flush for enterprise audit
            Log::warning('Tenant cache flushed', [
                'tenant_id' => $this->tenantId,
                'namespace' => $this->namespace,
                'keys_deleted' => count($keys),
                'success' => $result,
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'timestamp' => now()
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Tenant cache flush failed', [
                'tenant_id' => $this->tenantId,
                'namespace' => $this->namespace,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return false;
        }
    }

    /**
     * Get cache statistics for the current tenant
     */
    public function getStats(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'namespace' => $this->namespace,
            'metrics' => $this->metrics,
            'connection' => 'tenant_cache',
            'timestamp' => now()
        ];
    }

    /**
     * Enterprise cache warming for dashboard statistics
     */
    public function warmDashboardCache(): array
    {
        $warmed = [];
        $dashboardKeys = [
            'dashboard.stats' => fn() => $this->getDashboardStats(),
            'dashboard.recent_activity' => fn() => $this->getRecentActivity(),
            'dashboard.license_status' => fn() => $this->getLicenseStatus(),
        ];

        foreach ($dashboardKeys as $key => $callback) {
            try {
                $this->remember($key, 300, $callback); // 5-minute cache
                $warmed[] = $key;
            } catch (\Exception $e) {
                Log::error('Dashboard cache warming failed', [
                    'tenant_id' => $this->tenantId,
                    'key' => $key,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Dashboard cache warmed', [
            'tenant_id' => $this->tenantId,
            'keys_warmed' => $warmed,
            'total_keys' => count($dashboardKeys),
            'timestamp' => now()
        ]);

        return $warmed;
    }

    /**
     * Build enterprise namespace for tenant isolation
     */
    private function buildNamespace(): string
    {
        $app = config('app.name', 'laravel');
        $env = config('app.env', 'production');

        return strtolower("arioli:{$env}:tenant:{$this->tenantId}:");
    }

    /**
     * Get namespaced key with enterprise security
     */
    private function getNamespacedKey(string $key): string
    {
        // Sanitize key to prevent cache key injection
        $sanitizedKey = preg_replace('/[^a-zA-Z0-9._-]/', '_', $key);

        return $this->namespace . $sanitizedKey;
    }

    /**
     * Get current tenant ID from various sources
     */
    private function getCurrentTenantId(): string
    {
        // Try to get tenant from authenticated user
        $user = Auth::user() ?? Auth::guard('cliente')->user();
        if ($user && $user->tenant_id) {
            return $user->tenant_id;
        }

        // Try to get from tenancy context
        if (function_exists('tenant') && tenant()) {
            return tenant()->id;
        }

        // Try to get from request
        $request = request();
        if ($request && $request->get('tenant_id')) {
            return $request->get('tenant_id');
        }

        // Fallback to system namespace for admin operations
        return 'system';
    }

    /**
     * Record performance metrics for enterprise monitoring
     */
    private function recordMetrics(string $operation, string $key, bool $hit, float $startTime): void
    {
        $executionTime = microtime(true) - $startTime;

        if (!isset($this->metrics[$operation])) {
            $this->metrics[$operation] = [
                'count' => 0,
                'hits' => 0,
                'misses' => 0,
                'total_time' => 0,
                'avg_time' => 0
            ];
        }

        $this->metrics[$operation]['count']++;
        $this->metrics[$operation]['total_time'] += $executionTime;
        $this->metrics[$operation]['avg_time'] = $this->metrics[$operation]['total_time'] / $this->metrics[$operation]['count'];

        if (in_array($operation, ['get', 'remember'])) {
            if ($hit) {
                $this->metrics[$operation]['hits']++;
            } else {
                $this->metrics[$operation]['misses']++;
            }
        }
    }

    /**
     * Helper methods for dashboard cache warming
     */
    private function getDashboardStats(): array
    {
        // Implementation would get actual dashboard stats
        return ['stats' => 'cached_data'];
    }

    private function getRecentActivity(): array
    {
        // Implementation would get actual recent activity
        return ['activity' => 'cached_data'];
    }

    private function getLicenseStatus(): array
    {
        // Implementation would get actual license status
        return ['license' => 'cached_data'];
    }
}