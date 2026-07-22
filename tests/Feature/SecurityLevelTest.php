<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\SecurityLevel;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SecurityLevelTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_level_setup_page_and_crud(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('security-levels.index'))
            ->assertOk()
            ->assertSee('Security Level')
            ->assertSee('Level No.')
            ->assertSee('Description')
            ->assertSee('SLA');

        $this->actingAs($user)->post(route('security-levels.store'), [
            'level_no' => 'Level 1',
            'description' => str_repeat('A', 500),
            'sla' => '4 Hours',
        ])->assertRedirect(route('security-levels.index'));

        $this->assertDatabaseHas('security_level', [
            'level_no' => 'Level 1',
            'description' => str_repeat('A', 500),
            'sla' => '4 Hours',
        ]);
    }

    public function test_security_level_has_many_tickets(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['registered_by' => $user->id]);
        $level = SecurityLevel::create(['level_no' => 'Level 2', 'description' => 'High', 'sla' => '8 Hours']);
        $ticket = Ticket::create([
            'client_id' => $client->id, 'company_name' => $client->company_name,
            'contact_person' => $client->contact_person, 'email_address' => $client->email_address,
            'contact_number' => '+63 9000000000', 'issue_encountered' => 'Test issue',
            'scenario' => 'Test scenario', 'expected_result' => 'Expected result',
            'full_name' => 'Test User', 'contact_email' => 'test@example.com',
            'contact_phone' => '123', 'status' => 'new', 'created_by' => $user->id,
            'security_level_id' => $level->id,
        ]);

        $this->assertTrue(Schema::hasColumn('tickets', 'security_level_id'));
        $this->assertTrue($level->tickets->contains($ticket));
        $this->assertTrue($ticket->securityLevel->is($level));
    }
}
