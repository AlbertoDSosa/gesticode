<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Users\{User, UserProfile};

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
            'model_has_roles'
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
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
            'name' => 'Alberto D. Sosa',
            'email' => 'albertodsosa@gmail.com',
            'password' => Hash::make('yalasabes')
        ]);


        UserProfile::factory()->create([
            'user_id' => $alberto->id,
            'first_name' => $alberto->name
        ]);
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
