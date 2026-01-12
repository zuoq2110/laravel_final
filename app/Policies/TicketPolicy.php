<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine if the user can view any tickets.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Admin có thể xem tất cả
        if ($user->isAdmin()) {
            return true;
        }

        // Agent có thể xem tickets được assign cho mình hoặc chưa có agent
        if ($user->isAgent()) {
            return $ticket->agent_id === $user->id;
        }

        // User chỉ có thể xem tickets của chính mình
        return $ticket->user_id === $user->id;
    }

    /**
     * Determine if the user can create tickets.
     */
    public function create(User $user): bool {
        if(!$user->isAgent()){
            return true;
        }
        return false;
    }

    /**
     * Determine if the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool{
        if($user->isAdmin()){
            return true;
        }
        else if($user->isAgent()){
            return $user->id === $ticket->agent_id;
        }
        return false;
    }

    /**
     * Determine if the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Chỉ admin mới có thể xóa ticket
        return $user->isAdmin();
    }

    /**
     * Determine if the user can assign ticket to agent.
     */
    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can change ticket status.
     */
    public function changeStatus(User $user, Ticket $ticket): bool
    {
        // Admin có thể thay đổi status bất kỳ
        if ($user->isAdmin()) {
            return true;
        }

        // Agent có thể thay đổi status của tickets được assign cho mình
        if ($user->isAgent()) {
            return $ticket->agent_id === $user->id;
        }

        // User có thể close ticket của mình
        return $ticket->user_id === $user->id && in_array($ticket->status, ['open', 'in_progress']);
    }

    /**
     * Determine if the user can restore the ticket.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the ticket.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }
}