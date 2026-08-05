<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheMonitoringService;
use App\Services\TenantCacheService;
use App\Services\CacheInvalidationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:monitor
                            {action=status : Action to perform (status|stats|health|clear|warm)}
                            {--tenant= : Specific tenant ID for tenant operations}
                            {--format=table : Output format (table|json)}
                            {--export= : Export results to file}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor and manage enterprise Redis cache for multitenant SaaS';

    private CacheMonitoringService $monitoring;
    private TenantCacheService $tenantCache;
    private CacheInvalidationService $invalidation;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->monitoring = new CacheMonitoringService();
        $this->tenantCache = new TenantCacheService();
        $this->invalidation = new CacheInvalidationService();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        try {
            switch ($action) {
                case 'status':
                    return $this->showCacheStatus();

                case 'stats':
                    return $this->showCacheStats();

                case 'health':
                    return $this->showHealthStatus();

                case 'clear':
                    return $this->clearCache();

                case 'warm':
                    return $this->warmCache();

                default:
                    $this->error("Unknown action: {$action}");
                    $this->line('Available actions: status, stats, health, clear, warm');
                    return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("Command failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    /**
     * Show overall cache status
     */
    private function showCacheStatus(): int
    {
        $this->info('🚀 Arioli SaaS - Enterprise Cache Status');
        $this->newLine();

        try {
            // Redis connection test
            $redis = Redis::connection();
            $redisInfo = $redis->info();

            $this->line('✅ Redis Connection: <info>CONNECTED</info>');
            $this->line("📍 Redis Version: <info>{$redisInfo['redis_version']}</info>");
            $this->line("⏱️  Uptime: <info>" . $this->formatUptime($redisInfo['uptime_in_seconds']) . "</info>");
            $this->line("💾 Memory Used: <info>{$redisInfo['used_memory_human']}</info>");
            $this->line("🔌 Connected Clients: <info>{$redisInfo['connected_clients']}</info>");

            $this->newLine();

            // Cache stores status
            $this->info('📦 Cache Stores Status:');
            $stores = ['default', 'cache', 'tenant', 'sessions', 'statistics', 'licensing'];

            foreach ($stores as $store) {
                try {
                    $connection = Cache::store($store);
                    $testKey = 'arioli_health_check_' . time();
                    $connection->put($testKey, 'test', 1);
                    $connection->forget($testKey);

                    $this->line("  ✅ {$store}: <info>OK</info>");
                } catch (\Exception $e) {
                    $this->line("  ❌ {$store}: <error>ERROR - {$e->getMessage()}</error>");
                }
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Redis Connection: FAILED - {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    /**
     * Show detailed cache statistics
     */
    private function showCacheStats(): int
    {
        $this->info('📊 Enterprise Cache Statistics');
        $this->newLine();

        $metrics = $this->monitoring->getMetrics();

        if (isset($metrics['error'])) {
            $this->error("Failed to get metrics: {$metrics['error']}");
            return self::FAILURE;
        }

        // Performance metrics table
        if ($this->option('format') === 'table') {
            $this->table(['Metric', 'Value'], [
                ['Hit Rate', $metrics['performance']['hit_rate_percentage'] . '%'],
                ['Miss Rate', $metrics['performance']['miss_rate_percentage'] . '%'],
                ['Total Hits', number_format($metrics['performance']['total_hits'])],
                ['Total Misses', number_format($metrics['performance']['total_misses'])],
                ['Total Requests', number_format($metrics['performance']['total_requests'])],
                ['Operations/sec', number_format($metrics['network']['instantaneous_ops_per_sec'])],
                ['Total Keys', number_format($metrics['keys']['total_keys'])],
                ['Keys with TTL', number_format($metrics['keys']['expires'])],
            ]);

            $this->newLine();

            // Memory usage table
            $this->info('💾 Memory Usage:');
            $this->table(['Metric', 'Value'], [
                ['Used Memory', $metrics['redis_info']['used_memory_human']],
                ['Peak Memory', $metrics['redis_info']['used_memory_peak_human']],
                ['Connected Clients', $metrics['redis_info']['connected_clients']],
            ]);

        } else {
            // JSON output
            $this->line(json_encode($metrics, JSON_PRETTY_PRINT));
        }

        // Export if requested
        if ($exportFile = $this->option('export')) {
            file_put_contents($exportFile, json_encode($metrics, JSON_PRETTY_PRINT));
            $this->info("📁 Metrics exported to: {$exportFile}");
        }

        return self::SUCCESS;
    }

    /**
     * Show cache health status
     */
    private function showHealthStatus(): int
    {
        $this->info('🏥 Cache Health Check');
        $this->newLine();

        $health = $this->monitoring->getHealthStatus();

        // Overall status
        $statusColor = match($health['overall']) {
            'healthy' => 'info',
            'warning' => 'comment',
            'critical' => 'error',
            default => 'comment'
        };

        $this->line("Overall Status: <{$statusColor}>" . strtoupper($health['overall']) . "</{$statusColor}>");

        // Individual checks
        $checks = [
            'Redis Connected' => $health['redis_connected'],
            'Hit Rate Acceptable' => $health['hit_rate_acceptable'],
            'Memory Usage Acceptable' => $health['memory_usage_acceptable'],
            'Performance Acceptable' => $health['performance_acceptable'],
        ];

        $this->newLine();
        foreach ($checks as $check => $status) {
            $icon = $status ? '✅' : '❌';
            $color = $status ? 'info' : 'error';
            $this->line("{$icon} {$check}: <{$color}>" . ($status ? 'PASS' : 'FAIL') . "</{$color}>");
        }

        // Issues and recommendations
        if (!empty($health['issues'])) {
            $this->newLine();
            $this->warn('⚠️  Issues Found:');
            foreach ($health['issues'] as $issue) {
                $this->line("  • {$issue}");
            }
        }

        if (!empty($health['recommendations'])) {
            $this->newLine();
            $this->info('💡 Recommendations:');
            foreach ($health['recommendations'] as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }

        return $health['overall'] === 'critical' ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Clear cache with options
     */
    private function clearCache(): int
    {
        $tenantId = $this->option('tenant');

        if ($tenantId) {
            // Clear specific tenant cache
            $this->info("🧹 Clearing cache for tenant: {$tenantId}");

            $tenantCache = new TenantCacheService($tenantId);
            $result = $tenantCache->flushTenant();

            if ($result) {
                $this->info("✅ Tenant cache cleared successfully");
            } else {
                $this->error("❌ Failed to clear tenant cache");
                return self::FAILURE;
            }

        } else {
            // Clear all cache
            if (!$this->confirm('⚠️  This will clear ALL cache data. Continue?')) {
                $this->info('Operation cancelled.');
                return self::SUCCESS;
            }

            $this->info('🧹 Clearing all cache stores...');

            $stores = ['default', 'cache', 'tenant', 'sessions', 'statistics', 'licensing'];
            $cleared = [];
            $failed = [];

            foreach ($stores as $store) {
                try {
                    Cache::store($store)->flush();
                    $cleared[] = $store;
                    $this->line("  ✅ Cleared: {$store}");
                } catch (\Exception $e) {
                    $failed[] = "{$store}: {$e->getMessage()}";
                    $this->line("  ❌ Failed: {$store}");
                }
            }

            $this->newLine();
            $this->info("✅ Cleared " . count($cleared) . " cache stores");

            if (!empty($failed)) {
                $this->warn("⚠️  Failed to clear " . count($failed) . " stores");
                foreach ($failed as $failure) {
                    $this->line("  • {$failure}");
                }
            }
        }

        return self::SUCCESS;
    }

    /**
     * Warm cache with common data
     */
    private function warmCache(): int
    {
        $tenantId = $this->option('tenant');

        if ($tenantId) {
            // Warm specific tenant cache
            $this->info("🔥 Warming cache for tenant: {$tenantId}");

            $tenantCache = new TenantCacheService($tenantId);
            $warmed = $tenantCache->warmDashboardCache();

            $this->info("✅ Warmed " . count($warmed) . " cache keys for tenant");
            foreach ($warmed as $key) {
                $this->line("  • {$key}");
            }

        } else {
            $this->info('🔥 Warming system cache...');

            // Warm system-level cache
            $this->line("  • Warming configuration cache...");
            \Artisan::call('config:cache');

            $this->line("  • Warming route cache...");
            \Artisan::call('route:cache');

            $this->line("  • Warming view cache...");
            \Artisan::call('view:cache');

            $this->info("✅ System cache warmed successfully");
        }

        return self::SUCCESS;
    }

    /**
     * Format uptime seconds to human readable
     */
    private function formatUptime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($days > 0) {
            return "{$days} days, {$hours} hours";
        } elseif ($hours > 0) {
            return "{$hours} hours, {$minutes} minutes";
        } else {
            return "{$minutes} minutes";
        }
    }
}