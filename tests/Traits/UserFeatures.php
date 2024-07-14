<?php

namespace Tests\Traits;

use App\Models\Users\User;
use App\Models\Users\UserProfile;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

trait UserFeatures {
    /**
     * @return User
    */
    public function createUser(
        array $attributes = [],
    )
    {
        $userAttributes = collect($attributes)->only('email', 'name', 'password', 'email_verified_at')->toArray();
        $user = User::factory()->create($userAttributes);
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'first_name' => $user->name
        ]);
        $user->assignRole($attributes['role']);
        $this->assignPermissionsToRole($user->role);

        return $user;
    }

    /**
     * @return Role
    */
    public function createRole(array $attributes)
    {
        return Role::create([
            'name' => $attributes['name'],
            'display_name' => $attributes['display_name'],
            'description' => $attributes['description'] ?? null
        ]);
    }
    public function createPermission(array $attributes)
    {
        return Permission::create([
            'name' => $attributes['name'],
        ]);
    }

    public function createRoles()
    {
        $this->createPermissions();

        $arrayOfRolesNames = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Es un usuario que tiene acceso a todos los apartados sin restricción ninguna.',
                'assignable_to_customer' => false,
                'view' => 'admin',
                'guard_name' => 'web'
            ],
            [
                'name' => 'technician',
                'display_name' => 'Technician',
                'assignable_to_customer' => false,
                'view' => 'technincian',
                'description' => 'Tiene acceso similar a casi todo, pero con ciertos limites (por definir)',
                'guard_name' => 'web'
            ],
            [
                'name' => 'external-reseller',
                'display_name' => 'External Reseller',
                'assignable_to_customer' => true,
                'view' => 'reseller',
                'description' => 'Es similar al técnico, en cuento a las opciones a las que puede acceder, pero está limitado a varios clientes que tenga asignados y sus hotspots',
                'guard_name' => 'web'
            ],
            [
                'name' => 'technical-customer',
                'display_name' => 'Technical Customer',
                'assignable_to_customer' => true,
                'view' => 'customer',
                'description' => 'Es un usuario que tiene acceso como técnico a todos los hotspot que sean del cliente, pero con algún privilegio más de tipo técnico',
                'guard_name' => 'web'
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer',
                'assignable_to_customer' => true,
                'view' => 'customer',
                'description' => 'Es un usuario que tiene acceso a todos los hotspot del cliente',
                'guard_name' => 'web'
            ]
        ];

        $roles = collect($arrayOfRolesNames);

        Role::insert($roles->toArray());
    }

    public function createPermissions()
    {
        $arrayOfPermissionNames = [
            'show-users',
            'create-users',
            'create-admin-users',
            'edit-users',
            'edit-admin-users',
            'delete-users',
            'show-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'show-customer-routes',
            'be-assigned-to-many-customers',
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());
    }

    public function makeUser(array $attributes = [])
    {
        return User::factory()->make($attributes);
    }

    public function assignPermissionsToRole(Role $role)
    {
        switch ($role->name) {
            case 'admin':
                $role->givePermissionTo(Permission::all());
                break;
            case 'technician':
                $role->givePermissionTo([
                    'show-users',
                    'create-users',
                    'edit-users',
                    'show-customers',
                    'create-customers',
                    'edit-customers'
                ]);
                break;
        }
    }
}
