<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Support\SystemVersion;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AboutUsController extends Controller
{
    public function __invoke(): View
    {
        $settings = SystemSetting::current();
        $releaseDate = CarbonImmutable::parse((string) config('app.first_release_date', '2026-07-07'));
        $logoUrl = $settings->logo_path && Storage::disk('public')->exists($settings->logo_path)
            ? '/storage/'.ltrim($settings->logo_path, '/')
            : null;

        $versionInfo = [
            'version' => SystemVersion::current(),
            'release_date' => $releaseDate->format('F j, Y'),
            'release_day' => $releaseDate->format('l'),
            'release_label' => $releaseDate->format('l, F j, Y'),
        ];
        $maintainerName = 'Xceler8 Technologies Inc.';

        return view('about-us', compact('logoUrl', 'maintainerName', 'settings', 'versionInfo'));
    }
}
