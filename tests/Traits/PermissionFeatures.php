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
            'removable' => $attributes['removable'] ?? true,
            'editable' => $attributes['editable'] ?? true
        ]);
    }

    public function createPermission(array $attributes)
    {
        return Permission::create([
            'name' => $attributes['name'],
            'name' => $attributes['name'],
            'module_name' => $attributes['module_name'],
            'guard_name' => $attributes['guard_name'] ?? 'web',
            'level' => $attributes['level'] ?? 'regular',
            'removable' => $attributes['removable'] ?? true,
            'editable' => $attributes['editable'] ?? true,
            'assignable' => $attributes['assignable'] ?? true
        ]);
    }

    public function createPermissions(array $arrayOfPermissionNames)
    {

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return [
                'name' => $permission['name'],
                'module_name' => $permission['module_name'],
                'guard_name' => $permission['guard_name'] ?? 'web',
                'level' => $attributes['level'] ?? 'regular',
                'removable' => $attributes['removable'] ?? true,
                'editable' => $attributes['editable'] ?? true,
                'assignable' => $attributes['assignable'] ?? true,
                'created_at' => now(),
                'updated_at' => now()
            ];
        });

        Permission::insert($permissions->toArray());
    }
}
