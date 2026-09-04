<?php

namespace App\Filament\Modules\PageTypes;

use App\Filament\Forms\Components\HighlightedTextInput;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ContactPageType
{
    public static function getSchema(): array
    {
        return [
            Section::make('Úvodní banner')->schema([
                TextInput::make('content.hero.eyebrow')->label('Krátký text nad nadpisem')->required(),
                HighlightedTextInput::make('content.hero.heading')->label('Nadpis')->legacy('content.hero.title', 'content.hero.accent')->required(),
                CuratorPicker::make('content.hero.image')->label('Pozadí hero'),
            ])->description('Nadpis a pozadí úvodní části kontaktu.')->icon(Heroicon::OutlinedPhoto)->iconColor('primary')->columns(2),
            Section::make('Kontaktní sekce')->schema([
                TextInput::make('content.contact.eyebrow')->label('Krátký text nad nadpisem')->required(),
                HighlightedTextInput::make('content.contact.heading')->label('Nadpis')->legacy('content.contact.title', 'content.contact.accent')->required(),
                TextInput::make('content.contact.general_label')->label('Popisek obecných dotazů')->required(),
                TextInput::make('content.contact.marketing_label')->label('Popisek marketingu')->required(),
                TextInput::make('content.contact.venue_label')->label('Popisek stadionu')->required(),
                TextInput::make('content.contact.hours_label')->label('Popisek úředních hodin')->required(),
            ])->description('Texty vedle kontaktního formuláře a kontaktních údajů.')->icon(Heroicon::OutlinedEnvelope)->iconColor('primary')->columns(2),
            Section::make('Mapa')->schema([
                TextInput::make('content.map.title')->label('Název stadionu v kartě')->default('CCM Aréna')->required(),
                TextInput::make('content.map.address')->label('Adresa v kartě')->required(),
                TextInput::make('content.map.link_label')->label('Text odkazu')->required(),
                TextInput::make('content.map.link_url')->label('Odkaz na mapu')->url()->columnSpanFull(),
                TextInput::make('content.map.latitude')->label('Zeměpisná šířka pinu')->numeric()->default(49.21677515339962)->required(),
                TextInput::make('content.map.longitude')->label('Zeměpisná délka pinu')->numeric()->default(17.66014925582014)->required(),
            ])->description('Adresa, vložená mapa a odkaz pro návštěvníky.')->icon(Heroicon::OutlinedMapPin)->iconColor('primary')->columns(2),
            Section::make('FAQ')->schema([
                TextInput::make('content.faq.eyebrow')->label('Krátký text nad nadpisem')->required(),
                TextInput::make('content.faq.title')->label('Nadpis')->required(),
                Repeater::make('content.faq.items')->label('Otázky a odpovědi')->schema([
                    TextInput::make('question')->label('Otázka')->required()->columnSpanFull(),
                    Textarea::make('answer')->label('Odpověď')->rows(4)->required()->columnSpanFull(),
                ])->reorderable()->columnSpanFull(),
            ])->description('Otázky a odpovědi zobrazené ve spodní části stránky.')->icon(Heroicon::OutlinedQuestionMarkCircle)->iconColor('primary')->columns(2),
        ];
    }
}
