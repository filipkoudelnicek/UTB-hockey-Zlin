<?php

namespace Database\Seeders;

use App\Models\Navigation;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $locale = 'cs';

        // Načti stránky podle slugu
        $pages = Page::where('lang_locale', $locale)
            ->where('active', true)
            ->get()
            ->keyBy('slug');

        // ── Sestavení položek hlavního menu ──────────────────────────────
        $mainItems = [];

        // Domovská stránka — homepage má slug '' takže full_slug je '' nebo '/'
        if ($home = $pages->get('')) {
            $mainItems[] = $this->makePageItem($home, 'Domů');
        }

        // O nás
        if ($about = $pages->get('o-nas')) {
            $mainItems[] = $this->makePageItem($about);
        }

        // Blog
        if ($blog = $pages->get('blog')) {
            $mainItems[] = $this->makePageItem($blog);
        }

        // Kontakt
        if ($contact = $pages->get('kontakt')) {
            $mainItems[] = $this->makePageItem($contact);
        }

        // ── Hlavní navigace ───────────────────────────────────────────────
        Navigation::firstOrCreate(
            ['handle' => 'main'],
            [
                'name'        => 'Hlavní menu',
                'lang_locale' => $locale,
                'items'       => $mainItems,
            ]
        );

        // ── Patičková navigace ────────────────────────────────────────────
        $footerItems = [];

        if ($about = $pages->get('o-nas')) {
            $footerItems[] = $this->makePageItem($about);
        }
        if ($contact = $pages->get('kontakt')) {
            $footerItems[] = $this->makePageItem($contact);
        }

        Navigation::firstOrCreate(
            ['handle' => 'footer'],
            [
                'name'        => 'Patičkové menu',
                'lang_locale' => $locale,
                'items'       => $footerItems,
            ]
        );
    }

    /**
     * Sestaví položku menu ze stránky.
     * URL se odvodí z full_slug (prázdný slug = homepage = '/').
     */
    private function makePageItem(Page $page, ?string $labelOverride = null): array
    {
        $fullSlug = $page->full_slug ?? $page->slug;
        $url      = '/' . ltrim($fullSlug, '/');
        // prázdná cesta = homepage
        if ($url === '//') {
            $url = '/';
        }

        return [
            'id'       => Str::uuid()->toString(),
            'label'    => $labelOverride ?? $page->title,
            'url'      => $url,
            'target'   => '_self',
            'type'     => 'page',
            'model_id' => $page->id,
            'children' => [],
        ];
    }
}
