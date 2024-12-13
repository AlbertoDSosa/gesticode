<?php

namespace Tests\Feature\SiteSettings;

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

    #[Group('site-settings'), Test]
    public function only_users_with_permissions_can_display_the_setting_logos_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'user']);
        $guest = $this->createUser(['role' => 'guest']);
        $technician = $this->createUser(['role' => 'technician']);
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser(['role' => 'super-admin']);

        $this->actingAs($user);

        $this->get('/site-settings/logos')->assertForbidden();

        $this->actingAs($guest);

        $this->get('/site-settings/logos')->assertForbidden();

        $this->actingAs($technician);

        $this->get('/site-settings/logos')->assertSuccessful();

        $this->actingAs($admin);

        $this->get('/site-settings/logos')->assertSuccessful();

        $this->actingAs($superAdmin);

        $this->get('/site-settings/logos')->assertSuccessful();
    }

    #[Group('site-settings'), Test]
    public function only_users_with_permissions_can_update_logos(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = $this->createUser(['role' => 'user']);
        $guest = $this->createUser(['role' => 'guest']);
        $technician = $this->createUser(['role' => 'technician']);
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
            ->test('pages.site-settings.logos')
            ->call('update')
            ->assertForbidden();

        Volt::actingAs($guest)
            ->test('pages.site-settings.logos')
            ->call('update')
            ->assertForbidden();

        Volt::actingAs($technician)
            ->test('pages.site-settings.logos')
            ->call('update')
            ->assertSuccessful();

        Volt::actingAs($admin)
            ->test('pages.site-settings.logos')
            ->call('update')
            ->assertSuccessful();

        Volt::actingAs($superAdmin)
            ->test('pages.site-settings.logos')
            ->call('update')
            ->assertSuccessful();

    }

    #[Group('site-settings'), Test]
    public function the_logos_file_can_be_image(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        Storage::fake('public');

        $file = UploadedFile::fake()->create('file.pdf');

        Volt::actingAs($admin)
            ->test('pages.site-settings.logos')
            ->set('logo', $file)
            ->call('update')
            ->assertHasErrors('logo');
    }

    #[Group('site-settings'), Test]
    public function can_update_logos(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = $this->createUser(['role' => 'admin']);

        Storage::fake('public');

        $logo = UploadedFile::fake()->image('logo.png', 32, 32)->size(150);

        Volt::actingAs($admin)
            ->test('pages.site-settings.logos')
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


