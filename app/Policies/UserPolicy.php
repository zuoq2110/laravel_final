<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        // Chỉ admin có thể xem danh sách tất cả users
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin có thể xem thông tin bất kỳ user nào
        if ($user->isAdmin()) {
            return true;
        }

        // Agent có thể xem thông tin user của tickets được assign
        if ($user->isAgent()) {
            return $model->tickets()->whereAgentId($user->id)->exists() || 
                   $user->id === $model->id;
        }

        // User chỉ có thể xem thông tin của chính mình
        return $user->id === $model->id;
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        // Chỉ admin có thể tạo user mới
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admin có thể update bất kỳ user nào
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can change role of another user.
     */
    public function changeRole(User $user, User $model): bool
    {
        // Chỉ admin có thể thay đổi role
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Admin có thể xóa user khác (nhưng không thể xóa chính mình)
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine if the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine if the user can manage agents.
     */
    public function manageAgents(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view user statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->isAdmin();
    }
}