<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_the_public_landing_page(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSee('SAP Business One Support Portal')
            ->assertSee('Welcome Back')
            ->assertSee('Sign in')
            ->assertSee('Email Address')
            ->assertSee('Password')
            ->assertSee('Contact Support')
            ->assertSee('&copy; 2026 Xceler8 Technologies Inc.', false);
    }

    public function test_database_user_can_sign_in(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();

        $this->post(route('login.store'), [
            'email' => 'admin@xceler8.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_database_credentials_are_rejected(): void
    {
        $this->seed();

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'admin@xceler8.test',
                'password' => 'incorrect-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_registration_requires_authentication(): void
    {
        $this->get(route('users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_sign_out(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
