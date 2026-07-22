<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Role;
use App\Models\SapProduct;
use App\Models\SecurityLevel;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('tickets.create'))->assertRedirect(route('login'));
    }

    public function test_create_ticket_page_contains_client_lookup_data(): void
    {
        $user = User::factory()->create();
        $product = SapProduct::query()->where('sap_product', 'SAP Business One')->firstOrFail();
        $client = Client::factory()->create([
            'registered_by' => $user->id,
            'company_name' => 'Acme Corporation',
            'db_version' => 'Microsoft SQL Server 2022',
        ]);
        $user->clientUser()->create(['client_id' => $client->id]);
        $client->sapProducts()->sync([$product->id]);

        $this->actingAs($user)
            ->get(route('tickets.create'))
            ->assertOk()
            ->assertSee('Create Ticket')
            ->assertSee($client->company_name)
            ->assertSee('Microsoft SQL Server 2022');
    }

    public function test_authenticated_user_can_create_a_ticket(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $product = SapProduct::query()->where('sap_product', 'SAP Business One')->firstOrFail();
        $client = Client::factory()->create([
            'registered_by' => $user->id,
            'db_version' => 'HANA 2.0',
        ]);
        $user->clientUser()->create(['client_id' => $client->id]);
        $client->sapProducts()->sync([$product->id]);

        $this->actingAs($user)->post(route('tickets.store'), [
            'client_id' => $client->id,
            'issue_encountered' => 'Unable to post an invoice',
            'scenario' => 'Open A/R Invoice and select Add.',
            'expected_result' => 'The invoice should be saved.',
            'full_name' => 'Juan Dela Cruz',
            'contact_email' => 'juan@example.com',
            'contact_phone' => 'Office ext. 42',
            'other_information' => 'Happens for all users.',
            'attachment' => UploadedFile::fake()->create('invoice-error.png', 100, 'image/png'),
        ])->assertRedirect(route('tickets.create'));

        $this->assertDatabaseHas('tickets', [
            'client_id' => $client->id,
            'company_name' => $client->company_name,
            'product_related' => 'SAP Business One',
            'database_version' => 'HANA 2.0',
            'issue_encountered' => 'Unable to post an invoice',
            'created_by' => $user->id,
            'contact_phone' => 'Office ext. 42',
            'attachment_original_name' => 'invoice-error.png',
            'status' => 'New',
            'ticket_status_id' => 17,
        ]);

        $this->assertSame('New', Ticket::query()->latest('id')->firstOrFail()->ticketStatus?->status);

        $attachmentPath = Ticket::query()->latest('id')->value('attachment');
        Storage::disk('local')->assertExists($attachmentPath);

        $ticket = Ticket::query()->latest('id')->firstOrFail();
        $this->actingAs($user)
            ->get(route('tickets.attachment', $ticket))
            ->assertOk()
            ->assertHeader('content-disposition', 'inline; filename="invoice-error.png"');

        $this->actingAs($user)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee('data-ticket-modal-open="attachment-modal-'.$ticket->id.'"', false)
            ->assertSee('Attachment: invoice-error.png');
    }

    public function test_authenticated_user_can_view_their_client_tickets_with_analytics(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $client = Client::factory()->create([
            'company_name' => 'Canonical Client Company',
            'registered_by' => $user->id,
        ]);
        $otherClient = Client::factory()->create([
            'company_name' => 'Other Client Company',
            'registered_by' => $otherUser->id,
        ]);
        $user->clientUser()->create(['client_id' => $client->id]);

        $ticket = Ticket::create($this->ticketPayload($client, $otherUser, [
            'company_name' => 'Outdated Ticket Company',
            'issue_encountered' => 'Invoice posting error',
            'status' => 'in-progress',
        ]));
        Ticket::create($this->ticketPayload($otherClient, $otherUser, [
            'issue_encountered' => 'Hidden ticket',
            'status' => 'closed',
        ]));

        $this->actingAs($user)
            ->get(route('tickets.index', ['view' => $ticket->id]))
            ->assertOk()
            ->assertSee('My Ticket')
            ->assertSee('Ticket Status Guide')
            ->assertSee('class="ticket-card ticket-metric"', false)
            ->assertSee('Submitted Tickets')
            ->assertSee('Company Name')
            ->assertSee('Outdated Ticket Company')
            ->assertDontSee('Canonical Client Company')
            ->assertSee('View Ticket Details')
            ->assertSee('data-ticket-modal-open="ticket-modal-'.$ticket->id.'"', false)
            ->assertSee('Invoice posting error')
            ->assertSee('In Progress')
            ->assertSee('TKT-'.str_pad((string) $ticket->id, 5, '0', STR_PAD_LEFT))
            ->assertDontSee('Hidden ticket')
            ->assertSee('href="'.route('tickets.index').'"', false);
    }

    public function test_admin_can_view_tickets_from_all_clients(): void
    {
        $this->seed();

        $admin = User::factory()->create();
        $admin->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);
        $ticketOwner = User::factory()->create();
        $firstClient = Client::factory()->create(['registered_by' => $ticketOwner->id]);
        $secondClient = Client::factory()->create(['registered_by' => $ticketOwner->id]);

        Ticket::create($this->ticketPayload($firstClient, $ticketOwner, [
            'issue_encountered' => 'First client ticket',
        ]));
        Ticket::create($this->ticketPayload($secondClient, $ticketOwner, [
            'issue_encountered' => 'Second client ticket',
        ]));

        $this->actingAs($admin)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee('First client ticket')
            ->assertSee('Second client ticket')
            ->assertSee('Showing 1 to 2 of 2 tickets');
    }

    public function test_ticket_status_summary_is_case_insensitive_and_equals_total_tickets(): void
    {
        $this->seed();

        $admin = User::factory()->create();
        $admin->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);
        $ticketOwner = User::factory()->create();
        $client = Client::factory()->create(['registered_by' => $ticketOwner->id]);

        Ticket::create($this->ticketPayload($client, $ticketOwner, ['status' => 'Open']));
        Ticket::create($this->ticketPayload($client, $ticketOwner, ['status' => 'open']));
        Ticket::create($this->ticketPayload($client, $ticketOwner, ['status' => 'New']));

        $response = $this->actingAs($admin)->get(route('tickets.index'));
        $statusGuide = collect($response->viewData('statusGuide'));
        $statusCards = $statusGuide->reject(fn (array $card): bool => $card['label'] === 'Total Tickets');

        $response->assertOk();
        $this->assertSame(3, $statusGuide->firstWhere('label', 'Total Tickets')['value']);
        $this->assertSame(2, $statusGuide->firstWhere('label', 'Open')['value']);
        $this->assertSame(1, $statusGuide->firstWhere('label', 'New')['value']);
        $this->assertSame(3, $statusCards->sum('value'));
    }

    public function test_ticket_owner_can_add_a_resolution_step(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['registered_by' => $user->id]);
        $user->clientUser()->create(['client_id' => $client->id]);
        $ticket = Ticket::create($this->ticketPayload($client, $user));

        $this->actingAs($user)
            ->post(route('tickets.resolutions.store', $ticket), [
                'date' => '2026-07-11 10:15:00',
                'description' => 'Verified username and password.',
            ])
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('ticket_resolutions', [
            'ticket_id' => $ticket->id,
            'date' => '2026-07-11 10:15:00',
            'description' => 'Verified username and password.',
        ]);

        $this->actingAs($user)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee('Resolution / Steps')
            ->assertSee('>View<', false)
            ->assertSee('>Edit<', false)
            ->assertSee('Verified username and password.');

        $resolution = $ticket->resolutions()->firstOrFail();
        $this->actingAs($user)
            ->put(route('tickets.resolutions.update', [$ticket, $resolution]), [
                'date' => '2026-07-11 10:30:00',
                'description' => 'Cleared the browser cache and cookies.',
            ])
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('ticket_resolutions', [
            'id' => $resolution->id,
            'ticket_id' => $ticket->id,
            'description' => 'Cleared the browser cache and cookies.',
        ]);
    }

    public function test_ticket_owner_can_update_security_level_and_status(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['registered_by' => $user->id]);
        $user->clientUser()->create(['client_id' => $client->id]);
        $ticket = Ticket::create($this->ticketPayload($client, $user));
        $securityLevel = SecurityLevel::create(['level_no' => 'Level 1', 'description' => 'Critical issue', 'sla' => '4 Hours']);
        $ticketStatus = TicketStatus::where('status', 'in-progress')->firstOrFail();

        $this->actingAs($user)
            ->put(route('tickets.classification.update', $ticket), [
                'security_level_id' => $securityLevel->id,
                'ticket_status_id' => $ticketStatus->id,
            ])
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'security_level_id' => $securityLevel->id,
            'ticket_status_id' => $ticketStatus->id,
            'status' => 'in-progress',
        ]);

        $this->actingAs($user)->get(route('tickets.index'))
            ->assertOk()
            ->assertSee('Security Level')
            ->assertSee('Level 1')
            ->assertSee('Save Ticket Details');
    }

    public function test_customer_ticket_details_are_read_only(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $customerRole = Role::where('slug', 'customer')->firstOrFail();
        $user->roles()->sync([$customerRole->id]);

        $client = Client::factory()->create(['registered_by' => $user->id]);
        $user->clientUser()->create(['client_id' => $client->id]);
        $ticket = Ticket::create($this->ticketPayload($client, $user));
        $resolution = $ticket->resolutions()->create([
            'date' => '2026-07-13 09:00:00',
            'description' => 'Consultant resolution details.',
        ]);

        $this->actingAs($user)
            ->get(route('tickets.index', ['view' => $ticket->id]))
            ->assertOk()
            ->assertSee('name="ticket_status_id" required disabled', false)
            ->assertSee('name="security_level_id" disabled', false)
            ->assertSee('>View<', false)
            ->assertDontSee('Save Ticket Details')
            ->assertDontSee('>Edit<', false)
            ->assertDontSee('Update Resolution / Steps');

        $this->actingAs($user)
            ->put(route('tickets.classification.update', $ticket), [
                'ticket_status_id' => TicketStatus::where('status', 'open')->firstOrFail()->id,
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->put(route('tickets.resolutions.update', [$ticket, $resolution]), [
                'date' => '2026-07-13 10:00:00',
                'description' => 'Unauthorized edit.',
            ])
            ->assertForbidden();
    }

    /** @return array<string, mixed> */
    private function ticketPayload(Client $client, User $user, array $overrides = []): array
    {
        return array_merge([
            'client_id' => $client->id,
            'company_name' => $client->company_name,
            'contact_person' => $client->contact_person,
            'email_address' => $client->email_address,
            'contact_number' => '+63 '.$client->contact_number,
            'product_related' => 'SAP Business One',
            'software_version' => '10.00.130',
            'patch_or_fp' => 'FP 2008',
            'database_version' => 'MSSQL 2019',
            'issue_encountered' => 'Support request',
            'scenario' => 'Steps to reproduce.',
            'expected_result' => 'Expected behavior.',
            'full_name' => 'Juan Dela Cruz',
            'contact_email' => 'juan@example.com',
            'contact_phone' => '0916-123-4567',
            'status' => 'new',
            'created_by' => $user->id,
        ], $overrides);
    }
}
