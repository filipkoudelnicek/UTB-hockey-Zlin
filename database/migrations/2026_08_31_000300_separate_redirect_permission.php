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

        foreach (AdminPermissions::DEFINITIONS as $key => $definition) {
            Permission::updateOrCreate(['key' => $key], $definition);
        }

        $redirectPermissionId = Permission::query()
            ->where('key', 'website.redirects')
            ->value('id');

        Role::query()
            ->where('name', 'admin')
            ->first()
            ?->permissions()
            ->detach($redirectPermissionId);

        Role::query()
            ->where('name', 'root')
            ->first()
            ?->permissions()
            ->syncWithoutDetaching([$redirectPermissionId]);
    }

    public function down(): void
    {
        // Role assignments are user data and must not be removed on rollback.
    }
};
