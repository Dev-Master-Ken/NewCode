<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function update(User $user, User $model)
    {
        // A user can update their own profile
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        // Only admins can delete a user
        return $user->role === 'admin';
    }

    // Add this method to handle the "admin" authorization check
    public function admin(User $user)
    {
        // Only admins can access this
        return $user->role === 'admin';
    }
}
