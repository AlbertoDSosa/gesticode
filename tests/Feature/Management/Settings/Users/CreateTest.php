<?php

namespace Tests\Feature\Management\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

    #[Group('users'), Test]
    public function only_admin_users_can_display_the_create_user_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/management/settings/users/create')->assertForbidden();

        $this->actingAs($admin);

        $this->get('/management/settings/users/create')->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get('/management/settings/users/create')->assertSuccessful();
    }

    #[Group('users'), Test]
    public function only_superadmin_users_can_list_superadmin_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($admin);

        $this->get('/management/settings/users/create')->assertDontSeeText('Super Administrator');

        $this->actingAs($superAdmin);

        $this->get('/management/settings/users/create')->assertSeeText('Super Administrator');
    }

    #[Group('users'), Test]
    public function form_fields_should_have_correct_data(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        $this->actingAs($admin);

        Volt::test('pages.management.settings.users.create')
            ->set('name', 'Test User 1')
            ->set('email', 'test_1@gmail.com')
            ->set('password', 'password')
            ->set('role', 'technician')
            ->call('create')
            ->assertHasNoErrors();

        Volt::test('pages.management.settings.users.create')
            ->set('name', 'Test User 2')
            ->set('email', 'test_2test')
            ->set('password', 'passwo')
            ->set('role', 'test')
            ->call('create')
            ->assertHasErrors(['email', 'password', 'role']);
    }

    #[Group('users'), Test]
    public function only_admin_users_can_create_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($user)
            ->test('pages.management.settings.users.create')
            ->set('name', 'Test User 1')
            ->set('email', 'test_1@test.com')
            ->set('password', 'password')
            ->set('role', 'technician')
            ->call('create')
            ->assertUnauthorized();

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.create')
            ->set('name', 'Test User 2')
            ->set('email', 'test_2@gmail.com')
            ->set('password', 'password')
            ->set('role', 'technician')
            ->call('create')
            ->assertRedirect(route('management.settings.users'));

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.create')
            ->set('name', 'Test User 3')
            ->set('email', 'test_3@gmail.com')
            ->set('password', 'password')
            ->set('role', 'admin')
            ->call('create')
            ->assertRedirect(route('management.settings.users'));

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.create')
            ->set('name', 'Test User 4')
            ->set('email', 'test_4@gmail.com')
            ->set('password', 'password')
            ->set('role', 'technician')
            ->call('create')
            ->assertRedirect(route('management.settings.users'));

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.create')
            ->set('name', 'Test User 5')
            ->set('email', 'test_5@gmail.com')
            ->set('password', 'password')
            ->set('role', 'admin')
            ->call('create')
            ->assertRedirect(route('management.settings.users'));

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.create')
            ->set('name', 'Test User 6')
            ->set('email', 'test_6@gmail.com')
            ->set('password', 'password')
            ->set('role', 'super-admin')
            ->call('create')
            ->assertRedirect(route('management.settings.users'));
    }

    #[Group('users'), Test]
    public function only_superadmin_user_can_create_superadmin_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.create')
            ->set('name', 'Test User 1')
            ->set('email', 'test_1@gmail.com')
            ->set('password', 'password')
            ->set('role', 'super-admin')
            ->call('create')
            ->assertUnauthorized();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.create')
            ->set('name', 'Test User 2')
            ->set('email', 'test_2@gmail.com')
            ->set('password', 'password')
            ->set('role', 'super-admin')
            ->call('create')
            ->assertRedirect(route('management.settings.users'));
    }

    #[Group('users'), Test]
    public function can_create_new_user(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.create')
            ->set('name', 'Test User')
            ->set('email', 'test@gmail.com')
            ->set('password', 'password')
            ->set('role', 'technician')
            ->call('create')
            ->assertRedirect(route('management.settings.users'));

        $this->get('/management/settings/users')
            ->assertSeeText('Test User')
            ->assertSeeText('test@gmail.com');
    }

    // #[Group('users'), Test]
    // public function my_test(): void
    // {
    //     $this->seed(RolesAndPermissionsSeeder::class);

    //     $user = $this->createUser(['role' => 'technician']);
    //     $admin = $this->createUser(['role' => 'admin']);
    //     $superAdmin = $this->createUser(['role' => 'super-admin']);

    // }
}
