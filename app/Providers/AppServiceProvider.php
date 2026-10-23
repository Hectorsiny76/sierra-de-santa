<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Models\User;
use App\UserRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (?User $user, string $ability, array $arguments = []) {
            $subject = $arguments[0] ?? null;

            if ($subject instanceof User || $subject instanceof Reservation) {
                return null;
            }

           return $user?->role === UserRole::Admin ? true : null;
        });
    }
}
