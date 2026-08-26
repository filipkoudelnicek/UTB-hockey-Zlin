<?php

namespace App\Livewire;

use App\Services\ArticleService;
use App\Services\LanguageService;
use Livewire\Component;
use App\Models\Article;
use Illuminate\Support\Str;

class LatestBlogs extends Component
{
    public $latestArticles;
    
    public function mount()
    {
        $currentLanguage = LanguageService::getCurrentLanguage();
        $this->latestArticles = ArticleService::getLatestArticles(2, $currentLanguage->locale);
    }

    /**
     * Získá URL pro konkrétní článek se správným jazykovým prefixem
     */
    public function getArticleUrl(Article $article): string
    {
        return ArticleService::getArticleUrl($article);
    }
    
    /**
     * Získá první odstavec z obsahu a omezí jej na zadanou délku.
     */
    public function getFirstParagraph($content, $limit = 125): string
    {
        if (!$content) {
            return '';
        }
        
        $matches = [];
        preg_match('/<p>(.*?)<\/p>/s', $content, $matches);
        $firstParagraph = isset($matches[1]) ? $matches[1] : strip_tags($content);
        
        return Str::limit(strip_tags($firstParagraph), $limit);
    }
    
    public function render()
    {
        return view('livewire.latest-blogs');
    }
}
