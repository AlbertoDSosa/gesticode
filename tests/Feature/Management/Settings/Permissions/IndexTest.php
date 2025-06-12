<?php

namespace Tests\Feature\Management\Settings\Permissions;

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

    #[Group('permissions'), Test]
    public function only_admin_users_can_display_the_permissions_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/management/settings/permissions')->assertForbidden();

        $this->actingAs($admin);

        $this->get('/management/settings/permissions')->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get('/management/settings/permissions')->assertSuccessful();
    }

    #[Group('permissions'), Test]
    public function only_superadmin_users_can_list_superadmin_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->createPermission([
            'name' => 'super admin permission level',
            'level' => 'super-admin',
            'module_name' => 'test'
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.index')
            ->assertDontSeeText('super admin permission level');

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.index')
            ->assertSeeText('super admin permission level');
    }

    #[Group('permissions'), Test]
    public function only_admin_users_can_delete_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission1 = $this->createPermission([
            'name' => 'permission test 1',
            'level' => 'regular',
            'module_name' => 'test'
        ]);

        $permission2 = $this->createPermission([
            'name' => 'permission test 2',
            'level' => 'regular',
            'module_name' => 'test'
        ]);

        Volt::actingAs($user)
            ->test('pages.management.settings.permissions.index')
            ->call('delete', $permission1->id)
            ->assertUnauthorized();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.index')
            ->call('delete', $permission1->id)
            ->assertSuccessful();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.index')
            ->call('delete', $permission2->id)
            ->assertSuccessful();
    }

    #[Group('permissions'), Test]
    public function only_superadmin_users_can_delete_superadmin_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission1 = $this->createPermission([
            'name' => 'permission test 1',
            'level' => 'admin',
            'module_name' => 'test'
        ]);

        $permission2 = $this->createPermission([
            'name' => 'permission test 2',
            'level' => 'super-admin',
            'module_name' => 'test'
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.index')
            ->call('delete', $permission1->id)
            ->assertSuccessful();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.index')
            ->call('delete', $permission2->id)
            ->assertUnauthorized();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.permissions.index')
            ->call('delete', $permission2->id)
            ->assertSuccessful();
    }

    #[Group('permissions'), Test]
    public function can_delete_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        $permission = $this->createPermission([
            'name' => 'permission test',
            'level' => 'regular',
            'module_name' => 'test'
        ]);

        $this->actingAs($admin);

        Volt::test('pages.management.settings.permissions.index')
            ->call('delete', $permission->id)
            ->assertSuccessful();

        Volt::actingAs($admin)
            ->test('pages.management.settings.permissions.index')
            ->assertDontSeeText('permission test');

    }

    // #[Group('permissions'), Test]
    // public function my_test(): void
    // {
    //     $this->markTestSkipped();
    //     $this->seed(RolesAndPermissionsSeeder::class);

    //     $user = $this->createUser(['role' => 'technician']);
    //     $admin = $this->createUser(['role' => 'admin']);
    //     $superAdmin = $this->createUser(['role' => 'super-admin']);

    // }
}
