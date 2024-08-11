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
            ['name' => 'list roles', 'module_name' => 'roles', 'level' => 'normal'],
            ['name' => 'show admin role', 'module_name' => 'roles', 'level' => 'admin'],
            ['name' => 'show super admin role', 'module_name' => 'roles', 'level' => 'super-admin'],
            ['name' => 'create roles', 'module_name' => 'roles', 'level' => 'normal'],
            ['name' => 'edit roles', 'module_name' => 'roles', 'level' => 'normal'],
            ['name' => 'delete roles', 'module_name' => 'roles', 'level' => 'normal'],
            ['name' => 'list permissions', 'module_name' => 'permissions', 'level' => 'normal'],
            ['name' => 'show admin permissions', 'module_name' => 'permissions', 'level' => 'super-admin'],
            ['name' => 'create permissions', 'module_name' => 'permissions', 'level' => 'normal'],
            ['name' => 'create admin permissions', 'module_name' => 'permissions', 'level' => 'admin'],
            ['name' => 'create super admin permissions', 'module_name' => 'permissions', 'level' => 'super-admin'],
            ['name' => 'edit permissions', 'module_name' => 'permissions', 'level' => 'normal'],
            ['name' => 'delete permissions', 'module_name' => 'permissions', 'level' => 'normal'],
            ['name' => 'list logo settings', 'module_name' => 'logo settings', 'level' => 'normal'],
            ['name' => 'edit logo settings', 'module_name' => 'logo settings', 'level' => 'normal'],
            ['name' => 'list system settings', 'module_name' => 'system settings', 'level' => 'normal'],
            ['name' => 'show app system settings', 'module_name' => 'system settings', 'level' => 'normal'],
            ['name' => 'edit app system settings', 'module_name' => 'system settings', 'level' => 'normal'],
            ['name' => 'show db system settings', 'module_name' => 'system settings', 'level' => 'normal'],
            ['name' => 'edit db system settings', 'module_name' => 'system settings', 'level' => 'normal'],
            ['name' => 'list users', 'module_name' => 'users', 'level' => 'normal'],
            ['name' => 'show admin users', 'module_name' => 'users', 'level' => 'admin'],
            ['name' => 'show super admin users', 'module_name' => 'users', 'level' => 'super-admin'],
            ['name' => 'create users', 'module_name' => 'users', 'level' => 'normal'],
            ['name' => 'create admin users', 'module_name' => 'users', 'level' => 'admin'],
            ['name' => 'create super admin users', 'module_name' => 'users', 'level' => 'super-admin'],
            ['name' => 'edit users', 'module_name' => 'users', 'level' => 'normal'],
            ['name' => 'edit admin users', 'module_name' => 'users', 'level' => 'admin'],
            ['name' => 'edit super admin users', 'module_name' => 'users', 'level' => 'super-admin'],
            ['name' => 'delete users', 'module_name' => 'users', 'level' => 'normal'],
            ['name' => 'delete admin users', 'module_name' => 'users', 'level' => 'admin'],
            ['name' => 'delete super admin users', 'module_name' => 'users', 'level' => 'super-admin'],
            ['name' => 'assign admin role', 'module_name' => 'users', 'level' => 'admin'],
            ['name' => 'assign super admin role', 'module_name' => 'users', 'level' => 'super-admin'],
            // ['name' => 'list customers', 'module_name' => 'customers', 'level' => ''],
            // ['name' => 'show all customers', 'module_name' => 'customers', 'level' => ''],
            // ['name' => 'show own customers', 'module_name' => 'customers', 'level' => ''],
            // ['name' => 'create customers', 'module_name' => 'customers', 'level' => ''],
            // ['name' => 'edit all customers', 'module_name' => 'customers', 'level' => ''],
            // ['name' => 'edit own customers', 'module_name' => 'customers', 'level' => ''],
            // ['name' => 'delete customers', 'module_name' => 'customers', 'level' => ''],
            // ['name' => 'show customer routes', 'module_name' => 'customers', 'level' => ''],
            // ['name' => 'be assigned to customer', 'module_name' => 'customers', 'level' => ''],
            // ['name' => 'be assigned to many customers', 'module_name' => 'customers', 'level' => ''],
            // ['name' => '', 'module_name' => 'customers', 'level' => ''],
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return [
                'name' => $permission['name'],
                'module_name' => $permission['module_name'],
                'guard_name' => 'web',
                'level' => $permission['level'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        Permission::insert($permissions->toArray());

        // Crear roles y asignar permisos

        $superAdmin = Role::create([
            'name' => 'super-admin',
            'display_name' => 'Super Administrator',
            'description' => 'Es un usuario que tiene acceso a todos los apartados sin restricción ninguna.',
            'assignable_to_customer' => false,
            'view' => 'admin',
            'guard_name' => 'web'
        ]);

        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Es un usuario que tiene acceso a todos los apartados con algunas restricciones.',
            'assignable_to_customer' => false,
            'view' => 'admin',
            'guard_name' => 'web'
        ]);

        $admin->givePermissionTo([
            'list roles',
            'show admin role',
            'create roles',
            'edit roles',
            'delete roles',
            'list permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'list logo settings',
            'edit logo settings',
            'list system settings',
            'show app system settings',
            'edit app system settings',
            'show db system settings',
            'edit db system settings',
            'list users',
            'show admin users',
            'create users',
            'create admin users',
            'edit users',
            'edit admin users',
            'delete users',
            'delete admin users',
            'assign admin role',
            // 'list customers',
            // 'show all customers',
            // 'create customers',
            // 'edit all customers',
            // 'delete customers',
            // 'show customer routes',
            // 'be assigned to customer',
            // 'be assigned to many customers'
        ]);

        $technician = Role::create([
            'name' => 'technician',
            'display_name' => 'Technician',
            'description' => 'Solo tendrá accseso a areas y acciones por definir',
            'assignable_to_customer' => true,
            'view' => 'technician',
            'guard_name' => 'web'
        ]);

        // $technician->givePermissionTo([
        //     'list customers',
        //     'show own customers',
        //     'edit own customers',
        //     'be assigned to many customers'
        // ]);

        // $external_reseller = Role::create([
        //     'name' => 'external-reseller',
        //     'display_name' => 'External Reseller',
        //     'description' => 'Es similar al técnico, en cuento a las opciones a las que puede acceder, pero está limitado a varios clientes que tenga asignados y sus hotspots',
        //     'assignable_to_customer' => true,
        //     'view' => 'reseller',
        //     'guard_name' => 'web'
        // ]);

        // $external_reseller->givePermissionTo([
        //     'list customers',
        //     'show own customers',
        //     'edit own customers',
        //     'show customer routes',
        //     'be assigned to many customers'
        // ]);

        // $technical_customer = Role::create([
        //     'name' => 'technical-customer',
        //     'display_name' => 'Technical Customer',
        //     'description' => 'Es un usuario que tiene acceso como técnico a todos los hotspot que sean del cliente, pero con algún privilegio más de tipo técnico',
        //     'assignable_to_customer' => true,
        //     'view' => 'customer',
        //     'guard_name' => 'web'
        // ]);

        // $technical_customer->givePermissionTo([
        //     'show customer routes',
        //     'be assigned to customer'
        // ]);

        // $customer = Role::create([
        //     'name' => 'customer',
        //     'display_name' => 'Customer',
        //     'description' => 'Es un usuario que tiene acceso a todos los hotspot del cliente',
        //     'assignable_to_customer' => true,
        //     'view' => 'customer',
        //     'guard_name' => 'web'
        // ]);

        // $customer->givePermissionTo([
        //     'show customer routes',
        //     'be assigned to customer'
        // ]);
    }
}
