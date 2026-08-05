<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TenantCacheService;
use App\Services\CacheMonitoringService;
use App\Services\CacheInvalidationService;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Enterprise cache service provider - simplified for debugging
     */

    /**
     * Register cache services
     */
    public function register(): void
    {
        // Basic service registration only
        $this->app->singleton(TenantCacheService::class);
        $this->app->singleton(CacheMonitoringService::class);
        $this->app->singleton(CacheInvalidationService::class);

        // Basic aliases
        $this->app->alias(TenantCacheService::class, 'tenant.cache');
        $this->app->alias(CacheMonitoringService::class, 'cache.monitoring');
        $this->app->alias(CacheInvalidationService::class, 'cache.invalidation');
    }

    /**
     * Bootstrap cache services
     */
    public function boot(): void
    {
        \Log::info('CacheServiceProvider booted successfully');

        // ✅ Working features
        $this->configureRedisOptimization();
        $this->registerCustomCacheStores();
        $this->registerCacheMacros();

        // ❌ Event listeners disabled due to circular dependency issues
        // $this->registerCacheEventListeners();

        \Log::info('Enterprise cache system loaded', [
            'redis_optimization' => 'enabled',
            'custom_stores' => 'enabled',
            'cache_macros' => 'enabled',
            'event_listeners' => 'disabled_due_to_circular_deps'
        ]);
    }

    /**
     * Register cache event listeners for monitoring (safe version)
     */
    private function registerCacheEventListeners(): void
    {
        // Safe event listeners that avoid circular dependencies
        // Only register if we're not in console mode to avoid boot issues
        if (!app()->runningInConsole()) {

            \Event::listen(\Illuminate\Cache\Events\CacheHit::class, function ($event) {
                try {
                    // Use lazy loading to avoid circular dependencies
                    if (app()->bound(CacheMonitoringService::class)) {
                        $monitoring = app()->make(CacheMonitoringService::class);
                        $monitoring->recordHit($event->key);
                    }
                } catch (\Exception $e) {
                    // Silently log error to avoid breaking the application
                    \Log::debug('Cache hit monitoring failed', [
                        'key' => $event->key ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            });

            \Event::listen(\Illuminate\Cache\Events\CacheMissed::class, function ($event) {
                try {
                    if (app()->bound(CacheMonitoringService::class)) {
                        $monitoring = app()->make(CacheMonitoringService::class);
                        $monitoring->recordMiss($event->key);
                    }
                } catch (\Exception $e) {
                    \Log::debug('Cache miss monitoring failed', [
                        'key' => $event->key ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            });

            \Event::listen(\Illuminate\Cache\Events\KeyWritten::class, function ($event) {
                try {
                    if (app()->bound(CacheMonitoringService::class)) {
                        $monitoring = app()->make(CacheMonitoringService::class);
                        $valueSize = is_string($event->value) ? strlen($event->value) : strlen(serialize($event->value));
                        $monitoring->recordWrite($event->key, null, null, $valueSize);
                    }
                } catch (\Exception $e) {
                    \Log::debug('Cache write monitoring failed', [
                        'key' => $event->key ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            });
        }
    }

    /**
     * Register helpful cache macros
     */
    private function registerCacheMacros(): void
    {
        // Macro for tenant-aware remember
        \Illuminate\Support\Facades\Cache::macro('tenantRemember', function ($key, $ttl, $callback) {
            $tenantCache = app(TenantCacheService::class);
            return $tenantCache->remember($key, $ttl, $callback);
        });

        // Macro for warming multiple keys
        \Illuminate\Support\Facades\Cache::macro('warmKeys', function (array $keys, $ttl = 3600) {
            $warmed = [];
            foreach ($keys as $key => $callback) {
                try {
                    $this->remember($key, $ttl, $callback);
                    $warmed[] = $key;
                } catch (\Exception $e) {
                    \Log::error("Failed to warm cache key: {$key}", ['error' => $e->getMessage()]);
                }
            }
            return $warmed;
        });

        // Macro for invalidating patterns
        \Illuminate\Support\Facades\Cache::macro('forgetPattern', function ($pattern) {
            if ($this->getStore()->getRedis()) {
                $redis = $this->getStore()->getRedis();
                $keys = $redis->keys($pattern);
                if (!empty($keys)) {
                    return $redis->del($keys) > 0;
                }
            }
            return false;
        });

        // Macro for getting cache size
        \Illuminate\Support\Facades\Cache::macro('getSize', function ($key = null) {
            if ($this->getStore()->getRedis()) {
                $redis = $this->getStore()->getRedis();
                if ($key) {
                    return $redis->memory('usage', $key) ?? 0;
                } else {
                    $info = $redis->info();
                    return $info['used_memory'] ?? 0;
                }
            }
            return 0;
        });
    }

    /**
     * Register custom cache stores
     */
    private function registerCustomCacheStores(): void
    {
        // Register tenant-aware cache store
        \Illuminate\Support\Facades\Cache::extend('tenant_aware', function ($app, $config) {
            return \Illuminate\Support\Facades\Cache::repository(
                new \Illuminate\Cache\RedisStore(
                    $app['redis'],
                    $config['prefix'] ?? 'tenant_cache',
                    $config['connection'] ?? 'tenant_cache'
                )
            );
        });
    }

    /**
     * Configure Redis optimization settings
     */
    private function configureRedisOptimization(): void
    {
        // Log Redis extension availability for enterprise monitoring
        if (extension_loaded('redis') && class_exists(\Redis::class)) {
            \Log::info('Redis extension loaded', [
                'extension' => 'redis',
                'class_available' => class_exists(\Redis::class),
                'phpredis_version' => phpversion('redis') ?: 'unknown',
            ]);
        } else {
            \Log::warning('Redis extension not available', [
                'extension_loaded' => extension_loaded('redis'),
                'class_exists' => class_exists(\Redis::class),
            ]);
        }
    }

    /**
     * Get the services provided by the provider
     */
    public function provides(): array
    {
        return [
            TenantCacheService::class,
            CacheMonitoringService::class,
            CacheInvalidationService::class,
        ];
    }
}