<?php

namespace Tests\Feature\Management\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\RolesAndPermissionsSeeder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    #[Group('settings'), Test]
    public function only_admin_users_can_display_the_settings_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/management/settings')->assertForbidden();

        $this->actingAs($admin);

        $this->get('/management/settings')->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get('/management/settings')->assertSuccessful();
    }
}
