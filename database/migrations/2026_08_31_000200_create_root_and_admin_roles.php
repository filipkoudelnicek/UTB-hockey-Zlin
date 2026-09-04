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

        // Rename the original full-access role without losing its user links.
        $root = Role::query()->where('name', 'root')->first()
            ?? Role::query()->where('name', 'Administrátor')->first()
            ?? new Role();

        $root->fill([
            'name' => 'root',
            'description' => 'Úplný přístup do celé administrace.',
            'is_administrator' => true,
        ])->save();
        $root->permissions()->sync(Permission::pluck('id')->all());

        $admin = Role::updateOrCreate(['name' => 'admin'], [
            'description' => 'Přístup do celé administrace kromě Page Routes, jazyků a přesměrování.',
            'is_administrator' => false,
        ]);
        $admin->permissions()->sync(
            Permission::query()
                ->whereNotIn('key', ['website.languages', 'website.page_routes', 'website.redirects'])
                ->pluck('id')
                ->all(),
        );
    }

    public function down(): void
    {
        // Role assignments are user data and must not be removed on rollback.
    }
};
