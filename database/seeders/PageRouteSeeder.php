<?php

namespace Database\Seeders;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TeamController;
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
        if (! Schema::hasTable('page_routes')) {
            return;
        }

        $defaultLocale = UrlService::getDefaultLocale();
        $languages = Language::where('active', true)->get();

        if ($languages->isEmpty()) {
            $languages = collect([(object) ['locale' => $defaultLocale]]);
        }

        $typeIds = Schema::hasTable('page_types')
            ? PageType::pluck('id', 'handle')->toArray()
            : [];

        $definitions = [
            ['homepage', 'homepage', '/', PageController::class, 'homepage'],
            ['matches', 'page.matches', '/{slug}', PageController::class, 'show'],
            ['team', 'page.team', '/{slug}', PageController::class, 'show'],
            ['blog', 'page.blog', '/{slug}', PageController::class, 'show'],
            ['about', 'page.about', '/{slug}', PageController::class, 'show'],
            ['contact', 'page.contact', '/{slug}', PageController::class, 'show'],
            ['text', 'page.text', '/{slug}', PageController::class, 'show'],
        ];

        foreach ($languages as $language) {
            $locale = $language->locale;
            $prefix = $locale === $defaultLocale ? '' : $locale . '.';

            foreach ($definitions as [$handle, $routeName, $path, $controller, $action]) {
                PageRoute::updateOrCreate(
                    ['name' => $prefix . $routeName],
                    [
                        'page_type_id' => $typeIds[$handle] ?? null,
                        'path' => $path,
                        'method' => 'GET',
                        'controller' => $controller,
                        'action' => $action,
                        'lang_locale' => $locale,
                        'is_active' => true,
                        'auto_generate' => true,
                    ],
                );
            }

            PageRoute::updateOrCreate(
                ['name' => $prefix . 'article.show'],
                [
                    'page_type_id' => null,
                    'path' => '/{page:blog}/{articleSlug}',
                    'method' => 'GET',
                    'controller' => ArticleController::class,
                    'action' => 'showArticle',
                    'lang_locale' => $locale,
                    'is_active' => true,
                    'auto_generate' => true,
                ],
            );

            PageRoute::updateOrCreate(
                ['name' => $prefix . 'player.show'],
                [
                    'page_type_id' => null,
                    'path' => '/{page:team}/{playerSlug}',
                    'method' => 'GET',
                    'controller' => TeamController::class,
                    'action' => 'showPlayer',
                    'lang_locale' => $locale,
                    'is_active' => true,
                    'auto_generate' => true,
                ],
            );
        }
    }
}
