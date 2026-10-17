<?php

namespace App\Policies;

use App\Models\User;
use App\UserRole;

class UserPolicy
{

    /**
     *
     * @param User $user
     * @param User $authUser
     * @return bool
     *
     * Determine that the only one to be able to get access to the resource is the owner
     *
     */

    protected function isSelf(User $user, User $authUser): bool
    {
        return $user->id === $authUser->id;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $this->isSelf($user, $model);
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
    public function update(User $user, User $model): bool
    {
        return $this->isSelf($user, $model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $this->isSelf($user, $model);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    public function viewReservations(User $user, User $authUser): bool
    {
        return $this->isSelf($user, $authUser);
    }

    public function createReservation(User $user, User $authUser): bool
    {
        return $this->isSelf($user, $authUser);
    }
}
