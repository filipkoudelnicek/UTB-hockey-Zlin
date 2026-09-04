<?php

namespace App\Observers;

use App\Models\Page;
use App\Models\PageRoute;
use App\Models\PageType;
use Illuminate\Support\Facades\Cache;

/**
 * Additive consistency layer for the existing Route Builder.
 * When marketing changes a PageRoute, dependent Page.full_slug values are
 * refreshed without changing the PageRoute core model itself.
 */
class PageRouteObserver
{
    public function saved(PageRoute $route): void
    {
        $pairs = [
            [(int) $route->page_type_id, (string) $route->lang_locale],
            [(int) $route->getOriginal('page_type_id'), (string) $route->getOriginal('lang_locale')],
        ];

        foreach (array_unique($pairs, SORT_REGULAR) as [$pageTypeId, $locale]) {
            $this->refreshPages($pageTypeId, $locale);
        }
    }

    public function deleted(PageRoute $route): void
    {
        $this->refreshPages((int) $route->page_type_id, (string) $route->lang_locale);
    }

    private function refreshPages(int $pageTypeId, string $locale): void
    {
        PageRoute::clearCache();

        if (! $pageTypeId || $locale === '') {
            return;
        }

        Cache::forget("page_routes.type.{$pageTypeId}.{$locale}");

        $handle = PageType::query()->whereKey($pageTypeId)->value('handle');
        if (! $handle) {
            return;
        }

        Page::query()
            ->where('type', $handle)
            ->where('lang_locale', $locale)
            ->get()
            ->each(function (Page $page): void {
                $page->full_slug = $page->computeFullSlug();
                $page->saveQuietly();
            });
    }
}
