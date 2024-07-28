<?php

namespace App\Policies;


use App\Models\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

class RolePolicy
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
        return $user->can('list roles');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->can('create roles');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->can('edit roles');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->can('delete roles');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return void
     */
    public function restore(User $user, Role $role): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Role  $role
     * @return void
     */
    public function forceDelete(User $user, Role $role): void
    {
        //
    }
}
