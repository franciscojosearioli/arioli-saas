<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TenantSessionMiddleware
{
    /**
     * Enterprise tenant session isolation middleware
     * Ensures complete session separation between tenants for security compliance
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->initializeTenantSession($request);

        $response = $next($request);

        $this->finalizeTenantSession($request);

        return $response;
    }

    /**
     * Initialize tenant-specific session context
     */
    private function initializeTenantSession(Request $request): void
    {
        $tenantId = $this->extractTenantId($request);

        if (!$tenantId) {
            return; // No tenant context, use default session
        }

        // Enterprise logging for session initialization
        Log::info('Tenant session initialization', [
            'tenant_id' => $tenantId,
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        // Set tenant context in session for isolation
        $this->setTenantContext($tenantId);

        // Validate session tenant consistency for security
        $this->validateSessionTenantConsistency($tenantId);

        // Apply tenant-specific session configuration
        $this->configureTenantSessionSettings($tenantId);
    }

    /**
     * Finalize tenant session processing
     */
    private function finalizeTenantSession(Request $request): void
    {
        $tenantId = $this->extractTenantId($request);

        if (!$tenantId) {
            return;
        }

        // Enterprise logging for session finalization
        Log::info('Tenant session finalized', [
            'tenant_id' => $tenantId,
            'session_id' => session()->getId(),
            'session_lifetime' => session()->get('_tenant_session_lifetime', 0),
            'timestamp' => now()
        ]);

        // Record session metrics for enterprise monitoring
        $this->recordSessionMetrics($tenantId);
    }

    /**
     * Extract tenant ID from various request sources
     */
    private function extractTenantId(Request $request): ?string
    {
        // Priority 1: Authenticated user's tenant
        $user = Auth::user() ?? Auth::guard('cliente')->user();
        if ($user && $user->tenant_id) {
            return $user->tenant_id;
        }

        // Priority 2: Tenancy context (if using Stancl/Tenancy)
        if (function_exists('tenant') && tenant()) {
            return tenant()->id;
        }

        // Priority 3: Subdomain extraction for tenant routing
        $host = $request->getHost();
        if ($tenantId = $this->extractTenantFromSubdomain($host)) {
            return $tenantId;
        }

        // Priority 4: Request parameters (for API calls)
        if ($request->has('tenant_id')) {
            return $request->get('tenant_id');
        }

        // Priority 5: Session storage (existing session)
        if (Session::has('tenant_id')) {
            return Session::get('tenant_id');
        }

        return null;
    }

    /**
     * Extract tenant from subdomain pattern.
     *
     * NOTE: The SaaS Core only receives requests for central domains (admin.*, cliente.*).
     * Tenant app requests (*.servis.*, *.clinica.*, *.loteos.*, *.tallerpro.*, etc.) are
     * routed by nginx directly to each app container and never reach this middleware.
     * These patterns exist as a fallback for edge cases (e.g. shared-container deployments).
     */
    private function extractTenantFromSubdomain(string $host): ?string
    {
        // Commercial domains (ADR-001 public_domain values)
        if (preg_match('/^([^.]+)\.loteos\./', $host, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^([^.]+)\.servis\./', $host, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^([^.]+)\.clinica\./', $host, $matches)) {
            return $matches[1];
        }

        // Legacy slug-based domains (backward compat for existing tenants)
        if (preg_match('/^([^.]+)\.tallerpro\./', $host, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^([^.]+)\.historias-clinicas\./', $host, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Set tenant context in session for complete isolation
     */
    private function setTenantContext(string $tenantId): void
    {
        // Set tenant ID in session
        Session::put('tenant_id', $tenantId);
        Session::put('_tenant_session_start', time());
        Session::put('_tenant_session_lifetime', 0);

        // Generate tenant-specific session identifier
        $tenantSessionId = $this->generateTenantSessionId($tenantId);
        Session::put('_tenant_session_id', $tenantSessionId);

        // Enterprise audit trail
        Log::info('Tenant context set in session', [
            'tenant_id' => $tenantId,
            'tenant_session_id' => $tenantSessionId,
            'session_id' => session()->getId(),
            'timestamp' => now()
        ]);
    }

    /**
     * Validate session tenant consistency for security
     */
    private function validateSessionTenantConsistency(string $currentTenantId): void
    {
        $sessionTenantId = Session::get('tenant_id');

        // If session has different tenant, it's a potential security issue
        if ($sessionTenantId && $sessionTenantId !== $currentTenantId) {
            Log::warning('Tenant session inconsistency detected', [
                'current_tenant_id' => $currentTenantId,
                'session_tenant_id' => $sessionTenantId,
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
                'security_event' => 'tenant_session_mismatch'
            ]);

            // Clear session for security
            Session::flush();
            Session::regenerate();

            // Log security action
            Log::warning('Session cleared due to tenant mismatch', [
                'cleared_tenant_id' => $sessionTenantId,
                'new_tenant_id' => $currentTenantId,
                'new_session_id' => session()->getId(),
                'timestamp' => now()
            ]);
        }
    }

    /**
     * Apply tenant-specific session configuration
     */
    private function configureTenantSessionSettings(string $tenantId): void
    {
        // Set tenant-specific session lifetime
        $lifetime = $this->getTenantSessionLifetime($tenantId);
        config(['session.lifetime' => $lifetime]);

        // Set tenant-specific cookie configuration
        $cookieName = config('session.cookie') . '-' . $tenantId;
        config(['session.cookie' => $cookieName]);

        // Enterprise audit
        Log::info('Tenant session configuration applied', [
            'tenant_id' => $tenantId,
            'session_lifetime' => $lifetime,
            'cookie_name' => $cookieName,
            'timestamp' => now()
        ]);
    }

    /**
     * Record session metrics for enterprise monitoring
     */
    private function recordSessionMetrics(string $tenantId): void
    {
        $startTime = Session::get('_tenant_session_start', time());
        $currentTime = time();
        $duration = $currentTime - $startTime;

        // Update session lifetime
        Session::put('_tenant_session_lifetime', $duration);

        // Record metrics for enterprise dashboards
        Log::info('Tenant session metrics', [
            'tenant_id' => $tenantId,
            'session_id' => session()->getId(),
            'session_duration' => $duration,
            'session_data_size' => strlen(serialize(Session::all())),
            'timestamp' => now()
        ]);

        // Store metrics in cache for dashboard reporting
        $cacheKey = "session_metrics:tenant_{$tenantId}:daily";
        $metrics = cache()->get($cacheKey, []);
        $metrics[] = [
            'duration' => $duration,
            'timestamp' => $currentTime,
            'session_id' => session()->getId()
        ];
        cache()->put($cacheKey, $metrics, 86400); // Store for 24 hours
    }

    /**
     * Generate tenant-specific session identifier
     */
    private function generateTenantSessionId(string $tenantId): string
    {
        return hash('sha256', $tenantId . session()->getId() . time());
    }

    /**
     * Get tenant-specific session lifetime
     */
    private function getTenantSessionLifetime(string $tenantId): int
    {
        // Default session lifetime
        $defaultLifetime = config('session.lifetime', 120);

        // Tenant-specific lifetime configuration (could be stored in database)
        $tenantLifetimes = [
            'premium_tenants' => 240,  // 4 hours for premium
            'standard_tenants' => 120, // 2 hours for standard
        ];

        // Get tenant type from cache or database
        $tenantType = cache()->remember(
            "tenant_type:{$tenantId}",
            3600,
            fn() => $this->getTenantType($tenantId)
        );

        return $tenantLifetimes[$tenantType] ?? $defaultLifetime;
    }

    /**
     * Get tenant type for session configuration
     */
    private function getTenantType(string $tenantId): string
    {
        // This would typically query the database for tenant plan/type
        // For now, return standard as default
        return 'standard_tenants';
    }
}