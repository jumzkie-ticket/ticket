<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientRegistrationController extends Controller
{
    /**
     * @return array<string, string>
     */
    public static function industries(): array
    {
        return [
            'manufacturing' => 'Manufacturing',
            'distribution-retail' => 'Distribution / Retail',
            'services' => 'Services',
            'healthcare' => 'Healthcare',
            'construction' => 'Construction',
            'food-beverage' => 'Food & Beverage',
            'others' => 'Others',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function sapProducts(): array
    {
        return [
            'sap-business-one' => 'SAP Business One',
            'sap-s4hana' => 'SAP S/4HANA',
            'sap-btp' => 'SAP Business Technology Platform',
            'sap-successfactors' => 'SAP SuccessFactors',
            'sap-businessobjects' => 'SAP BusinessObjects',
            'other' => 'Other SAP Product',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function companySizes(): array
    {
        return [
            '1-10' => '1 - 10 users',
            '11-25' => '11 - 25 users',
            '26-50' => '26 - 50 users',
            '51-100' => '51 - 100 users',
            '101-250' => '101 - 250 users',
            '251-plus' => '251+ users',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function supportMethods(): array
    {
        return [
            'email' => 'Email',
            'phone' => 'Phone',
            'support-portal' => 'Support Portal / Ticket',
            'live-chat' => 'Live Chat',
        ];
    }

    public function index(): View
    {
        return view('clients.registration', [
            'analytics' => $this->analytics(),
            'companySizes' => self::companySizes(),
            'industries' => self::industries(),
            'psgcBaseUrl' => 'https://psgc.gitlab.io/api',
            'sapProducts' => self::sapProducts(),
            'supportMethods' => self::supportMethods(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email_address' => ['required', 'email', 'max:255'],
            'contact_country_code' => ['required', 'string', Rule::in(['+63'])],
            'contact_number' => ['required', 'string', 'max:40'],
            'region_code' => ['required', 'string', 'max:20'],
            'region_name' => ['required', 'string', 'max:255'],
            'province_code' => ['nullable', 'string', 'max:20'],
            'province_name' => ['nullable', 'string', 'max:255'],
            'city_municipality_code' => ['required', 'string', 'max:20'],
            'city_municipality_name' => ['required', 'string', 'max:255'],
            'barangay_code' => ['required', 'string', 'max:20'],
            'barangay_name' => ['required', 'string', 'max:255'],
            'building_details' => ['required', 'string', 'max:500'],
            'industry_type' => ['required', Rule::in(array_keys(self::industries()))],
            'sap_product_used' => ['required', Rule::in(array_keys(self::sapProducts()))],
            'software_version_patch' => ['required', 'string', 'max:120'],
            'company_size' => ['required', Rule::in(array_keys(self::companySizes()))],
            'preferred_support_method' => ['required', Rule::in(array_keys(self::supportMethods()))],
            'additional_notes' => ['nullable', 'string', 'max:2000'],
            'accepted_terms' => ['accepted'],
        ]);

        $data['accepted_terms'] = true;
        $data['accepted_at'] = now();
        $data['registered_by'] = $request->user()?->id;
        $data['status'] = 'active';

        Client::create($data);

        return redirect()
            ->route('clients.registration')
            ->with('status', 'Client registered successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function analytics(): array
    {
        $now = CarbonImmutable::now();
        $monthStart = $now->startOfMonth();
        $lastMonthStart = $monthStart->subMonth();
        $lastMonthEnd = $monthStart->subSecond();

        $totalClients = Client::query()->count();
        $totalBeforeMonth = Client::query()
            ->where('created_at', '<', $monthStart)
            ->count();
        $newThisMonth = Client::query()
            ->whereBetween('created_at', [$monthStart, $now])
            ->count();
        $newLastMonth = Client::query()
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
        $activeClients = Client::query()
            ->where('status', 'active')
            ->count();
        $activeBeforeMonth = Client::query()
            ->where('status', 'active')
            ->where('created_at', '<', $monthStart)
            ->count();
        $completedClients = Client::query()
            ->where('accepted_terms', true)
            ->whereNotNull('accepted_at')
            ->count();

        $completionRate = $totalClients > 0
            ? (int) round(($completedClients / $totalClients) * 100)
            : 0;

        $overview = [
            [
                'label' => 'Total Registered Clients',
                'value' => number_format($totalClients),
                'icon' => 'client-icon-users',
                'tone' => 'blue',
                'trend' => $this->trendLabel($totalClients, $totalBeforeMonth),
            ],
            [
                'label' => 'New Registrations This Month',
                'value' => number_format($newThisMonth),
                'icon' => 'client-icon-user-plus',
                'tone' => 'green',
                'trend' => $this->trendLabel($newThisMonth, $newLastMonth),
            ],
            [
                'label' => 'Active Client Companies',
                'value' => number_format($activeClients),
                'icon' => 'client-icon-building',
                'tone' => 'violet',
                'trend' => $this->trendLabel($activeClients, $activeBeforeMonth),
            ],
            [
                'label' => 'Registration Completion Rate',
                'value' => "{$completionRate}%",
                'icon' => 'client-icon-gauge',
                'tone' => 'amber',
                'trend' => $this->trendLabel($completionRate, 0),
            ],
        ];

        $industryCounts = Client::query()
            ->select('industry_type', DB::raw('count(*) as aggregate'))
            ->groupBy('industry_type')
            ->pluck('aggregate', 'industry_type');

        $colors = [
            'manufacturing' => '#2563eb',
            'distribution-retail' => '#35b95f',
            'services' => '#8b5cf6',
            'healthcare' => '#f8b31d',
            'construction' => '#f97316',
            'food-beverage' => '#14b8a6',
            'others' => '#18aac4',
        ];

        $segments = [];
        $gradientStops = [];
        $cursor = 0;

        foreach (self::industries() as $key => $label) {
            $count = (int) ($industryCounts[$key] ?? 0);

            if ($count === 0 || $totalClients === 0) {
                continue;
            }

            $percent = (int) round(($count / $totalClients) * 100);
            $next = min(100, $cursor + $percent);
            $color = $colors[$key] ?? '#2563eb';

            $segments[] = compact('color', 'count', 'key', 'label', 'percent');
            $gradientStops[] = "{$color} {$cursor}% {$next}%";
            $cursor = $next;
        }

        if ($segments !== [] && $cursor < 100) {
            $lastColor = $segments[array_key_last($segments)]['color'];
            $gradientStops[] = "{$lastColor} {$cursor}% 100%";
        }

        return [
            'overview' => $overview,
            'industry' => [
                'segments' => $segments,
                'gradient' => $gradientStops === []
                    ? 'var(--line) 0% 100%'
                    : implode(', ', $gradientStops),
            ],
        ];
    }

    private function trendLabel(int $current, int $previous): string
    {
        if ($previous === 0) {
            return $current > 0 ? 'New this month' : '0% vs last month';
        }

        $change = (int) round((($current - $previous) / $previous) * 100);
        $prefix = $change > 0 ? '+' : '';

        return "{$prefix}{$change}% vs last month";
    }
}
