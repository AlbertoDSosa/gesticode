<?php

namespace Tests\Feature\Management\Settings\Users;

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

    #[Group('users'), Test]
    public function only_admin_users_can_display_the__users_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/management/settings/users')->assertForbidden();

        $this->actingAs($admin);

        $this->get('/management/settings/users')->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get('/management/settings/users')->assertSuccessful();
    }

    #[Group('users'), Test]
    public function only_superadmin_users_can_list_other_superadmin_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($admin);
        $this->get('/management/settings/users')->assertDontSeeText($superAdmin->name);

        $this->actingAs($superAdmin);
        $this->get('/management/settings/users')->assertSeeText($superAdmin->name);

    }

    #[Group('users'), Test]
    public function can_delete_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.index')
            ->call('delete', $user->id)
            ->assertSuccessful();

        $this->get('/management/settings/users')->assertDontSeeText($user->name);

    }

    #[Group('users'), Test]
    public function only_admin_users_can_delete_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user1 = $this->createUser(['role' => 'technician']);
        $user2 = $this->createUser(['role' => 'technician']);
        $user3 = $this->createUser(['role' => 'technician']);

        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($user1)
            ->test('pages.management.settings.users.index')
            ->call('delete', $user2->id)
            ->assertForbidden();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.users.index')
            ->call('delete', $user3->id)
            ->assertSuccessful();

        Volt::actingAs($admin)
            ->test('pages.management.settings.users.index')
            ->call('delete', $user2->id)
            ->assertSuccessful();
    }

    #[Group('users'), Test]
    public function only_superadmin_users_can_delete_superadmin_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin1 = $this->createUser(['role' => 'admin']);
        $superAdmin1 = $this->createUser(['role' => 'super-admin']);

        $admin2 = $this->createUser(['role' => 'admin']);
        $superAdmin2 = $this->createUser(['role' => 'super-admin']);

        $admin3 = $this->createUser(['role' => 'admin']);
        $superAdmin3 = $this->createUser(['role' => 'super-admin']);

        Volt::actingAs($admin1)
            ->test('pages.management.settings.users.index')
            ->call('delete', $admin2->id)
            ->assertSuccessful();

        Volt::actingAs($superAdmin1)
            ->test('pages.management.settings.users.index')
            ->call('delete', $admin1->id)
            ->assertSuccessful();

        Volt::actingAs($superAdmin1)
            ->test('pages.management.settings.users.index')
            ->call('delete', $superAdmin2->id)
            ->assertSuccessful();

            Volt::actingAs($admin3)
            ->test('pages.management.settings.users.index')
            ->call('delete', $superAdmin3->id)
            ->assertForbidden();
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
