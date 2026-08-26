<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Vytvoří ukázkové stránky pro každý základní typ.
     * Pořadí záleží: homepage musí vzniknout před ostatními slug-based stránkami,
     * protože PageObserver při každém save() přepočítá full_slug přes PageRoute.
     */
    public function run(): void
    {
        $locale = 'cs';

        $pages = [
            // ── Homepage ─────────────────────────────────────────────────
            // Slug je prázdný řetězec — route má path '/', full_slug bude ''.
            [
                'slug'        => '',
                'lang_locale' => $locale,
                'type'        => 'homepage',
                'title'       => 'Domovská stránka',
                'active'      => true,
                'content'     => [
                    'title'  => 'Vítejte na našem webu',
                    'text'   => '<p>Krátký uvítací text na domovské stránce.</p>',
                    'button' => ['label' => 'Více informací', 'url' => '/o-nas', 'target' => '_self'],
                ],
            ],

            // ── Blog přehled ─────────────────────────────────────────────
            [
                'slug'        => 'blog',
                'lang_locale' => $locale,
                'type'        => 'blog',
                'title'       => 'Blog',
                'active'      => true,
                'content'     => [
                    'title' => 'Náš blog',
                    'text'  => '<p>Přečtěte si naše nejnovější články.</p>',
                ],
            ],

            // ── Kontakt ──────────────────────────────────────────────────
            [
                'slug'        => 'kontakt',
                'lang_locale' => $locale,
                'type'        => 'contact',
                'title'       => 'Kontakt',
                'active'      => true,
                'content'     => [
                    'title' => 'Kontaktujte nás',
                    'text'  => '<p>Rádi zodpovíme vaše dotazy.</p>',
                ],
            ],

            // ── O nás ─────────────────────────────────────────────────────
            [
                'slug'        => 'o-nas',
                'lang_locale' => $locale,
                'type'        => 'about',
                'title'       => 'O nás',
                'active'      => true,
                'content'     => [
                    'title' => 'Kdo jsme',
                    'text'  => '<p>Jsme tým odborníků připravených pomoci.</p>',
                ],
            ],

            // ── Textová stránka — ukázka ──────────────────────────────────
            [
                'slug'        => 'o-webu',
                'lang_locale' => $locale,
                'type'        => 'text',
                'title'       => 'O webu',
                'active'      => true,
                'content'     => [
                    'text' => '<p>Tato stránka je ukázkový obsah vygenerovaný seederem.</p>',
                ],
            ],
        ];

        foreach ($pages as $data) {
            $data['content'] = json_encode($data['content']);

            Page::firstOrCreate(
                ['slug' => $data['slug'], 'lang_locale' => $data['lang_locale']],
                $data
            );
        }
    }
}
