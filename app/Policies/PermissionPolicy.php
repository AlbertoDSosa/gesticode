<?php

namespace App\Policies;


use App\Models\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function view(User $user): bool
    {
        return $user->can('list permissions');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->can('create permissions');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('edit permissions');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete permissions');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Permission  $permission
     * @return void
     */
    public function restore(User $user, Permission $permission): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Permission  $permission
     * @return void
     */
    public function forceDelete(User $user, Permission $permission): void
    {
        //
    }
}
