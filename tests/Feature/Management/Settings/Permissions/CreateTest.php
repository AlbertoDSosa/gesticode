<?php

namespace Tests\Feature\Management\Settings\Permissions;

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

    #[Group('permissions'), Test]
    public function only_admin_users_can_display_the_create_permissions_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'user']);
        $technician = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/management/settings/permissions/create')->assertForbidden();

        $this->actingAs($technician);

        $this->get('/management/settings/permissions/create')->assertForbidden();

        $this->actingAs($admin);

        $this->get('/management/settings/permissions/create')->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get('/management/settings/permissions/create')->assertSuccessful();
    }

    #[Group('permissions'), Test]
    public function only_superadmin_users_can_list_superadmin_permission_level(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($admin);

        $this->actingAs($superAdmin);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->assertDontSeeText('super-admin');

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.create')
            ->assertSeeText('super-admin');

    }

    #[Group('permissions'), Test]
    public function form_fields_should_have_correct_data(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertHasNoErrors();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertHasErrors(['name']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', '')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertHasErrors(['name']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-2')
            ->set('module_name', '')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertHasErrors(['module_name']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-2')
            ->set('module_name', 'test')
            ->set('level', '')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertHasErrors(['level']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-2')
            ->set('module_name', 'test')
            ->set('level', 'normal')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertHasErrors(['level']);
    }

    #[Group('permissions'), Test]
    public function only_admin_users_can_create_permisions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $technician = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($technician)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertUnauthorized();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertSuccessful();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', true)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertSuccessful();
    }

    #[Group('permissions'), Test]
    public function only_superadmin_users_can_create_superadmin_permisions_level(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'super-admin')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', false)
            ->call('create')
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'super-admin')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', false)
            ->call('create')
            ->assertSuccessful();
    }

    #[Group('permissions'), Test]
    public function if_permission_has_superadmin_level_it_should_not_be_editable_removable_or_assignable(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'super-admin')
            ->set('removable', true)
            ->set('editable', false)
            ->set('assignable', false)
            ->call('create')
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'super-admin')
            ->set('removable', false)
            ->set('editable', true)
            ->set('assignable', false)
            ->call('create')
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'super-admin')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', true)
            ->call('create')
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'super-admin')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', false)
            ->call('create')
            ->assertSuccessful();

    }

    #[Group('permissions'), Test]
    public function if_permission_has_admin_level_it_should_not_be_editable_or_removable(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'admin')
            ->set('removable', true)
            ->set('editable', false)
            ->set('assignable', false)
            ->call('create')
            ->assertForbidden();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'admin')
            ->set('removable', false)
            ->set('editable', true)
            ->set('assignable', true)
            ->call('create')
            ->assertForbidden();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test-permission-1')
            ->set('module_name', 'test')
            ->set('level', 'admin')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', true)
            ->call('create')
            ->assertSuccessful();
    }

    #[Group('permissions'), Test]
    public function can_create_new_permission(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.create')
            ->set('name', 'test permission 1')
            ->set('module_name', 'test')
            ->set('level', 'regular')
            ->set('removable', false)
            ->set('editable', false)
            ->set('assignable', true)
            ->call('create')
            ->assertRedirect(route('management.settings.permissions'));

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.index')
            ->assertSeeText('test permission 1');
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
