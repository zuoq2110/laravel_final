<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isAgent()) {
            return $ticket->agent_id === $user->id;
        }

        return $ticket->user_id === $user->id;
    }

    public function create(User $user): bool {
        if(!$user->isAgent()){
            return true;
        }
        return false;
    }
    public function update(User $user, Ticket $ticket): bool{
        if($user->isAdmin()){
            return true;
        }
        else if($user->isAgent()){
            return $user->id === $ticket->agent_id;
        }
        return false;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    
}