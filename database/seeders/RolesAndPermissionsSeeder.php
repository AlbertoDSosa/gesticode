<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos

        $arrayOfPermissionNames = [
            ['name' => 'list roles', 'module_name' => 'roles', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'show admin role', 'module_name' => 'roles', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'show super admin role', 'module_name' => 'roles', 'level' => 'super-admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'create roles', 'module_name' => 'roles', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'edit roles', 'module_name' => 'roles', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'edit admin roles', 'module_name' => 'roles', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'delete roles', 'module_name' => 'roles', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'list permissions', 'module_name' => 'permissions', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'show admin permissions', 'module_name' => 'permissions', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'show super admin permissions', 'module_name' => 'permissions', 'level' => 'super-admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'create permissions', 'module_name' => 'permissions', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'create admin permissions', 'module_name' => 'permissions', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'create super admin permissions', 'module_name' => 'permissions', 'level' => 'super-admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'edit permissions', 'module_name' => 'permissions', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'delete permissions', 'module_name' => 'permissions', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'delete admin permissions', 'module_name' => 'permissions', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'delete super admin permissions', 'module_name' => 'permissions', 'level' => 'super-admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'list users', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'show admin users', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'show super admin users', 'module_name' => 'users', 'level' => 'super-admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'create users', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'create admin users', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'create super admin users', 'module_name' => 'users', 'level' => 'super-admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'edit users', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'edit admin users', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'edit super admin users', 'module_name' => 'users', 'level' => 'super-admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'delete users', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'delete admin users', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'delete super admin users', 'module_name' => 'users', 'level' => 'super-admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'assign roles', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'assign admin role', 'module_name' => 'users', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'assign super admin role', 'module_name' => 'users', 'level' => 'super-admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'list system settings', 'module_name' => 'system settings', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'show app system settings', 'module_name' => 'system settings', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'edit app system settings', 'module_name' => 'system settings', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'show db system settings', 'module_name' => 'system settings', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'edit db system settings', 'module_name' => 'system settings', 'level' => 'admin', 'removable' => false, 'editable' => false, 'assignable' => false],
            ['name' => 'show site settings', 'module_name' => 'site settings', 'level' => 'regular', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'show logo settings', 'module_name' => 'site settings', 'level' => 'regular', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'edit logo settings', 'module_name' => 'site settings', 'level' => 'regular', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'show identity settings', 'module_name' => 'site settings', 'level' => 'regular', 'removable' => false, 'editable' => false, 'assignable' => true],
            ['name' => 'edit identity settings', 'module_name' => 'site settings', 'level' => 'regular', 'removable' => false, 'editable' => false, 'assignable' => true]
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return [
                'name' => $permission['name'],
                'module_name' => $permission['module_name'],
                'guard_name' => 'web',
                'level' => $permission['level'],
                'editable' => $permission['editable'],
                'removable' => $permission['removable'],
                'assignable' => $permission['assignable'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        Permission::insert($permissions->toArray());

        // Crear roles y asignar permisos

        Role::create([
            'name' => 'super-admin',
            'display_name' => 'Super Administrator',
            'description' => 'Es un usuario que tiene acceso a todos los apartados sin restricción ninguna.',
            'removable' => false,
            'editable' => false,
            'guard_name' => 'web'
        ]);

        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Es un usuario que tiene acceso a todos los apartados con algunas restricciones.',
            'removable' => false,
            'editable' => false,
            'guard_name' => 'web'
        ]);

        $admin->givePermissionTo([
            'list roles',
            'show admin role',
            'create roles',
            'edit roles',
            'edit admin roles',
            'delete roles',
            'list permissions',
            'show admin permissions',
            'create permissions',
            'create admin permissions',
            'edit permissions',
            'delete permissions',
            'delete admin permissions',
            'list users',
            'show admin users',
            'create users',
            'create admin users',
            'edit users',
            'edit admin users',
            'delete users',
            'delete admin users',
            'assign roles',
            'assign admin role',
            'list system settings',
            'show app system settings',
            'edit app system settings',
            'show db system settings',
            'edit db system settings',
            'show site settings',
            'show logo settings',
            'edit logo settings',
            'show identity settings',
            'edit identity settings',
        ]);

        $technician = Role::create([
            'name' => 'technician',
            'display_name' => 'Technician',
            'description' => 'Solo tendrá accseso a areas y acciones por definir',
            'removable' => false,
            'editable' => false,
            'guard_name' => 'web'
        ]);

        $technician->givePermissionTo([
            'show site settings',
            'show logo settings',
            'edit logo settings',
            'show identity settings',
            'edit identity settings',
        ]);

        $guest = Role::create([
            'name' => 'guest',
            'display_name' => 'Guest',
            'description' => 'Solo tendrá accseso a areas y acciones por definir',
            'removable' => false,
            'editable' => false,
            'guard_name' => 'web'
        ]);

        Role::create([
            'name' => 'user',
            'display_name' => 'User Test',
            'description' => 'Esto es un usuario de test',
            'removable' => true,
            'editable' => true,
            'guard_name' => 'web'
        ]);
    }
}
