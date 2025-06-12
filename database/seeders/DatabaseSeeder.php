<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Users\{User, UserProfile};

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            RolesAndPermissionsSeeder::class
        ]);

        $this->createData();
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

    public function createData()
    {
        $this->createAdminUsers();
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
