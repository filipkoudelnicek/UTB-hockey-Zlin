<?php

namespace App\Filament\Modules\PageTypes;

use App\Filament\Forms\Components\HighlightedTextInput;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ClubPageType
{
    public static function getSchema(): array
    {
        return [
            Section::make('Úvodní banner')->schema([
                TextInput::make('content.hero.eyebrow')->label('Krátký text nad nadpisem')->required(),
                HighlightedTextInput::make('content.hero.heading')->label('Nadpis')->legacy('content.hero.title', 'content.hero.accent')->required(),
                CuratorPicker::make('content.hero.image')->label('Pozadí hero'),
            ])->description('Nadpis a pozadí úvodní části klubu.')->icon(Heroicon::OutlinedPhoto)->iconColor('primary')->columns(2),
            Section::make('Náš příběh')->schema([
                TextInput::make('content.story.eyebrow')->label('Krátký text nad nadpisem')->required(),
                HighlightedTextInput::make('content.story.heading')->label('Nadpis')->legacy('content.story.title', 'content.story.accent')->required(),
                Textarea::make('content.story.lead')->label('Úvodní text')->rows(4)->required()->columnSpanFull(),
                Textarea::make('content.story.text')->label('Navazující text')->rows(5)->required()->columnSpanFull(),
                CuratorPicker::make('content.story.image')->label('Obrázek příběhu')->required(),
            ])->description('Hlavní vyprávění o klubu a ilustrační obrázek.')->icon(Heroicon::OutlinedBookOpen)->iconColor('primary')->columns(2),
            Section::make('Milníky')->schema([
                TextInput::make('content.milestones_eyebrow')->label('Krátký text nad nadpisem')->required(),
                TextInput::make('content.milestones_title')->label('Nadpis')->required(),
                Repeater::make('content.milestones')->label('Milníky')->schema([
                    TextInput::make('year')->label('Rok / období')->required(),
                    TextInput::make('title')->label('Nadpis')->required(),
                    Textarea::make('description')->label('Popis')->rows(3)->required()->columnSpanFull(),
                ])->columns(2)->maxItems(4)->reorderable()->columnSpanFull(),
            ])->description('Časová osa důležitých událostí klubu.')->icon(Heroicon::OutlinedClock)->iconColor('primary')->columns(2),
            Section::make('Hodnoty')->schema([
                TextInput::make('content.values_eyebrow')->label('Krátký text nad nadpisem')->required(),
                TextInput::make('content.values_title')->label('Nadpis')->required(),
                Repeater::make('content.values')->label('Hodnoty klubu')->schema([
                    TextInput::make('title')->label('Název')->required(),
                    Textarea::make('text')->label('Text')->rows(3)->required()->columnSpanFull(),
                ])->columns(2)->reorderable()->columnSpanFull(),
            ])->description('Hodnoty, které se zobrazují jako samostatné bloky na webu.')->icon(Heroicon::OutlinedSparkles)->iconColor('primary')->columns(2),
            Section::make('Vedení klubu')->schema([
                TextInput::make('content.leadership.eyebrow')->label('Krátký text nad nadpisem')->required(),
                TextInput::make('content.leadership.title')->label('Nadpis')->required(),
                Repeater::make('content.leadership.people')->label('Lidé ve vedení')->schema([
                    TextInput::make('name')->label('Jméno')->required(),
                    TextInput::make('position')->label('Pozice')->required(),
                    TextInput::make('email')->label('E-mail')->email(),
                    CuratorPicker::make('photo')->label('Fotka')->required(),
                ])->columns(2)->reorderable()->columnSpanFull(),
            ])->description('Lidé vedení klubu a jejich kontaktní údaje.')->icon(Heroicon::OutlinedUserGroup)->iconColor('primary')->columns(2),
        ];
    }
}
