<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminPermissions;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (AdminPermissions::DEFINITIONS as $key => $definition) {
            Permission::updateOrCreate(['key' => $key], $definition);
        }

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

        $contentEditor = Role::firstOrCreate(['name' => 'Editor obsahu'], [
            'description' => 'Spravuje aktuality, stránky, partnery a menu.',
        ]);
        if ($contentEditor->wasRecentlyCreated) {
            $contentEditor->permissions()->sync(Permission::whereIn('key', [
                'content.articles', 'content.pages', 'content.partners', 'content.menu',
            ])->pluck('id')->all());
        }

        $sportsEditor = Role::firstOrCreate(['name' => 'Sportovní redaktor'], [
            'description' => 'Spravuje zápasy, hráče a sportovní přehledy.',
        ]);
        if ($sportsEditor->wasRecentlyCreated) {
            $sportsEditor->permissions()->sync(Permission::whereIn('key', [
                'sport.matches', 'sport.players', 'sport.settings', 'reports.view',
            ])->pluck('id')->all());
        }

        $admin->manageableRoles()->sync(
            Role::query()
                ->whereKeyNot($admin->getKey())
                ->where('is_administrator', false)
                ->pluck('id')
                ->all(),
        );

        User::query()->first()?->roles()->syncWithoutDetaching([$root->id]);
    }
}
