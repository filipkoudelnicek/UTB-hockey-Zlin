<?php

namespace App\Filament\Modules\PageTypes;

use App\Filament\Forms\Components\HighlightedTextInput;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class HomepagePageType
{
    public static function getSchema(): array
    {
        return [
            Section::make('Úvodní banner')->schema([
                TextInput::make('content.hero.eyebrow')->label('Text nad nadpisem'),
                HighlightedTextInput::make('content.hero.heading')->label('Hlavní nadpis')->legacy('content.hero.title', 'content.hero.accent'),
                Textarea::make('content.hero.text')->label('Úvodní text')->rows(3)->columnSpanFull(),
                TextInput::make('content.hero.cta_label')->label('Text tlačítka'),
                CuratorPicker::make('content.hero.image')->label('Pozadí hero'),
            ])->description('První velký blok stránky s nadpisem, krátkým textem a tlačítkem.')->icon(Heroicon::OutlinedPhoto)->iconColor('primary')->columns(2),

            Section::make('Zápasy na úvodní stránce')->schema([
                TextInput::make('content.matches.eyebrow')->label('Krátký text nad nadpisem'),
                TextInput::make('content.matches.title')->label('Nadpis'),
                TextInput::make('content.matches.all_label')->label('Odkaz na všechny zápasy'),
                TextInput::make('content.matches.detail_label')->label('Odkaz na detail'),
            ])->description('Nadpis sekce a odkazy k automaticky doplňovaným zápasům.')->icon(Heroicon::OutlinedTrophy)->iconColor('primary')->columns(2),

            Section::make('Sociální sítě')->schema([
                TextInput::make('content.social.eyebrow')->label('Krátký text nad nadpisem'),
                HighlightedTextInput::make('content.social.heading')->label('Nadpis')->legacy('content.social.title', 'content.social.accent'),
            ])->description('Texty nad automaticky načítanými příspěvky ze sociálních sítí.')->icon(Heroicon::OutlinedShare)->iconColor('primary')->columns(2),

            Section::make('Promo týmu')->schema([
                TextInput::make('content.team.eyebrow')->label('Krátký text nad nadpisem'),
                HighlightedTextInput::make('content.team.heading')->label('Nadpis')->legacy('content.team.title', 'content.team.accent'),
                Textarea::make('content.team.text')->label('Text')->rows(3)->columnSpanFull(),
                TextInput::make('content.team.cta_label')->label('Text tlačítka'),
            ])->description('Textový blok vedle automaticky se střídajících aktivních hráčů.')->icon(Heroicon::OutlinedUsers)->iconColor('primary')->columns(2),

            Section::make('Promo klubu')->schema([
                TextInput::make('content.club.eyebrow')->label('Krátký text nad nadpisem'),
                HighlightedTextInput::make('content.club.heading')->label('Nadpis')->legacy('content.club.title', 'content.club.accent'),
                Textarea::make('content.club.text')->label('Text')->rows(4)->columnSpanFull(),
                TextInput::make('content.club.cta_label')->label('Text tlačítka'),
                Repeater::make('content.club.stats')->label('Čísla klubu')->schema([
                    TextInput::make('value')->label('Hodnota'),
                    TextInput::make('label')->label('Popisek'),
                ])->columns(2)->maxItems(3)->reorderable(false)->columnSpanFull(),
            ])->description('Představovací blok klubu včetně nejdůležitějších čísel.')->icon(Heroicon::OutlinedBuildingOffice2)->iconColor('primary')->columns(2),

            Section::make('Tabulka soutěže')->schema([
                HighlightedTextInput::make('content.standings.heading')->label('Nadpis')->legacy('content.standings.title', 'content.standings.accent')->columnSpanFull(),
                Textarea::make('content.standings.text')->label('Text')->rows(3)->columnSpanFull(),
                TextInput::make('content.standings.cta_label')->label('Text odkazu'),
            ])->description('Texty vedle aktuální tabulky soutěže. Hodnoty tabulky upravujete v Přehledech.')->icon(Heroicon::OutlinedChartBar)->iconColor('primary')->columns(2),

            Section::make('Aktuality')->schema([
                TextInput::make('content.news.eyebrow')->label('Krátký text nad nadpisem'),
                HighlightedTextInput::make('content.news.heading')->label('Nadpis')->legacy('content.news.title', 'content.news.accent'),
                TextInput::make('content.news.all_label')->label('Odkaz na všechny aktuality'),
                TextInput::make('content.news.read_label')->label('Text odkazu na článek'),
            ])->description('Nadpisy a odkazy pro automatický výpis posledních aktualit.')->icon(Heroicon::OutlinedNewspaper)->iconColor('primary')->columns(2),

            Section::make('Partneři')->schema([
                TextInput::make('content.partners.title')->label('Nadpis sekce'),
            ])->description('Loga partnerů se načítají automaticky ze sekce Partneři.')->icon(Heroicon::OutlinedHeart)->iconColor('primary'),
        ];
    }
}
