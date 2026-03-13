<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $guard = config('auth.defaults.guard');
        $now = now();

        DB::table($tableNames['permissions'])->insert([
            'name' => 'manage redirects',
            'guard_name' => $guard,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $permissionId = DB::table($tableNames['permissions'])
            ->where('name', 'manage redirects')
            ->where('guard_name', $guard)
            ->value('id');

        // Assign to super-admin and admin roles
        $roleIds = DB::table($tableNames['roles'])
            ->whereIn('name', ['super-admin', 'admin'])
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table($tableNames['role_has_permissions'])->insert([
                'permission_id' => $permissionId,
                'role_id' => $roleId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        $guard = config('auth.defaults.guard');

        $permissionId = DB::table($tableNames['permissions'])
            ->where('name', 'manage redirects')
            ->where('guard_name', $guard)
            ->value('id');

        if ($permissionId) {
            DB::table($tableNames['role_has_permissions'])->where('permission_id', $permissionId)->delete();
            DB::table($tableNames['permissions'])->where('id', $permissionId)->delete();
        }
    }
};
