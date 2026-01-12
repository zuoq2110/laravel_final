<?php

namespace App\Policies;

use App\Models\Label;
use App\Models\User;

class LabelPolicy
{
    /**
     * Determine if the user can view any labels.
     */
    public function viewAny(User $user): bool
    {
        // Tất cả user có thể xem labels
        return true;
    }

    /**
     * Determine if the user can view the label.
     */
    public function view(User $user, Label $label): bool
    {
        // Tất cả user có thể xem label
        return true;
    }

    /**
     * Determine if the user can create labels.
     */
    public function create(User $user): bool
    {
        // Chỉ admin có thể tạo label
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the label.
     */
    public function update(User $user, Label $label): bool
    {
        // Chỉ admin có thể update label
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the label.
     */
    public function delete(User $user, Label $label): bool
    {
        // Chỉ admin có thể xóa label
        return $user->isAdmin();
    }

    /**
     * Determine if the user can restore the label.
     */
    public function restore(User $user, Label $label): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the label.
     */
    public function forceDelete(User $user, Label $label): bool
    {
        return $user->isAdmin();
    }
}