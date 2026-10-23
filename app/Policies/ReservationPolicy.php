<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use App\UserRole;
use Illuminate\Auth\Access\Response;

class ReservationPolicy
{

    protected function isAdmin(User $user): bool
    {
        return $user?->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id && $reservation->start_date > now();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return $this->isAdmin($user);
    }

    public function viewTrashed(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
