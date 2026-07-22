<?php

namespace Database\Seeders;

use App\Models\AccountManager;
use App\Models\IndustryBusinessType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SapProduct;
use App\Models\SupportContactInfo;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissionNames = [
            'Analytics',
            'Announcements',
            'Account Manager',
            'About Us',
            'Assign FC',
            'Clients',
            'Client Registration',
            'Contact Support',
            'Create Ticket',
            'Dashboard Access',
            'Industry / Business Type',
            'Knowledge Base',
            'My Ticket',
            'Package',
            'Product Details',
            'Reports',
            'Role Management',
            'Security Level',
            'Service Order',
            'Service Order List',
            'SLA & Performance',
            'Product Used',
            'System Settings',
            'Ticket Status',
            'User Registration',
            'Work Agreement',
        ];

        $permissions = collect($permissionNames)
            ->mapWithKeys(function (string $name) {
                $slug = Str::slug($name);

                return [
                    $slug => Permission::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $name,
                            'category' => $name,
                            'description' => "Allows access to {$name}.",
                        ],
                    ),
                ];
            });

        $roles = [
            'admin' => [
                'name' => 'Admin',
                'permissions' => $permissions->keys()->all(),
            ],
            'customer' => [
                'name' => 'Customer',
                'permissions' => ['announcements', 'contact-support', 'create-ticket', 'knowledge-base', 'my-ticket'],
            ],
            'consultant' => [
                'name' => 'Consultant',
                'permissions' => ['account-manager', 'analytics', 'assign-fc', 'clients', 'client-registration', 'create-ticket', 'dashboard-access', 'industry-business-type', 'knowledge-base', 'my-ticket', 'package', 'product-details', 'product-used', 'reports', 'security-level', 'service-order', 'service-order-list', 'sla-performance', 'ticket-status', 'work-agreement'],
            ],
        ];

        $roleModels = [];

        foreach ($roles as $slug => $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $roleData['name'],
                    'status' => 'active',
                ],
            );

            $roleModels[$slug] = $role;

            $role->permissions()->sync(
                collect($roleData['permissions'])
                    ->map(fn (string $permissionSlug) => $permissions[$permissionSlug]->id)
                    ->all(),
            );
        }

        $legacyUser = User::where('email', 'test@example.com')->first();

        if ($legacyUser && ! User::where('email', 'admin@xceler8.test')->exists()) {
            $legacyUser->update([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@xceler8.test',
                'password' => Hash::make('password'),
            ]);
        }

        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@xceler8.test',
                'role' => 'admin',
            ],
            [
                'first_name' => 'Codex',
                'last_name' => 'Admin',
                'email' => 'codex.management@example.test',
                'role' => 'admin',
            ],
            [
                'first_name' => 'AA',
                'last_name' => 'Customer',
                'email' => 'client@xceler8.test',
                'role' => 'customer',
            ],
            [
                'first_name' => 'Jumar',
                'last_name' => 'Amores',
                'email' => 'consultant@xceler8.test',
                'role' => 'consultant',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'password' => Hash::make('password'),
                ],
            );

            $user->roles()->sync([$roleModels[$userData['role']]->id]);
        }

        foreach (IndustryBusinessType::defaults() as $industry) {
            IndustryBusinessType::query()->firstOrCreate(['industry' => $industry]);
        }

        foreach (SapProduct::defaults() as $product) {
            SapProduct::query()->firstOrCreate(['sap_product' => $product]);
        }

        foreach (AccountManager::defaults() as $accountManager) {
            AccountManager::query()->firstOrCreate(['account_manager' => $accountManager]);
        }

        // Seed initial Assign FC values
        $this->call(AssignFcSeeder::class);

        SystemSetting::query()->firstOrCreate([], SystemSetting::defaults());
        SupportContactInfo::query()->firstOrCreate([], SupportContactInfo::defaults());
    }
}
