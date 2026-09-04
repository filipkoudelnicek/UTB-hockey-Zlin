<?php

namespace App\Filament\Modules\PageTypes;

use App\Filament\Forms\Components\HighlightedTextInput;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class NewsPageType
{
    public static function getSchema(): array
    {
        return [
            Section::make('Úvodní banner')->schema([
                TextInput::make('content.hero.eyebrow')->label('Krátký text nad nadpisem')->required(),
                HighlightedTextInput::make('content.hero.heading')->label('Nadpis')->legacy('content.hero.title', 'content.hero.accent')->required(),
                CuratorPicker::make('content.hero.image')->label('Pozadí hero'),
            ])->description('Nadpis a pozadí úvodní části aktualit.')->icon(Heroicon::OutlinedPhoto)->iconColor('primary')->columns(2),
            Section::make('Výpis aktualit')->schema([
                TextInput::make('content.list.empty')->label('Text při prázdném výpisu')->required()->columnSpanFull(),
            ])->description('Aktuality se načítají automaticky. Můžete upravit pouze zprávu pro prázdný výpis.')->icon(Heroicon::OutlinedNewspaper)->iconColor('primary')->columns(2),
        ];
    }
}
