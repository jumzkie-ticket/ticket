<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\AccountManager;
use App\Models\IndustryBusinessType;
use App\Models\SapProduct;
use App\Models\AssignFc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClientRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_registration_requires_authentication(): void
    {
        $this->get(route('clients.registration'))
            ->assertRedirect(route('login'));
    }

    public function test_clients_table_contains_registration_fields(): void
    {
        $this->assertTrue(Schema::hasTable('clients'));
        $this->assertTrue(Schema::hasTable('account_manager'));
        $this->assertTrue(Schema::hasTable('industry_business_types'));
        $this->assertTrue(Schema::hasTable('products'));
        $this->assertTrue(Schema::hasTable('client_sap_product'));
        $this->assertFalse(Schema::hasTable('sap_products'));

        foreach ([
            'company_name',
            'contact_person',
            'designation',
            'email_address',
            'contact_country_code',
            'contact_number',
            'region_code',
            'region_name',
            'province_code',
            'province_name',
            'city_municipality_code',
            'city_municipality_name',
            'barangay_code',
            'barangay_name',
            'building_details',
            'industry_business_type_id',
            'version_number',
            'patch_or_fp',
            'db_version',
            'company_size',
            'account_manager_id',
            'assign_fc_id',
            'preferred_support_method',
            'additional_notes',
            'accepted_terms',
            'accepted_at',
            'status',
            'registered_by',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('clients', $column), "Missing clients.{$column}");
        }

        $this->assertFalse(Schema::hasColumn('clients', 'industry_type'));
        $this->assertFalse(Schema::hasColumn('clients', 'sap_product_used'));
        $this->assertFalse(Schema::hasColumn('clients', 'software_version_patch'));
        $this->assertFalse(Schema::hasColumn('clients', 'account_manager'));
        $this->assertFalse(Schema::hasColumn('clients', 'sap_product_id'));
    }

    public function test_authenticated_user_can_view_client_registration(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $response = $this->get(route('clients.registration'));

        $response
            ->assertStatus(200)
            ->assertSee('Client Registration')
            ->assertSee('Company Information')
            ->assertSee('Designation')
            ->assertSee('Address Details')
            ->assertSee('Business Details')
            ->assertSee('Manufacturing')
            ->assertSee('SAP Business One')
            ->assertSee('Select Products')
            ->assertSee('Version Number format: (10.00.130)')
            ->assertSee('Patch (PL 01) / Package Name (FP 2008)')
            ->assertSee('Support Preferences')
            ->assertSee('Account Manager')
            ->assertSee('Assign FC')
            ->assertSee('Terms and Conditions')
            ->assertSee('Registration Overview')
            ->assertSee('Industry Distribution')
            ->assertSee('Enter contact number')
            ->assertSee('PH +63')
            ->assertSee(route('clients.country-codes'))
            ->assertSee('https://psgc.gitlab.io/api')
            ->assertSee('href="'.route('clients.registration').'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_country_code_dropdown_api_returns_philippines_first(): void
    {
        $user = User::factory()->create();
        Cache::forget('client-registration.country-codes');
        Http::fake([
            'restcountries.com/*' => Http::response([
                ['cca2' => 'US', 'name' => ['common' => 'United States'], 'idd' => ['root' => '+1', 'suffixes' => ['']]],
                ['cca2' => 'PH', 'name' => ['common' => 'Philippines'], 'idd' => ['root' => '+6', 'suffixes' => ['3']]],
            ]),
        ]);

        $this->actingAs($user)
            ->getJson(route('clients.country-codes'))
            ->assertOk()
            ->assertJsonPath('0.iso', 'PH')
            ->assertJsonPath('0.dial_code', '+63')
            ->assertJsonPath('1.iso', 'US');
    }

    public function test_authenticated_user_can_register_a_client(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();
        $this->actingAs($user);
        $accountManager = AccountManager::where('account_manager', 'Juan Dela Cruz')->firstOrFail();
        $industry = IndustryBusinessType::where('industry', 'Distribution / Retail')->firstOrFail();
        $sapProduct = SapProduct::where('sap_product', 'SAP Business One')->firstOrFail();
        $secondProduct = SapProduct::where('sap_product', 'SAP S/4HANA')->firstOrFail();

        // Ensure an Assign FC value exists for the dropdown
        $assignFc = AssignFc::first() ?: AssignFc::create(['assign_fc' => 'FC-001']);

        $payload = $this->validPayload();
        $payload['assign_fc_id'] = $assignFc->id;
        $payload['sap_product_ids'] = [$sapProduct->id, $secondProduct->id];

        $response = $this->post(route('clients.store'), $payload);

        $response
            ->assertRedirect(route('clients.registration'))
            ->assertSessionHas('status', 'Client registered successfully.');

        $this->assertDatabaseHas('clients', [
            'company_name' => 'Northwind Distribution Inc.',
            'contact_person' => 'Maria Santos',
            'designation' => 'IT Manager',
            'email_address' => 'maria.santos@northwind.test',
            'contact_country_code' => '+63',
            'contact_number' => 'Office extension 42',
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
            'assign_fc_id' => $assignFc->id,
            'preferred_support_method' => 'support-portal',
            'additional_notes' => 'Primary support contact prefers ticket updates.',
            'accepted_terms' => true,
            'status' => 'active',
            'registered_by' => $user->id,
        ]);

        $this->assertDatabaseHas('client_sap_product', [
            'client_id' => Client::where('email_address', 'maria.santos@northwind.test')->value('id'),
            'sap_product_id' => $sapProduct->id,
        ]);
        $this->assertDatabaseHas('client_sap_product', [
            'client_id' => Client::where('email_address', 'maria.santos@northwind.test')->value('id'),
            'sap_product_id' => $secondProduct->id,
        ]);

        $client = Client::query()->where('email_address', 'maria.santos@northwind.test')->firstOrFail();

            $this->assertTrue($client->accepted_terms);
            $this->assertNotNull($client->accepted_at);
            $this->assertSame('FC-001', $client->assignFc->assign_fc);
            $this->assertSame('Distribution / Retail', $client->industryBusinessType->industry);
            $this->assertCount(2, $client->sapProducts);
            $this->assertSame('Juan Dela Cruz', $client->accountManager->account_manager);
    }

    public function test_client_contact_number_accepts_plain_text(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'admin@xceler8.test')->firstOrFail());

        $payload = $this->validPayload();
        $payload['contact_country_code'] = '+1';
        $payload['contact_number'] = 'Office extension 42';

        $this->post(route('clients.store'), $payload)
            ->assertRedirect(route('clients.registration'))
            ->assertSessionDoesntHaveErrors('contact_number');

        $this->assertDatabaseHas('clients', [
            'contact_country_code' => '+1',
            'contact_number' => 'Office extension 42',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        $industry = IndustryBusinessType::where('industry', 'Distribution / Retail')->firstOrFail();
        $sapProduct = SapProduct::where('sap_product', 'SAP Business One')->firstOrFail();
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
            'sap_product_ids' => [$sapProduct->id],
            'version_number' => '10.00.130',
            'patch_or_fp' => 'FP 2008',
            'db_version' => 'MSSQL 2019',
            'company_size' => '26-50',
            'account_manager_id' => $accountManager->id,
            'assign_fc_id' => null,
            'preferred_support_method' => 'support-portal',
            'additional_notes' => 'Primary support contact prefers ticket updates.',
            'accepted_terms' => '1',
        ];
    }
}
