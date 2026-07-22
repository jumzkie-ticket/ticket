<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Support\SystemVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SystemSettingsController extends Controller
{
    public function __invoke(): View
    {
        $settings = SystemSetting::current();
        $databaseConnection = (string) config('database.default');

        $systemInfo = [
            'Version' => SystemVersion::current(),
            'Last Updated' => $settings->updated_at?->format('M d, Y h:i A') ?? 'Not saved yet',
            'Environment' => Str::title((string) config('app.env', 'production')),
            'Database' => match ($databaseConnection) {
                'pgsql' => 'PostgreSQL',
                'mysql' => 'MySQL',
                'sqlite' => 'SQLite',
                'sqlsrv' => 'SQL Server',
                default => Str::title($databaseConnection),
            },
            'Server' => 'xti-app-01',
            'Uptime' => '15d 6h 42m',
        ];

        return view('system-settings', compact('settings', 'systemInfo'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = SystemSetting::current();

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'system_name' => ['required', 'string', 'max:255'],
            'time_zone' => ['required', Rule::in(['America/New_York', 'Asia/Singapore', 'Asia/Manila'])],
            'date_format' => ['required', Rule::in(['F j, Y', 'Y-m-d', 'd/m/Y'])],
            'theme' => ['required', Rule::in(['light', 'dark', 'system'])],
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'backup_frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly'])],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
        ]);

        foreach ([
            'email_notifications',
            'ticket_alerts',
            'system_announcements',
            'weekly_reports',
            'auto_backup',
            'maintenance_mode',
        ] as $checkbox) {
            $data[$checkbox] = $request->boolean($checkbox);
        }

        $data['primary_color'] = strtoupper($data['primary_color']);

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('system-logos', 'public');
        }

        unset($data['logo']);

        $settings->update($data);
        SystemVersion::markChanged();

        return redirect()
            ->route('system-settings')
            ->with('status', 'System settings saved successfully.');
    }
}
