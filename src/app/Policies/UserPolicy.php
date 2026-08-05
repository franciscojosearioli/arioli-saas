<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     * Admins can view all users, clients can view users in their tenant
     */
    public function viewAny(User $user): Response
    {
        // Both admins and tenant users can view users (with proper scoping)
        Log::info('User viewAny authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $user->tenant_id === null && $user->client_id === null,
            'policy' => 'UserPolicy@viewAny',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return Response::allow();
    }

    /**
     * Determine if the user can view the specific user.
     * Complete tenant isolation - users can only see users in their context
     */
    public function view(User $authUser, User $targetUser): Response
    {
        $isAdmin = $authUser->tenant_id === null && $authUser->client_id === null;
        $sameUser = $authUser->id === $targetUser->id;
        $sameTenant = $authUser->tenant_id === $targetUser->tenant_id;
        $hasAccess = $isAdmin || $sameUser || ($authUser->tenant_id && $sameTenant);

        // Enterprise audit logging for user access
        Log::info('User view authorization check', [
            'auth_user_id' => $authUser->id,
            'auth_user_email' => $authUser->email,
            'auth_user_tenant_id' => $authUser->tenant_id,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'target_user_tenant_id' => $targetUser->tenant_id,
            'is_admin' => $isAdmin,
            'same_user' => $sameUser,
            'same_tenant' => $sameTenant,
            'access_granted' => $hasAccess,
            'policy' => 'UserPolicy@view',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$hasAccess) {
            Log::warning('Unauthorized user access attempt', [
                'auth_user_id' => $authUser->id,
                'auth_user_email' => $authUser->email,
                'auth_user_tenant_id' => $authUser->tenant_id,
                'target_user_id' => $targetUser->id,
                'target_user_tenant_id' => $targetUser->tenant_id,
                'violation_type' => 'cross_tenant_user_access',
                'policy' => 'UserPolicy@view',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $hasAccess
            ? Response::allow()
            : Response::deny('You can only access users within your tenant scope.');
    }

    /**
     * Determine if the user can create users.
     * Only admin users can create new users in the central system
     */
    public function create(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('User creation authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'UserPolicy@create',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Non-admin user creation attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'policy' => 'UserPolicy@create',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can create users in the central system.');
    }

    /**
     * Determine if the user can update the target user.
     * Admins can update any user, users can update themselves
     */
    public function update(User $authUser, User $targetUser): Response
    {
        $isAdmin = $authUser->tenant_id === null && $authUser->client_id === null;
        $sameUser = $authUser->id === $targetUser->id;
        $hasAccess = $isAdmin || $sameUser;

        Log::info('User update authorization check', [
            'auth_user_id' => $authUser->id,
            'auth_user_email' => $authUser->email,
            'auth_user_tenant_id' => $authUser->tenant_id,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'target_user_tenant_id' => $targetUser->tenant_id,
            'is_admin' => $isAdmin,
            'same_user' => $sameUser,
            'access_granted' => $hasAccess,
            'policy' => 'UserPolicy@update',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$hasAccess) {
            Log::warning('Unauthorized user update attempt', [
                'auth_user_id' => $authUser->id,
                'auth_user_email' => $authUser->email,
                'auth_user_tenant_id' => $authUser->tenant_id,
                'target_user_id' => $targetUser->id,
                'target_user_tenant_id' => $targetUser->tenant_id,
                'policy' => 'UserPolicy@update',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $hasAccess
            ? Response::allow()
            : Response::deny('You can only update your own profile or be an administrator.');
    }

    /**
     * Determine if the user can delete the target user.
     * Only admin users can delete users from the central system
     */
    public function delete(User $authUser, User $targetUser): Response
    {
        $isAdmin = $authUser->tenant_id === null && $authUser->client_id === null;
        $isDeletingSelf = $authUser->id === $targetUser->id;

        // Critical operation - comprehensive audit logging
        Log::info('User deletion authorization check', [
            'auth_user_id' => $authUser->id,
            'auth_user_email' => $authUser->email,
            'auth_user_tenant_id' => $authUser->tenant_id,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'target_user_tenant_id' => $targetUser->tenant_id,
            'is_admin' => $isAdmin,
            'is_deleting_self' => $isDeletingSelf,
            'access_granted' => $isAdmin,
            'policy' => 'UserPolicy@delete',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::critical('Unauthorized user deletion attempt', [
                'auth_user_id' => $authUser->id,
                'auth_user_email' => $authUser->email,
                'auth_user_tenant_id' => $authUser->tenant_id,
                'target_user_id' => $targetUser->id,
                'target_user_email' => $targetUser->email,
                'target_user_tenant_id' => $targetUser->tenant_id,
                'is_deleting_self' => $isDeletingSelf,
                'severity' => 'critical_security_violation',
                'policy' => 'UserPolicy@delete',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        if ($isDeletingSelf && $isAdmin) {
            Log::critical('Admin attempting to delete own account', [
                'auth_user_id' => $authUser->id,
                'auth_user_email' => $authUser->email,
                'severity' => 'critical_admin_self_deletion',
                'policy' => 'UserPolicy@delete',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return Response::deny('Administrators cannot delete their own account.');
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can delete users.');
    }

    /**
     * Determine if the user can assign roles to other users.
     * Only admin users can manage user roles and permissions
     */
    public function assignRole(User $authUser, User $targetUser): Response
    {
        $isAdmin = $authUser->tenant_id === null && $authUser->client_id === null;

        Log::info('User role assignment authorization check', [
            'auth_user_id' => $authUser->id,
            'auth_user_email' => $authUser->email,
            'auth_user_tenant_id' => $authUser->tenant_id,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'target_user_tenant_id' => $targetUser->tenant_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'UserPolicy@assignRole',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Unauthorized role assignment attempt', [
                'auth_user_id' => $authUser->id,
                'auth_user_email' => $authUser->email,
                'target_user_id' => $targetUser->id,
                'policy' => 'UserPolicy@assignRole',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can assign roles to users.');
    }

    /**
     * Determine if the user can impersonate other users.
     * Only admin users can impersonate for debugging and support
     */
    public function impersonate(User $authUser, User $targetUser): Response
    {
        $isAdmin = $authUser->tenant_id === null && $authUser->client_id === null;
        $isSameUser = $authUser->id === $targetUser->id;

        // Critical operation - always log impersonation attempts
        Log::info('User impersonation authorization check', [
            'auth_user_id' => $authUser->id,
            'auth_user_email' => $authUser->email,
            'auth_user_tenant_id' => $authUser->tenant_id,
            'target_user_id' => $targetUser->id,
            'target_user_email' => $targetUser->email,
            'target_user_tenant_id' => $targetUser->tenant_id,
            'is_admin' => $isAdmin,
            'is_same_user' => $isSameUser,
            'access_granted' => $isAdmin && !$isSameUser,
            'policy' => 'UserPolicy@impersonate',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::critical('Unauthorized impersonation attempt', [
                'auth_user_id' => $authUser->id,
                'auth_user_email' => $authUser->email,
                'target_user_id' => $targetUser->id,
                'severity' => 'critical_security_violation',
                'policy' => 'UserPolicy@impersonate',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        if ($isSameUser) {
            return Response::deny('You cannot impersonate yourself.');
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can impersonate users.');
    }

    /**
     * Determine if the user can access admin panel features.
     * Only users without tenant_id (admin users) can access admin features
     */
    public function accessAdmin(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Admin panel access authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'UserPolicy@accessAdmin',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Unauthorized admin panel access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'policy' => 'UserPolicy@accessAdmin',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can access the admin panel.');
    }

    /**
     * Determine if the user can access client portal features.
     * Only users with tenant_id (client users) can access client portal
     */
    public function accessClientPortal(User $user): Response
    {
        $isClientUser = $user->tenant_id !== null;

        Log::info('Client portal access authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_client_user' => $isClientUser,
            'access_granted' => $isClientUser,
            'policy' => 'UserPolicy@accessClientPortal',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isClientUser) {
            Log::warning('Unauthorized client portal access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'policy' => 'UserPolicy@accessClientPortal',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isClientUser
            ? Response::allow()
            : Response::deny('Only client users can access the client portal.');
    }
}