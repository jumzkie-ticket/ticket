<?php

namespace Tests\Feature;

use App\Support\SystemVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_dashboard_with_menu_pane(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $this->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSee('Welcome to Xceler8 Support System')
            ->assertSee('Analytics Overview')
            ->assertSee('Recent Tickets')
            ->assertSee('Quick Summary')
            ->assertSee('User Registration')
            ->assertSee('Support Schedule')
            ->assertSee('profile-dropdown', false)
            ->assertSee('Sign Out')
            ->assertSee('&copy; 2026 Xceler8 Technologies Inc.', false)
            ->assertSee('Version '.SystemVersion::current());

        $this->assertMatchesRegularExpression('/^v\d+\.\d{2}\.\d{2}$/', SystemVersion::current());
    }

    public function test_authenticated_root_redirects_to_dashboard(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $this->get('/')
            ->assertRedirect(route('dashboard'));
    }
}
