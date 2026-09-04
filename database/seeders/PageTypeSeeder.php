<?php

namespace Database\Seeders;

use App\Models\PageType;
use Illuminate\Database\Seeder;

class PageTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['handle'=>'homepage','label'=>'Úvodní stránka','template'=>'pages.homepage','schema_class'=>'App\\Filament\\Modules\\PageTypes\\HomepagePageType','controller'=>'App\\Http\\Controllers\\PageController'],
            ['handle'=>'matches','label'=>'Zápasy','template'=>'pages.matches','schema_class'=>'App\\Filament\\Modules\\PageTypes\\MatchesPageType','controller'=>'App\\Http\\Controllers\\PageController'],
            ['handle'=>'team','label'=>'Tým','template'=>'pages.team','schema_class'=>'App\\Filament\\Modules\\PageTypes\\TeamPageType','controller'=>'App\\Http\\Controllers\\PageController'],
            ['handle'=>'blog','label'=>'Aktuality – přehled','template'=>'pages.blog','schema_class'=>'App\\Filament\\Modules\\PageTypes\\NewsPageType','controller'=>'App\\Http\\Controllers\\PageController'],
            ['handle'=>'about','label'=>'Klub','template'=>'pages.about','schema_class'=>'App\\Filament\\Modules\\PageTypes\\ClubPageType','controller'=>'App\\Http\\Controllers\\PageController'],
            ['handle'=>'contact','label'=>'Kontakt','template'=>'pages.contact','schema_class'=>'App\\Filament\\Modules\\PageTypes\\ContactPageType','controller'=>'App\\Http\\Controllers\\PageController'],
            ['handle'=>'text','label'=>'Textová stránka','template'=>'pages.text','schema_class'=>'App\\Filament\\Modules\\PageTypes\\TextPageType','controller'=>'App\\Http\\Controllers\\PageController'],
        ];
        foreach ($types as $data) {
            PageType::updateOrCreate(['handle'=>$data['handle']], $data);
        }
    }
}
