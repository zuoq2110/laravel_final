<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine if the user can view any comments.
     */
    public function viewAny(User $user): bool
    {
        // Admin và agent có thể xem tất cả comments
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view the comment.
     */
    public function view(User $user, Comment $comment): bool
    {
        // Admin có thể xem tất cả
        if ($user->isAdmin()) {
            return true;
        }

        // Agent có thể xem comments của tickets được assign cho mình
        if ($user->isAgent()) {
            $ticket = $comment->ticket;
            return $ticket->agent_id === $user->id;
        }

        // User có thể xem comments của tickets mình tạo hoặc comments mình viết
        return $comment->ticket->user_id === $user->id;
    }

    /**
     * Determine if the user can create comments.
     */
    public function create(User $user): bool
    {
        // Tất cả user đã đăng nhập có thể tạo comment
        return true;
    }

    /**
     * Determine if the user can create comment on specific ticket.
     */
    public function createOnTicket(User $user, $ticket): bool
    {
        // Admin có thể comment trên mọi ticket
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

    /**
     * Determine if the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Admin có thể update tất cả comments
        if ($user->isAdmin()) {
            return true;
        }

        // User chỉ có thể update comment của chính mình trong vòng 15 phút
        return $comment->user_id === $user->id && 
               $comment->created_at->diffInMinutes(now()) <= 15;
    }

    /**
     * Determine if the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Admin có thể xóa bất kỳ comment nào
        if ($user->isAdmin()) {
            return true;
        }

        // User có thể xóa comment của mình trong vòng 15 phút
        return $comment->user_id === $user->id && 
               $comment->created_at->diffInMinutes(now()) <= 15;
    }

    /**
     * Determine if the user can restore the comment.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the comment.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->isAdmin();
    }
}