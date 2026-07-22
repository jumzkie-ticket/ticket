<?php

namespace Tests\Feature;

use App\Models\ContactSupportRequest;
use App\Models\SupportContactInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContactSupportTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_support_requires_authentication(): void
    {
        $this->get(route('contact-support'))
            ->assertRedirect(route('login'));
    }

    public function test_contact_support_tables_and_default_information_are_available(): void
    {
        $this->seed();

        $this->assertTrue(Schema::hasTable('support_contact_infos'));
        $this->assertTrue(Schema::hasTable('contact_support_requests'));

        $this->assertDatabaseHas('support_contact_infos', SupportContactInfo::defaults());
    }

    public function test_authenticated_user_can_view_contact_support(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $response = $this->get(route('contact-support'));

        $response
            ->assertStatus(200)
            ->assertSee('Contact Support')
            ->assertSee('Send Us a Message')
            ->assertSee('Support Information')
            ->assertSee('sap-support@xceler8inc.com')
            ->assertSee('(02) 8893 8888')
            ->assertSee('8F Solar Century Tower 100 Tordesillas St. Cor H.V. Dela Costa St. Salcedo Village, Makati City, Metro Manila, Philippines')
            ->assertSee('8:00 AM - 6:00 PM (GMT+8)')
            ->assertSee('Support Channels')
            ->assertSee('Need Quick Help?')
            ->assertSee('href="'.route('contact-support').'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_authenticated_user_can_submit_contact_support_request(): void
    {
        Storage::fake('public');

        $this->seed();
        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $response = $this->post(route('contact-support.store'), [
            'full_name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'subject' => 'Cannot access dashboard',
            'priority' => 'high',
            'category' => 'technical-support',
            'message' => 'The dashboard stays blank after I sign in.',
            'attachment' => UploadedFile::fake()->create('error-log.txt', 1, 'text/plain'),
        ]);

        $response
            ->assertRedirect(route('contact-support'))
            ->assertSessionHas('status', 'Your support request has been submitted.');

        $this->assertDatabaseHas('contact_support_requests', [
            'full_name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'subject' => 'Cannot access dashboard',
            'priority' => 'high',
            'category' => 'technical-support',
            'message' => 'The dashboard stays blank after I sign in.',
            'status' => 'new',
        ]);

        $request = ContactSupportRequest::query()->firstOrFail();

        $this->assertNotNull($request->attachment_path);
        Storage::disk('public')->assertExists($request->attachment_path);
    }
}
