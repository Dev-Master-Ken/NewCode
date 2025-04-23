<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Vehicle;
use App\Policies\UserPolicy;
use App\Policies\BookingPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\VehiclePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Booking::class => BookingPolicy::class,
        Review::class => ReviewPolicy::class,
        Vehicle::class => VehiclePolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });
        Gate::define('view-all-users', function ($user) {
            return $user->role === 'admin';
        });
        Gate::define('owner', function ($user) {
            return $user->role === 'owner';
        });
        Gate::define('renter', function ($user) {
            return $user->role === 'renter';
        });
    }
}
