<?php

namespace Tests\Feature\Management\Settings\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // first include all the normal setUp operations
        parent::setUp();

        // now de-register all the roles and permissions by clearing the permission cache
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    #[Group('roles'), Test]
    public function only_admin_users_can_display_the_roles_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/management/settings/roles')->assertForbidden();

        $this->actingAs($admin);

        $this->get('/management/settings/roles')->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get('/management/settings/roles')->assertSuccessful();
    }

    #[Group('roles'), Test]
    public function only_superadmin_users_can_list_superadmin_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($admin);
        $this->get('/management/settings/roles')->assertDontSeeText('Super Administrator');

        $this->actingAs($superAdmin);
        $this->get('/management/settings/roles')->assertSeeText('Super Administrator');
    }

   #[Group('roles'), Test]
    public function only_admin_users_can_delete_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $role1 = $this->createRole([
            'name' => 'role-1',
            'display_name' => 'Role 1'
        ]);

        $role2 = $this->createRole([
            'name' => 'role-2',
            'display_name' => 'Role 2'
        ]);


        Volt::actingAs($user)
            ->test('pages.management.settings.roles.index')
            ->call('delete', $role1->id)
            ->assertUnauthorized();

        Volt::actingAs($admin)
            ->test('pages.management.settings.roles.index')
            ->call('delete', $role1->id)
            ->assertSuccessful();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.index')
            ->call('delete', $role2->id)
            ->assertSuccessful();
    }

    #[Group('roles'), Test]
    public function users_cant_delete_roles_if_arent_removables(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $role1 = $this->createRole([
            'name' => 'role-1',
            'display_name' => 'Role 1',
            'removable' => true
        ]);

        $role2 = $this->createRole([
            'name' => 'role-2',
            'display_name' => 'Role 2',
            'removable' => false
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.roles.index')
            ->call('delete', $role1->id)
            ->assertSuccessful();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.index')
            ->call('delete', $role2->id)
            ->assertForbidden();
    }

    #[Group('roles'), Test]
    public function can_delete_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        $role = $this->createRole([
            'name' => 'role-test',
            'display_name' => 'Role Test'
        ]);

        $this->actingAs($admin);

        Volt::test('pages.management.settings.roles.index')
            ->call('delete', $role->id)
            ->assertSuccessful();

        $this->get('/management/settings/roles')->assertDontSeeText('Role Test');
    }

    // #[Group('roles'), Test]
    // public function my_test(): void
    // {
    //     $this->markTestSkipped();
    //     $this->seed(RolesAndPermissionsSeeder::class);

    //     $user = $this->createUser(['role' => 'technician']);
    //     $admin = $this->createUser(['role' => 'admin']);
    //     $superAdmin = $this->createUser(['role' => 'super-admin']);

    // }
}
