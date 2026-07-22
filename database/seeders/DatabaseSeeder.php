<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
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
            'Client Management',
            'Contact Support',
            'Dashboard Access',
            'Knowledge Base',
            'Reports',
            'SLA & Performance',
            'System Settings',
            'Ticket Management',
            'User Registration',
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
                'permissions' => ['announcements', 'contact-support', 'knowledge-base', 'ticket-management'],
            ],
            'consultant' => [
                'name' => 'Consultant',
                'permissions' => ['analytics', 'client-management', 'dashboard-access', 'knowledge-base', 'reports', 'sla-performance', 'ticket-management'],
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

        SystemSetting::query()->firstOrCreate([], SystemSetting::defaults());
        SupportContactInfo::query()->firstOrCreate([], SupportContactInfo::defaults());
    }
}
