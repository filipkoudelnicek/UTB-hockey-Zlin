<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Facades\Schema;

class PageService
{
    /**
     * Najde stejnou stránku v jiném jazyce
     */
    public static function getSamePageInDiffLang(string $currentUrl, string $targetLocale): ?Page
    {
        if (!Schema::hasTable('pages')) {
            return null;
        }
        
        $slug = UrlService::getSlugFromUrl($currentUrl);
        
        if (!$slug) {
            return self::getHomepageForLocale($targetLocale);
        }
        
        if ($slug === '/') {
            return self::getHomepageForLocale($targetLocale);
        }
        
        $currentPage = Page::active()
            ->where('slug', $slug)
            ->first();
            
        if (!$currentPage) {
            return self::getHomepageForLocale($targetLocale);
        }
        
        $targetPage = Page::active()
            ->where('type', $currentPage->type)
            ->where('lang_locale', $targetLocale)
            ->first();
            
        return $targetPage ?? self::getHomepageForLocale($targetLocale);
    }
    
    /**
     * Získá homepage pro daný jazyk
     */
    public static function getHomepageForLocale(string $locale)
    {
        if (!Schema::hasTable('pages')) {
            return null;
        }
        
        return Page::active()
            ->where('type', 'homepage')
            ->where('lang_locale', $locale)
            ->first();
    }
    
    /**
     * Získá stránku blogu pro daný jazyk
     */
    public static function getBlogPage(?string $locale = null): ?Page
    {
        if (!Schema::hasTable('pages')) {
            return null;
        }
        
        $locale = $locale ?? UrlService::getDefaultLocale();
        
        return Page::active()
            ->where('type', 'blog')
            ->where('lang_locale', $locale)
            ->first();
    }

    /**
     * Generuje absolutní URL pro stránku.
     * Logika URL (včetně full_slug) je v Page::getUrlAttribute(), zde přidáme doménu.
     */
    public static function getPageUrl(Page $page): string
    {
        if ($page->type === 'homepage') {
            return UrlService::getHomepageUrl($page->lang_locale);
        }

        return url($page->url);
    }

    /**
     * Získá všechny aktivní stránky
     */
    public static function getActivePages()
    {
        if (!Schema::hasTable('pages')) {
            return collect([]);
        }
        
        try {
            return Page::active()->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

}

