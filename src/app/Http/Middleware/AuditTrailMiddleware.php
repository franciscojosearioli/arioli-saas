<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditTrailMiddleware
{
    /**
     * Enterprise audit trail middleware for comprehensive logging
     * Logs all requests with user context, tenant information, and security events
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $requestId = $request->header('X-Request-ID') ?: uniqid('req_', true);

        // Get user context for audit trail
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        $userEmail = $user ? $user->email : null;
        $userTenantId = $user ? $user->tenant_id : null;

        // Determine if this is an admin or client operation
        $isAdminOperation = $user && $user->tenant_id === null && $user->client_id === null;
        $isClientOperation = $user && ($user->tenant_id !== null || $user->client_id !== null);
        $isGuestOperation = !$user;

        // Extract important request information
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        $routeAction = $route ? $route->getActionName() : null;
        $httpMethod = $request->getMethod();
        $requestPath = $request->getPathInfo();
        $requestUrl = $request->fullUrl();
        $userAgent = $request->userAgent();
        $ipAddress = $request->ip();

        // Log enterprise audit information - REQUEST START
        Log::info('Enterprise audit trail - Request initiated', [
            'request_id' => $requestId,
            'http_method' => $httpMethod,
            'request_path' => $requestPath,
            'request_url' => $requestUrl,
            'route_name' => $routeName,
            'route_action' => $routeAction,
            'user_id' => $userId,
            'user_email' => $userEmail,
            'user_tenant_id' => $userTenantId,
            'is_admin_operation' => $isAdminOperation,
            'is_client_operation' => $isClientOperation,
            'is_guest_operation' => $isGuestOperation,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'timestamp' => now(),
            'session_id' => $request->session()->getId(),
            'request_size' => strlen($request->getContent()),
        ]);

        // Log sensitive operations with enhanced detail
        if ($this->isSensitiveOperation($httpMethod, $requestPath)) {
            Log::warning('Sensitive operation detected', [
                'request_id' => $requestId,
                'operation_type' => 'sensitive',
                'http_method' => $httpMethod,
                'request_path' => $requestPath,
                'user_id' => $userId,
                'user_email' => $userEmail,
                'user_tenant_id' => $userTenantId,
                'ip_address' => $ipAddress,
                'timestamp' => now(),
            ]);
        }

        // Log cross-tenant access attempts
        if ($this->isCrossTenantRisk($request, $userTenantId)) {
            Log::warning('Potential cross-tenant access detected', [
                'request_id' => $requestId,
                'risk_type' => 'cross_tenant_access',
                'user_tenant_id' => $userTenantId,
                'request_path' => $requestPath,
                'user_id' => $userId,
                'user_email' => $userEmail,
                'ip_address' => $ipAddress,
                'timestamp' => now(),
            ]);
        }

        // Log admin operations for compliance
        if ($isAdminOperation) {
            Log::info('Admin operation audit', [
                'request_id' => $requestId,
                'operation_type' => 'admin',
                'admin_user_id' => $userId,
                'admin_user_email' => $userEmail,
                'http_method' => $httpMethod,
                'request_path' => $requestPath,
                'route_name' => $routeName,
                'ip_address' => $ipAddress,
                'timestamp' => now(),
            ]);
        }

        // Process the request
        $response = $next($request);

        // Calculate execution time
        $executionTime = microtime(true) - $startTime;
        $responseStatus = $response->getStatusCode();
        $responseSize = strlen($response->getContent());

        // Log enterprise audit information - REQUEST COMPLETED
        Log::info('Enterprise audit trail - Request completed', [
            'request_id' => $requestId,
            'http_method' => $httpMethod,
            'request_path' => $requestPath,
            'route_name' => $routeName,
            'user_id' => $userId,
            'user_email' => $userEmail,
            'user_tenant_id' => $userTenantId,
            'response_status' => $responseStatus,
            'response_size' => $responseSize,
            'execution_time_ms' => round($executionTime * 1000, 2),
            'is_admin_operation' => $isAdminOperation,
            'is_client_operation' => $isClientOperation,
            'ip_address' => $ipAddress,
            'timestamp' => now(),
        ]);

        // Log errors and security issues
        if ($responseStatus >= 400) {
            $logLevel = $responseStatus >= 500 ? 'error' : 'warning';
            $errorType = $responseStatus >= 500 ? 'server_error' : 'client_error';

            Log::log($logLevel, 'Request completed with error', [
                'request_id' => $requestId,
                'error_type' => $errorType,
                'response_status' => $responseStatus,
                'http_method' => $httpMethod,
                'request_path' => $requestPath,
                'user_id' => $userId,
                'user_email' => $userEmail,
                'user_tenant_id' => $userTenantId,
                'execution_time_ms' => round($executionTime * 1000, 2),
                'ip_address' => $ipAddress,
                'timestamp' => now(),
            ]);

            // Log authentication/authorization failures
            if (in_array($responseStatus, [401, 403])) {
                Log::warning('Authentication/Authorization failure', [
                    'request_id' => $requestId,
                    'security_event' => 'auth_failure',
                    'response_status' => $responseStatus,
                    'http_method' => $httpMethod,
                    'request_path' => $requestPath,
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'user_tenant_id' => $userTenantId,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'timestamp' => now(),
                ]);
            }
        }

        // Log performance issues
        if ($executionTime > 5.0) { // Requests longer than 5 seconds
            Log::warning('Slow request detected', [
                'request_id' => $requestId,
                'performance_issue' => 'slow_request',
                'execution_time_ms' => round($executionTime * 1000, 2),
                'http_method' => $httpMethod,
                'request_path' => $requestPath,
                'user_id' => $userId,
                'user_tenant_id' => $userTenantId,
                'response_status' => $responseStatus,
                'timestamp' => now(),
            ]);
        }

        return $response;
    }

    /**
     * Determine if the operation is sensitive and requires enhanced logging
     *
     * @param string $method
     * @param string $path
     * @return bool
     */
    private function isSensitiveOperation(string $method, string $path): bool
    {
        // Define sensitive operation patterns
        $sensitivePatterns = [
            // User management
            'DELETE' => ['/admin/users/', '/admin/tenants/'],
            'PUT' => ['/admin/users/', '/admin/tenants/', '/admin/licenses/'],
            'PATCH' => ['/admin/users/', '/admin/tenants/', '/admin/licenses/'],

            // Financial operations
            'POST' => ['/admin/orders/', '/webhook/', '/refund/'],
            'PUT' => ['/admin/orders/'],
            'DELETE' => ['/admin/orders/'],

            // License management
            'POST' => ['/admin/licenses/'],
            'PUT' => ['/admin/licenses/'],
            'DELETE' => ['/admin/licenses/'],
        ];

        if (!isset($sensitivePatterns[$method])) {
            return false;
        }

        foreach ($sensitivePatterns[$method] as $pattern) {
            if (str_contains($path, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the request poses a cross-tenant access risk
     *
     * @param Request $request
     * @param string|null $userTenantId
     * @return bool
     */
    private function isCrossTenantRisk(Request $request, ?string $userTenantId): bool
    {
        // Skip check for admin users (tenant_id = null)
        if ($userTenantId === null) {
            return false;
        }

        $path = $request->getPathInfo();

        // Check for tenant ID in URL parameters
        $routeParams = $request->route() ? $request->route()->parameters() : [];

        // Look for tenant_id parameters that don't match the user's tenant
        if (isset($routeParams['tenant']) && $routeParams['tenant'] !== $userTenantId) {
            return true;
        }

        if (isset($routeParams['tenant_id']) && $routeParams['tenant_id'] !== $userTenantId) {
            return true;
        }

        // Check query parameters
        $queryTenantId = $request->query('tenant_id');
        if ($queryTenantId && $queryTenantId !== $userTenantId) {
            return true;
        }

        // Check request body for tenant_id (for POST/PUT requests)
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $bodyTenantId = $request->input('tenant_id');
            if ($bodyTenantId && $bodyTenantId !== $userTenantId) {
                return true;
            }
        }

        return false;
    }
}