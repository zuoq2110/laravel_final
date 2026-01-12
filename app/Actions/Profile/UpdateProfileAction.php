<?php

namespace App\Actions\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UpdateProfileAction
{
    public function execute(User $user, array $validatedData): User
    {
        $user->fill($validatedData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}