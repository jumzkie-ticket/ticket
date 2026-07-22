<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['sap_product'])]
class SapProduct extends Model
{
    protected $table = 'products';

    /**
     * @return array<int, string>
     */
    public static function defaults(): array
    {
        return [
            'SAP Business One',
            'SAP S/4HANA',
            'SAP Business Technology Platform',
            'SAP SuccessFactors',
            'SAP BusinessObjects',
            'Other SAP Product',
        ];
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_sap_product')->withTimestamps();
    }
}
