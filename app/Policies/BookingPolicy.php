<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        // Renters can create bookings
        return $user->role === 'renter';
    }

    public function update(User $user, Booking $booking)
    {
        // Only the owner of the booking or an admin can update a booking
        return $user->role === 'owner' && $user->id === $booking->vehicle->owner_id || $user->role === 'admin';
    }

    public function delete(User $user, Booking $booking)
    {
        // Renters can cancel their own bookings
        return $user->role === 'renter' && $user->id === $booking->user_id;
    }
}
