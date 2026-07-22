<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        DB::table('permissions')->updateOrInsert(
            ['slug' => 'work-agreement'],
            [
                'name' => 'Work Agreement',
                'category' => 'Work Agreement',
                'description' => 'Allows access to Work Agreement.',
                'updated_at' => $now,
                'created_at' => DB::table('permissions')->where('slug', 'work-agreement')->value('created_at') ?? $now,
            ],
        );

        $permissionId = DB::table('permissions')->where('slug', 'work-agreement')->value('id');
        $roleIds = DB::table('roles')->whereIn('slug', ['admin', 'consultant'])->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('slug', 'work-agreement')->value('id');

        if ($permissionId) {
            DB::table('permission_role')->where('permission_id', $permissionId)->delete();
            DB::table('permissions')->where('id', $permissionId)->delete();
        }
    }
};
