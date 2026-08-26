<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $locale = 'cs';
        $user   = User::first();

        Article::firstOrCreate(
            ['slug' => 'vitejte-na-blogu', 'lang_locale' => $locale],
            [
                'user_id'      => $user?->id,
                'title'        => 'Vítejte na blogu',
                'active'       => true,
                'publish_time' => now(),
                'content'      => json_encode([
                    'perex' => 'Toto je první ukázkový článek vygenerovaný seederem.',
                    'text'  => '<p>Vítejte na našem blogu! Tento článek byl vytvořen automaticky '
                             . 'při inicializaci projektu. Nahraďte jej svým vlastním obsahem.</p>'
                             . '<p>Články spravujete v administraci v sekci <strong>Obsah → Články</strong>.</p>',
                ]),
            ]
        );
    }
}
