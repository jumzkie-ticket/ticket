<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['level_no', 'description', 'sla'])]
class SecurityLevel extends Model
{
    protected $table = 'security_level';

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
