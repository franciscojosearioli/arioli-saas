<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Support\CacheKeyStrategy;

class CacheInvalidationService
{
    /**
     * Enterprise cache invalidation strategies for tenant data consistency
     * Ensures data integrity across multitenant architecture
     */

    private TenantCacheService $tenantCache;
    private array $invalidationLog = [];

    public function __construct(?string $tenantId = null)
    {
        $this->tenantCache = new TenantCacheService($tenantId);
    }

    /**
     * Invalidate all tenant-related cache when tenant data changes
     */
    public function invalidateTenantData(string $tenantId, array $context = []): bool
    {
        $startTime = microtime(true);
        $invalidatedKeys = [];

        try {
            // Dashboard and statistics caches
            $dashboardKeys = [
                CacheKeyStrategy::dashboardStats($tenantId),
                CacheKeyStrategy::dashboardRecentActivity($tenantId),
                CacheKeyStrategy::dashboardTicketStats($tenantId),
            ];

            foreach ($dashboardKeys as $key) {
                if ($this->invalidateKey($key)) {
                    $invalidatedKeys[] = $key;
                }
            }

            // User-specific caches for this tenant
            $this->invalidateUserCachesForTenant($tenantId, $invalidatedKeys);

            // License and configuration caches
            $systemKeys = [
                CacheKeyStrategy::licenseValidation($tenantId),
                CacheKeyStrategy::licenseStatus($tenantId),
                CacheKeyStrategy::tenantConfig($tenantId),
            ];

            foreach ($systemKeys as $key) {
                if ($this->invalidateKey($key)) {
                    $invalidatedKeys[] = $key;
                }
            }

            $executionTime = microtime(true) - $startTime;

            // Enterprise audit logging
            Log::info('Tenant cache invalidation completed', [
                'tenant_id' => $tenantId,
                'context' => $context,
                'invalidated_keys' => $invalidatedKeys,
                'total_keys' => count($invalidatedKeys),
                'execution_time_ms' => round($executionTime * 1000, 2),
                'timestamp' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Tenant cache invalidation failed', [
                'tenant_id' => $tenantId,
                'context' => $context,
                'error' => $e->getMessage(),
                'partial_invalidated_keys' => $invalidatedKeys,
                'timestamp' => now()
            ]);

            return false;
        }
    }

