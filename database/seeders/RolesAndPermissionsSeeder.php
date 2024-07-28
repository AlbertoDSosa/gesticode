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
            ['name' => 'list roles', 'module_name' => 'roles'],
            ['name' => 'show admin role', 'module_name' => 'roles'],
            ['name' => 'show super admin role', 'module_name' => 'roles'],
            ['name' => 'create roles', 'module_name' => 'roles'],
            ['name' => 'edit roles', 'module_name' => 'roles'],
            ['name' => 'delete roles', 'module_name' => 'roles'],
            ['name' => 'list permissions', 'module_name' => 'permissions'],
            // ['name' => 'show permissions', 'module_name' => 'permissions'],
            ['name' => 'create permissions', 'module_name' => 'permissions'],
            ['name' => 'edit permissions', 'module_name' => 'permissions'],
            ['name' => 'delete permissions', 'module_name' => 'permissions'],
            ['name' => 'list logo settings', 'module_name' => 'logo settings'],
            ['name' => 'edit logo settings', 'module_name' => 'logo settings'],
            ['name' => 'list system settings', 'module_name' => 'system settings'],
            ['name' => 'show app system settings', 'module_name' => 'system settings'],
            ['name' => 'edit app system settings', 'module_name' => 'system settings'],
            ['name' => 'show db system settings', 'module_name' => 'system settings'],
            ['name' => 'edit db system settings', 'module_name' => 'system settings'],
            ['name' => 'list users', 'module_name' => 'users'],
            ['name' => 'show admin users', 'module_name' => 'users'],
            ['name' => 'show super admin users', 'module_name' => 'users'],
            ['name' => 'create users', 'module_name' => 'users'],
            ['name' => 'create admin users', 'module_name' => 'users'],
            ['name' => 'create super admin users', 'module_name' => 'users'],
            ['name' => 'edit users', 'module_name' => 'users'],
            ['name' => 'edit admin users', 'module_name' => 'users'],
            ['name' => 'edit super admin users', 'module_name' => 'users'],
            ['name' => 'delete users', 'module_name' => 'users'],
            ['name' => 'delete admin users', 'module_name' => 'users'],
            ['name' => 'delete super admin users', 'module_name' => 'users'],
            ['name' => 'assign admin role', 'module_name' => 'users'],
            ['name' => 'assign super admin role', 'module_name' => 'users'],
            // ['name' => 'list customers', 'module_name' => 'customers'],
            // ['name' => 'show all customers', 'module_name' => 'customers'],
            // ['name' => 'show own customers', 'module_name' => 'customers'],
            // ['name' => 'create customers', 'module_name' => 'customers'],
            // ['name' => 'edit all customers', 'module_name' => 'customers'],
            // ['name' => 'edit own customers', 'module_name' => 'customers'],
            // ['name' => 'delete customers', 'module_name' => 'customers'],
            // ['name' => 'show customer routes', 'module_name' => 'customers'],
            // ['name' => 'be assigned to customer', 'module_name' => 'customers'],
            // ['name' => 'be assigned to many customers', 'module_name' => 'customers'],
            // ['name' => '', 'module_name' => 'customers'],
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return [
                'name' => $permission['name'],
                'module_name' => $permission['module_name'],
                'guard_name' => 'web',
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
