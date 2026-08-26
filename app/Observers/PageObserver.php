<?php

namespace App\Observers;

use App\Models\Page;
use App\Models\PageRoute;
use App\Services\UrlService;
use Illuminate\Support\Facades\Schema;

class PageObserver
{
    /**
     * Before saving: recompute full_slug from parent chain.
     * The current DB value (old full_slug) is still accessible via getOriginal().
     */
    public function saving(Page $page): void
    {
        if (!Schema::hasTable('pages')) {
            return;
        }

        $page->full_slug = $page->computeFullSlug();
    }

    /**
     * After deleting: regenerate sitemap.
     */
    public function deleted(Page $page): void
    {
        PageRoute::clearCache();
        try {
            UrlService::generateSitemap();
        } catch (\Throwable $e) {
            //
        }
    }

    public function saved(Page $page): void
    {
        if (!Schema::hasTable('pages')) {
            return;
        }

        // Invaliduj cache routes při jakékoliv změně stránky
        // (slug, active, type — vše může ovlivnit registraci routes)
        PageRoute::clearCache();

        try {
            UrlService::generateSitemap();
        } catch (\Throwable $e) {
            //
        }
    }
}
