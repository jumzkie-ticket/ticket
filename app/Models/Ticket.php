<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'client_id',
    'company_name',
    'contact_person',
    'email_address',
    'contact_number',
    'product_related',
    'software_version',
    'patch_or_fp',
    'database_version',
    'issue_encountered',
    'scenario',
    'expected_result',
    'full_name',
    'contact_email',
    'contact_phone',
    'other_information',
    'attachment',
    'attachment_original_name',
    'status',
    'security_level_id',
    'ticket_status_id',
    'created_by',
])]
class Ticket extends Model
{
    public function resolutions(): HasMany
    {
        return $this->hasMany(TicketResolution::class)->orderBy('date');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function securityLevel(): BelongsTo
    {
        return $this->belongsTo(SecurityLevel::class);
    }

    public function ticketStatus(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
