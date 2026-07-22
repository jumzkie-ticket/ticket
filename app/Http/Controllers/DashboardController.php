<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_users' => User::count(),
            'admins' => $this->usersInRole('admin'),
            'customers' => $this->usersInRole('customer'),
            'consultants' => $this->usersInRole('consultant'),
        ];

        $metricCards = [
            [
                'label' => 'Total Tickets',
                'value' => '128',
                'change' => '+ 12% vs last 30 days',
                'tone' => 'blue',
                'icon' => 'icon-inbox',
            ],
            [
                'label' => 'Open Tickets',
                'value' => '34',
                'change' => '+ 8% vs last 30 days',
                'tone' => 'amber',
                'icon' => 'icon-clock',
            ],
            [
                'label' => 'Resolved Tickets',
                'value' => '82',
                'change' => '+ 15% vs last 30 days',
                'tone' => 'green',
                'icon' => 'icon-check-circle',
            ],
            [
                'label' => 'Closed Tickets',
                'value' => '12',
                'change' => '- 5% vs last 30 days',
                'tone' => 'red',
                'icon' => 'icon-x-square',
            ],
            [
                'label' => 'Customer Satisfaction',
                'value' => '4.6 / 5',
                'change' => '+ 6% vs last 30 days',
                'tone' => 'violet',
                'icon' => 'icon-star',
            ],
        ];

        $trend = [
            ['label' => 'May 19', 'value' => 20],
            ['label' => 'May 20', 'value' => 25],
            ['label' => 'May 21', 'value' => 38],
            ['label' => 'May 22', 'value' => 30],
            ['label' => 'May 23', 'value' => 50],
            ['label' => 'May 24', 'value' => 32],
            ['label' => 'May 25', 'value' => 40],
        ];

        $statusBreakdown = [
            ['label' => 'Open', 'value' => 34, 'percent' => '26.6%', 'color' => '#1766ff'],
            ['label' => 'Resolved', 'value' => 82, 'percent' => '64.1%', 'color' => '#2fc56f'],
            ['label' => 'Closed', 'value' => 12, 'percent' => '9.3%', 'color' => '#ff5148'],
        ];

        $productBreakdown = [
            ['label' => 'SAP B1', 'value' => 58, 'percent' => '45.3%', 'color' => '#1766ff'],
            ['label' => 'E-Sweldo Payroll', 'value' => 28, 'percent' => '21.9%', 'color' => '#2fc56f'],
            ['label' => 'HIS & EMR', 'value' => 16, 'percent' => '12.5%', 'color' => '#f4a51c'],
            ['label' => 'Xceler8 Addon', 'value' => 12, 'percent' => '9.4%', 'color' => '#765cff'],
            ['label' => 'SAP Cloud ERP', 'value' => 14, 'percent' => '10.9%', 'color' => '#18aac4'],
        ];

        $recentTickets = [
            ['ticket' => 'XB1-1048', 'summary' => 'Sales order approval workflow', 'product' => 'SAP B1', 'status' => 'Open', 'priority' => 'High', 'updated' => 'Today, 10:24 AM'],
            ['ticket' => 'PAY-884', 'summary' => 'Payroll report variance check', 'product' => 'E-Sweldo Payroll', 'status' => 'Resolved', 'priority' => 'Medium', 'updated' => 'Yesterday, 4:15 PM'],
            ['ticket' => 'EMR-277', 'summary' => 'Patient billing integration queue', 'product' => 'HIS & EMR', 'status' => 'Open', 'priority' => 'High', 'updated' => 'May 24, 2024'],
            ['ticket' => 'ADD-531', 'summary' => 'Addon license renewal request', 'product' => 'Xceler8 Addon', 'status' => 'Closed', 'priority' => 'Low', 'updated' => 'May 23, 2024'],
        ];

        $quickSummary = [
            ['label' => 'Total Tickets (All Time)', 'value' => '1,245', 'tone' => 'blue', 'icon' => 'icon-inbox'],
            ['label' => 'Resolved (All Time)', 'value' => '1,056', 'tone' => 'green', 'icon' => 'icon-check-circle'],
            ['label' => 'Closed (All Time)', 'value' => '189', 'tone' => 'red', 'icon' => 'icon-x-square'],
            ['label' => 'Average Response Time', 'value' => '2.6 hrs', 'tone' => 'violet', 'icon' => 'icon-clock'],
            ['label' => 'Customer Satisfaction', 'value' => '4.6 / 5', 'tone' => 'amber', 'icon' => 'icon-star'],
            ['label' => 'Registered Users', 'value' => (string) $stats['total_users'], 'tone' => 'blue', 'icon' => 'icon-users'],
        ];

        return view('dashboard', compact(
            'metricCards',
            'productBreakdown',
            'quickSummary',
            'recentTickets',
            'stats',
            'statusBreakdown',
            'trend',
        ));
    }

    private function usersInRole(string $slug): int
    {
        return User::whereHas('roles', fn ($query) => $query->where('slug', $slug))->count();
    }
}
