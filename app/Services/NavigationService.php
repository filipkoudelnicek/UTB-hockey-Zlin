<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Page;

class NavigationService
{
    /**
     * Keep Menu Manager labels/targets, but resolve model-backed URLs at render
     * time so a changed page slug/full_slug is reflected automatically.
     */
    public static function resolveItems(array $items): array
    {
        return array_map(function (array $item): array {
            $type = $item['type'] ?? 'custom';
            $modelId = isset($item['model_id']) ? (int) $item['model_id'] : null;

            if ($modelId && $type === 'page') {
                if ($page = Page::query()->find($modelId)) {
                    $item['url'] = $page->url;
                }
            } elseif ($modelId && $type === 'article') {
                if ($article = Article::query()->find($modelId)) {
                    $item['url'] = $article->url;
                }
            }

            $item['children'] = self::resolveItems($item['children'] ?? []);

            return $item;
        }, $items);
    }
}
