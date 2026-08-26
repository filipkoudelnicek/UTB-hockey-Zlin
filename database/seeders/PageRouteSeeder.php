<?php

namespace Database\Seeders;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PageController;
use App\Models\Language;
use App\Models\PageRoute;
use App\Models\PageType;
use App\Services\UrlService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PageRouteSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('page_routes')) {
            return;
        }

        $defaultLocale = UrlService::getDefaultLocale();
        $languages     = Language::where('active', true)->get();

        // Fallback — pokud tabulka languages ještě není naplněná
        if ($languages->isEmpty()) {
            $languages = collect([
                (object) ['locale' => $defaultLocale],
            ]);
        }

        // Načti page type ID podle handle
        $typeIds = [];
        if (Schema::hasTable('page_types')) {
            $typeIds = PageType::pluck('id', 'handle')->toArray();
        }

        // Definice všech page-type routes:
        // handle      => page type handle (null = bez vazby)
        // route_name  => suffix názvu route (prefix jazyka se přidá automaticky)
        // path        => URL pattern
        // action      => metoda controlleru
        // controller  => třída controlleru
        // auto        => registrovat jen pokud existují stránky daného typu
        $definitions = [
            [
                'handle'     => 'homepage',
                'route_name' => 'homepage',
                'path'       => '/{slug}',
                'controller' => PageController::class,
                'action'     => 'homepage',
                'auto'       => true,
            ],
            [
                'handle'     => 'text',
                'route_name' => 'page.text',
                'path'       => '/{slug}',
                'controller' => PageController::class,
                'action'     => 'show',
                'auto'       => true,
            ],
            [
                'handle'     => 'blog',
                'route_name' => 'page.blog',
                'path'       => '/{slug}',
                'controller' => PageController::class,
                'action'     => 'show',
                'auto'       => true,
            ],
            [
                'handle'     => 'contact',
                'route_name' => 'page.contact',
                'path'       => '/{slug}',
                'controller' => PageController::class,
                'action'     => 'show',
                'auto'       => true,
            ],
            [
                'handle'     => 'about',
                'route_name' => 'page.about',
                'path'       => '/{slug}',
                'controller' => PageController::class,
                'action'     => 'show',
                'auto'       => true,
            ],
        ];

        foreach ($languages as $language) {
            $locale    = $language->locale;
            $isDefault = $locale === $defaultLocale;
            $prefix    = $isDefault ? '' : $locale . '.';

            foreach ($definitions as $def) {
                $pageTypeId = isset($def['handle']) ? ($typeIds[$def['handle']] ?? null) : null;

                PageRoute::firstOrCreate(
                    ['name' => $prefix . $def['route_name']],
                    [
                        'page_type_id' => $pageTypeId,
                        'path'         => $def['path'],
                        'method'       => 'GET',
                        'controller'   => $def['controller'],
                        'action'       => $def['action'],
                        'lang_locale'  => $locale,
                        'is_active'    => true,
                        'auto_generate'=> $def['auto'],
                    ]
                );
            }

            PageRoute::firstOrCreate(
                ['name' => $prefix . 'article.show'],
                [
                    'page_type_id' => null,
                    'path'         => '/{page:blog}/{articleSlug}',
                    'method'       => 'GET',
                    'controller'   => ArticleController::class,
                    'action'       => 'showArticle',
                    'lang_locale'  => $locale,
                    'is_active'    => true,
                    'auto_generate'=> true,
                ]
            );
        }
    }
}
