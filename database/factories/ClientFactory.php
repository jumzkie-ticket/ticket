<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'designation' => 'Manager',
            'email_address' => fake()->safeEmail(),
            'contact_country_code' => '+63',
            'contact_number' => '123-456-7890',
            'region_code' => '01',
            'region_name' => 'National Capital Region',
            'province_code' => '0133',
            'province_name' => 'Metro Manila',
            'city_municipality_code' => '0133',
            'city_municipality_name' => 'Makati',
            'barangay_code' => '0001',
            'barangay_name' => 'Barangay 1',
            'building_details' => 'Test Building',
            'industry_business_type_id' => 1,
            'version_number' => '10.0',
            'patch_or_fp' => 'FP01',
            'company_size' => '1-10',
            'preferred_support_method' => 'email',
            'accepted_terms' => true,
            'accepted_at' => now(),
            'status' => 'active',
            'registered_by' => 1,
        ];
    }
}
