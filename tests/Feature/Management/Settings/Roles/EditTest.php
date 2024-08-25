<?php

namespace Tests\Feature\Management\Settings\Roles;

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

    #[Group('roles'), Test]
    public function only_admin_users_can_display_edit_role_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);
        $role = $this->createRole([
            'name' => 'role',
            'display_name' => 'Role'
        ]);

        $this->actingAs($user);

        $this->get("/management/settings/roles/edit/{$role->id}")->assertForbidden();

        $this->actingAs($admin);

        $this->get("/management/settings/roles/edit/{$role->id}")->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get("/management/settings/roles/edit/{$role->id}")->assertSuccessful();
    }

    #[Group('roles'), Test]
    public function only_superadmin_users_can_display_edit_superadmin_role_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);
        $role = $this->createRole([
            'name' => 'role',
            'display_name' => 'Role'
        ]);

        $this->actingAs($user);

        $this->get("/management/settings/roles/edit/{$role->id}")->assertForbidden();

        $this->actingAs($admin);

        $this->get("/management/settings/roles/edit/{$role->id}")->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get("/management/settings/roles/edit/{$role->id}")->assertSuccessful();
    }

    #[Group('roles'), Test]
    public function only_superadmin_users_can_list_superadmin_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $role = $this->createRole([
            'name' => 'role',
            'display_name' => 'Role'
        ]);

        $this->actingAs($admin);

        $this->get("/management/settings/roles/edit/{$role->id}")->assertDontSeeText('show super admin users');

        $this->actingAs($superAdmin);

        $this->get("/management/settings/roles/edit/{$role->id}")->assertSeeText('show super admin users');

    }

    #[Group('roles'), Test]
    public function form_fields_should_have_correct_data(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($superAdmin);

        $role1 = $this->createRole([
            'name' => 'role-1',
            'display_name' => 'Role 1'
        ]);

        $role2 = $this->createRole([
            'name' => 'role-2',
            'display_name' => 'Role 2'
        ]);

        $role3 = $this->createRole([
            'name' => 'role-3',
            'display_name' => 'Role 3'
        ]);

        $role4 = $this->createRole([
            'name' => 'role-4',
            'display_name' => 'Role 4'
        ]);

        Volt::test('pages.management.settings.roles.edit', ['role' => $role1])
            ->set('name', 'role-2')
            ->set('display_name', 'Role 2')
            ->set('removable', false)
            ->call('update')
            ->assertHasErrors(['name']);

        Volt::test('pages.management.settings.roles.edit', ['role' => $role1])
            ->set('name', 'test-role-1')
            ->set('display_name', 'Test Role 2')
            ->set('removable', false)
            ->call('update')
            ->assertHasNoErrors();

        Volt::test('pages.management.settings.roles.edit', ['role' => $role2])
            ->set('name', '')
            ->set('display_name', 'Test Role 2')
            ->set('removable', true)
            ->call('update')
            ->assertHasErrors(['name']);

        Volt::test('pages.management.settings.roles.edit', ['role' => $role3])
            ->set('name', 'test-role-3')
            ->set('display_name', '')
            ->set('removable', true)
            ->call('update')
            ->assertHasErrors(['display_name']);

        Volt::test('pages.management.settings.roles.edit', ['role' => $role4])
            ->set('name', 'test-role-4')
            ->set('display_name', 'Test Role 4')
            ->set('removable', true)
            ->set('permissions', [20000])
            ->call('update')
            ->assertHasErrors(['permissions']);
    }

    #[Group('roles'), Test]
    public function only_admin_users_can_edit_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $role = $this->createRole([
            'name' => 'role',
            'display_name' => 'Role'
        ]);

        Volt::actingAs($user)
            ->test('pages.management.settings.roles.edit', ['role' => $role])
            ->set('name', 'test-role-1')
            ->set('display_name', 'Test Role 1')
            ->call('update')
            ->assertUnauthorized();

        Volt::actingAs($admin)
            ->test('pages.management.settings.roles.edit', ['role' => $role])
            ->set('name', 'test-role-2')
            ->set('display_name', 'Test Role 2')
            ->call('update')
            ->assertOK();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.edit', ['role' => $role])
            ->set('name', 'test-role-3')
            ->set('display_name', 'Test Role 3')
            ->call('update')
            ->assertOk();
    }

    #[Group('roles'), Test]
    public function can_edit_roles_if_are_editables(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $role1 = $this->createRole([
            'name' => 'test-role-1',
            'display_name' => 'Test Role 1',
            'editable' => true
        ]);

        $role2 = $this->createRole([
            'name' => 'test-role-2',
            'display_name' => 'Test Role 2',
            'editable' => false
        ]);

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.edit', ['role' => $role1])
            ->set('name', 'test-role-3')
            ->set('display_name', 'Test Role 3')
            ->call('update')
            ->assertOk();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.edit', ['role' => $role2])
            ->set('name', 'test-role-4')
            ->set('display_name', 'Test Role 4')
            ->call('update')
            ->assertForbidden();
    }

    #[Group('roles'), Test]
    public function only_superadmin_user_can_assing_superadmin_permissions_level(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission = $this->createPermission([
            'name' => 'super admin permission level',
            'level' => 'super-admin',
            'module_name' => 'test'
        ]);

        $role = $this->createRole([
            'name' => 'role',
            'display_name' => 'Role'
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.roles.edit', ['role' => $role])
            ->set('name', 'test-role-1')
            ->set('display_name', 'Test Role 1')
            ->set('removable', true)
            ->set('permissions', [$permission->id])
            ->call('update')
            ->assertUnauthorized();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.edit', ['role' => $role])
            ->set('name', 'test-role-2')
            ->set('display_name', 'Test Role 2')
            ->set('removable', true)
            ->set('permissions', [$permission->id])
            ->call('update')
            ->assertOk();
    }

    #[Group('roles'), Test]
    public function only_can_assing_permissions_if_are_editables(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission1 = $this->createPermission([
            'name' => 'permission test 1',
            'level' => 'regular',
            'module_name' => 'test',
            'editable' => false
        ]);

        $permission2 = $this->createPermission([
            'name' => 'permission test 2',
            'level' => 'regular',
            'module_name' => 'test',
            'editable' => true
        ]);

        $role = $this->createRole([
            'name' => 'role',
            'display_name' => 'Role'
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.roles.edit', ['role' => $role])
            ->set('name', 'test-role-1')
            ->set('display_name', 'Test Role 1')
            ->set('removable', true)
            ->set('permissions', [$permission1->id])
            ->call('update')
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.edit', ['role' => $role])
            ->set('name', 'test-role-2')
            ->set('display_name', 'Test Role 2')
            ->set('removable', true)
            ->set('permissions', [$permission2->id])
            ->call('update')
            ->assertOk();
    }

    #[Group('roles'), Test]
    public function can_edit_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $role = $this->createRole([
            'name' => 'test-role-1',
            'display_name' => 'Test Role 1',
            'removable' => true
        ]);

        $permission1 = $this->createPermission([
            'name' => 'super admin permission level',
            'level' => 'super-admin',
            'module_name' => 'test'
        ]);

        $permission2 = $this->createPermission([
            'name' => 'admin permission level',
            'level' => 'admin',
            'module_name' => 'test'
        ]);

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.roles.edit', ['role' => $role])
            ->assertSet('display_name', 'Test Role 1')
            ->assertSet('name', 'test-role-1')
            ->assertSet('removable', true)
            ->assertSet('permissions', [])
            ->set('name', 'test-role-2')
            ->set('display_name', 'Test Role 2')
            ->set('removable', false)
            ->set('permissions', [$permission1->id, $permission2->id])
            ->call('update')
            ->assertRedirect(route('management.settings.roles'));

        $this->assertTrue($role->hasPermissionTo($permission1->name));
        $this->assertTrue($role->hasPermissionTo($permission2->name));

        $this->get('/management/settings/roles')
            ->assertSeeText('Test Role 2')
            ->assertDontSeeText('Test Role 1');

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
