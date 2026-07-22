<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['account_manager'])]
class AccountManager extends Model
{
    protected $table = 'account_manager';

    /**
     * @return array<int, string>
     */
    public static function defaults(): array
    {
        return [
            'Juan Dela Cruz',
            'Paolo Cruz',
            'Maria Santos',
        ];
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
