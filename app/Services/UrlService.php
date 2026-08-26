<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Article;
use App\Models\Language;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UrlService
{
    protected static $skipSlugs = ['admin', 'livewire'];
    
    /**
     * Získá výchozí jazyk — čte z DB, fallback na config APP_LOCALE.
     * Výsledek je cachován navždy a invalidován při změně Language záznamu.
     */
    public static function getDefaultLocale(): string
    {
        return Cache::rememberForever('url_service.default_locale', function () {
            try {
                if (Schema::hasTable('languages')) {
                    $default = Language::where('is_default', true)->first();
                    if ($default) {
                        return $default->locale;
                    }
                    // Fallback: první aktivní jazyk
                    $first = Language::where('active', true)->first();
                    if ($first) {
                        return $first->locale;
                    }
                }
            } catch (\Exception $e) {
                // ignoruj
            }

            return config('app.locale', 'cs');
        });
    }
    
    /**
     * Získání slugu z URL
     */
    public static function getSlugFromUrl(string $url, bool $getLastPart = false): ?string
    {
        $defaultLocale = self::getDefaultLocale();
        
        if ($url === '/') {
            return $url;
        }

        if ($url === '/' . $defaultLocale) {
            return '/';
        }

        $urlArr = explode('/', ltrim($url, '/'));
        $mainSlug = $url;

        if (isset($urlArr[0])) {
            $mainSlug = $urlArr[0];
            
            $locales = self::getLocales();
            if (in_array($mainSlug, $locales)) {
                if (isset($urlArr[1])) {
                    $mainSlug = $urlArr[1];
                } else {
                    return '/';
                }
            }
        }

        if (in_array($mainSlug, self::$skipSlugs)) {
            return null;
        }

        if ($getLastPart) {
            $mainSlug = $urlArr[count($urlArr) - 1];
            $slugArr = explode('?', $mainSlug);
            $mainSlug = $slugArr[0];
        }

        $mainSlug = explode('?', $mainSlug);
        return $mainSlug[0];
    }

    /**
     * Získá seznam aktivních jazyků.
     * Výsledek je cachován navždy a invalidován při změně Language záznamu.
     */
    public static function getLanguages()
    {
        return Cache::rememberForever('url_service.languages', function () {
            try {
                if (Schema::hasTable('languages')) {
                    return Language::where('active', true)->get();
                }
            } catch (\Exception $e) {
                // ignoruj
            }

            return collect([]);
        });
    }

    /**
     * Invaliduje cachované jazyky — voláno z Language modelu po každé změně.
     */
    public static function clearLanguageCache(): void
    {
        Cache::forget('url_service.default_locale');
        Cache::forget('url_service.languages');
    }

    /**
     * Generuje URL pro domovskou stránku
     */
    public static function getHomepageUrl(?string $locale = null): string
    {
        $defaultLocale = self::getDefaultLocale();
        
        if (!$locale || $locale === $defaultLocale) {
            return url('/');
        }
        
        return url('/' . $locale);
    }

    /**
     * Vrací pole s lokalizacemi pro použití v routách
     */
    public static function getLocales(): array
    {
        try {
            if (Schema::hasTable('languages')) {
                return self::getLanguages()->pluck('locale')->toArray();
            }
            return [self::getDefaultLocale()];
        } catch (\Exception $e) {
            return [self::getDefaultLocale()];
        }
    }

    /**
     * Sestaví Sitemap objekt (pro dynamický response i zápis do souboru)
     */
    public static function buildSitemap(): Sitemap
    {
        $sitemap = Sitemap::create();
        $languages = self::getLanguages();
        $defaultLocale = self::getDefaultLocale();

        $sitemap->add(Url::create(self::getHomepageUrl()));

        foreach ($languages as $language) {
            if ($language->locale !== $defaultLocale) {
                $sitemap->add(Url::create(self::getHomepageUrl($language->locale)));
            }
        }

        PageService::getActivePages()
            ->each(function ($page) use ($sitemap) {
                if ($page->type === 'homepage') return;
                $sitemap->add(Url::create(PageService::getPageUrl($page)));
            });

        ArticleService::getActiveArticles()
            ->each(function ($article) use ($sitemap) {
                $sitemap->add(Url::create(ArticleService::getArticleUrl($article)));
            });

        return $sitemap;
    }

    /**
     * Zapíše sitemapu do public/sitemap.xml (voláno z admin panelu)
     */
    public static function generateSitemap(): string
    {
        try {
            if (!Schema::hasTable('languages') || !Schema::hasTable('pages') || !Schema::hasTable('articles')) {
                return 'Některé z potřebných tabulek (languages, pages, articles) ještě neexistují.';
            }

            self::buildSitemap()->writeToFile(public_path('sitemap.xml'));

            return 'Sitemap byl úspěšně vygenerován.';
        } catch (\Exception $e) {
            return 'Chyba při generování sitemap: ' . $e->getMessage();
        }
    }
}