<?php

namespace Tests\Feature\Management\Settings\Permissions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class EditTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        // first include all the normal setUp operations
        parent::setUp();

        // now de-register all the roles and permissions by clearing the permission cache
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    #[Group('permissions'), Test]
    public function only_admin_users_can_display_the_edit_permissions_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'user']);
        $technician = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission = $this->createPermission([
            'name' => 'permission test',
            'module_name' => 'test'
        ]);

        $this->actingAs($user);

        $this->get("/management/settings/permissions/edit/{$permission->id}")->assertForbidden();

        $this->actingAs($technician);

        $this->get("/management/settings/permissions/edit/{$permission->id}")->assertForbidden();

        $this->actingAs($admin);

        $this->get("/management/settings/permissions/edit/{$permission->id}")->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get("/management/settings/permissions/edit/{$permission->id}")->assertSuccessful();
    }

    #[Group('permissions'), Test]
    public function form_fields_should_have_correct_data(): void
    {
        $this->markTestSkipped();
        $this->seed(RolesAndPermissionsSeeder::class);

        $guest = $this->createUser(['role' => 'guest']);
        $technician = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

    }

    // #[Group('permissions'), Test]
    // public function my_test(): void
    // {
    //     $this->markTestSkipped();
    //     $this->seed(RolesAndPermissionsSeeder::class);

    //     $guest = $this->createUser(['role' => 'guest']);
    //     $technician = $this->createUser(['role' => 'technician']);
    //     $admin = $this->createUser(['role' => 'admin']);
    //     $superAdmin = $this->createUser(['role' => 'super-admin']);

    // }
}
