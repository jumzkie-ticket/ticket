<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Package;
use App\Models\SapProduct;

class ServiceOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'industry_business_type_id',
        'software_version',
        'patch_or_fp',
        'support_start_date',
        'support_end_date',
        'cas_accredited',
        'support_inclusion',
        'man_days',
        'unused_man_days',
        'used_man_days',
        'license_type',
        'professional',
        'limited',
        'indirect',
        'starter',
        'mssql',
        'notes',
        'attach_service_order',
        'attach_service_order_original_name',
    ];

    protected $casts = [
        'professional' => 'integer',
        'limited' => 'integer',
        'indirect' => 'integer',
        'starter' => 'integer',
        'mssql' => 'integer',
        'cas_accredited' => 'boolean',
        'support_start_date' => 'date',
        'support_end_date' => 'date',
    ];

    // explicit table name after migration rename
    protected $table = 'service_order';

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function industryBusinessType(): BelongsTo
    {
        return $this->belongsTo(IndustryBusinessType::class);
    }

    public function sapProducts(): BelongsToMany
    {
        return $this->belongsToMany(SapProduct::class, 'service_order_sap_product', 'service_order_id', 'sap_product_id')->withTimestamps();
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'service_order_package', 'service_order_id', 'package_id')->withTimestamps();
    }

    public function getRemainingManDaysAttribute(): int
    {
        return max(0, (int) $this->man_days + (int) $this->unused_man_days - (int) $this->used_man_days);
    }
}
