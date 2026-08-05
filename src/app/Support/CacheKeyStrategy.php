<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class CacheKeyStrategy
{
    /**
     * Enterprise cache key strategy for optimized performance
     * Provides consistent, collision-free cache keys across all components
     */

    /**
     * Dashboard statistics cache keys with tenant isolation
     */
    public static function dashboardStats(string $tenantId): string
    {
        return "dashboard:stats:{$tenantId}";
    }

    public static function dashboardRecentActivity(string $tenantId, int $days = 7): string
    {
        return "dashboard:activity:{$tenantId}:days_{$days}";
    }

    public static function dashboardTicketStats(string $tenantId): string
    {
        return "dashboard:tickets:{$tenantId}";
    }

    /**
     * License validation cache keys for high-frequency access
     */
    public static function licenseValidation(string $tenantId): string
    {
        return "license:validation:{$tenantId}";
    }

    public static function licenseStatus(string $tenantId): string
    {
        return "license:status:{$tenantId}";
    }

    public static function licenseExpiration(string $tenantId): string
    {
        return "license:expiration:{$tenantId}";
    }

    /**
     * User-specific cache keys
     */
    public static function userProfile(int $userId): string
    {
        return "user:profile:{$userId}";
    }

    public static function userPermissions(int $userId): string
    {
        return "user:permissions:{$userId}";
    }

    public static function userTenantAccess(int $userId, string $tenantId): string
    {
        return "user:tenant_access:{$userId}:{$tenantId}";
    }

    /**
     * Ticket system cache keys
     */
    public static function ticketStats(string $tenantId): string
    {
        return "tickets:stats:{$tenantId}";
    }

    public static function ticketList(string $tenantId, array $filters = []): string
    {
        $filterHash = md5(serialize($filters));
        return "tickets:list:{$tenantId}:filters_{$filterHash}";
    }

    public static function ticketDetail(int $ticketId): string
    {
        return "ticket:detail:{$ticketId}";
    }

    /**
     * Admin panel cache keys
     */
    public static function adminTenantList(array $filters = []): string
    {
        $filterHash = md5(serialize($filters));
        return "admin:tenants:filters_{$filterHash}";
    }

    public static function adminStats(): string
    {
        return "admin:stats:global";
    }

    public static function adminLicenseStats(): string
    {
        return "admin:licenses:stats";
    }

    /**
     * Session-related cache keys
     */
    public static function sessionData(string $sessionId): string
    {
        return "session:data:{$sessionId}";
    }

    public static function sessionTenant(string $sessionId): string
    {
        return "session:tenant:{$sessionId}";
    }

    /**
     * Performance optimization cache keys
     */
    public static function queryResult(string $table, array $conditions = []): string
    {
        $conditionHash = md5(serialize($conditions));
        return "query:{$table}:conditions_{$conditionHash}";
    }

    public static function modelCollection(string $model, string $tenantId, array $params = []): string
    {
        $paramHash = md5(serialize($params));
        return "model:{$model}:tenant_{$tenantId}:params_{$paramHash}";
    }

    /**
     * API response cache keys
     */
    public static function apiResponse(string $endpoint, array $params = []): string
    {
        $paramHash = md5(serialize($params));
        return "api:response:{$endpoint}:params_{$paramHash}";
    }

    public static function apiLicenseValidation(string $tenantId): string
    {
        return "api:license_validation:{$tenantId}";
    }

    /**
     * File and asset cache keys
     */
    public static function fileCache(string $filename): string
    {
        return "file:cache:" . md5($filename);
    }

    public static function imageCache(string $imagePath, array $dimensions = []): string
    {
        $dimHash = empty($dimensions) ? 'original' : md5(serialize($dimensions));
        return "image:cache:" . md5($imagePath) . ":{$dimHash}";
    }

    /**
     * Configuration cache keys
     */
    public static function tenantConfig(string $tenantId): string
    {
        return "config:tenant:{$tenantId}";
    }

    public static function systemConfig(): string
    {
        return "config:system";
    }

    /**
     * Enterprise reporting cache keys
     */
    public static function monthlyReport(string $tenantId, string $month): string
    {
        return "report:monthly:{$tenantId}:{$month}";
    }

    public static function performanceMetrics(string $tenantId, string $period = 'daily'): string
    {
        return "metrics:performance:{$tenantId}:{$period}";
    }

    /**
     * Search and filtering cache keys
     */
    public static function searchResults(string $query, string $type = 'global'): string
    {
        $queryHash = md5($query);
        return "search:{$type}:query_{$queryHash}";
    }

    public static function filterResults(string $entity, array $filters): string
    {
        $filterHash = md5(serialize($filters));
        return "filter:{$entity}:filters_{$filterHash}";
    }

    /**
     * Notification cache keys
     */
    public static function userNotifications(int $userId): string
    {
        return "notifications:user:{$userId}";
    }

    public static function tenantNotifications(string $tenantId): string
    {
        return "notifications:tenant:{$tenantId}";
    }

    /**
     * Utility methods for cache key generation
     */

    /**
     * Generate cache key with automatic tenant context
     */
    public static function withTenantContext(string $baseKey): string
    {
        $tenantId = self::getCurrentTenantId();
        return "{$baseKey}:tenant_{$tenantId}";
    }

    /**
     * Generate cache key with user context
     */
    public static function withUserContext(string $baseKey): string
    {
        $userId = self::getCurrentUserId();
        return "{$baseKey}:user_{$userId}";
    }

    /**
     * Generate cache key with timestamp for time-sensitive data
     */
    public static function withTimestamp(string $baseKey, int $minutes = 60): string
    {
        $timeSlot = floor(time() / ($minutes * 60));
        return "{$baseKey}:time_{$timeSlot}";
    }

    /**
     * Generate cache key for paginated results
     */
    public static function withPagination(string $baseKey, int $page, int $perPage): string
    {
        return "{$baseKey}:page_{$page}:per_{$perPage}";
    }

    /**
     * Generate versioned cache key for invalidation strategies
     */
    public static function withVersion(string $baseKey, string $version = '1.0'): string
    {
        return "{$baseKey}:v{$version}";
    }

    /**
     * Generate hierarchical cache key for nested data
     */
    public static function hierarchical(array $segments): string
    {
        return implode(':', array_filter($segments));
    }

    /**
     * Helper methods
     */
    private static function getCurrentTenantId(): string
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

        // Fallback
        return 'system';
    }

    private static function getCurrentUserId(): int
    {
        $user = Auth::user() ?? Auth::guard('cliente')->user();
        return $user ? $user->id : 0;
    }

    /**
     * Cache TTL constants for different data types
     */
    public const TTL_VERY_SHORT = 60;        // 1 minute - real-time data
    public const TTL_SHORT = 300;           // 5 minutes - dashboard stats
    public const TTL_MEDIUM = 1800;         // 30 minutes - user permissions
    public const TTL_LONG = 3600;           // 1 hour - configuration data
    public const TTL_VERY_LONG = 86400;     // 24 hours - static data
    public const TTL_PERMANENT = 604800;     // 1 week - rarely changing data

    /**
     * Get recommended TTL for different data types
     */
    public static function getTTL(string $dataType): int
    {
        return match($dataType) {
            'dashboard_stats' => self::TTL_SHORT,
            'license_validation' => self::TTL_SHORT,
            'user_permissions' => self::TTL_MEDIUM,
            'tenant_config' => self::TTL_LONG,
            'static_content' => self::TTL_VERY_LONG,
            'system_config' => self::TTL_PERMANENT,
            default => self::TTL_MEDIUM,
        };
    }
}