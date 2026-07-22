<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_settings_requires_authentication(): void
    {
        $this->get(route('system-settings'))
            ->assertRedirect(route('login'));
    }

    public function test_system_settings_table_exists_and_seeds_default_row(): void
    {
        $this->seed();

        $this->assertTrue(Schema::hasTable('system_settings'));
        $this->assertDatabaseCount('system_settings', 1);
        $this->assertDatabaseHas('system_settings', [
            'company_name' => 'Xceler8 Technologies Inc.',
            'system_name' => 'XTI Ticket Support System',
        ]);
    }

    public function test_authenticated_user_can_view_system_settings_with_menu_pane(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $response = $this->get(route('system-settings'));

        $response
            ->assertStatus(200)
            ->assertSee('System Settings')
            ->assertSee('Configure system preferences and platform options.')
            ->assertSee('General Settings')
            ->assertSee('Notification Settings')
            ->assertSee('Appearance / Branding')
            ->assertSeeText('Backup & Maintenance')
            ->assertSee('System Information')
            ->assertSee('Support Schedule')
            ->assertSee('Sign Out');

        $this->assertMatchesRegularExpression('/v\d+\.\d{2}\.\d{2}/', $response->getContent());
    }

    public function test_appearance_branding_shows_primary_color_palette_suggestions(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $this->get(route('system-settings'))
            ->assertStatus(200)
            ->assertSee('id="primary_color"', false)
            ->assertSee('id="primary_color_picker"', false)
            ->assertSee('name="theme" value="light"', false)
            ->assertSee('name="theme" value="dark"', false)
            ->assertSee('name="theme" value="system"', false)
            ->assertSee('id="appearance-preview"', false)
            ->assertSee('Preview updates instantly. Save changes to apply the theme across all modules.')
            ->assertSee('.brand-logo-image', false)
            ->assertSee('height: 66px', false)
            ->assertSee('flex-direction: column', false)
            ->assertSee('aria-label="Suggested primary colors"', false)
            ->assertSee('data-color="#2563EB"', false)
            ->assertSee('data-color="#20B96F"', false);
    }

    public function test_authenticated_user_can_save_system_settings(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $this->put(route('system-settings.update'), [
            'company_name' => 'Acme Support Inc.',
            'system_name' => 'Acme Ticket Desk',
            'time_zone' => 'Asia/Singapore',
            'date_format' => 'Y-m-d',
            'email_notifications' => '1',
            'ticket_alerts' => '0',
            'system_announcements' => '1',
            'weekly_reports' => '1',
            'theme' => 'dark',
            'primary_color' => '#123abc',
            'auto_backup' => '0',
            'backup_frequency' => 'weekly',
            'maintenance_mode' => '1',
        ])
            ->assertRedirect(route('system-settings'))
            ->assertSessionHas('status', 'System settings saved successfully.');

        $this->assertDatabaseHas('system_settings', [
            'company_name' => 'Acme Support Inc.',
            'system_name' => 'Acme Ticket Desk',
            'time_zone' => 'Asia/Singapore',
            'date_format' => 'Y-m-d',
            'email_notifications' => 1,
            'ticket_alerts' => 0,
            'system_announcements' => 1,
            'weekly_reports' => 1,
            'theme' => 'dark',
            'primary_color' => '#123ABC',
            'auto_backup' => 0,
            'backup_frequency' => 'weekly',
            'maintenance_mode' => 1,
        ]);

        $this->assertSame(1, SystemSetting::count());

        $this->get(route('system-settings'))
            ->assertStatus(200)
            ->assertSee('value="Acme Support Inc."', false)
            ->assertSee('value="Acme Ticket Desk"', false)
            ->assertSee('value="#123ABC"', false)
            ->assertSee('aria-label="Acme Ticket Desk dashboard"', false)
            ->assertSee('&copy; 2026 Acme Support Inc.', false)
            ->assertSee('System settings saved successfully.', false);
    }

    public function test_authenticated_user_can_upload_system_logo(): void
    {
        Storage::fake('public');
        $this->seed();

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $this->post(route('system-settings.update'), [
            '_method' => 'PUT',
            'company_name' => 'Xceler8 Technologies Inc.',
            'system_name' => 'XTI Ticket Support System',
            'time_zone' => 'America/New_York',
            'date_format' => 'F j, Y',
            'email_notifications' => '1',
            'ticket_alerts' => '1',
            'system_announcements' => '1',
            'weekly_reports' => '0',
            'theme' => 'light',
            'primary_color' => '#2563EB',
            'auto_backup' => '1',
            'backup_frequency' => 'daily',
            'maintenance_mode' => '0',
            'logo' => UploadedFile::fake()->createWithContent(
                'logo.svg',
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80"><rect width="120" height="80" fill="#2563EB"/></svg>',
            ),
        ])
            ->assertRedirect(route('system-settings'))
            ->assertSessionHas('status', 'System settings saved successfully.');

        $settings = SystemSetting::firstOrFail();

        $this->assertNotNull($settings->logo_path);
        $this->assertStringStartsWith('system-logos/', $settings->logo_path);
        Storage::disk('public')->assertExists($settings->logo_path);

        $this->assertDatabaseHas('system_settings', [
            'id' => $settings->id,
            'logo_path' => $settings->logo_path,
        ]);

        $logoUrl = '/storage/'.$settings->logo_path;

        $this->get(route('system-settings'))
            ->assertStatus(200)
            ->assertSee('aria-label="System logo preview"', false)
            ->assertSee('id="logo-preview-image"', false)
            ->assertSee('src="'.$logoUrl.'"', false)
            ->assertDontSee('alt="System logo preview"', false);
    }

    public function test_system_logo_preview_falls_back_when_saved_file_is_missing(): void
    {
        Storage::fake('public');
        $this->seed();

        $settings = SystemSetting::current();
        $settings->update(['logo_path' => 'system-logos/missing-logo.png']);

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $this->get(route('system-settings'))
            ->assertStatus(200)
            ->assertSee('id="logo-preview-placeholder"', false)
            ->assertDontSee('src="/storage/system-logos/missing-logo.png"', false)
            ->assertDontSee('alt="System logo preview"', false);
    }

    public function test_saved_appearance_is_applied_to_shared_modules(): void
    {
        $this->seed();

        SystemSetting::current()->update([
            'theme' => 'dark',
            'primary_color' => '#123ABC',
        ]);

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        foreach ([
            route('dashboard'),
            route('about-us'),
            route('clients.index'),
            route('clients.registration'),
            route('service-order-details.index'),
            route('service-order-details.detail'),
            route('industry-business-types.index'),
            route('sap-products.index'),
            route('account-managers.index'),
            route('assign-fcs.index'),
            route('packages.index'),
            route('product-details'),
            route('contact-support'),
            route('roles.index'),
            route('users.index'),
            route('system-settings'),
        ] as $url) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee('class="app-theme-dark"', false)
                ->assertSee('--blue: #123ABC', false)
                ->assertSee('--blue-rgb: 18, 58, 188', false)
                ->assertSee('--primary: #123ABC', false)
                ->assertSee(':root.app-theme-dark .content-area :is(', false)
                ->assertSee('color: var(--ink) !important', false)
                ->assertSee('background: var(--panel) !important', false)
                ->assertSee('Shared appearance layer', false);
        }
    }
}
