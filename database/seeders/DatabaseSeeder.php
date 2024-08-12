<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Users\{User, UserProfile};
use App\Models\Customers\{Customer, CustomerProfile};

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables([
            'users',
            'user_profiles',
            'permissions',
            'model_has_permissions',
            'roles',
            'role_has_permissions',
            'model_has_roles',
            'customers',
            'customer_profiles',
            'user_has_customers'
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class
        ]);

        $this->createData();
    }

    protected function truncateTables(array $tables)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    public function createAdminUsers()
    {

        $alberto = User::factory()->create([
            'name' => 'Alberto D.Sosa',
            'email' => 'albertodsosa@gmail.com',
            'password' => Hash::make('yalasabes'),
            'assignable_to_customer' => false,
            'view' => 'admin',
        ]);

        UserProfile::factory()->create([
            'user_id' => $alberto->id,
            'first_name' => $alberto->name
        ]);

        $alberto->assignRole('super-admin');

        $orlando = User::factory()->create([
            'name' => 'Orlando D.Sosa',
            'email' => 'orlandodsosa@gmail.com',
            'password' => Hash::make('yalasabes'),
            'assignable_to_customer' => false,
            'view' => 'admin',
        ]);

        UserProfile::factory()->create([
            'user_id' => $orlando->id,
            'first_name' => $orlando->name
        ]);

        $orlando->assignRole('admin');

        $sebas = User::factory()->create([
            'name' => 'Sebatián D.Sosa',
            'email' => 'sebasdsosa@gmail.com',
            'password' => Hash::make('yalasabes'),
            'assignable_to_customer' => true,
            'view' => 'technician'
        ]);

        UserProfile::factory()->create([
            'user_id' => $sebas->id,
            'first_name' => $sebas->name
        ]);

        $sebas->assignRole('technician');
    }

    public function createCustomers()
    {
        $marhaba = Customer::factory()->create([
            'name' => 'Restaurante Marhaba',
            'email' => 'info@restaurantemarhaba.com',
            'slug' => 'restaurante-marhaba'
        ]);

        CustomerProfile::factory()->create([
            'customer_id' => $marhaba->id
        ]);

        $laruta = Customer::factory()->create([
            'name' => 'Cerbeceria La Ruta',
            'email' => 'info@cervecerialaruta.com',
            'slug' => 'cerbeceria-la-ruta'
        ]);

        CustomerProfile::factory()->create([
            'customer_id' => $laruta->id
        ]);

        return compact('laruta', 'marhaba');
    }

    public function createData()
    {
        $this->createAdminUsers();
        $this->createCustomers();
    }

    public function getPermissionNames($permissions)
    {
        $assigned = [];

        foreach ($permissions as $permission) {
            $assigned[] = $permission->name;
        }

        return $assigned;
    }

    public function assignPermissions($user)
    {
        $permissions = $user->getPermissionsViaRoles()->all();
        $names = $this->getPermissionNames($permissions);
        $user->givePermissionTo($names);
    }
}
