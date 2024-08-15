<?php

namespace Tests\Feature\Management\Settings\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class CreateTest extends TestCase
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
    public function only_admin_users_can_display_create_role_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/management/settings/roles/create')->assertForbidden();

        $this->actingAs($admin);

        $this->get('/management/settings/roles/create')->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get('/management/settings/roles/create')->assertSuccessful();
    }

    #[Group('roles'), Test]
    public function only_superadmin_users_can_list_superadmin_permissions(): void
    {
        // $this->markTestSkipped();
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($admin);

        $this->get('/management/settings/roles/create')->assertDontSeeText('show super admin permissions');

        $this->actingAs($superAdmin);

        $this->get('/management/settings/roles/create')->assertSeeText('show super admin permissions');

    }

    #[Group('roles'), Test]
    public function form_fields_should_have_correct_data(): void
    {
        // $this->markTestSkipped();
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($superAdmin);

        Volt::test('pages.management.settings.roles.create')
            ->set('name', 'test-role-1')
            ->set('display_name', 'Test Role 1')
            ->set('removable', true)
            ->call('create')
            ->assertHasNoErrors();

        Volt::test('pages.management.settings.roles.create')
            ->set('name', '')
            ->set('display_name', 'Test Role 2')
            ->set('removable', true)
            ->call('create')
            ->assertHasErrors(['name']);

        Volt::test('pages.management.settings.roles.create')
            ->set('name', 'test-role-3')
            ->set('display_name', '')
            ->set('removable', true)
            ->call('create')
            ->assertHasErrors(['display_name']);

        Volt::test('pages.management.settings.roles.create')
            ->set('name', 'test-role-4')
            ->set('display_name', 'Test Role 4')
            ->set('removable', true)
            ->set('permissions', [20000])
            ->call('create')
            ->assertHasErrors(['permissions']);

        Volt::test('pages.management.settings.roles.create')
            ->set('name', 'test-role-5')
            ->set('display_name', 'Test Role 5')
            ->set('removable', true)
            ->set('permissions', 'array')
            ->call('create')
            ->assertHasErrors(['permissions']);
    }

    #[Group('roles'), Test]
    public function only_superadmin_user_can_assing_superadmin_permissions_level(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission =$this->createPermission([
            'name' => 'super admin permission level',
            'level' => 'super-admin',
            'module_name' => 'test'
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.roles.create')
            ->set('name', 'test-role-1')
            ->set('display_name', 'Test Role 1')
            ->set('removable', true)
            ->set('permissions', [$permission->id])
            ->call('create')
            ->assertUnauthorized();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.create')
            ->set('name', 'test-role-2')
            ->set('display_name', 'Test Role 2')
            ->set('removable', true)
            ->set('permissions', [$permission->id])
            ->call('create')
            ->assertRedirect(route('management.settings.roles'));

    }

    #[Group('roles'), Test]
    public function can_create_new_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.create')
            ->set('name', 'test-role')
            ->set('display_name', 'Test Role')
            ->set('removable', true)
            ->call('create')
            ->assertRedirect(route('management.settings.roles'));

        $this->get('/management/settings/roles')
            ->assertSeeText('Test Role');
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
