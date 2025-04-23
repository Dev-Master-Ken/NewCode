<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->role === 'owner';
    }

    public function update(User $user, Vehicle $vehicle)
    {
        return $user->role === 'owner' && $user->id === $vehicle->owner_id;
    }

    public function delete(User $user, Vehicle $vehicle)
    {
        return $user->role === 'owner' && $user->id === $vehicle->owner_id;
    }
}
