<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ArticleService;
use App\Models\Article;

class ArticleList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /**
     * Získá URL pro konkrétní článek se správným jazykovým prefixem
     */
    public function getArticleUrl(Article $article): string
    {
        return ArticleService::getArticleUrl($article);
    }

    public function render()
    {
        $articles = ArticleService::getPaginatedActiveArticlesInCurrentLanguage(6);
        
        return view('livewire.article-list', [
            'articles' => $articles,
        ]);
    }
}
