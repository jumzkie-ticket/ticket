<?php

namespace App\Http\Controllers;

use App\Models\ClientUser;
use App\Models\Ticket;
use App\Models\TicketResolution;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const CHART_COLORS = [
        '#4f46e5',
        '#22c55e',
        '#f59e0b',
        '#a855f7',
        '#ec4899',
        '#06b6d4',
        '#f97316',
        '#14b8a6',
        '#6366f1',
        '#84cc16',
        '#eab308',
        '#f43f5e',
    ];

    public function __invoke(Request $request): View
    {
        if ($request->user()->roles()->where('slug', 'customer')->exists()) {
            return $this->customerDashboard($request->user());
        }

        $stats = [
            'total_users' => User::count(),
            'admins' => $this->usersInRole('admin'),
            'customers' => $this->usersInRole('customer'),
            'consultants' => $this->usersInRole('consultant'),
        ];

        $metricCards = $this->ticketStatusGuide(Ticket::query());

        $trend = $this->ticketTrend();
        $statusBreakdown = $this->ticketStatusBreakdown();
        $productBreakdown = $this->ticketProductBreakdown();
        $statusGradient = $this->donutGradient($statusBreakdown);
        $productGradient = $this->donutGradient($productBreakdown);

        $recentTickets = [
            ['ticket' => 'XB1-1048', 'summary' => 'Sales order approval workflow', 'product' => 'SAP B1', 'status' => 'Open', 'priority' => 'High', 'updated' => 'Today, 10:24 AM'],
            ['ticket' => 'PAY-884', 'summary' => 'Payroll report variance check', 'product' => 'E-Sweldo Payroll', 'status' => 'Resolved', 'priority' => 'Medium', 'updated' => 'Yesterday, 4:15 PM'],
            ['ticket' => 'EMR-277', 'summary' => 'Patient billing integration queue', 'product' => 'HIS & EMR', 'status' => 'Open', 'priority' => 'High', 'updated' => 'May 24, 2024'],
            ['ticket' => 'ADD-531', 'summary' => 'Addon license renewal request', 'product' => 'Xceler8 Addon', 'status' => 'Closed', 'priority' => 'Low', 'updated' => 'May 23, 2024'],
        ];

        $quickSummary = $this->quickSummary($stats['total_users']);

        return view('dashboard', compact(
            'metricCards',
            'productBreakdown',
            'productGradient',
            'quickSummary',
            'recentTickets',
            'stats',
            'statusBreakdown',
            'statusGradient',
            'trend',
        ));
    }

    private function customerDashboard(User $user): View
    {
        $tickets = Ticket::query()->whereIn('client_id', ClientUser::query()
            ->select('client_id')
            ->where('user_id', $user->id));
        $statusGuide = $this->ticketStatusGuide($tickets);

        return view('customer-dashboard', [
            'statusGuide' => $statusGuide,
            'user' => $user,
        ]);
    }

    private function ticketStatusGuide($tickets)
    {
        $statusCounts = (clone $tickets)
            ->selectRaw("LOWER(REPLACE(TRIM(status), '-', ' ')) AS normalized_status, COUNT(*) AS aggregate")
            ->groupByRaw("LOWER(REPLACE(TRIM(status), '-', ' '))")
            ->pluck('aggregate', 'normalized_status');
        $ticketStatusOptions = TicketStatus::query()
            ->orderBy('id')
            ->get()
            ->unique(fn (TicketStatus $ticketStatus): string => mb_strtolower(str_replace('-', ' ', trim($ticketStatus->status))))
            ->values();
        $statusGuide = collect([[
            'label' => 'Total Tickets',
            'value' => (clone $tickets)->count(),
            'icon' => 'icon-ticket',
            'palette' => ['light' => '#5c54f4', 'dark' => '#8f88ff'],
        ]])->concat($ticketStatusOptions->map(function (TicketStatus $ticketStatus) use ($statusCounts): array {
            $statusKey = strtolower(str_replace(' ', '-', $ticketStatus->status));
            $normalizedStatus = mb_strtolower(str_replace('-', ' ', trim($ticketStatus->status)));

            return [
                'label' => TicketController::statuses()[$statusKey] ?? ucwords(str_replace('-', ' ', $ticketStatus->status)),
                'value' => (int) ($statusCounts[$normalizedStatus] ?? 0),
                'icon' => match ($statusKey) {
                    'resolved' => 'icon-check-circle',
                    'closed', 'no-helpdesk', 'no-maintenance' => 'icon-x-square',
                    'in-progress', 'hold', 'pending-from-client', 'pending-from-dev', 'pending-from-sales', 'pending-from-xti' => 'icon-clock',
                    default => 'icon-inbox',
                },
                'palette' => $ticketStatus->palette(),
            ];
        }));
        $representedStatuses = $ticketStatusOptions
            ->map(fn (TicketStatus $ticketStatus): string => mb_strtolower(str_replace('-', ' ', trim($ticketStatus->status))));
        $unassignedCount = (int) $statusCounts->except($representedStatuses->all())->sum();

        if ($unassignedCount > 0) {
            $statusGuide->push([
                'label' => 'Unassigned Status',
                'value' => $unassignedCount,
                'icon' => 'icon-help',
                'palette' => ['light' => '#64748b', 'dark' => '#cbd5e1'],
            ]);
        }

        return $statusGuide;
    }

    private function ticketTrend(): Collection
    {
        $start = now()->startOfDay()->subDays(6);
        $counts = Ticket::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) AS ticket_date, COUNT(*) AS aggregate')
            ->groupByRaw('DATE(created_at)')
            ->pluck('aggregate', 'ticket_date');

        return collect(range(0, 6))->map(function (int $day) use ($counts, $start): array {
            $date = $start->copy()->addDays($day);

            return [
                'label' => $date->format('M j'),
                'value' => (int) ($counts[$date->toDateString()] ?? 0),
            ];
        });
    }

    private function ticketStatusBreakdown(): Collection
    {
        $counts = Ticket::query()
            ->where('created_at', '>=', now()->startOfDay()->subDays(6))
            ->selectRaw("LOWER(REPLACE(TRIM(status), '-', ' ')) AS normalized_status, COUNT(*) AS aggregate")
            ->groupByRaw("LOWER(REPLACE(TRIM(status), '-', ' '))")
            ->pluck('aggregate', 'normalized_status')
            ->sortDesc();
        $statuses = TicketStatus::query()
            ->get()
            ->keyBy(fn (TicketStatus $status): string => mb_strtolower(str_replace('-', ' ', trim($status->status))));

        $items = $counts->map(function (int|string $count, string $normalizedStatus) use ($statuses): array {
            $ticketStatus = $statuses->get($normalizedStatus);
            $statusKey = str_replace(' ', '-', $normalizedStatus);

            return [
                'label' => TicketController::statuses()[$statusKey] ?? ucwords($normalizedStatus),
                'value' => (int) $count,
                'color' => $ticketStatus
                    ? $ticketStatus->palette()['light']
                    : TicketStatus::paletteFor($normalizedStatus)['light'],
            ];
        })->values();

        return $this->withPercentages($items);
    }

    private function ticketProductBreakdown(): Collection
    {
        $counts = Ticket::query()
            ->where('created_at', '>=', now()->startOfDay()->subDays(29))
            ->pluck('product_related')
            ->flatMap(function (?string $products): array {
                if (blank($products)) {
                    return ['Unspecified'];
                }

                return collect(explode(',', $products))
                    ->map(fn (string $product): string => trim($product))
                    ->filter()
                    ->unique(fn (string $product): string => mb_strtolower($product))
                    ->values()
                    ->all();
            })
            ->countBy()
            ->sortDesc();

        $colorIndex = 0;
        $items = $counts->map(function (int $count, string $product) use (&$colorIndex): array {
            return [
                'label' => $product,
                'value' => $count,
                'color' => self::CHART_COLORS[$colorIndex++ % count(self::CHART_COLORS)],
            ];
        })->values();

        return $this->withPercentages($items);
    }

    private function withPercentages(Collection $items): Collection
    {
        $total = (int) $items->sum('value');
        $position = 0.0;

        return $items->map(function (array $item) use ($total, &$position): array {
            $percentage = $total > 0 ? ($item['value'] / $total) * 100 : 0;
            $item['percent'] = number_format($percentage, 2).'%';
            $item['start'] = $position;
            $position += $percentage;
            $item['end'] = $position;

            return $item;
        });
    }

    private function donutGradient(Collection $items): string
    {
        if ($items->isEmpty()) {
            return 'conic-gradient(#dbe3f0 0 100%)';
        }

        $stops = $items->map(fn (array $item): string => sprintf(
            '%s %.4f%% %.4f%%',
            $item['color'],
            $item['start'],
            $item['end'],
        ))->implode(', ');

        return "conic-gradient({$stops})";
    }

    private function quickSummary(int $registeredUsers): array
    {
        $ticketCounts = Ticket::query()
            ->selectRaw("COUNT(*) AS total,
                SUM(CASE WHEN LOWER(REPLACE(TRIM(status), '-', ' ')) = 'resolved' THEN 1 ELSE 0 END) AS resolved_count,
                SUM(CASE WHEN LOWER(REPLACE(TRIM(status), '-', ' ')) = 'closed' THEN 1 ELSE 0 END) AS closed_count")
            ->first();

        return [
            ['label' => 'Total Tickets (All Time)', 'value' => number_format((int) $ticketCounts->total), 'tone' => 'blue', 'icon' => 'icon-inbox'],
            ['label' => 'Resolved (All Time)', 'value' => number_format((int) $ticketCounts->resolved_count), 'tone' => 'green', 'icon' => 'icon-check-circle'],
            ['label' => 'Closed (All Time)', 'value' => number_format((int) $ticketCounts->closed_count), 'tone' => 'red', 'icon' => 'icon-x-square'],
            ['label' => 'Average Response Time', 'value' => $this->averageResponseTime(), 'tone' => 'violet', 'icon' => 'icon-clock'],
            ['label' => 'Customer Satisfaction', 'value' => 'N/A', 'tone' => 'amber', 'icon' => 'icon-star'],
            ['label' => 'Registered Users', 'value' => number_format($registeredUsers), 'tone' => 'blue', 'icon' => 'icon-users'],
        ];
    }

    private function averageResponseTime(): string
    {
        $firstResponses = TicketResolution::query()
            ->selectRaw('ticket_id, MIN(date) AS first_response_at')
            ->groupBy('ticket_id')
            ->get()
            ->keyBy('ticket_id');

        $responseMinutes = Ticket::query()
            ->whereIn('id', $firstResponses->keys())
            ->get(['id', 'created_at'])
            ->map(function (Ticket $ticket) use ($firstResponses): float {
                $firstResponse = Carbon::parse($firstResponses[$ticket->id]->first_response_at);

                return (float) $ticket->created_at->diffInMinutes($firstResponse, false);
            })
            ->filter(fn (float $minutes): bool => $minutes >= 0);

        if ($responseMinutes->isEmpty()) {
            return 'N/A';
        }

        $averageMinutes = (float) $responseMinutes->average();

        if ($averageMinutes < 60) {
            return number_format($averageMinutes, 0).' min';
        }

        return number_format($averageMinutes / 60, 1).' hrs';
    }

    private function usersInRole(string $slug): int
    {
        return User::whereHas('roles', fn ($query) => $query->where('slug', $slug))->count();
    }
}
