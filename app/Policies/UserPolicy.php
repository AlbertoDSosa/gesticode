<?php

namespace App\Policies;

use App\Models\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response
     */
    public function create(User $user): Response
    {
        return $user->can('create users')
                    ? Response::allow()
                    : Response::denyWithStatus(401);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return Response
     */
    public function update(User $user, $model): Response
    {
        return $user->can('edit users')
                    ? Response::allow()
                    : Response::denyWithStatus(401);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return Response
     */
    public function delete(User $user): Response
    {
        return $user->can('delete users')
                    ? Response::allow()
                    : Response::denyWithStatus(401);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @return void
     */
    public function restore(User $user): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return void
     */
    public function forceDelete(User $user): void
    {
        //
    }
}
