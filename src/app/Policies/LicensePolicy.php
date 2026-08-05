<?php

namespace App\Policies;

use App\Models\User;
use App\Models\License;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class LicensePolicy
{
    /**
     * Determine if the user can view any licenses.
     * Admins can view all licenses, clients can view their tenant's licenses
     */
    public function viewAny(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('License viewAny authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $isAdmin,
            'policy' => 'LicensePolicy@viewAny',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return Response::allow(); // Both admins and clients can view licenses (with proper scoping)
    }

    /**
     * Determine if the user can view the license.
     * Complete tenant isolation - clients only see their tenant's licenses
     */
    public function view(User $user, License $license): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;
        $ownsTenant = $user->tenant_id === $license->tenant_id;
        $hasAccess = $isAdmin || $ownsTenant;

        // Enterprise audit logging for license access
        Log::info('License view authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'license_id' => $license->id,
            'license_tenant_id' => $license->tenant_id,
            'license_active' => $license->active,
            'license_expires_at' => $license->expires_at,
            'is_admin' => $isAdmin,
            'owns_tenant' => $ownsTenant,
            'access_granted' => $hasAccess,
            'policy' => 'LicensePolicy@view',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$hasAccess) {
            Log::warning('Unauthorized license access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'license_id' => $license->id,
                'license_tenant_id' => $license->tenant_id,
                'violation_type' => 'cross_tenant_license_access',
                'policy' => 'LicensePolicy@view',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $hasAccess
            ? Response::allow()
            : Response::deny('You can only access licenses for your own tenant.');
    }

    /**
     * Determine if the user can create licenses.
     * Only admin users can create and assign licenses
     */
    public function create(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('License creation authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'LicensePolicy@create',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Non-admin license creation attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'policy' => 'LicensePolicy@create',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can create licenses.');
    }

    /**
     * Determine if the user can update the license.
     * Only admin users can modify license terms and status
     */
    public function update(User $user, License $license): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('License update authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'license_id' => $license->id,
            'license_tenant_id' => $license->tenant_id,
            'license_active' => $license->active,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'LicensePolicy@update',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Unauthorized license modification attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'license_id' => $license->id,
                'license_tenant_id' => $license->tenant_id,
                'policy' => 'LicensePolicy@update',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can modify licenses.');
    }

    /**
     * Determine if the user can delete the license.
     * Only admin users can delete licenses
     */
    public function delete(User $user, License $license): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        // Critical operation - comprehensive audit logging
        Log::info('License deletion authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'license_id' => $license->id,
            'license_tenant_id' => $license->tenant_id,
            'license_active' => $license->active,
            'license_expires_at' => $license->expires_at,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'LicensePolicy@delete',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::critical('Unauthorized license deletion attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'license_id' => $license->id,
                'license_tenant_id' => $license->tenant_id,
                'severity' => 'critical_security_violation',
                'policy' => 'LicensePolicy@delete',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can delete licenses.');
    }

    /**
     * Determine if the user can activate/deactivate licenses.
     * Only admin users can change license activation status
     */
    public function activate(User $user, License $license): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('License activation authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'license_id' => $license->id,
            'license_tenant_id' => $license->tenant_id,
            'current_status' => $license->active,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'LicensePolicy@activate',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can activate/deactivate licenses.');
    }

    /**
     * Determine if the user can extend license periods.
     * Only admin users can modify license expiration dates
     */
    public function extend(User $user, License $license): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('License extension authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'license_id' => $license->id,
            'license_tenant_id' => $license->tenant_id,
            'current_expires_at' => $license->expires_at,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'LicensePolicy@extend',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can extend license periods.');
    }

    /**
     * Determine if the user can validate licenses.
     * Used by the ValidateLicense middleware for tenant access control
     */
    public function validate(User $user, License $license): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;
        $ownsTenant = $user->tenant_id === $license->tenant_id;
        $hasAccess = $isAdmin || $ownsTenant;

        // This is called frequently by middleware, log at info level
        Log::info('License validation authorization check', [
            'user_id' => $user->id,
            'user_tenant_id' => $user->tenant_id,
            'license_id' => $license->id,
            'license_tenant_id' => $license->tenant_id,
            'license_active' => $license->active,
            'license_expires_at' => $license->expires_at,
            'is_admin' => $isAdmin,
            'owns_tenant' => $ownsTenant,
            'access_granted' => $hasAccess,
            'policy' => 'LicensePolicy@validate',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return $hasAccess
            ? Response::allow()
            : Response::deny('License validation failed - tenant mismatch.');
    }
}