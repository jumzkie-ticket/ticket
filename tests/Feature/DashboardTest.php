<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\TicketResolution;
use App\Models\User;
use App\Support\SystemVersion;
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

        $response = $this->get(route('dashboard'));

        $response
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

        $metricLabels = collect($response->viewData('metricCards'))->pluck('label');

        foreach ([
            'Total Tickets',
            'New',
            'Open',
            'In Progress',
            'Resolved',
            'Closed',
            'Hold',
            'No Helpdesk',
            'No Maintenance',
            'Pending From Client',
            'Pending From Dev',
            'Pending From Sales',
            'Pending From XTI',
        ] as $label) {
            $this->assertContains($label, $metricLabels);
        }

        $metricCards = collect($response->viewData('metricCards'));
        $this->assertSame(
            (int) $metricCards->firstWhere('label', 'Total Tickets')['value'],
            $metricCards->where('label', '!=', 'Total Tickets')->sum('value'),
        );

        $this->assertMatchesRegularExpression('/^v\d+\.\d{2}\.\d{2}$/', SystemVersion::current());
    }

    public function test_authenticated_root_redirects_to_dashboard(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $this->get('/')
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_analytics_charts_use_actual_ticket_data(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@xceler8.test')->firstOrFail();
        $client = Client::factory()->create(['registered_by' => $admin->id]);

        $openTicket = $this->createTicket($client, $admin, 'Open', 'SAP Business One, Xceler8 Addon', now()->subDays(2));
        $this->createTicket($client, $admin, 'New', 'SAP Business One', now());
        $this->createTicket($client, $admin, 'Closed', 'Other Product', now()->subDays(20));
        TicketResolution::create([
            'ticket_id' => $openTicket->id,
            'date' => now()->subDay(),
            'description' => 'Initial response provided.',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $trend = collect($response->viewData('trend'));
        $statuses = collect($response->viewData('statusBreakdown'))->keyBy('label');
        $products = collect($response->viewData('productBreakdown'))->keyBy('label');
        $quickSummary = collect($response->viewData('quickSummary'))->pluck('value', 'label');

        $this->assertSame(2, $trend->sum('value'));
        $this->assertSame(1, $statuses['Open']['value']);
        $this->assertSame(1, $statuses['New']['value']);
        $this->assertFalse($statuses->has('Closed'));
        $this->assertSame(2, $products['SAP Business One']['value']);
        $this->assertSame(1, $products['Xceler8 Addon']['value']);
        $this->assertSame(1, $products['Other Product']['value']);
        $this->assertSame('3', $quickSummary['Total Tickets (All Time)']);
        $this->assertSame('0', $quickSummary['Resolved (All Time)']);
        $this->assertSame('1', $quickSummary['Closed (All Time)']);
        $this->assertSame('24.0 hrs', $quickSummary['Average Response Time']);
        $this->assertSame('N/A', $quickSummary['Customer Satisfaction']);
        $this->assertSame(number_format(User::count()), $quickSummary['Registered Users']);
    }

    public function test_customer_receives_customer_landing_page(): void
    {
        $this->seed();

        $customer = User::where('email', 'client@xceler8.test')->firstOrFail();

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Customer Support Portal')
            ->assertSee('How can we help you today?')
            ->assertSee('Find a ticket')
            ->assertSee('Create New Ticket')
            ->assertSee('View My Tickets')
            ->assertSee('Customer Ticket Status Summary')
            ->assertSee('Total Tickets')
            ->assertSee('Pending From Client')
            ->assertSee('Open')
            ->assertSee('New')
            ->assertDontSee('class="customer-stats"', false)
            ->assertDontSee('My ticket summary')
            ->assertDontSee('Recent Tickets')
            ->assertDontSee('View all tickets')
            ->assertDontSee('Analytics Overview')
            ->assertDontSee('Client Registration')
            ->assertDontSee('System Settings');
    }

    private function createTicket(
        Client $client,
        User $user,
        string $status,
        string $products,
        \DateTimeInterface $createdAt,
    ): Ticket {
        $ticket = Ticket::create([
            'client_id' => $client->id,
            'company_name' => $client->company_name,
            'contact_person' => $client->contact_person,
            'email_address' => $client->email_address,
            'contact_number' => $client->contact_number,
            'product_related' => $products,
            'issue_encountered' => 'Dashboard analytics test ticket',
            'scenario' => 'Test scenario',
            'expected_result' => 'Test result',
            'full_name' => $client->contact_person,
            'contact_email' => $client->email_address,
            'contact_phone' => $client->contact_number,
            'status' => $status,
            'created_by' => $user->id,
        ]);

        Ticket::query()->whereKey($ticket)->update([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        return $ticket->refresh();
    }
}
