<?php

namespace Tests\Traits;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

trait PermissionFeatures {

    /**
     * @return Role
    */
    public function createRole(array $attributes)
    {
        return Role::create([
            'name' => $attributes['name'],
            'display_name' => $attributes['display_name'],
            'description' => $attributes['description'] ?? null,
            'guard_name' => $attributes['guard_name'] ?? 'web',
            'removable' => $attributes['removable'] ?? true
        ]);
    }

    public function createPermission(array $attributes)
    {
        return Permission::create([
            'name' => $attributes['name'],
            'level' => $attributes['level'] ?? 'normal',
            'name' => $attributes['name'],
            'module_name' => $attributes['module_name'],
            'guard_name' => $attributes['guard_name'] ?? 'web'
        ]);
    }


    public function createPermissions(array $arrayOfPermissionNames)
    {

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return [
                'name' => $permission['name'],
                'module_name' => $permission['module_name'],
                'guard_name' => $permission['guard_name'] ?? 'web',
                'created_at' => now(),
                'updated_at' => now(),
                'level' => $permission['level'] ?? 'normal'
            ];
        });

        Permission::insert($permissions->toArray());
    }
}