    /**
     * Invalidate user-specific cache when user data changes
     */
    public function invalidateUserData(int $userId, string $tenantId, array $context = []): bool
    {
        $startTime = microtime(true);
        $invalidatedKeys = [];

        try {
            // User-specific caches
            $userKeys = [
                CacheKeyStrategy::userProfile($userId),
                CacheKeyStrategy::userPermissions($userId),
                CacheKeyStrategy::userTenantAccess($userId, $tenantId),
                CacheKeyStrategy::userNotifications($userId),
            ];

            foreach ($userKeys as $key) {
                if ($this->invalidateKey($key)) {
                    $invalidatedKeys[] = $key;
                }
            }

            // Tenant dashboard might be affected by user changes
            $this->invalidateKey(CacheKeyStrategy::dashboardStats($tenantId));
            $invalidatedKeys[] = CacheKeyStrategy::dashboardStats($tenantId);

            $executionTime = microtime(true) - $startTime;

            Log::info('User cache invalidation completed', [
                'user_id' => $userId,
                'tenant_id' => $tenantId,
                'context' => $context,
                'invalidated_keys' => $invalidatedKeys,
                'total_keys' => count($invalidatedKeys),
                'execution_time_ms' => round($executionTime * 1000, 2),
                'timestamp' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('User cache invalidation failed', [
                'user_id' => $userId,
                'tenant_id' => $tenantId,
                'context' => $context,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return false;
        }
    }

    /**
     * Invalidate ticket-related cache when tickets change
     */
    public function invalidateTicketData(int $ticketId, string $tenantId, array $context = []): bool
    {
        try {
            $invalidatedKeys = [];

            // Specific ticket cache
            $ticketKey = CacheKeyStrategy::ticketDetail($ticketId);
            if ($this->invalidateKey($ticketKey)) {
                $invalidatedKeys[] = $ticketKey;
            }

            // Tenant ticket statistics
            $statsKey = CacheKeyStrategy::ticketStats($tenantId);
            if ($this->invalidateKey($statsKey)) {
                $invalidatedKeys[] = $statsKey;
            }

            // Dashboard statistics (tickets affect dashboard)
            $dashboardKey = CacheKeyStrategy::dashboardStats($tenantId);
            if ($this->invalidateKey($dashboardKey)) {
                $invalidatedKeys[] = $dashboardKey;
            }

            // Invalidate ticket list caches (all variations)
            $this->invalidateTicketListCaches($tenantId, $invalidatedKeys);

            Log::info('Ticket cache invalidation completed', [
                'ticket_id' => $ticketId,
                'tenant_id' => $tenantId,
                'context' => $context,
                'invalidated_keys' => $invalidatedKeys,
                'total_keys' => count($invalidatedKeys),
                'timestamp' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Ticket cache invalidation failed', [
                'ticket_id' => $ticketId,
                'tenant_id' => $tenantId,
                'context' => $context,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return false;
        }
    }

    /**
     * Invalidate license-related cache when licenses change
     */
    public function invalidateLicenseData(string $tenantId, array $context = []): bool
    {
        try {
            $invalidatedKeys = [];

            // License-specific caches
            $licenseKeys = [
                CacheKeyStrategy::licenseValidation($tenantId),
                CacheKeyStrategy::licenseStatus($tenantId),
                CacheKeyStrategy::licenseExpiration($tenantId),
            ];

            foreach ($licenseKeys as $key) {
                if ($this->invalidateKey($key)) {
                    $invalidatedKeys[] = $key;
                }
            }

            // Dashboard statistics (license affects dashboard)
            $dashboardKey = CacheKeyStrategy::dashboardStats($tenantId);
            if ($this->invalidateKey($dashboardKey)) {
                $invalidatedKeys[] = $dashboardKey;
            }

            // Admin license statistics
            $adminStatsKey = CacheKeyStrategy::adminLicenseStats();
            if ($this->invalidateKey($adminStatsKey)) {
                $invalidatedKeys[] = $adminStatsKey;
            }

            Log::info('License cache invalidation completed', [
                'tenant_id' => $tenantId,
                'context' => $context,
                'invalidated_keys' => $invalidatedKeys,
                'total_keys' => count($invalidatedKeys),
                'timestamp' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('License cache invalidation failed', [
                'tenant_id' => $tenantId,
                'context' => $context,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return false;
        }
    }

    /**
     * Invalidate admin-level cache when system data changes
     */
    public function invalidateAdminData(array $context = []): bool
    {
        try {
            $invalidatedKeys = [];

            // Admin global statistics
            $adminKeys = [
                CacheKeyStrategy::adminStats(),
                CacheKeyStrategy::adminLicenseStats(),
            ];

            foreach ($adminKeys as $key) {
                if ($this->invalidateKey($key)) {
                    $invalidatedKeys[] = $key;
                }
            }

            // System configuration
            $systemKey = CacheKeyStrategy::systemConfig();
            if ($this->invalidateKey($systemKey)) {
                $invalidatedKeys[] = $systemKey;
            }

            // Invalidate admin tenant list caches
            $this->invalidateAdminListCaches($invalidatedKeys);

            Log::info('Admin cache invalidation completed', [
                'context' => $context,
                'invalidated_keys' => $invalidatedKeys,
                'total_keys' => count($invalidatedKeys),
                'timestamp' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Admin cache invalidation failed', [
                'context' => $context,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return false;
        }
    }

    /**
     * Cascade invalidation based on data relationships
     */
    public function cascadeInvalidation(string $entityType, array $entityData): bool
    {
        try {
            switch ($entityType) {
                case 'tenant_updated':
                    return $this->invalidateTenantData($entityData['tenant_id'], [
                        'trigger' => 'tenant_updated',
                        'entity_data' => $entityData
                    ]);

                case 'user_updated':
                    return $this->invalidateUserData(
                        $entityData['user_id'],
                        $entityData['tenant_id'],
                        ['trigger' => 'user_updated', 'entity_data' => $entityData]
                    );

                case 'ticket_created':
                case 'ticket_updated':
                case 'ticket_deleted':
                    return $this->invalidateTicketData(
                        $entityData['ticket_id'],
                        $entityData['tenant_id'],
                        ['trigger' => $entityType, 'entity_data' => $entityData]
                    );

                case 'license_updated':
                case 'license_expired':
                    return $this->invalidateLicenseData($entityData['tenant_id'], [
                        'trigger' => $entityType,
                        'entity_data' => $entityData
                    ]);

                default:
                    Log::warning('Unknown entity type for cascade invalidation', [
                        'entity_type' => $entityType,
                        'entity_data' => $entityData,
                        'timestamp' => now()
                    ]);
                    return false;
            }

        } catch (\Exception $e) {
            Log::error('Cascade invalidation failed', [
                'entity_type' => $entityType,
                'entity_data' => $entityData,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return false;
        }
    }

    /**
     * Get invalidation statistics for enterprise monitoring
     */
    public function getInvalidationStats(): array
    {
        return [
            'invalidation_log' => $this->invalidationLog,
            'total_operations' => count($this->invalidationLog),
            'timestamp' => now()
        ];
    }

    /**
     * Helper method to invalidate a single key with logging
     */
    private function invalidateKey(string $key): bool
    {
        try {
            $result = Cache::forget($key);

            $this->invalidationLog[] = [
                'key' => $key,
                'success' => $result,
                'timestamp' => microtime(true)
            ];

            return $result;

        } catch (\Exception $e) {
            $this->invalidationLog[] = [
                'key' => $key,
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => microtime(true)
            ];

            return false;
        }
    }

    /**
     * Invalidate user caches for a specific tenant
     */
    private function invalidateUserCachesForTenant(string $tenantId, array &$invalidatedKeys): void
    {
        // This would typically query for all users in the tenant
        // For now, we'll invalidate patterns that are commonly used
        $patterns = [
            "user:*:tenant_{$tenantId}",
            "session:*:tenant_{$tenantId}",
        ];

        foreach ($patterns as $pattern) {
            $this->invalidatePattern($pattern, $invalidatedKeys);
        }
    }

    /**
     * Invalidate ticket list caches with different filter combinations
     */
    private function invalidateTicketListCaches(string $tenantId, array &$invalidatedKeys): void
    {
        // Common filter patterns for ticket lists
        $patterns = [
            "tickets:list:{$tenantId}:*",
            "tickets:stats:{$tenantId}",
        ];

        foreach ($patterns as $pattern) {
            $this->invalidatePattern($pattern, $invalidatedKeys);
        }
    }

    /**
     * Invalidate admin list caches
     */
    private function invalidateAdminListCaches(array &$invalidatedKeys): void
    {
        $patterns = [
            "admin:tenants:*",
            "admin:tickets:*",
            "admin:licenses:*",
        ];

        foreach ($patterns as $pattern) {
            $this->invalidatePattern($pattern, $invalidatedKeys);
        }
    }

    /**
     * Invalidate keys matching a pattern
     */
    private function invalidatePattern(string $pattern, array &$invalidatedKeys): void
    {
        try {
            // Get Redis connection and find matching keys
            $redis = Cache::getRedis();
            $keys = $redis->keys($pattern);

            foreach ($keys as $key) {
                if ($this->invalidateKey($key)) {
                    $invalidatedKeys[] = $key;
                }
            }

        } catch (\Exception $e) {
            Log::error('Pattern invalidation failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
        }
    }
}