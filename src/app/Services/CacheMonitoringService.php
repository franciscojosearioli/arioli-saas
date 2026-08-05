<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Support\CacheKeyStrategy;

class CacheMonitoringService
{
    /**
     * Enterprise cache performance monitoring for Redis optimization
     * Provides comprehensive metrics for cache hit rates and performance analysis
     */

    private const METRICS_KEY = 'cache_metrics';
    private const METRICS_TTL = 3600; // 1 hour

    /**
     * Record cache hit for performance monitoring
     */
    public function recordHit(string $key, string $tenantId = null, float $executionTime = null): void
    {
        $this->recordMetric('hit', $key, $tenantId, $executionTime);
    }

    /**
     * Record cache miss for performance monitoring
     */
    public function recordMiss(string $key, string $tenantId = null, float $executionTime = null): void
    {
        $this->recordMetric('miss', $key, $tenantId, $executionTime);
    }

    /**
     * Record cache write operation
     */
    public function recordWrite(string $key, string $tenantId = null, float $executionTime = null, int $valueSize = null): void
    {
        $this->recordMetric('write', $key, $tenantId, $executionTime, ['value_size' => $valueSize]);
    }

    /**
     * Get comprehensive cache performance metrics
     */
    public function getMetrics(): array
    {
        try {
            $redis = Redis::connection();

            // Get Redis INFO stats
            $redisInfo = $redis->info();

            // Calculate derived metrics
            $totalHits = $redisInfo['keyspace_hits'] ?? 0;
            $totalMisses = $redisInfo['keyspace_misses'] ?? 0;
            $totalRequests = $totalHits + $totalMisses;
            $hitRate = $totalRequests > 0 ? ($totalHits / $totalRequests) * 100 : 0;

            $metrics = [
                // Redis server metrics
                'redis_info' => [
                    'version' => $redisInfo['redis_version'] ?? 'unknown',
                    'uptime_seconds' => $redisInfo['uptime_in_seconds'] ?? 0,
                    'connected_clients' => $redisInfo['connected_clients'] ?? 0,
                    'used_memory' => $redisInfo['used_memory'] ?? 0,
                    'used_memory_human' => $redisInfo['used_memory_human'] ?? '0B',
                    'used_memory_peak' => $redisInfo['used_memory_peak'] ?? 0,
                    'used_memory_peak_human' => $redisInfo['used_memory_peak_human'] ?? '0B',
                ],

                // Cache performance metrics
                'performance' => [
                    'total_hits' => $totalHits,
                    'total_misses' => $totalMisses,
                    'total_requests' => $totalRequests,
                    'hit_rate_percentage' => round($hitRate, 2),
                    'miss_rate_percentage' => round(100 - $hitRate, 2),
                ],

                // Key statistics
                'keys' => [
                    'total_keys' => $redisInfo['db0']['keys'] ?? 0,
                    'expires' => $redisInfo['db0']['expires'] ?? 0,
                    'avg_ttl' => $redisInfo['db0']['avg_ttl'] ?? 0,
                ],

                // Connection and network stats
                'network' => [
                    'total_connections_received' => $redisInfo['total_connections_received'] ?? 0,
                    'total_commands_processed' => $redisInfo['total_commands_processed'] ?? 0,
                    'instantaneous_ops_per_sec' => $redisInfo['instantaneous_ops_per_sec'] ?? 0,
                    'total_net_input_bytes' => $redisInfo['total_net_input_bytes'] ?? 0,
                    'total_net_output_bytes' => $redisInfo['total_net_output_bytes'] ?? 0,
                ],

                // Custom application metrics
                'application' => $this->getApplicationMetrics(),

                // Tenant-specific metrics
                'tenants' => $this->getTenantMetrics(),

                'timestamp' => now(),
            ];

            // Cache the metrics for dashboard use
            Cache::put(CacheKeyStrategy::systemConfig() . ':metrics', $metrics, self::METRICS_TTL);

            return $metrics;

        } catch (\Exception $e) {
            Log::error('Failed to get cache metrics', [
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return ['error' => 'Metrics unavailable', 'timestamp' => now()];
        }
    }

    /**
     * Get cache health status for enterprise monitoring
     */
    public function getHealthStatus(): array
    {
        try {
            $metrics = $this->getMetrics();

            $status = [
                'overall' => 'healthy',
                'redis_connected' => true,
                'hit_rate_acceptable' => true,
                'memory_usage_acceptable' => true,
                'performance_acceptable' => true,
                'issues' => [],
                'recommendations' => [],
            ];

            // Check hit rate health (below 70% is concerning)
            $hitRate = $metrics['performance']['hit_rate_percentage'] ?? 0;
            if ($hitRate < 70) {
                $status['hit_rate_acceptable'] = false;
                $status['overall'] = 'warning';
                $status['issues'][] = "Low cache hit rate: {$hitRate}%";
                $status['recommendations'][] = 'Review cache key strategies and TTL settings';
            }

            // Check memory usage (above 80% is concerning)
            $usedMemory = $metrics['redis_info']['used_memory'] ?? 0;
            $maxMemory = $this->getRedisMaxMemory();
            if ($maxMemory > 0) {
                $memoryUsagePercent = ($usedMemory / $maxMemory) * 100;
                if ($memoryUsagePercent > 80) {
                    $status['memory_usage_acceptable'] = false;
                    $status['overall'] = 'warning';
                    $status['issues'][] = "High memory usage: {$memoryUsagePercent}%";
                    $status['recommendations'][] = 'Consider increasing Redis memory or implementing cache cleanup';
                }
            }

            // Check operations per second (performance indicator)
            $opsPerSec = $metrics['network']['instantaneous_ops_per_sec'] ?? 0;
            if ($opsPerSec > 10000) {
                $status['recommendations'][] = 'High operations per second detected, monitor for potential bottlenecks';
            }

            // Set overall status based on individual checks
            if (in_array(false, [$status['redis_connected'], $status['hit_rate_acceptable'], $status['memory_usage_acceptable']])) {
                $status['overall'] = count($status['issues']) > 2 ? 'critical' : 'warning';
            }

            return $status;

        } catch (\Exception $e) {
            return [
                'overall' => 'critical',
                'redis_connected' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ];
        }
    }

    /**
     * Get tenant-specific cache performance metrics
     */
    public function getTenantCacheMetrics(string $tenantId): array
    {
        $cacheKey = CacheKeyStrategy::withTenantContext('metrics:performance');

        return Cache::remember($cacheKey, 300, function () use ($tenantId) {
            try {
                $redis = Redis::connection();
                $pattern = "arioli:*:tenant:{$tenantId}:*";
                $keys = $redis->keys($pattern);

                $totalKeys = count($keys);
                $totalMemory = 0;
                $keyTypes = [];

                foreach ($keys as $key) {
                    $type = $redis->type($key);
                    $keyTypes[$type] = ($keyTypes[$type] ?? 0) + 1;

                    // Estimate memory usage (approximation)
                    $totalMemory += $redis->memory('usage', $key) ?? 0;
                }

                return [
                    'tenant_id' => $tenantId,
                    'total_keys' => $totalKeys,
                    'total_memory_bytes' => $totalMemory,
                    'total_memory_human' => $this->formatBytes($totalMemory),
                    'key_types' => $keyTypes,
                    'average_key_size' => $totalKeys > 0 ? round($totalMemory / $totalKeys) : 0,
                    'timestamp' => now(),
                ];

            } catch (\Exception $e) {
                Log::error('Failed to get tenant cache metrics', [
                    'tenant_id' => $tenantId,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'tenant_id' => $tenantId,
                    'error' => 'Metrics unavailable',
                    'timestamp' => now(),
                ];
            }
        });
    }

    /**
     * Get slow cache operations for performance analysis
     */
    public function getSlowOperations(int $thresholdMs = 100): array
    {
        $slowOpsKey = CacheKeyStrategy::systemConfig() . ':slow_operations';

        return Cache::get($slowOpsKey, []);
    }

    /**
     * Get cache usage by category for optimization insights
     */
    public function getCacheUsageByCategory(): array
    {
        try {
            $redis = Redis::connection();
            $allKeys = $redis->keys('*');

            $categories = [
                'dashboard' => [],
                'tickets' => [],
                'licenses' => [],
                'users' => [],
                'sessions' => [],
                'other' => [],
            ];

            foreach ($allKeys as $key) {
                $category = $this->categorizeKey($key);
                $categories[$category][] = $key;
            }

            $usage = [];
            foreach ($categories as $category => $keys) {
                $totalMemory = 0;
                foreach ($keys as $key) {
                    $totalMemory += $redis->memory('usage', $key) ?? 0;
                }

                $usage[$category] = [
                    'key_count' => count($keys),
                    'total_memory_bytes' => $totalMemory,
                    'total_memory_human' => $this->formatBytes($totalMemory),
                    'average_key_size' => count($keys) > 0 ? round($totalMemory / count($keys)) : 0,
                ];
            }

            return $usage;

        } catch (\Exception $e) {
            Log::error('Failed to get cache usage by category', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Record cache operation metric
     */
    private function recordMetric(string $operation, string $key, ?string $tenantId, ?float $executionTime, array $additional = []): void
    {
        try {
            $metric = [
                'operation' => $operation,
                'key' => $key,
                'tenant_id' => $tenantId,
                'execution_time_ms' => $executionTime ? round($executionTime * 1000, 2) : null,
                'timestamp' => now(),
                ...$additional
            ];

            // Store in application metrics for analysis
            $metricsKey = CacheKeyStrategy::systemConfig() . ':operation_metrics';
            $currentMetrics = Cache::get($metricsKey, []);
            $currentMetrics[] = $metric;

            // Keep only last 1000 metrics to prevent memory issues
            if (count($currentMetrics) > 1000) {
                $currentMetrics = array_slice($currentMetrics, -1000);
            }

            Cache::put($metricsKey, $currentMetrics, self::METRICS_TTL);

            // Record slow operations separately
            if ($executionTime && $executionTime > 0.1) { // 100ms threshold
                $this->recordSlowOperation($metric);
            }

        } catch (\Exception $e) {
            Log::error('Failed to record cache metric', [
                'operation' => $operation,
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Record slow cache operation for performance analysis
     */
    private function recordSlowOperation(array $metric): void
    {
        $slowOpsKey = CacheKeyStrategy::systemConfig() . ':slow_operations';
        $slowOps = Cache::get($slowOpsKey, []);
        $slowOps[] = $metric;

        // Keep only last 100 slow operations
        if (count($slowOps) > 100) {
            $slowOps = array_slice($slowOps, -100);
        }

        Cache::put($slowOpsKey, $slowOps, self::METRICS_TTL);
    }

    /**
     * Get application-specific metrics
     */
    private function getApplicationMetrics(): array
    {
        $metricsKey = CacheKeyStrategy::systemConfig() . ':operation_metrics';
        $operations = Cache::get($metricsKey, []);

        if (empty($operations)) {
            return [];
        }

        $hits = array_filter($operations, fn($op) => $op['operation'] === 'hit');
        $misses = array_filter($operations, fn($op) => $op['operation'] === 'miss');
        $writes = array_filter($operations, fn($op) => $op['operation'] === 'write');

        $totalOps = count($operations);
        $hitCount = count($hits);
        $missCount = count($misses);

        return [
            'total_operations' => $totalOps,
            'hits' => $hitCount,
            'misses' => $missCount,
            'writes' => count($writes),
            'hit_rate' => $totalOps > 0 ? round(($hitCount / $totalOps) * 100, 2) : 0,
            'average_execution_time' => $this->calculateAverageExecutionTime($operations),
        ];
    }

    /**
     * Get tenant-specific metrics summary
     */
    private function getTenantMetrics(): array
    {
        // This would typically query for active tenants
        // For now, return a summary structure
        return [
            'total_tenants_with_cache' => 0, // Would be calculated from actual data
            'average_cache_size_per_tenant' => 0,
            'most_active_tenant' => null,
        ];
    }

    /**
     * Categorize cache key for usage analysis
     */
    private function categorizeKey(string $key): string
    {
        if (str_contains($key, 'dashboard')) return 'dashboard';
        if (str_contains($key, 'ticket')) return 'tickets';
        if (str_contains($key, 'license')) return 'licenses';
        if (str_contains($key, 'user')) return 'users';
        if (str_contains($key, 'session')) return 'sessions';

        return 'other';
    }

    /**
     * Calculate average execution time from operations
     */
    private function calculateAverageExecutionTime(array $operations): float
    {
        $times = array_filter(array_column($operations, 'execution_time_ms'));

        if (empty($times)) {
            return 0;
        }

        return round(array_sum($times) / count($times), 2);
    }

    /**
     * Get Redis max memory setting
     */
    private function getRedisMaxMemory(): int
    {
        try {
            $redis = Redis::connection();
            $config = $redis->config('get', 'maxmemory');

            return (int) ($config['maxmemory'] ?? 0);

        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}