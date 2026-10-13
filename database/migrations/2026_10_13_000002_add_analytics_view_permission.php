<?php

use App\Models\Permission;
use App\Models\Role;
use App\Support\AdminPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            return;
        }

        $permission = Permission::updateOrCreate(
            ['key' => 'analytics.view'],
            AdminPermissions::DEFINITIONS['analytics.view'],
        );

        Role::query()
            ->whereIn('name', ['root', 'admin'])
            ->get()
            ->each(fn (Role $role) => $role->permissions()->syncWithoutDetaching([$permission->getKey()]));
    }

    public function down(): void
    {
        // Role assignments are user data and must not be removed on rollback.
    }
};
