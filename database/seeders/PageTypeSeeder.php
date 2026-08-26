<?php

namespace Database\Seeders;

use App\Models\PageType;
use Illuminate\Database\Seeder;

class PageTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultTypes = [
            [
                'handle'       => 'homepage',
                'label'        => 'Domovská stránka',
                'template'     => 'pages.homepage',
                'schema_class' => 'App\\Filament\\Modules\\PageTypes\\HomepagePageType',
                'controller'   => 'App\\Http\\Controllers\\PageController',
            ],
            [
                'handle'       => 'text',
                'label'        => 'Textová stránka',
                'template'     => 'pages.text',
                'schema_class' => 'App\\Filament\\Modules\\PageTypes\\TextPageType',
                'controller'   => 'App\\Http\\Controllers\\PageController',
            ],
            [
                'handle'       => 'blog',
                'label'        => 'Blog – přehled',
                'template'     => 'pages.blog',
                'schema_class' => 'App\\Filament\\Modules\\PageTypes\\BlogPageType',
                'controller'   => 'App\\Http\\Controllers\\PageController',
            ],
            [
                'handle'       => 'contact',
                'label'        => 'Kontakt',
                'template'     => 'pages.contact',
                'schema_class' => 'App\\Filament\\Modules\\PageTypes\\ContactPageType',
                'controller'   => 'App\\Http\\Controllers\\PageController',
            ],
            [
                'handle'       => 'about',
                'label'        => 'O nás',
                'template'     => 'pages.about',
                'schema_class' => 'App\\Filament\\Modules\\PageTypes\\AboutPageType',
                'controller'   => 'App\\Http\\Controllers\\PageController',
            ],
        ];

        foreach ($defaultTypes as $data) {
            PageType::firstOrCreate(
                ['handle' => $data['handle']],
                $data
            );
        }
    }
}
