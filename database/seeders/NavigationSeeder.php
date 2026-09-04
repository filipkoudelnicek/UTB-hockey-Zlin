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
        $order = ['homepage','matches','team','blog','about','contact'];
        $labels = ['homepage'=>'Domů','matches'=>'Zápasy','team'=>'Tým','blog'=>'Aktuality','about'=>'O klubu','contact'=>'Kontakt'];
        $pages = Page::active()->where('lang_locale',$locale)->whereIn('type',$order)->get()->keyBy('type');
        $items=[];
        foreach ($order as $type) {
            if (!$page=$pages->get($type)) continue;
            $items[]=[
                'id'=>Str::uuid()->toString(),'label'=>$labels[$type] ?? $page->title,
                'url'=>$page->url,'target'=>'_self','type'=>'page','model_id'=>$page->id,'children'=>[],
            ];
        }
        Navigation::updateOrCreate(['handle'=>'main','lang_locale'=>$locale],['name'=>'Hlavní menu','items'=>$items]);
        Navigation::updateOrCreate(['handle'=>'footer','lang_locale'=>$locale],['name'=>'Patičkové menu','items'=>$items]);
    }
}
