<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'full_name',
    'email',
    'subject',
    'priority',
    'category',
    'message',
    'attachment_path',
    'status',
])]
class ContactSupportRequest extends Model
{
}
