<?php

namespace App\View\Components;

use App\Models\Article;
use App\Models\Navigation;
use App\Services\ArticleService;
use App\Services\LanguageService;
use App\Services\PageService;
use App\Services\NavigationService;
use App\Services\UrlService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    public array $navItems;
    public string $currentLocale;
    public string $homepageUrl;
    public $latestArticles;

    public function __construct()
    {
        $this->currentLocale = app()->getLocale();
        $this->homepageUrl   = PageService::getRelativeUrlByType('homepage', $this->currentLocale, UrlService::getHomepageUrl($this->currentLocale));

        $nav = Navigation::getByHandle('footer', $this->currentLocale);
        $this->navItems = NavigationService::resolveItems($nav?->items ?? []);

        $currentLanguage      = LanguageService::getCurrentLanguage();
        $this->latestArticles = ArticleService::getLatestArticles(2, $currentLanguage?->locale ?? $this->currentLocale);
    }

    public function getArticleUrl(Article $article): string
    {
        return ArticleService::getArticleUrl($article);
    }

    public function render(): View
    {
        return view('components.footer.footer', [
            'getArticleUrl' => fn (Article $article) => $this->getArticleUrl($article),
        ]);
    }
}
