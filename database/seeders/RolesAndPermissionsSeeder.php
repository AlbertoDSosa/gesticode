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
            'show roles',
            'create roles',
            'edit roles',
            'delete roles',
            'show permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'show settings logos',
            'edit settings logos',
            'show settings system',
            'edit settings system',
            'show users',
            'create users',
            'create admin-users',
            'edit users',
            'edit admin users',
            'delete users',
            'show customers',
            'create customers',
            'edit customers',
            'delete customers',
            'show customer routes',
            'be assigned to many customers',
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return [
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });



        Permission::insert($permissions->toArray());

        // Crear roles y asignar permisos

        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Es un usuario que tiene acceso a todos los apartados sin restricción ninguna.',
            'assignable_to_customer' => false,
            'view' => 'admin',
            'guard_name' => 'web'
        ]);

        $admin->givePermissionTo(Permission::all());

        $technician = Role::create([
            'name' => 'technician',
            'display_name' => 'Technician',
            'description' => 'Tiene acceso similar a casi todo, pero con ciertos limites (por definir)',
            'assignable_to_customer' => false,
            'view' => 'technician',
            'guard_name' => 'web'
        ]);

        $technician->givePermissionTo([
            'show users',
            'create users',
            'edit users',
            'show customers',
            'create customers',
            'edit customers',
            'be assigned to many customers'
        ]);

        $external_reseller = Role::create([
            'name' => 'external-reseller',
            'display_name' => 'External Reseller',
            'description' => 'Es similar al técnico, en cuento a las opciones a las que puede acceder, pero está limitado a varios clientes que tenga asignados y sus hotspots',
            'assignable_to_customer' => true,
            'view' => 'reseller',
            'guard_name' => 'web'
        ]);

        $external_reseller->givePermissionTo([
            'show customer routes',
            'be assigned to many customers'
        ]);

        $technical_customer = Role::create([
            'name' => 'technical-customer',
            'display_name' => 'Technical Customer',
            'description' => 'Es un usuario que tiene acceso como técnico a todos los hotspot que sean del cliente, pero con algún privilegio más de tipo técnico',
            'assignable_to_customer' => true,
            'view' => 'customer',
            'guard_name' => 'web'
        ]);

        $technical_customer->givePermissionTo([
            'show customer routes'
        ]);

        $customer = Role::create([
            'name' => 'customer',
            'display_name' => 'Customer',
            'description' => 'Es un usuario que tiene acceso a todos los hotspot del cliente',
            'assignable_to_customer' => true,
            'view' => 'customer',
            'guard_name' => 'web'
        ]);

        $customer->givePermissionTo([
            'show customer routes'
        ]);

    }
}
