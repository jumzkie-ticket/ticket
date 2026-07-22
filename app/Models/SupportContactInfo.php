<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'support_email',
    'phone_number',
    'office_address',
    'office_hours',
])]
class SupportContactInfo extends Model
{
    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'support_email' => 'sap-support@xceler8inc.com',
            'phone_number' => '(02) 8893 8888',
            'office_address' => '8F Solar Century Tower 100 Tordesillas St. Cor H.V. Dela Costa St. Salcedo Village, Makati City, Metro Manila, Philippines',
            'office_hours' => '8:00 AM - 6:00 PM (GMT+8)',
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate([], self::defaults());
    }
}
