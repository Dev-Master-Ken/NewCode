<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Review;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        // Only renters can create reviews
        return $user->role === 'renter';
    }

    public function update(User $user, Review $review)
    {
        // Renters can only update their own reviews
        return $user->role === 'renter' && $user->id === $review->user_id;
    }

    public function delete(User $user, Review $review)
    {
        // Renters can only delete their own reviews
        return $user->role === 'renter' && $user->id === $review->user_id;
    }
}
