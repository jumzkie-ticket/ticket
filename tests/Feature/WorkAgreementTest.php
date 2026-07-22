<?php

namespace Tests\Feature;

use App\Models\AssignFc;
use App\Models\Client;
use App\Models\User;
use App\Models\WorkAgreement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WorkAgreementTest extends TestCase
{
    use RefreshDatabase;

    public function test_work_agreement_requires_authentication(): void
    {
        $this->get(route('work-agreements.index'))
            ->assertRedirect(route('login'));
    }

    public function test_work_agreement_ui_contains_requested_fields_and_navigation(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $response = $this->get(route('work-agreements.index'));

        $response
            ->assertOk()
            ->assertSeeInOrder(['Industry / Business Type', 'Work Agreement'])
            ->assertSee('Work Agreement Form')
            ->assertSee('name="agreement_date"', false)
            ->assertSee('name="assign_fc_id"', false)
            ->assertSee('id="position"', false)
            ->assertSee('name="client_id"', false)
            ->assertSee('name="address"', false)
            ->assertSee('name="billable"', false)
            ->assertSee('name="non_billable"', false)
            ->assertSee('name="scope"', false)
            ->assertSee('name="objective"', false)
            ->assertSee('name="current_issue"', false)
            ->assertSee('name="proposed_solutions"', false)
            ->assertSee('name="estimated_man_days"', false)
            ->assertSee('name="project_manager_assign_fc_id"', false)
            ->assertSee('name="consultant_assign_fc_id"', false)
            ->assertSee('id="project_manager_designation"', false)
            ->assertSee('id="consultant_designation"', false)
            ->assertSee('name="accepted_by"', false)
            ->assertSee('name="accepted_by_designation"', false);

        foreach ([
            'work_agreement_no',
            'agreement_date',
            'assign_fc_id',
            'client_id',
            'address',
            'billable',
            'non_billable',
            'scope',
            'objective',
            'current_issue',
            'proposed_solutions',
            'note',
            'estimated_man_days',
            'project_manager',
            'project_manager_assign_fc_id',
            'consultant',
            'consultant_assign_fc_id',
            'accepted_by',
            'accepted_by_designation',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('work_agreements', $column), "Missing work_agreements.{$column}");
        }

        $this->assertFalse(Schema::hasColumn('work_agreements', 'project_manager_designation'));
        $this->assertFalse(Schema::hasColumn('work_agreements', 'consultant_designation'));
    }

    public function test_admin_can_create_update_and_delete_work_agreement(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@xceler8.test')->firstOrFail();
        $this->actingAs($admin);
        $client = Client::factory()->create([
            'company_name' => 'Acme Solutions',
            'building_details' => '8F Solar Century Tower',
            'barangay_name' => 'Bel-Air',
            'city_municipality_name' => 'Makati City',
            'province_name' => 'Metro Manila',
            'region_name' => 'NCR',
            'registered_by' => $admin->id,
        ]);
        $assignFc = AssignFc::create([
            'assign_fc' => 'Maria Santos',
            'designation' => 'Senior Functional Consultant',
        ]);

        $this->post(route('work-agreements.store'), $this->payload($client, $assignFc))
            ->assertRedirect(route('work-agreements.index'));

        $agreement = WorkAgreement::query()->firstOrFail();
        $this->assertSame('WA-0001', $agreement->work_agreement_no);
        $this->assertSame('8F Solar Century Tower, Bel-Air, Makati City, Metro Manila, NCR', $agreement->address);
        $this->assertTrue($agreement->billable);
        $this->assertFalse($agreement->non_billable);
        $this->assertTrue($agreement->client->is($client));
        $this->assertTrue($agreement->assignFc->is($assignFc));
        $this->assertTrue($agreement->projectManager->is($assignFc));
        $this->assertTrue($agreement->consultantFc->is($assignFc));
        $this->assertSame('Senior Functional Consultant', $agreement->projectManager->designation);
        $this->assertSame('Senior Functional Consultant', $agreement->consultantFc->designation);
        $this->assertSame('IT Manager', $agreement->accepted_by_designation);

        $this->get(route('work-agreements.index', ['edit' => $agreement->id]))
            ->assertOk()
            ->assertSee('Edit Work Agreement')
            ->assertSee('WA-0001')
            ->assertSee('Senior Functional Consultant');

        $this->put(route('work-agreements.update', $agreement), $this->payload($client, $assignFc, [
            'billable' => 0,
            'non_billable' => 1,
            'estimated_man_days' => 4.5,
            'note' => 'Updated agreement note.',
        ]))->assertRedirect(route('work-agreements.index', ['edit' => $agreement->id]));

        $this->assertDatabaseHas('work_agreements', [
            'id' => $agreement->id,
            'work_agreement_no' => 'WA-0001',
            'billable' => false,
            'non_billable' => true,
            'estimated_man_days' => 4.5,
            'note' => 'Updated agreement note.',
        ]);

        $this->delete(route('work-agreements.destroy', $agreement))
            ->assertRedirect(route('work-agreements.index'));
        $this->assertDatabaseMissing('work_agreements', ['id' => $agreement->id]);
    }

    public function test_billing_type_must_be_mutually_exclusive(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@xceler8.test')->firstOrFail();
        $this->actingAs($admin);
        $client = Client::factory()->create(['registered_by' => $admin->id]);
        $assignFc = AssignFc::create(['assign_fc' => 'FC-100', 'designation' => 'Consultant']);

        $this->post(route('work-agreements.store'), $this->payload($client, $assignFc, [
            'billable' => 1,
            'non_billable' => 1,
        ]))->assertSessionHasErrors('billable');

        $this->assertDatabaseCount('work_agreements', 0);
    }

    /** @return array<string, mixed> */
    private function payload(Client $client, AssignFc $assignFc, array $overrides = []): array
    {
        return array_merge([
            'agreement_date' => '2026-07-14',
            'assign_fc_id' => $assignFc->id,
            'client_id' => $client->id,
            'billable' => 1,
            'non_billable' => 0,
            'scope' => 'Review and improve the support workflow.',
            'objective' => 'Resolve the reported operational issue.',
            'current_issue' => 'Approval workflow is not completing.',
            'proposed_solutions' => 'Review configuration and deploy corrections.',
            'note' => 'Coordinate the activity with the client.',
            'estimated_man_days' => 3,
            'project_manager_assign_fc_id' => $assignFc->id,
            'consultant_assign_fc_id' => $assignFc->id,
            'accepted_by' => 'Client Representative',
            'accepted_by_designation' => 'IT Manager',
        ], $overrides);
    }
}
