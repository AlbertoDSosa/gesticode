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
    public function only_superadmin_users_can_display_superadmin_permission_level_pages(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission = $this->createPermission([
            'name' => 'super admin permission test',
            'module_name' => 'test',
            'level' => 'super-admin'
        ]);

        $this->actingAs($admin);

        $this->get("/management/settings/permissions/edit/{$permission->id}")->assertForbidden();

        $this->actingAs($superAdmin);

        $this->get("/management/settings/permissions/edit/{$permission->id}")->assertSuccessful();

    }

    #[Group('permissions'), Test]
    public function only_superadmin_users_can_list_superadmin_permission_level(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission = $this->createPermission([
            'name' => 'permission test',
            'module_name' => 'test'
        ]);

        $this->actingAs($admin);

        $this->get("/management/settings/permissions/edit/{$permission->id}")->assertDontSeeText('super-admin');

        $this->actingAs($superAdmin);

        $this->get("/management/settings/permissions/edit/{$permission->id}")->assertSeeText('super-admin');

    }

    #[Group('permissions'), Test]
    public function only_admin_users_can_edit_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $guest = $this->createUser(['role' => 'guest']);
        $technician = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission = $this->createPermission([
            'name' => 'permission test',
            'module_name' => 'test'
        ]);

        Volt::actingAs($guest)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->call('update')
            ->assertUnauthorized();

        Volt::actingAs($technician)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->call('update')
            ->assertUnauthorized();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->call('update')
            ->assertOk();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->call('update')
            ->assertOk();
    }


    #[Group('permissions'), Test]
    public function form_fields_should_have_correct_data(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        $permission1 = $this->createPermission([
            'name' => 'permission test 1',
            'module_name' => 'test'
        ]);

        $permission2 = $this->createPermission([
            'name' => 'permission test 2',
            'module_name' => 'test'
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission1])
            ->set('name', 'test permission 1')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('update')
            ->assertHasNoErrors();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission2])
            ->set('name', 'test permission 1')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('update')
            ->assertHasErrors(['name']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission2])
            ->set('name', '')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('update')
            ->assertHasErrors(['name']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission2])
            ->set('name', 'test permission 2')
            ->set('module_name', '')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('update')
            ->assertHasErrors(['module_name']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission2])
            ->set('name', 'test permission 2')
            ->set('module_name', 'test')
            ->set('level', '')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('update')
            ->assertHasErrors(['level']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission2])
            ->set('name', 'test permission 2')
            ->set('module_name', 'test')
            ->set('level', 'normal')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('update')
            ->assertHasErrors(['level']);

    }

    #[Group('permissions'), Test]
    public function if_permission_has_superadmin_level_it_should_not_be_editable(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission = $this->createPermission([
            'name' => 'super admin permission test',
            'module_name' => 'test',
            'level' => 'super-admin'
        ]);

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->set('level', 'regular')
            ->call('update')
            ->assertForbidden();
    }

    #[Group('permissions'), Test]
    public function if_you_set_permission_to_superadmin_level_it_should_not_be_editable_removable_or_assignable(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission = $this->createPermission([
            'name' => 'super admin permission test',
            'module_name' => 'test',
            'level' => 'regular'
        ]);

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->set('level', 'super-admin')
            ->set('removable', true)
            ->set('editable', false)
            ->set('assignable', false)
            ->call('update')
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->set('level', 'super-admin')
            ->set('removable', false)
            ->set('editable', true)
            ->set('assignable', false)
            ->call('update')
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->set('level', 'super-admin')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', true)
            ->call('update')
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->set('level', 'super-admin')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', false)
            ->call('update')
            ->assertOK();
    }

    #[Group('permissions'), Test]
    public function if_you_set_permission_to_admin_level_it_should_not_be_editable_or_removable(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        $permission = $this->createPermission([
            'name' => 'admin permission test',
            'module_name' => 'test',
            'level' => 'regular'
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->set('level', 'admin')
            ->set('removable', true)
            ->set('editable', false)
            ->set('assignable', true)
            ->call('update')
            ->assertForbidden();


        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->set('level', 'admin')
            ->set('removable', false)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('update')
            ->assertForbidden();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->set('level', 'admin')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', true)
            ->call('update')
            ->assertOK();
    }

    #[Group('permissions'), Test]
    public function should_can_be_edit_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        $permission = $this->createPermission([
            'name' => 'permission test',
            'module_name' => 'test',
            'level' => 'regular'
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.edit', ['permission' => $permission])
            ->set('level', 'admin')
            ->set('name', 'test permission')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', true)
            ->call('update')
            ->assertRedirect(route('management.settings.permissions'));

        $this->get('/management/settings/permissions')
            ->assertSeeText('test permission');
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
