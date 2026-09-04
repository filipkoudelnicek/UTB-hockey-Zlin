<?php

namespace App\Filament\Modules\PageTypes;

use App\Filament\Forms\Components\HighlightedTextInput;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class TeamPageType
{
    public static function getSchema(): array
    {
        return [
            Section::make('Úvodní banner')->schema([
                TextInput::make('content.hero.eyebrow')->label('Text před sezónou')->required(),
                HighlightedTextInput::make('content.hero.heading')->label('Nadpis')->legacy('content.hero.title', 'content.hero.accent')->required(),
                CuratorPicker::make('content.hero.image')->label('Pozadí hero'),
            ])->description('Nadpis a pozadí úvodní části stránky týmu.')->icon(Heroicon::OutlinedPhoto)->iconColor('primary')->columns(2),
            Section::make('Soupiska')->schema([
                TextInput::make('content.positions.empty')->label('Text při prázdné pozici')->required()->columnSpanFull(),
            ])->description('Hráči se načítají automaticky podle postu. Názvy pozic jsou pevně dané.')->icon(Heroicon::OutlinedUsers)->iconColor('primary')->columns(2),
            Section::make('Vedení a realizační tým')->schema([
                TextInput::make('content.leadership.eyebrow')->label('Krátký text nad nadpisem')->required(),
                HighlightedTextInput::make('content.leadership.heading')->label('Nadpis')->legacy('content.leadership.title', 'content.leadership.accent')->required()->columnSpanFull(),
                TextInput::make('content.leadership.cta_label')->label('Text tlačítka')->required(),
            ])->description('Texty sekce s vedením a realizačním týmem.')->icon(Heroicon::OutlinedUserGroup)->iconColor('primary')->columns(2),
        ];
    }
}
