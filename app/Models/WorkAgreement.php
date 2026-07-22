<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'work_agreement_no',
    'agreement_date',
    'assign_fc_id',
    'client_id',
    'address',
    'billable',
    'non_billable',
    'scope',
    'objective',
    'current_issue',
    'proposed_solutions',
    'note',
    'estimated_man_days',
    'project_manager_assign_fc_id',
    'project_manager',
    'consultant_assign_fc_id',
    'consultant',
    'accepted_by',
    'accepted_by_designation',
    'created_by',
])]
class WorkAgreement extends Model
{
    protected static function booted(): void
    {
        static::created(function (WorkAgreement $agreement): void {
            if (blank($agreement->work_agreement_no)) {
                $agreement->forceFill([
                    'work_agreement_no' => 'WA-'.str_pad((string) $agreement->id, 4, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'agreement_date' => 'date',
            'billable' => 'boolean',
            'non_billable' => 'boolean',
            'estimated_man_days' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignFc(): BelongsTo
    {
        return $this->belongsTo(AssignFc::class, 'assign_fc_id');
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(AssignFc::class, 'project_manager_assign_fc_id');
    }

    public function consultantFc(): BelongsTo
    {
        return $this->belongsTo(AssignFc::class, 'consultant_assign_fc_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
