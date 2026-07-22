<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        foreach ([
            'company_name',
            'contact_person',
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
            'industry_type',
            'sap_product_used',
            'software_version_patch',
            'company_size',
            'preferred_support_method',
            'additional_notes',
            'accepted_terms',
            'accepted_at',
            'status',
            'registered_by',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('clients', $column), "Missing clients.{$column}");
        }
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
            ->assertSee('Address Details')
            ->assertSee('Business Details')
            ->assertSee('Support Preferences')
            ->assertSee('Terms and Conditions')
            ->assertSee('Registration Overview')
            ->assertSee('Industry Distribution')
            ->assertSee('https://psgc.gitlab.io/api')
            ->assertSee('href="'.route('clients.registration').'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_authenticated_user_can_register_a_client(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@xceler8.test')->firstOrFail();
        $this->actingAs($user);

        $response = $this->post(route('clients.store'), $this->validPayload());

        $response
            ->assertRedirect(route('clients.registration'))
            ->assertSessionHas('status', 'Client registered successfully.');

        $this->assertDatabaseHas('clients', [
            'company_name' => 'Northwind Distribution Inc.',
            'contact_person' => 'Maria Santos',
            'email_address' => 'maria.santos@northwind.test',
            'contact_country_code' => '+63',
            'contact_number' => '917 123 4567',
            'region_code' => '010000000',
            'region_name' => 'Region 1 - Ilocos Region',
            'province_code' => '012800000',
            'province_name' => 'Ilocos Norte',
            'city_municipality_code' => '012805000',
            'city_municipality_name' => 'City of Batac',
            'barangay_code' => '012805001',
            'barangay_name' => 'Aglipay (Pob.)',
            'building_details' => 'Unit 8, Solar Century Tower',
            'industry_type' => 'distribution-retail',
            'sap_product_used' => 'sap-business-one',
            'software_version_patch' => '10.0 FP 2405',
            'company_size' => '26-50',
            'preferred_support_method' => 'support-portal',
            'additional_notes' => 'Primary support contact prefers ticket updates.',
            'accepted_terms' => true,
            'status' => 'active',
            'registered_by' => $user->id,
        ]);

        $client = Client::query()->where('email_address', 'maria.santos@northwind.test')->firstOrFail();

        $this->assertTrue($client->accepted_terms);
        $this->assertNotNull($client->accepted_at);
    }

    /**
     * @return array<string, string>
     */
    private function validPayload(): array
    {
        return [
            'company_name' => 'Northwind Distribution Inc.',
            'contact_person' => 'Maria Santos',
            'email_address' => 'maria.santos@northwind.test',
            'contact_country_code' => '+63',
            'contact_number' => '917 123 4567',
            'region_code' => '010000000',
            'region_name' => 'Region 1 - Ilocos Region',
            'province_code' => '012800000',
            'province_name' => 'Ilocos Norte',
            'city_municipality_code' => '012805000',
            'city_municipality_name' => 'City of Batac',
            'barangay_code' => '012805001',
            'barangay_name' => 'Aglipay (Pob.)',
            'building_details' => 'Unit 8, Solar Century Tower',
            'industry_type' => 'distribution-retail',
            'sap_product_used' => 'sap-business-one',
            'software_version_patch' => '10.0 FP 2405',
            'company_size' => '26-50',
            'preferred_support_method' => 'support-portal',
            'additional_notes' => 'Primary support contact prefers ticket updates.',
            'accepted_terms' => '1',
        ];
    }
}
