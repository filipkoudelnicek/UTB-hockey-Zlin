<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\PageRoute;
use App\Services\UrlService;
use Illuminate\Support\Facades\Schema;

class ArticleObserver
{
    public function saving(Article $article): void
    {
        if ($article->publish_time === null) {
            $article->publish_time = now();
        }
    }

    public function saved(Article $article): void
    {
        if (! Schema::hasTable('articles')) {
            return;
        }

        // Invaliduj cache routes — auto_generate routes pro blog závisí na existenci článků
        PageRoute::clearCache();

        try {
            UrlService::generateSitemap();
        } catch (\Throwable $e) {
            //
        }
    }

    public function deleted(Article $article): void
    {
        PageRoute::clearCache();
        try {
            UrlService::generateSitemap();
        } catch (\Throwable $e) {
            //
        }
    }
}
