<?php

namespace App\Services;

use App\Models\Article;
use App\Services\LanguageService;
use Illuminate\Support\Facades\Schema;

class ArticleService
{
    /**
     * Generuje absolutní URL pro článek.
     * Logika URL je v Article::getUrlAttribute(), zde jen přidáme doménu.
     */
    public static function getArticleUrl(Article $article): string
    {
        return url($article->url);
    }

    /**
     * Získá všechny aktivní a publikované články (publish_time v minulosti nebo null)
     */
    public static function getActiveArticles()
    {
        if (!Schema::hasTable('articles')) {
            return collect([]);
        }
        
        try {
            return Article::published()->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }
    
    /**
     * Získá aktivní a publikované články pro konkrétní jazyk
     */
    public static function getArticlesByLocale(string $locale)
    {
        if (!Schema::hasTable('articles')) {
            return collect([]);
        }
        
        try {
            return Article::published()
                ->where('lang_locale', $locale)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }
    
    /**
     * Získá nejnovější články
     */
    public static function getLatestArticles(int $limit = 2, ?string $locale = null)
    {
        if (!Schema::hasTable('articles')) {
            return collect([]);
        }
        
        try {
            $query = Article::published()
                ->orderBy('created_at', 'desc');
                
            if ($locale) {
                $query->where('lang_locale', $locale);
            }
            
            return $query->limit($limit)->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }
    
    /**
     * Získá aktivní články pro aktuální jazyk se stránkováním
     */
    public static function getPaginatedActiveArticlesInCurrentLanguage(int $perPage = 6)
    {
        if (!Schema::hasTable('articles')) {
            return collect([]);
        }
        
        try {
            $currentLanguage = LanguageService::getCurrentLanguage();
            
            if (!$currentLanguage) {
                return collect([]);
            }
            
            return Article::published()
                ->where('lang_locale', $currentLanguage->locale)
                ->latest()
                ->paginate($perPage);
        } catch (\Exception $e) {
            return collect([]);
        }
    }
}