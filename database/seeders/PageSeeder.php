<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'type' => 'homepage', 'slug' => '/', 'full_slug' => '/', 'title' => 'Domů',
                'content' => [
                    'hero' => ['eyebrow' => 'Ročník', 'title' => 'Hokej, který', 'accent' => 'hřeje.', 'heading' => 'Hokej, který <span data-highlight="accent">hřeje.</span>', 'text' => 'Jsme RedBricks. Jeden tým, jedna univerzita, jedna vášeň pro hru.', 'cta_label' => 'Prohlédnout zápasy', 'image' => null],
                    'matches' => ['eyebrow' => 'Na ledě', 'title' => 'Zápasy', 'all_label' => 'Všechny zápasy', 'next_label' => 'Nejbližší zápas', 'detail_label' => 'Detail zápasu', 'tickets_label' => 'Vstupenky', 'last_label' => 'Minulý zápas', 'no_next' => 'Další zápas zatím není naplánovaný.', 'no_last' => 'Zatím není odehraný žádný zápas.'],
                    'social' => ['eyebrow' => 'Ze sociálních sítí', 'heading' => 'RedBricks <span data-highlight="accent">online.</span>'],
                    'team' => ['eyebrow' => 'NAŠE SESTAVA', 'title' => 'JEDEN TÝM.', 'accent' => 'JEDEN CÍL.', 'heading' => 'JEDEN TÝM. <span data-highlight="accent">JEDEN CÍL.</span>', 'text' => 'Na ledě i mimo něj táhneme za jeden provaz. Poznejte hráče, kteří nosí oranžovo-vínové barvy s hrdostí.', 'cta_label' => 'POZNEJTE NÁŠ TÝM'],
                    'club' => ['eyebrow' => 'UTB RedBricks', 'title' => 'Víc než', 'accent' => 'hokej.', 'heading' => 'Víc než <span data-highlight="accent">hokej.</span>', 'text' => 'RedBricks je hokejový klub studentů Univerzity Tomáše Bati ve Zlíně. Spojujeme sportovní ambice, univerzitní život a komunitu, která drží při sobě.', 'cta_label' => 'O našem klubu', 'stats' => [['value' => '2017', 'label' => 'Rok založení'], ['value' => '40+', 'label' => 'Aktivních hráčů'], ['value' => '1', 'label' => 'Velká rodina']]],
                    'standings' => ['title' => 'Tabulka', 'accent' => 'soutěže', 'heading' => 'Tabulka <span data-highlight="accent">soutěže</span>', 'text' => 'Každý bod se počítá. Sledujte, jak se nám daří v aktuálním ročníku.', 'cta_label' => 'Kompletní tabulka'],
                    'news' => ['eyebrow' => 'Z redakce', 'title' => 'Poslední', 'accent' => 'aktuality', 'heading' => 'Poslední <span data-highlight="accent">aktuality</span>', 'all_label' => 'Všechny novinky', 'read_label' => 'Číst článek'],
                    'partners' => ['title' => 'Partneři klubu'],
                ],
            ],
            [
                'type' => 'matches', 'slug' => 'zapasy', 'full_slug' => 'zapasy', 'title' => 'Zápasy',
                'content' => [
                    'hero' => ['eyebrow' => 'ROČNÍK', 'title' => 'ZÁPASY', 'accent' => '& VÝSLEDKY', 'heading' => 'ZÁPASY <span data-highlight="accent">& VÝSLEDKY</span>', 'image' => null],
                    'filters' => ['all' => 'Všechny', 'upcoming' => 'Nadcházející', 'past' => 'Odehrané'],
                    'labels' => ['upcoming' => 'NADCHÁZEJÍCÍ', 'played' => 'ODEHRÁNO', 'tickets' => 'VSTUPENKY', 'report' => 'REPORT', 'win' => 'VÍTĚZSTVÍ', 'loss' => 'PROHRA', 'empty' => 'Pro tuto sezónu zatím nejsou vložené žádné zápasy.'],
                    'standings' => ['title' => 'AKTUÁLNÍ', 'accent' => 'TABULKA', 'heading' => 'AKTUÁLNÍ <span data-highlight="accent">TABULKA</span>', 'text' => 'Aktuální tabulka vybrané soutěže.'],
                ],
            ],
            [
                'type' => 'team', 'slug' => 'tym', 'full_slug' => 'tym', 'title' => 'Tým',
                'content' => [
                    'hero' => ['eyebrow' => 'A-TÝM', 'title' => 'NAŠE', 'accent' => 'SESTAVA', 'heading' => 'NAŠE <span data-highlight="accent">SESTAVA</span>', 'image' => null],
                    'positions' => ['goalkeepers' => 'BRANKÁŘI', 'defenders' => 'OBRÁNCI', 'forwards' => 'ÚTOČNÍCI', 'empty' => 'Pro tuto pozici zatím nejsou vloženi hráči.'],
                    'leadership' => ['eyebrow' => 'LIDÉ V ZÁZEMÍ', 'title' => 'POZNEJTE VEDENÍ', 'accent' => 'A REALIZAČNÍ TÝM', 'heading' => 'POZNEJTE VEDENÍ <span data-highlight="accent">A REALIZAČNÍ TÝM</span>', 'cta_label' => 'VEDENÍ KLUBU'],
                ],
            ],
            [
                'type' => 'blog', 'slug' => 'aktuality', 'full_slug' => 'aktuality', 'title' => 'Aktuality',
                'content' => [
                    'hero' => ['eyebrow' => 'CO SE DĚJE V KLUBU', 'title' => 'AKTUALITY', 'accent' => '& NOVINKY', 'heading' => 'AKTUALITY <span data-highlight="accent">& NOVINKY</span>', 'image' => null],
                    'list' => ['all_label' => 'Všechny', 'article_label' => 'Číst článek', 'empty' => 'Zatím nejsou publikované žádné aktuality.'],
                ],
            ],
            [
                'type' => 'about', 'slug' => 'klub', 'full_slug' => 'klub', 'title' => 'Klub',
                'content' => [
                    'hero' => ['eyebrow' => 'OD ROKU 2017', 'title' => 'VÍC NEŽ', 'accent' => 'JEN HOKEJ.', 'heading' => 'VÍC NEŽ <span data-highlight="accent">JEN HOKEJ.</span>', 'image' => null],
                    'story' => ['eyebrow' => 'NÁŠ PŘÍBĚH', 'title' => 'ZE ZLÍNA.', 'accent' => 'PRO ZLÍN.', 'heading' => 'ZE ZLÍNA. <span data-highlight="accent">PRO ZLÍN.</span>', 'lead' => 'UTB RedBricks vznikli z jednoduché myšlenky: dát studentům ve Zlíně klub, se kterým se mohou ztotožnit.', 'text' => 'Od prvního tréninku jsme vyrostli v pevnou součást univerzitního života. Spojujeme studenty napříč fakultami, reprezentujeme UTB po celé republice a vytváříme komunitu, která žije hokejem i mimo stadion.', 'image' => null],
                    'milestones_eyebrow' => 'MILNÍKY', 'milestones_title' => 'NAŠE CESTA',
                    'milestones' => [
                        ['year' => '2017', 'title' => 'ZALOŽENÍ KLUBU', 'description' => 'Vzniká univerzitní hokejový tým UTB RedBricks.'],
                        ['year' => '2019', 'title' => 'PRVNÍ VELKÉ ZÁPASY', 'description' => 'Klub se stává pevnou součástí univerzitního sportu ve Zlíně.'],
                        ['year' => '2022', 'title' => 'RŮST KOMUNITY', 'description' => 'Rozšiřujeme tým, realizační skupinu i fanouškovskou základnu.'],
                        ['year' => '2026', 'title' => 'DALŠÍ KAPITOLA', 'description' => 'RedBricks pokračují s ambicí růst sportovně i komunitně.'],
                    ],
                    'values_eyebrow' => 'CO NÁS DRŽÍ POHROMADĚ', 'values_title' => 'NAŠE HODNOTY',
                    'values' => [
                        ['title' => 'TÝM', 'text' => 'Na ledě i mimo něj táhneme za jeden provaz.'],
                        ['title' => 'HRDOST', 'text' => 'Reprezentujeme UTB, Zlín a komunitu, která stojí za námi.'],
                        ['title' => 'ENERGIE', 'text' => 'Do každého zápasu i projektu dáváme maximum.'],
                    ],
                    'leadership' => [
                        'eyebrow' => 'LIDÉ ZA TÝMEM',
                        'title' => 'VEDENÍ KLUBU',
                        'people' => [
                            ['name' => 'Petr Malina', 'position' => 'Prezident klubu', 'email' => 'petr@utbhockey.cz', 'photo' => null],
                            ['name' => 'Tereza Kubíková', 'position' => 'Generální manažerka', 'email' => 'tereza@utbhockey.cz', 'photo' => null],
                            ['name' => 'Radek Vlček', 'position' => 'Hlavní trenér', 'email' => 'radek@utbhockey.cz', 'photo' => null],
                            ['name' => 'Klára Nováková', 'position' => 'Marketing & komunikace', 'email' => 'klara@utbhockey.cz', 'photo' => null],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'contact', 'slug' => 'kontakt', 'full_slug' => 'kontakt', 'title' => 'Kontakt',
                'content' => [
                    'hero' => ['eyebrow' => 'JSME TU PRO VÁS', 'title' => 'OZVĚTE', 'accent' => 'SE NÁM.', 'heading' => 'OZVĚTE <span data-highlight="accent">SE NÁM.</span>', 'image' => null],
                    'contact' => ['eyebrow' => 'KONTAKTNÍ ÚDAJE', 'title' => 'UTB', 'accent' => 'REDBRICKS', 'heading' => 'UTB <span data-highlight="accent">REDBRICKS</span>', 'general_label' => 'OBECNÉ DOTAZY', 'marketing_label' => 'MARKETING & PARTNEŘI', 'venue_label' => 'DOMÁCÍ STADION', 'hours_label' => 'ÚŘEDNÍ HODINY'],
                    'map' => ['title' => 'CCM Aréna', 'address' => 'Březnická 4068, Zlín', 'link_label' => 'OTEVŘÍT V MAPÁCH', 'link_url' => 'https://www.google.com/maps/search/?api=1&query=CCM+Arena+Zlin', 'latitude' => 49.21677515339962, 'longitude' => 17.66014925582014],
                    'faq' => ['eyebrow' => 'NEJČASTĚJŠÍ DOTAZY', 'title' => 'CO VÁS ZAJÍMÁ', 'items' => [
                        ['question' => 'Kde koupím vstupenky?', 'answer' => 'Online přes klubový web nebo na pokladně PSG areny hodinu před utkáním. Studentům UTB je po předložení platné studentské karty připravena zvýhodněná vstupenka.'],
                        ['question' => 'Mohu se přidat k týmu?', 'answer' => 'Nábor probíhá před začátkem každého semestru. Napište nám na info@utbhockey.cz a pošleme vám termín tréninku. Vítáme studenty všech fakult i pokročilé začátečníky.'],
                        ['question' => 'Jak se stát partnerem?', 'answer' => 'Ozvěte se na marketing@utbhockey.cz. Připravíme nabídku spolupráce na míru – od logotypu na dresu až po prezentaci na domácích zápasech.'],
                        ['question' => 'Jak probíhá domácí zápas?', 'answer' => 'Brány PSG arény se otevírají hodinu před vhazováním. K dispozici jsou občerstvení, fanshop i možnost sezení v krytých tribunách.'],
                        ['question' => 'Kde parkovat u arény?', 'answer' => 'U PSG arény je k dispozici bezplatné parkování na přilehlém parkovišti. V den větších akcí doporučujeme využít MHD.'],
                        ['question' => 'Můžeme vás sledovat online?', 'answer' => 'Sledujte nás na Instagramu a Facebooku – zveřejňujeme výsledky, fotky ze zápasů i videozáznamy.'],
                    ]],
                ],
            ],
        ];

        foreach ($pages as $page) {
            $record = Page::firstOrNew([
                'type' => $page['type'],
                'lang_locale' => 'cs',
            ]);

            $existingContent = is_array($record->content) ? $record->content : [];

            if ($page['type'] === 'contact') {
                $map = is_array($existingContent['map'] ?? null) ? $existingContent['map'] : [];

                if (($map['title'] ?? null) === 'PSG ARENA') {
                    $map['title'] = 'CCM Aréna';
                }

                if (($map['link_url'] ?? null) === 'https://www.google.com/maps/search/?api=1&query=PSG+arena+Zlin') {
                    $map['link_url'] = 'https://www.google.com/maps/search/?api=1&query=CCM+Arena+Zlin';
                }

                if (abs((float) ($map['latitude'] ?? 0) - 49.2218) < 0.000001 && abs((float) ($map['longitude'] ?? 0) - 17.6577) < 0.000001) {
                    $map['latitude'] = 49.21677515339962;
                    $map['longitude'] = 17.66014925582014;
                }

                unset($map['embed_url']);
                $existingContent['map'] = $map;
            }

            if ($page['type'] === 'homepage' && blank(data_get($existingContent, 'news.read_label'))) {
                data_set($existingContent, 'news.read_label', 'Číst článek');
            }

            // Seeding must add newly introduced fixed fields without wiping content
            // that an editor has already changed in Filament. Existing values win.
            $record->fill([
                'slug' => $page['slug'],
                'full_slug' => $page['full_slug'],
                'title' => $page['title'],
                'lang_locale' => 'cs',
                'active' => $record->exists ? $record->active : true,
                'content' => array_replace_recursive(
                    $page['content'],
                    $existingContent,
                ),
            ])->save();
        }
    }
}
