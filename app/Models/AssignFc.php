<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['assign_fc', 'designation'])]
class AssignFc extends Model
{
    protected $table = 'assign_fc';

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'assign_fc_id');
    }

    public function workAgreements(): HasMany
    {
        return $this->hasMany(WorkAgreement::class, 'assign_fc_id');
    }

    public function managedWorkAgreements(): HasMany
    {
        return $this->hasMany(WorkAgreement::class, 'project_manager_assign_fc_id');
    }

    public function consultedWorkAgreements(): HasMany
    {
        return $this->hasMany(WorkAgreement::class, 'consultant_assign_fc_id');
    }
}
