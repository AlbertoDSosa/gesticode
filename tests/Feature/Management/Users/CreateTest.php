<?php

namespace Tests\Feature\Management\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_screen_can_be_rendered(): void
    {
        $this->markTestSkipped();
        $response = $this->get('/management/users/create');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.management.users.create');
    }

    public function test_new_users_can_create(): void
    {
        $this->markTestSkipped();
        $component = Volt::test('pages.management.users.create')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('user_create');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }
}
