<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Comment $comment): bool
    {
        // Admin có thể xem tất cả
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAgent()) {
            $ticket = $comment->ticket;
            return $ticket->agent_id === $user->id;
        }

        return $comment->ticket->user_id === $user->id;
    }
    public function create(User $user): bool
    {
        return true;
    }

    public function createOnTicket(User $user, $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Agent có thể comment trên tickets được assign cho mình hoặc tickets chưa có agent
        if ($user->isAgent()) {
            return $ticket->agent_id === $user->id || $ticket->agent_id === null;
        }

        // User có thể comment trên ticket của mình
        return $ticket->user_id === $user->id;
    }

 

}