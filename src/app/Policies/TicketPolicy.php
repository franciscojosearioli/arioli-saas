<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class TicketPolicy
{
    /**
     * Determine if the user can view any tickets.
     * Admins can view all tickets, clients only their tenant's tickets
     */
    public function viewAny(User $user): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Ticket viewAny authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_admin' => $isAdmin,
            'policy' => 'TicketPolicy@viewAny',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return Response::allow(); // Both admins and clients can view tickets (with proper scoping)
    }

    /**
     * Determine if the user can view the ticket.
     * Complete tenant isolation with enterprise audit trails
     */
    public function view(User $user, Ticket $ticket): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;
        $ownsTenant = $user->client_id !== null
            ? $user->client_id === $ticket->client_id
            : $user->tenant_id === $ticket->tenant_id;
        $ownsTicket = $user->id === $ticket->user_id;
        $hasAccess = $isAdmin || ($ownsTenant && $ownsTicket);

        // Comprehensive audit logging as per enterprise patterns
        Log::info('Ticket view authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'ticket_id' => $ticket->id,
            'ticket_tenant_id' => $ticket->tenant_id,
            'ticket_user_id' => $ticket->user_id,
            'is_admin' => $isAdmin,
            'owns_tenant' => $ownsTenant,
            'owns_ticket' => $ownsTicket,
            'access_granted' => $hasAccess,
            'policy' => 'TicketPolicy@view',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$hasAccess) {
            Log::warning('Unauthorized ticket access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'ticket_id' => $ticket->id,
                'ticket_tenant_id' => $ticket->tenant_id,
                'ticket_user_id' => $ticket->user_id,
                'violation_type' => 'cross_tenant_data_access',
                'policy' => 'TicketPolicy@view',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $hasAccess
            ? Response::allow()
            : Response::deny('You can only access your own tickets within your tenant.');
    }

    /**
     * Determine if the user can create tickets.
     * Only client users can create tickets for their tenant
     */
    public function create(User $user): Response
    {
        $isClientUser = $user->tenant_id !== null || $user->client_id !== null;

        Log::info('Ticket creation authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'is_client_user' => $isClientUser,
            'access_granted' => $isClientUser,
            'policy' => 'TicketPolicy@create',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isClientUser) {
            Log::warning('Admin attempting to create client ticket', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'policy' => 'TicketPolicy@create',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isClientUser
            ? Response::allow()
            : Response::deny('Only client users can create support tickets.');
    }

    /**
     * Determine if the user can update the ticket.
     * Clients can update their own tickets if editable, admins can update any
     */
    public function update(User $user, Ticket $ticket): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;
        $ownsTicket = $user->id === $ticket->user_id && ($user->client_id !== null
            ? $user->client_id === $ticket->client_id
            : $user->tenant_id === $ticket->tenant_id);
        $ticketEditable = in_array($ticket->status, ['abierto', 'esperando_cliente']);

        $hasAccess = $isAdmin || ($ownsTicket && $ticketEditable);

        Log::info('Ticket update authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'ticket_id' => $ticket->id,
            'ticket_tenant_id' => $ticket->tenant_id,
            'ticket_user_id' => $ticket->user_id,
            'ticket_status' => $ticket->status,
            'is_admin' => $isAdmin,
            'owns_ticket' => $ownsTicket,
            'ticket_editable' => $ticketEditable,
            'access_granted' => $hasAccess,
            'policy' => 'TicketPolicy@update',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$hasAccess) {
            Log::warning('Unauthorized ticket update attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'ticket_id' => $ticket->id,
                'ticket_tenant_id' => $ticket->tenant_id,
                'ticket_status' => $ticket->status,
                'reason' => $isAdmin ? 'admin_access_should_work' : ($ownsTicket ? 'ticket_not_editable' : 'not_owner'),
                'policy' => 'TicketPolicy@update',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $hasAccess
            ? Response::allow()
            : Response::deny('You can only update your own editable tickets.');
    }

    /**
     * Determine if the user can delete the ticket.
     * Only admin users can delete tickets
     */
    public function delete(User $user, Ticket $ticket): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Ticket deletion authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'ticket_id' => $ticket->id,
            'ticket_tenant_id' => $ticket->tenant_id,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'TicketPolicy@delete',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        if (!$isAdmin) {
            Log::warning('Unauthorized ticket deletion attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_tenant_id' => $user->tenant_id,
                'ticket_id' => $ticket->id,
                'policy' => 'TicketPolicy@delete',
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
        }

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can delete tickets.');
    }

    /**
     * Determine if the user can assign tickets to team members.
     * Only admin users can assign tickets
     */
    public function assign(User $user, Ticket $ticket): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Ticket assignment authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'ticket_id' => $ticket->id,
            'current_assigned_to' => $ticket->assigned_to,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'TicketPolicy@assign',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can assign tickets.');
    }

    /**
     * Determine if the user can update ticket status.
     * Only admin users can change ticket status
     */
    public function updateStatus(User $user, Ticket $ticket): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;

        Log::info('Ticket status update authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'ticket_id' => $ticket->id,
            'current_status' => $ticket->status,
            'is_admin' => $isAdmin,
            'access_granted' => $isAdmin,
            'policy' => 'TicketPolicy@updateStatus',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return $isAdmin
            ? Response::allow()
            : Response::deny('Only administrators can update ticket status.');
    }

    /**
     * Determine if the user can close tickets.
     * Admins and ticket owners can close tickets
     */
    public function close(User $user, Ticket $ticket): Response
    {
        $isAdmin = $user->tenant_id === null && $user->client_id === null;
        $ownsTicket = $user->id === $ticket->user_id && ($user->client_id !== null
            ? $user->client_id === $ticket->client_id
            : $user->tenant_id === $ticket->tenant_id);
        $hasAccess = $isAdmin || $ownsTicket;

        Log::info('Ticket close authorization check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_tenant_id' => $user->tenant_id,
            'ticket_id' => $ticket->id,
            'ticket_tenant_id' => $ticket->tenant_id,
            'ticket_user_id' => $ticket->user_id,
            'current_status' => $ticket->status,
            'is_admin' => $isAdmin,
            'owns_ticket' => $ownsTicket,
            'access_granted' => $hasAccess,
            'policy' => 'TicketPolicy@close',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return $hasAccess
            ? Response::allow()
            : Response::deny('You can only close your own tickets.');
    }
}