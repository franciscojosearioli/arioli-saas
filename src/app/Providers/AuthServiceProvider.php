<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

// Models
use App\Models\User;
use App\Models\Ticket;
use App\Models\License;
use App\Models\Order;
use App\Models\Tenant;

// Policies
use App\Policies\UserPolicy;
use App\Policies\TenantPolicy;
use App\Policies\TicketPolicy;
use App\Policies\LicensePolicy;
use App\Policies\OrderPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * Maps models to their corresponding authorization policies
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Core SaaS Models with Enterprise Authorization
        User::class     => UserPolicy::class,
        Tenant::class   => TenantPolicy::class,
        Ticket::class   => TicketPolicy::class,
        License::class  => LicensePolicy::class,
        Order::class    => OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     * Sets up enterprise authorization gates and policies
     */
    public function boot(): void
    {
        // Register all policies defined above
        $this->registerPolicies();

        // Log the authorization service initialization
        Log::info('Enterprise authorization system initialized', [
            'registered_policies' => array_keys($this->policies),
            'timestamp' => now(),
            'environment' => app()->environment()
        ]);

        // Enterprise Gates for Role-Based Access Control

        /**
         * Gate: admin-access
         * Only users without tenant_id can access admin features
         */
        Gate::define('admin-access', function (User $user) {
            $isAdmin = $user->tenant_id === null && $user->client_id === null;

            Log::info('Admin access gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'is_admin' => $isAdmin,
                'gate' => 'admin-access',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            if (!$isAdmin) {
                Log::warning('Unauthorized admin access attempt via gate', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_tenant_id' => $user->tenant_id,
                    'gate' => 'admin-access',
                    'ip_address' => request()->ip(),
                    'timestamp' => now()
                ]);
            }

            return $isAdmin;
        });

        /**
         * Gate: client-access
         * Users with tenant_id (portal SaaS) or client_id (portal Hosting/Dominio,
         * sin licencia) pueden acceder al portal cliente.
         */
        Gate::define('client-access', function (User $user) {
            $isClient = $user->tenant_id !== null || $user->client_id !== null;

            Log::info('Client access gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'user_client_id' => $user->client_id,
                'is_client' => $isClient,
                'gate' => 'client-access',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return $isClient;
        });

        /**
         * Gate: manage-tenants
         * Only admin users can manage tenant lifecycle
         */
        Gate::define('manage-tenants', function (User $user) {
            $canManage = $user->tenant_id === null && $user->client_id === null;

            Log::info('Tenant management gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'can_manage' => $canManage,
                'gate' => 'manage-tenants',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return $canManage;
        });

        /**
         * Gate: manage-licenses
         * Only admin users can manage licenses and billing
         */
        Gate::define('manage-licenses', function (User $user) {
            $canManage = $user->tenant_id === null && $user->client_id === null;

            Log::info('License management gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'can_manage' => $canManage,
                'gate' => 'manage-licenses',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return $canManage;
        });

        /**
         * Gate: support-tickets
         * Users can access support tickets based on their role and tenant
         */
        Gate::define('support-tickets', function (User $user) {
            // Both admin and client users can access tickets (with proper scoping)
            $hasAccess = true;

            Log::info('Support tickets gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'is_admin' => $user->tenant_id === null && $user->client_id === null,
                'has_access' => $hasAccess,
                'gate' => 'support-tickets',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return $hasAccess;
        });

        /**
         * Gate: financial-data
         * Only admin users can access financial and payment data
         */
        Gate::define('financial-data', function (User $user) {
            $canAccess = $user->tenant_id === null && $user->client_id === null;

            Log::info('Financial data gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'can_access' => $canAccess,
                'gate' => 'financial-data',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            if (!$canAccess) {
                Log::warning('Unauthorized financial data access attempt', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_tenant_id' => $user->tenant_id,
                    'gate' => 'financial-data',
                    'ip_address' => request()->ip(),
                    'timestamp' => now()
                ]);
            }

            return $canAccess;
        });

        /**
         * Gate: manage-settings
         * Only admin users can manage global system configuration
         */
        Gate::define('manage-settings', function (User $user) {
            $canManage = $user->tenant_id === null && $user->client_id === null;

            Log::info('Settings management gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'can_manage' => $canManage,
                'gate' => 'manage-settings',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return $canManage;
        });

        /**
         * Gate: manage-legal
         * Only admin users can manage contracts and legal documents
         */
        Gate::define('manage-legal', function (User $user) {
            $canManage = $user->tenant_id === null && $user->client_id === null;

            Log::info('Legal management gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'can_manage' => $canManage,
                'gate' => 'manage-legal',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return $canManage;
        });

        /**
         * Gate: manage-clients
         * Only admin users can manage the client CRM (clients, projects, assets, charges)
         */
        Gate::define('manage-clients', function (User $user) {
            $canManage = $user->tenant_id === null && $user->client_id === null;

            Log::info('Client management gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'can_manage' => $canManage,
                'gate' => 'manage-clients',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return $canManage;
        });

        /**
         * Gate: manage-invoices
         * Only admin users can manage invoices/billing documents
         */
        Gate::define('manage-invoices', function (User $user) {
            $canManage = $user->tenant_id === null && $user->client_id === null;

            Log::info('Invoice management gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'can_manage' => $canManage,
                'gate' => 'manage-invoices',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return $canManage;
        });

        /**
         * Gate: tenant-isolation
         * Validates tenant boundary enforcement for data access
         */
        Gate::define('tenant-isolation', function (User $user, $tenantId = null) {
            $isAdmin = $user->tenant_id === null && $user->client_id === null;
            $ownsTenant = $user->tenant_id === $tenantId;
            $hasAccess = $isAdmin || $ownsTenant;

            Log::info('Tenant isolation gate check', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'requested_tenant_id' => $tenantId,
                'is_admin' => $isAdmin,
                'owns_tenant' => $ownsTenant,
                'has_access' => $hasAccess,
                'gate' => 'tenant-isolation',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            if (!$hasAccess) {
                Log::warning('Tenant isolation violation detected', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_tenant_id' => $user->tenant_id,
                    'requested_tenant_id' => $tenantId,
                    'violation_type' => 'cross_tenant_access_attempt',
                    'gate' => 'tenant-isolation',
                    'ip_address' => request()->ip(),
                    'timestamp' => now()
                ]);
            }

            return $hasAccess;
        });
    }
}