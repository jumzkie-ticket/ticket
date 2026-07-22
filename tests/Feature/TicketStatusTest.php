<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TicketStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_status_setup_page_and_crud(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('ticket-statuses.index'))
            ->assertOk()->assertSee('Ticket Status Guide')->assertSee('Status');

        $this->actingAs($user)->post(route('ticket-statuses.store'), ['status' => 'Pending Client'])
            ->assertRedirect(route('ticket-statuses.index'));

        $this->assertDatabaseHas('ticket_status', ['status' => 'Pending Client']);

        $this->actingAs($user)->get(route('tickets.index'))
            ->assertOk()
            ->assertSee('Ticket Status Guide')
            ->assertSee('Pending Client')
            ->assertSee('Current status: Pending Client')
            ->assertSee('--ticket-status-light:');
    }

    public function test_ticket_status_is_related_to_tickets(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['registered_by' => $user->id]);
        $status = TicketStatus::whereRaw('LOWER(status) = ?', ['new'])->firstOrFail();
        $ticket = Ticket::create([
            'client_id' => $client->id, 'company_name' => $client->company_name,
            'contact_person' => $client->contact_person, 'email_address' => $client->email_address,
            'contact_number' => '+63 9000000000', 'issue_encountered' => 'Test issue',
            'scenario' => 'Test scenario', 'expected_result' => 'Expected result',
            'full_name' => 'Test User', 'contact_email' => 'test@example.com',
            'contact_phone' => '123', 'status' => 'new', 'ticket_status_id' => $status->id,
            'created_by' => $user->id,
        ]);

        $this->assertTrue(Schema::hasColumn('tickets', 'ticket_status_id'));
        $this->assertTrue($status->tickets->contains($ticket));
        $this->assertTrue($ticket->ticketStatus->is($status));
    }

    public function test_default_ticket_status_guide_contains_all_operational_statuses(): void
    {
        $user = User::factory()->create();

        foreach ([
            'hold',
            'no-helpdesk',
            'no-maintenance',
            'pending-from-client',
            'pending-from-dev',
            'pending-from-sales',
            'pending-from-xti',
        ] as $status) {
            $this->assertDatabaseHas('ticket_status', ['status' => $status]);
        }

        $this->actingAs($user)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertSee('Hold')
            ->assertSee('No Helpdesk')
            ->assertSee('No Maintenance')
            ->assertSee('Pending From Client')
            ->assertSee('Pending From Dev')
            ->assertSee('Pending From Sales')
            ->assertSee('Pending From XTI')
            ->assertSee('Ticket is temporarily on hold')
            ->assertSee('Client has no Helpdesk coverage')
            ->assertSee('Client has no active maintenance')
            ->assertSee('Awaiting information from the client')
            ->assertSee('Awaiting action from Development')
            ->assertSee('Awaiting action from Sales')
            ->assertSee('Awaiting internal action from XTI');
    }
}
