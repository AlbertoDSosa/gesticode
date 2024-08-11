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

    protected function setUp(): void
    {
        // first include all the normal setUp operations
        parent::setUp();

        // now de-register all the roles and permissions by clearing the permission cache
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

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

        $this->actingAs($admin);

        $this->get("/management/settings/users/edit/{$user->id}")->assertDontSeeText('Super Administrator');

        $this->actingAs($superAdmin);

        $this->get("/management/settings/users/edit/{$user->id}")->assertSeeText('Super Administrator');
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
            ->set('email', 'test_1@gmail.com')
            ->set('password', 'password')
            ->set('role', 'technician')
            ->call('update')
            ->assertHasNoErrors();

        Volt::test('pages.management.settings.users.edit', ['user' => $user])
            ->set('name', 'Test User 2')
            ->set('email', 'Test_2test')
            ->set('password', 'passwo')
            ->set('role', 'user')
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
            ->assertForbidden();

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
            ->assertForbidden();

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
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.edit', ['user' => $admin2])
            ->set('role', 'super-admin')
            ->call('update')
            ->assertRedirect(route('management.settings.users'));
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

        $this->get('/management/settings/users')
            ->assertSeeText('Test User')
            ->assertSeeText('test@gmail.com')
            ->assertDontSeeText($user->name)
            ->assertDontSeeText($user->email);
    }

    // #[Group('users'), Test]
    // public function my_test(): void
    // {
    //     $this->markTestSkipped();
    //     $this->seed(RolesAndPermissionsSeeder::class);

    //     $user = $this->createUser(['role' => 'technician']);
    //     $admin = $this->createUser(['role' => 'admin']);
    //     $superAdmin = $this->createUser(['role' => 'super-admin']);

    // }
}
