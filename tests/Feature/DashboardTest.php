<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesAndPermissionsSeeder;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // first include all the normal setUp operations
        parent::setUp();

        // now de-register all the roles and permissions by clearing the permission cache
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_only_authorized_users_can_display_the_site_settings_menu(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'user']);
        $guest = $this->createUser(['role' => 'guest']);
        $technician = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertDontSee('<span>Site Settings</span>', $escaped = false);

        $this->actingAs($guest);

        $this->get('/dashboard')
                ->assertDontSee('<span>Site Settings</span>', $escaped = false);

        $this->actingAs($technician);

        $this->get('/dashboard')
                ->assertSee('<span>Site Settings</span>', $escaped = false);

        $this->actingAs($admin);

        $this->get('/dashboard')
            ->assertSee('<span>Site Settings</span>', $escaped = false);

        $this->actingAs($superAdmin);

        $this->get('/dashboard')
            ->assertSee('<span>Site Settings</span>', $escaped = false);
    }

    public function test_only_admin_users_can_display_the_management_settings_menu(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'user']);
        $guest = $this->createUser(['role' => 'guest']);
        $technician = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertDontSee('<span>Settings</span>', $escaped = false)
            ->assertDontSee('<span>Tools</span>', $escaped = false)
            ->assertDontSeeText('MANAGEMENT');

        $this->actingAs($guest);

        $this->get('/dashboard')
            ->assertDontSee('<span>Settings</span>', $escaped = false)
            ->assertDontSee('<span>Tools</span>', $escaped = false)
            ->assertDontSeeText('MANAGEMENT');

        $this->actingAs($technician);

        $this->get('/dashboard')
            ->assertDontSee('<span>Settings</span>', $escaped = false)
            ->assertDontSee('<span>Tools</span>', $escaped = false)
            ->assertDontSeeText('MANAGEMENT');

        $this->actingAs($admin);

        $this->get('/dashboard')
            ->assertSee('<span>Settings</span>', $escaped = false)
            ->assertSee('<span>Tools</span>', $escaped = false)
            ->assertSeeText('MANAGEMENT');

        $this->actingAs($superAdmin);

        $this->get('/dashboard')
            ->assertSee('<span>Settings</span>', $escaped = false)
            ->assertSee('<span>Tools</span>', $escaped = false)
            ->assertSeeText('MANAGEMENT');
    }

}
