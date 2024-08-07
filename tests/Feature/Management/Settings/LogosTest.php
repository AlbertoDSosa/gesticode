<?php

namespace Tests\Feature\Management\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogosTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        // first include all the normal setUp operations
        parent::setUp();

        // now de-register all the roles and permissions by clearing the permission cache
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    #[Group('settings'), Test]
    public function only_admin_users_can_display_the_setting_logos_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/management/settings/logos')->assertForbidden();

        $this->actingAs($admin);

        $this->get('/management/settings/logos')->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get('/management/settings/logos')->assertSuccessful();
    }

    #[Group('settings'), Test]
    public function only_admin_users_can_update_logos(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        // 'logo'
        // 'favicon'
        // 'dark_logo'
        // 'guest_logo'
        // 'guest_background'
        // 'status'
        // 'disabledUpload'

        Volt::actingAs($user)
            ->test('pages.management.settings.logos')
            ->call('update')
            ->assertForbidden();

        Volt::actingAs($admin)
            ->test('pages.management.settings.logos')
            ->call('update')
            ->assertSuccessful();

        Volt::actingAs($superAdmin)
            ->test('pages.management.settings.logos')
            ->call('update')
            ->assertSuccessful();

    }

    #[Group('settings'), Test]
    public function can_update_logos(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        Storage::fake('public');

        $logo = UploadedFile::fake()->image('logo.png', 32, 32)->size(150);

        Volt::actingAs($admin)
            ->test('pages.management.settings.logos')
            ->set('logo', $logo)
            ->call('update')
            ->assertSuccessful();

        $this->assertEquals([
            "contentType" => "image",
            "content" => "/storage/1/logo.png"
          ],
          getLogoSettings('logo')
        );

        Storage::disk('public')->assertExists('/1/logo.png');
    }

    // #[Group('settings'), Test]
    // public function my_test(): void
    // {
    //     $this->seed(RolesAndPermissionsSeeder::class);

    //     $user = $this->createUser(['role' => 'technician']);
    //     $admin = $this->createUser(['role' => 'admin']);
    //     $superAdmin = $this->createUser(['role' => 'super-admin']);

    // }
}


