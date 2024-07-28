<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

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

    public function test_only_admin_users_can_display_the_settings_menu(): void
    {
        $this->createRoles();

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertDontSee('<span>Settings</span>', $escaped = false)
            ->assertDontSeeText('MANAGEMENT');

        $this->actingAs($admin);

        $this->get('/dashboard')
            ->assertSee('<span>Settings</span>', $escaped = false)
            ->assertSeeText('MANAGEMENT');
            $this->actingAs($admin);

        $this->actingAs($superAdmin);

        $this->get('/dashboard')
            ->assertSee('<span>Settings</span>', $escaped = false)
            ->assertSeeText('MANAGEMENT');
    }

}
