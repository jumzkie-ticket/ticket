<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
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
])]
class Client extends Model
{
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accepted_terms' => 'boolean',
            'accepted_at' => 'datetime',
        ];
    }
}
