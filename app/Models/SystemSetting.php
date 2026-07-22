<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_name',
    'system_name',
    'time_zone',
    'date_format',
    'email_notifications',
    'ticket_alerts',
    'system_announcements',
    'weekly_reports',
    'theme',
    'primary_color',
    'logo_path',
    'auto_backup',
    'backup_frequency',
    'maintenance_mode',
])]
class SystemSetting extends Model
{
    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'company_name' => 'Xceler8 Technologies Inc.',
            'system_name' => 'XTI Ticket Support System',
            'time_zone' => 'America/New_York',
            'date_format' => 'F j, Y',
            'email_notifications' => true,
            'ticket_alerts' => true,
            'system_announcements' => true,
            'weekly_reports' => false,
            'theme' => 'light',
            'primary_color' => '#2563EB',
            'logo_path' => null,
            'auto_backup' => true,
            'backup_frequency' => 'daily',
            'maintenance_mode' => false,
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate([], self::defaults());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_notifications' => 'boolean',
            'ticket_alerts' => 'boolean',
            'system_announcements' => 'boolean',
            'weekly_reports' => 'boolean',
            'auto_backup' => 'boolean',
            'maintenance_mode' => 'boolean',
        ];
    }
}
