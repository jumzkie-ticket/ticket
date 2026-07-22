<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['package'])]
class Package extends Model
{
    protected $table = 'packages';

    public function serviceOrders(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceOrderDetail::class,
            'service_order_package',
            'package_id',
            'service_order_id'
        )->withTimestamps();
    }
}
