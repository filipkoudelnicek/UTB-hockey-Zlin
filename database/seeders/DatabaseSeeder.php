<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jazyk musí být první — stránky a články mají FK na languages.locale
        $this->call(LanguageSeeder::class);

        // 2. Admin uživatel — články mají FK na users.id
        $this->call(UserSeeder::class);

        // 3. Typy stránek (PageType)
        $this->call(PageTypeSeeder::class);

        // 4. Page routes (DB záznamy pro dynamické routování)
        $this->call(PageRouteSeeder::class);

        // 5. Ukázkové stránky — PageObserver přepočítá full_slug přes PageRoute
        $this->call(PageSeeder::class);

        // 6. Ukázkový článek — vazba na uživatele a jazyk
        $this->call(ArticleSeeder::class);

        // 7. Navigace sestavená ze stránek (musí být po PageSeeder)
        $this->call(NavigationSeeder::class);

        // 8. Základní nastavení webu
        $this->call(SettingSeeder::class);
    }
}
