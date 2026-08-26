<?php

namespace App\Helpers;

use App\Models\Article;
use App\Models\Page;

/**
 * Resolves a stored link data array (from LinkModule) into a usable URL + target.
 *
 * @example
 *   $link = LinkHelper::resolve($page->content['cta'] ?? null);
 *   // ['url' => '/sluzby/detail', 'target' => '_self', 'label' => 'Více']
 *
 *   In Blade:
 *   <a href="{{ $link['url'] }}" target="{{ $link['target'] }}">{{ $link['label'] }}</a>
 */
class LinkHelper
{
    public static function resolve(array|null $data): array
    {
        $empty = ['url' => '#', 'target' => '_self', 'label' => ''];

        if (empty($data) || empty($data['type'])) {
            return $empty;
        }

        $label = $data['label'] ?? '';

        if ($data['type'] === 'external') {
            return [
                'url'    => $data['url'] ?? '#',
                'target' => '_blank',
                'label'  => $label,
            ];
        }

        // Internal
        $id = $data['id'] ?? null;
        if (!$id) {
            return array_merge($empty, ['label' => $label]);
        }

        try {
            if (($data['model'] ?? 'page') === 'article') {
                $record = Article::find($id);
            } else {
                $record = Page::find($id);
            }

            $url = $record?->url ?? '#';
        } catch (\Exception $e) {
            $url = '#';
        }

        return [
            'url'    => $url,
            'target' => '_self',
            'label'  => $label,
        ];
    }

    /**
     * Convenience: returns just the URL string for use in href.
     */
    public static function url(array|null $data): string
    {
        return static::resolve($data)['url'];
    }
}
