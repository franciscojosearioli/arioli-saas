<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class OrderPolicy
{
    /**
     * Determine if the user can view any orders.
     * Admins can view all orders, clients can view their tenant's orders
     */
    public function viewAny(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Order viewAny authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $isAdmin,
            'policy' => 'OrderPolicy@viewAny',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return Response::allow(); // Both admins and clients can view orders (with proper scoping)
    }

    /**
     * Determine if the user can view the order.
     * Complete tenant isolation - clients only see their tenant's orders
     */
    public function view(User $user, Order $order): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;
        $ownsTenant = $user->tenant_id === $order->tenant_id;
        $hasAccess = $isAdmin || $ownsTenant;

        // Enterprise audit logging for payment data access
        Log::info('Order view authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'order_id' => $order->id,
            'order_uuid' => $order->uuid,
            'order_tenant_id' => $order->tenant_id,
            'order_amount' => $order->amount,
            'order_status' => $order->status,
            'order_customer_email' => $order->customer_email,
            'is_admin' => $isAdmin,
            'owns_tenant' => $ownsTenant,
            'access_granted' => $hasAccess,
            'policy' => 'OrderPolicy@view',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$hasAccess) {
            Log::warning('Unauthorized order access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'order_id' => $order->id,
                'order_uuid' => $order->uuid,
                'order_tenant_id' => $order->tenant_id,
                'order_customer_email' => $order->customer_email,
                'violation_type' => 'cross_tenant_payment_access',
                'policy' => 'OrderPolicy@view',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $hasAccess
            ? Response::allow()
            : Response::deny('You can only access orders for your own tenant.');
    }

    /**
     * Determine if the user can create orders.
     * Orders are typically created through payment flow, not manual creation
     * Only admin users can manually create orders
     */
    public function create(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Order creation authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'OrderPolicy@create',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Non-admin order creation attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'policy' => 'OrderPolicy@create',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can manually create orders.');
    }

    /**
     * Determine if the user can update the order.
     * Only admin users can modify order status and details
     */
    public function update(User $user, Order $order): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Order update authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'order_id' => $order->id,
            'order_uuid' => $order->uuid,
            'order_tenant_id' => $order->tenant_id,
            'order_status' => $order->status,
            'order_amount' => $order->amount,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'OrderPolicy@update',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Unauthorized order modification attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'order_id' => $order->id,
                'order_uuid' => $order->uuid,
                'order_tenant_id' => $order->tenant_id,
                'policy' => 'OrderPolicy@update',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can modify orders.');
    }

    /**
     * Determine if the user can delete the order.
     * Only admin users can delete payment records (critical operation)
     */
    public function delete(User $user, Order $order): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        // Critical financial operation - comprehensive audit logging
        Log::info('Order deletion authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'order_id' => $order->id,
            'order_uuid' => $order->uuid,
            'order_tenant_id' => $order->tenant_id,
            'order_amount' => $order->amount,
            'order_status' => $order->status,
            'order_customer_email' => $order->customer_email,
            'mp_payment_id' => $order->mp_payment_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'OrderPolicy@delete',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::critical('Unauthorized order deletion attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'order_id' => $order->id,
                'order_uuid' => $order->uuid,
                'order_amount' => $order->amount,
                'order_customer_email' => $order->customer_email,
                'severity' => 'critical_financial_violation',
                'policy' => 'OrderPolicy@delete',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can delete payment records.');
    }

    /**
     * Determine if the user can process refunds for orders.
     * Only admin users can initiate refund processes
     */
    public function refund(User $user, Order $order): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;
        $orderPaid = $order->isApproved();

        // Critical financial operation logging
        Log::info('Order refund authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'order_id' => $order->id,
            'order_uuid' => $order->uuid,
            'order_tenant_id' => $order->tenant_id,
            'order_amount' => $order->amount,
            'order_status' => $order->status,
            'order_paid' => $orderPaid,
            'mp_payment_id' => $order->mp_payment_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin && $orderPaid,
            'policy' => 'OrderPolicy@refund',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::critical('Unauthorized refund attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'order_id' => $order->id,
                'order_amount' => $order->amount,
                'severity' => 'critical_financial_violation',
                'policy' => 'OrderPolicy@refund',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return Response::deny('Only administrators can process refunds.');
        }

        if (!$orderPaid) {
            Log::warning('Refund attempt on unpaid order', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'order_status' => $order->status,
                'policy' => 'OrderPolicy@refund',
                'timestamp' => now()
            ]);

            return Response::deny('Can only refund paid orders.');
        }

        return Response::allow();
    }

    /**
     * Determine if the user can view financial statistics.
     * Only admin users can access revenue and payment analytics
     */
    public function viewStatistics(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Order statistics access authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'OrderPolicy@viewStatistics',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Unauthorized financial statistics access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'policy' => 'OrderPolicy@viewStatistics',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can view financial statistics.');
    }

    /**
     * Determine if the user can access MercadoPago webhook endpoints.
     * Only the system itself should process webhooks (not regular users)
     */
    public function processWebhook(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        // Webhook processing typically doesn't go through user auth,
        // but this policy exists for manual webhook replay by admins
        Log::info('Webhook processing authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'OrderPolicy@processWebhook',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can manually process payment webhooks.');
    }
}