<?php

namespace App\Filament\Modules\PageTypes;

use App\Filament\Forms\Components\HighlightedTextInput;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class MatchesPageType
{
    public static function getSchema(): array
    {
        return [
            Section::make('Úvodní banner')->schema([
                TextInput::make('content.hero.eyebrow')->label('Text před sezónou')->required(),
                HighlightedTextInput::make('content.hero.heading')->label('Nadpis')->legacy('content.hero.title', 'content.hero.accent')->required(),
                CuratorPicker::make('content.hero.image')->label('Pozadí hero'),
            ])->description('Nadpis a pozadí úvodní části zápasů.')->icon(Heroicon::OutlinedPhoto)->iconColor('primary')->columns(2),
            Section::make('Tabulka')->schema([
                HighlightedTextInput::make('content.standings.heading')->label('Nadpis')->legacy('content.standings.title', 'content.standings.accent')->required()->columnSpanFull(),
                Textarea::make('content.standings.text')->label('Text')->rows(3)->required()->columnSpanFull(),
            ])->description('Texty vedle tabulky soutěže. Hodnoty tabulky upravujete v Přehledech.')->icon(Heroicon::OutlinedChartBar)->iconColor('primary')->columns(2),
        ];
    }
}
