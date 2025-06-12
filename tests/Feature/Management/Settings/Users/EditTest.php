<?php

namespace Tests\Feature\Management\Settings\Users;

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

    #[Group('users'), Test]
    public function only_admin_users_can_display_the_update_user_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get("/management/settings/users/edit/{$user->id}")->assertForbidden();

        $this->actingAs($admin);

        $this->get("/management/settings/users/edit/{$user->id}")->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get("/management/settings/users/edit/{$user->id}")->assertSuccessful();

    }

    #[Group('users'), Test]
    public function only_superadmin_users_can_list_superadmin_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.edit', ['user' => $user])
            ->assertDontSeeText('Super Administrator');

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.edit', ['user' => $user])
            ->assertSeeText('Super Administrator');
    }

    #[Group('users'), Test]
    public function form_fields_has_correct_data(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);

        $this->actingAs($admin);

        Volt::test('pages.management.settings.users.edit', ['user' => $user])
            ->set('name', 'Test User 1')
            ->assertSet('role', 'technician')
            ->set('email', 'test_1@gmail.com')
            ->set('password', 'password')
            ->set('role', 'guest')
            ->set('active', true)
            ->call('update')
            ->assertHasNoErrors();

        Volt::test('pages.management.settings.users.edit', ['user' => $user])
            ->set('name', 'Test User 2')
            ->set('email', 'Test_2test')
            ->set('password', 'passwo')
            ->set('role', 'test')
            ->set('active', false)
            ->call('update')
            ->assertHasErrors(['email', 'password', 'role']);
    }

    #[Group('users'), Test]
    public function only_admin_users_can_update_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($user)
            ->test('pages.management.settings.users.edit', ['user' => $user])
            ->call('update')
            ->assertUnauthorized();

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.edit', ['user' => $user])
            ->call('update')
            ->assertRedirect(route('management.settings.users'));

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.edit', ['user' => $user])
            ->call('update')
            ->assertRedirect(route('management.settings.users'));
    }

    #[Group('users'), Test]
    public function only_superadmin_users_can_update_superadmin_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);
        $superAdmin2 = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.edit', ['user' => $superAdmin])
            ->call('update')
            ->assertUnauthorized();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.edit', ['user' => $superAdmin2])
            ->call('update')
            ->assertRedirect(route('management.settings.users'));
    }

    #[Group('users'), Test]
    public function only_superadmin_users_can_assign_superadmin_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $admin2 = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.edit', ['user' => $admin2])
            ->set('role', 'super-admin')
            ->call('update')
            ->assertUnauthorized();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.edit', ['user' => $admin2])
            ->set('role', 'super-admin')
            ->call('update')
            ->assertRedirect(route('management.settings.users'));
    }

    #[Group('users'), Test]
    public function only_superadmin_users_can_list_superadmin_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.edit', ['user' => $superAdmin])
            ->assertDontSeeText('show super admin users');

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.edit', ['user' => $superAdmin])
            ->assertSeeText('show super admin users');
    }

    #[Group('users'), Test]
    public function only_superadmin_user_can_assign_superadmin_permissions_level(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission = $this->createPermission([
            'name' => 'super admin permission level',
            'level' => 'super-admin',
            'module_name' => 'test'
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.edit', ['user' => $admin])
            ->set('permissions', [$permission->id])
            ->call('update')
            ->assertUnauthorized();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.edit', ['user' => $superAdmin])
            ->set('permissions', [$permission->id])
            ->call('update')
            ->assertOk();
    }

    #[Group('users'), Test]
    public function only_can_assign_permissions_if_are_assignables(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $permission1 = $this->createPermission([
            'name' => 'permission test 1',
            'level' => 'regular',
            'module_name' => 'test',
            'assignable' => false
        ]);

        $permission2 = $this->createPermission([
            'name' => 'permission test 2',
            'level' => 'regular',
            'module_name' => 'test',
            'assignable' => true
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.edit', ['user' => $admin])
            ->set('permissions', [$permission1->id])
            ->call('update')
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.edit', ['user' => $admin])
            ->set('permissions', [$permission2->id])
            ->call('update')
            ->assertOk();
    }

    #[Group('users'), Test]
    public function only_can_assign_free_user_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $technician = $this->createUser(['role' => 'technician']);

        $rolePermissions = $technician->mainRole->permissions()->pluck('id')->toArray();


        $permission1 = $this->createPermission([
            'name' => 'permission test 1',
            'level' => 'regular',
            'module_name' => 'test',
            'assignable' => true
        ]);

        $permission2 = $this->createPermission([
            'name' => 'permission test 2',
            'level' => 'regular',
            'module_name' => 'test',
            'assignable' => true
        ]);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.edit', ['user' => $technician])
            ->set('permissions', collect($rolePermissions)->merge([$permission1->id, $permission2->id])->toArray())
            ->call('update')
            ->assertOk();

        $this->assertSame($technician->permissions()->pluck('name')->toArray(), [$permission1->name, $permission2->name]);
    }

    #[Group('users'), Test]
    public function can_update_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.edit', ['user' => $user])
            ->set('name', 'Test User')
            ->set('email', 'test@gmail.com')
            ->call('update')
            ->assertRedirect(route('management.settings.users'));

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.index')
            ->assertSeeText('Test User')
            ->assertSeeText('test@gmail.com')
            ->assertDontSeeText($user->name)
            ->assertDontSeeText($user->email);
    }
}
