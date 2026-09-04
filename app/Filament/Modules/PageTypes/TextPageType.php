<?php

namespace App\Filament\Modules\PageTypes;

use App\Filament\Forms\Components\HighlightedTextInput;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class TextPageType
{
    public static function getSchema()
    {
        return [
            Section::make('Úvodní banner')
                ->description('Texty a pozadí úvodní části stránky.')
                ->icon(Heroicon::OutlinedPhoto)
                ->iconColor('primary')
                ->schema([
                    TextInput::make('content.hero.eyebrow')->label('Text nad nadpisem'),
                    HighlightedTextInput::make('content.hero.heading')
                        ->label('Nadpis')
                        ->legacy('content.hero.title', 'content.hero.accent'),
                    CuratorPicker::make('content.hero.image')->label('Pozadí hero'),
                ])
                ->columns(2),
        ];
    }
}
