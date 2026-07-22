<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
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
])]
class Client extends Model
{
    use HasFactory;

    public function industryBusinessType(): BelongsTo
    {
        return $this->belongsTo(IndustryBusinessType::class);
    }

    public function sapProducts(): BelongsToMany
    {
        return $this->belongsToMany(SapProduct::class, 'client_sap_product')->withTimestamps();
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(AccountManager::class);
    }

    public function assignFc(): BelongsTo
    {
        return $this->belongsTo(AssignFc::class, 'assign_fc_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function clientUsers(): HasMany
    {
        return $this->hasMany(ClientUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'client_users')->withTimestamps();
    }

    public function workAgreements(): HasMany
    {
        return $this->hasMany(WorkAgreement::class);
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
