<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $modules = [
            'Dashboard Access', 'Create Ticket', 'My Ticket', 'Knowledge Base', 'Announcements',
            'Clients', 'Client Registration', 'Service Order', 'Service Order List', 'Industry / Business Type',
            'Account Manager', 'Assign FC', 'Package', 'Product Details', 'Product Used', 'Security Level',
            'Ticket Status', 'Analytics', 'Reports', 'SLA & Performance', 'About Us', 'Role Management',
            'System Settings', 'User Registration', 'Contact Support',
        ];

        $now = now();
        foreach ($modules as $module) {
            $slug = Str::slug($module);
            DB::table('permissions')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $module,
                    'category' => $module,
                    'description' => "Allows access to {$module}.",
                    'updated_at' => $now,
                    'created_at' => DB::table('permissions')->where('slug', $slug)->value('created_at') ?? $now,
                ],
            );
        }

        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');
        if ($adminRoleId) {
            $permissionIds = DB::table('permissions')->whereIn('slug', collect($modules)->map(fn (string $module): string => Str::slug($module)))->pluck('id');
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $adminRoleId,
                    'permission_id' => $permissionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Module permissions are retained to avoid removing active role assignments.
    }
};
