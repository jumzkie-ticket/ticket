<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\AccountManager;
use App\Models\IndustryBusinessType;
use App\Models\SapProduct;
use App\Models\AssignFc;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientRegistrationController extends Controller
{
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
            'accountManagers' => AccountManager::query()->orderBy('account_manager')->get(),
            'analytics' => $this->analytics(),
            'companySizes' => self::companySizes(),
            'industries' => IndustryBusinessType::query()->orderBy('industry')->get(),
            'countryCodeApiUrl' => route('clients.country-codes'),
            'psgcBaseUrl' => 'https://psgc.gitlab.io/api',
            'sapProducts' => SapProduct::query()->orderBy('sap_product')->get(),
            'supportMethods' => self::supportMethods(),
            'assignFcs' => AssignFc::query()->orderBy('assign_fc')->get(),
        ]);
    }

    public function countryCodes(): JsonResponse
    {
        $countryCodes = Cache::remember('client-registration.country-codes', now()->addDay(), function (): array {
            $countries = Http::acceptJson()
                ->timeout(15)
                ->get('https://restcountries.com/v3.1/all', [
                    'fields' => 'name,cca2,idd',
                ])
                ->throw()
                ->json();

            return collect($countries)
                ->flatMap(function (array $country): array {
                    $root = data_get($country, 'idd.root', '');
                    $suffixes = data_get($country, 'idd.suffixes', ['']) ?: [''];

                    return collect($suffixes)->map(fn (string $suffix): array => [
                        'iso' => $country['cca2'] ?? '',
                        'dial_code' => $root.$suffix,
                        'name' => data_get($country, 'name.common', $country['cca2'] ?? ''),
                    ])->all();
                })
                ->filter(fn (array $country): bool => preg_match('/^\+\d{1,6}$/', $country['dial_code']) === 1)
                ->sortBy(fn (array $country): string => ($country['iso'] === 'PH' ? '0-' : '1-').$country['name'])
                ->values()
                ->all();
        });

        return response()->json($countryCodes);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'email_address' => ['required', 'email', 'max:255'],
            'contact_country_code' => ['required', 'string', 'max:10', 'regex:/^\+\d{1,6}$/'],
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
            'industry_business_type_id' => ['required', 'integer', Rule::exists('industry_business_types', 'id')],
            'sap_product_ids' => ['required', 'array', 'min:1'],
            'sap_product_ids.*' => ['integer', 'distinct', Rule::exists('products', 'id')],
            'version_number' => ['required', 'string', 'max:40'],
            'patch_or_fp' => ['required', 'string', 'max:120'],
            'db_version' => ['required', 'string', 'max:120'],
            'company_size' => ['required', Rule::in(array_keys(self::companySizes()))],
            'account_manager_id' => ['nullable', 'integer', Rule::exists('account_manager', 'id')],
            'assign_fc_id' => ['nullable', 'integer', Rule::exists('assign_fc', 'id')],
            'preferred_support_method' => ['required', Rule::in(array_keys(self::supportMethods()))],
            'additional_notes' => ['nullable', 'string', 'max:2000'],
            'accepted_terms' => ['accepted'],
        ]);

        $productIds = array_values(array_unique(array_map('intval', $data['sap_product_ids'])));
        unset($data['sap_product_ids']);
        $data['accepted_terms'] = true;
        $data['accepted_at'] = now();
        $data['registered_by'] = $request->user()?->id;
        $data['status'] = 'active';

        $client = Client::create($data);
        $client->sapProducts()->sync($productIds);

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

        $counts = Client::query()->selectRaw(
            "COUNT(*) AS total_clients,
             SUM(CASE WHEN created_at < ? THEN 1 ELSE 0 END) AS total_before_month,
             SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) AS new_this_month,
             SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) AS new_last_month,
             SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_clients,
             SUM(CASE WHEN status = 'active' AND created_at < ? THEN 1 ELSE 0 END) AS active_before_month,
             SUM(CASE WHEN accepted_terms = ? AND accepted_at IS NOT NULL THEN 1 ELSE 0 END) AS completed_clients",
            [$monthStart, $monthStart, $now, $lastMonthStart, $lastMonthEnd, $monthStart, true],
        )->first();

        $totalClients = (int) $counts->total_clients;
        $totalBeforeMonth = (int) $counts->total_before_month;
        $newThisMonth = (int) $counts->new_this_month;
        $newLastMonth = (int) $counts->new_last_month;
        $activeClients = (int) $counts->active_clients;
        $activeBeforeMonth = (int) $counts->active_before_month;
        $completedClients = (int) $counts->completed_clients;

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
            ->join('industry_business_types', 'clients.industry_business_type_id', '=', 'industry_business_types.id')
            ->select('industry_business_types.industry', DB::raw('count(*) as aggregate'))
            ->groupBy('industry_business_types.id', 'industry_business_types.industry')
            ->orderBy('industry_business_types.industry')
            ->get();

        $colors = ['#2563eb', '#35b95f', '#8b5cf6', '#f8b31d', '#18aac4', '#f97316', '#14b8a6'];

        $segments = [];
        $gradientStops = [];
        $cursor = 0;

        foreach ($industryCounts as $index => $industryCount) {
            $count = (int) $industryCount->aggregate;

            if ($count === 0 || $totalClients === 0) {
                continue;
            }

            $percent = (int) round(($count / $totalClients) * 100);
            $next = min(100, $cursor + $percent);
            $color = $colors[$index % count($colors)];
            $label = $industryCount->industry;

            $segments[] = compact('color', 'count', 'label', 'percent');
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
