<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only admins can view the user management screen.
     */
    public function viewAny(User $currentUser): bool
    {
        return $currentUser->isAdmin();
    }

    /**
     * An admin cannot demote/change their own role; must be another user.
     */
    public function updateRole(User $currentUser, User $targetUser): bool
    {
        return $currentUser->isAdmin() && $currentUser->id !== $targetUser->id;
    }
}
