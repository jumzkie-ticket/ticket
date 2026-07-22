<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\AccountManager;
use App\Models\IndustryBusinessType;
use App\Models\SapProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_clients_list_requires_authentication(): void
    {
        $this->get(route('clients.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_empty_clients_list_ui(): void
    {
        $this->seed();
        $this->signIn();

        $this->get(route('clients.index'))
            ->assertStatus(200)
            ->assertSee('Clients')
            ->assertSee('Total Clients')
            ->assertSee('Active Clients')
            ->assertSee('New This Month')
            ->assertSee('Industries')
            ->assertSee('Client List')
            ->assertSee('Register New Client')
            ->assertSee('No registered clients yet')
            ->assertSee('href="'.route('clients.index').'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_authenticated_user_can_view_clients_with_analytics_and_actions(): void
    {
        $this->seed();
        $this->signIn();

        $client = Client::create($this->validClientPayload());
        $client->sapProducts()->sync([SapProduct::where('sap_product', 'SAP Business One')->value('id')]);

        $this->get(route('clients.index'))
            ->assertStatus(200)
            ->assertSee('Northwind Distribution Inc.')
            ->assertSee('Maria Santos')
            ->assertSee('IT Manager')
            ->assertSee('Distribution / Retail')
            ->assertSee('SAP Business One')
            ->assertSee('Juan Dela Cruz')
            ->assertSee('Active')
            ->assertSee('aria-label="View Northwind Distribution Inc."', false)
            ->assertSee('aria-label="Edit Northwind Distribution Inc."', false)
            ->assertSee('aria-label="Delete Northwind Distribution Inc."', false);
    }

    public function test_authenticated_user_can_view_edit_and_delete_client(): void
    {
        $this->seed();
        $this->signIn();

        $client = Client::create($this->validClientPayload());
        $client->sapProducts()->sync([SapProduct::where('sap_product', 'SAP Business One')->value('id')]);
        $accountManager = AccountManager::where('account_manager', 'Paolo Cruz')->firstOrFail();
        $industry = IndustryBusinessType::where('industry', 'Manufacturing')->firstOrFail();
        $product = SapProduct::where('sap_product', 'SAP S/4HANA')->firstOrFail();
        $secondProduct = SapProduct::where('sap_product', 'SAP Business One')->firstOrFail();

        $this->get(route('clients.index', ['view' => $client->id]))
            ->assertStatus(200)
            ->assertSee('View Client')
            ->assertSee('Northwind Distribution Inc.')
            ->assertSee('Unit 8, Solar Century Tower, Aglipay (Pob.), City of Batac, Ilocos Norte, Region 1 - Ilocos Region')
            ->assertSee('Database Version')
            ->assertSee('MSSQL 2019')
            ->assertSee('Support Portal / Ticket');

        $this->get(route('clients.index', ['edit' => $client->id]))
            ->assertStatus(200)
            ->assertSee('Edit Client')
            ->assertSee('Northwind Distribution Inc.')
            ->assertSee('Version Number format: (10.00.130)')
            ->assertSee('Select Products')
            ->assertSee('Database Version (MSSQL 2019)');

        $this->put(route('clients.update', $client), [
            'company_name' => 'Northwind Retail Group',
            'contact_person' => 'Ana Reyes',
            'designation' => 'Operations Lead',
            'email_address' => 'ana.reyes@northwind.test',
            'contact_country_code' => '+63',
            'contact_number' => '918-222-3333',
            'industry_business_type_id' => $industry->id,
            'sap_product_ids' => [$product->id, $secondProduct->id],
            'version_number' => '10.00.140',
            'patch_or_fp' => 'PL 02',
            'db_version' => 'MSSQL 2022',
            'company_size' => '51-100',
            'account_manager_id' => $accountManager->id,
            'preferred_support_method' => 'email',
            'status' => 'inactive',
        ])->assertRedirect(route('clients.index', ['view' => $client->id]));

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'company_name' => 'Northwind Retail Group',
            'contact_person' => 'Ana Reyes',
            'designation' => 'Operations Lead',
            'email_address' => 'ana.reyes@northwind.test',
            'contact_number' => '918-222-3333',
            'industry_business_type_id' => $industry->id,
            'db_version' => 'MSSQL 2022',
            'account_manager_id' => $accountManager->id,
            'preferred_support_method' => 'email',
            'status' => 'inactive',
        ]);
        $this->assertDatabaseHas('client_sap_product', ['client_id' => $client->id, 'sap_product_id' => $product->id]);
        $this->assertDatabaseHas('client_sap_product', ['client_id' => $client->id, 'sap_product_id' => $secondProduct->id]);

        $this->delete(route('clients.destroy', $client))
            ->assertRedirect(route('clients.index'));

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validClientPayload(): array
    {
        $industry = IndustryBusinessType::where('industry', 'Distribution / Retail')->firstOrFail();
        $product = SapProduct::where('sap_product', 'SAP Business One')->firstOrFail();
        $accountManager = AccountManager::where('account_manager', 'Juan Dela Cruz')->firstOrFail();

        return [
            'company_name' => 'Northwind Distribution Inc.',
            'contact_person' => 'Maria Santos',
            'designation' => 'IT Manager',
            'email_address' => 'maria.santos@northwind.test',
            'contact_country_code' => '+63',
            'contact_number' => '917-123-4567',
            'region_code' => '010000000',
            'region_name' => 'Region 1 - Ilocos Region',
            'province_code' => '012800000',
            'province_name' => 'Ilocos Norte',
            'city_municipality_code' => '012805000',
            'city_municipality_name' => 'City of Batac',
            'barangay_code' => '012805001',
            'barangay_name' => 'Aglipay (Pob.)',
            'building_details' => 'Unit 8, Solar Century Tower',
            'industry_business_type_id' => $industry->id,
            'version_number' => '10.00.130',
            'patch_or_fp' => 'FP 2008',
            'db_version' => 'MSSQL 2019',
            'company_size' => '26-50',
            'account_manager_id' => $accountManager->id,
            'preferred_support_method' => 'support-portal',
            'additional_notes' => 'Primary support contact prefers ticket updates.',
            'accepted_terms' => true,
            'accepted_at' => now(),
            'status' => 'active',
        ];
    }

    private function signIn(): User
    {
        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();

        $this->actingAs($user);

        return $user;
    }
}
