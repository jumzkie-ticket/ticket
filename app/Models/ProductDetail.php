<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'helpdesk_support_packages',
    'man_days',
    'helpdesk_coverage_months',
    'helpdesk_support_fee',
    'total_amount_vat_inc',
])]
class ProductDetail extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'man_days' => 'integer',
            'helpdesk_coverage_months' => 'integer',
            'helpdesk_support_fee' => 'decimal:2',
            'total_amount_vat_inc' => 'decimal:2',
        ];
    }
}
