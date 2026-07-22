<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutUsTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_us_requires_authentication(): void
    {
        $this->get(route('about-us'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_about_us_from_system_settings_data(): void
    {
        $this->seed();
        config([
            'app.version' => 'v1.99',
            'app.first_release_date' => '2026-07-07',
        ]);

        SystemSetting::current()->update([
            'company_name' => 'Acme Support Inc.',
            'system_name' => 'Acme Ticket Desk',
        ]);

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $response = $this->get(route('about-us'));

        $response
            ->assertStatus(200)
            ->assertSee('About Us')
            ->assertSee('Acme Support Inc.')
            ->assertSee('Acme Ticket Desk')
            ->assertSee('Xceler8 Technologies Inc. is a technology solutions provider dedicated to delivering innovative, reliable, and efficient software solutions that empower businesses to streamline operations and enhance customer satisfaction.')
            ->assertSee('Through continuous innovation and a commitment to excellence, we aim to accelerate digital transformation and create lasting value for our clients and partners.')
            ->assertSee('v1.99')
            ->assertSee('First Release')
            ->assertSee('July 7, 2026')
            ->assertDontSee('Release Day')
            ->assertDontSee('Release Details')
            ->assertDontSee('Released On')
            ->assertSee('Maintained By')
            ->assertSee('<p class="maintainer-value">Xceler8 Technologies Inc.</p>', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('href="'.route('about-us').'"', false);

        $this->assertMatchesRegularExpression('/v1\.99\.\d{2}/', $response->getContent());
    }
}
