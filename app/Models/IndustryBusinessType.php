<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['industry'])]
class IndustryBusinessType extends Model
{
    /**
     * @return array<int, string>
     */
    public static function defaults(): array
    {
        return [
            'Manufacturing',
            'Distribution / Retail',
            'Services',
            'Healthcare',
            'Construction',
            'Food & Beverage',
            'Others',
        ];
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
