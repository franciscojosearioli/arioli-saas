<?php

namespace App\Policies;

use App\Models\User;
use Stancl\Tenancy\Database\Models\Tenant;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class TenantPolicy
{
    /**
     * Determine if the user can view any tenants.
     * Only admin users (tenant_id = null) can see all tenants
     */
    public function viewAny(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        // Log admin tenant access for audit
        if ($isAdmin) {
            Log::info('Admin tenant access granted', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'action' => 'viewAny',
                'scope' => 'all_tenants',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);
        } else {
            Log::warning('Non-admin tenant access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'action' => 'viewAny',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can view all tenants.');
    }

    /**
     * Determine if the user can view the tenant.
     * Admins can view any tenant, clients can only view their own
     */
    public function view(User $user, Tenant $tenant): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;
        $hasAccess = $isAdmin || $user->tenant_id === $tenant->id;

        // Log tenant access attempt
        Log::info('Tenant view authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'requested_tenant_id' => $tenant->id,
            'is_admin' => $isAdmin,
            'access_granted' => $hasAccess,
            'policy' => 'TenantPolicy@view',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$hasAccess) {
            Log::warning('Unauthorized tenant access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'requested_tenant_id' => $tenant->id,
                'policy' => 'TenantPolicy@view',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $hasAccess
            ? Response::allow()
            : Response::deny('You can only access your own tenant data.');
    }

    /**
     * Determine if the user can create tenants.
     * Only admin users can create new tenants
     */
    public function create(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Tenant creation authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'TenantPolicy@create',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can create tenants.');
    }

    /**
     * Determine if the user can update the tenant.
     * Admins can update any tenant, clients cannot update tenants
     */
    public function update(User $user, Tenant $tenant): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Tenant update authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'target_tenant_id' => $tenant->id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'TenantPolicy@update',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Unauthorized tenant update attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'target_tenant_id' => $tenant->id,
                'policy' => 'TenantPolicy@update',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can update tenants.');
    }

    /**
     * Determine if the user can delete the tenant.
     * Only admin users can delete tenants
     */
    public function delete(User $user, Tenant $tenant): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Tenant deletion authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'target_tenant_id' => $tenant->id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'TenantPolicy@delete',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Unauthorized tenant deletion attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'target_tenant_id' => $tenant->id,
                'policy' => 'TenantPolicy@delete',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can delete tenants.');
    }

    /**
     * Determine if the user can access tenant statistics.
     * Only admin users can access cross-tenant statistics
     */
    public function viewStatistics(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Tenant statistics access check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'TenantPolicy@viewStatistics',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can view tenant statistics.');
    }
}