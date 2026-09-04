<?php

namespace App\Filament\Modules\PageTypes;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use App\Filament\Modules\LinkModule;

class AboutPageType
{
    public static function getSchema()
    {
        return [
            Section::make('Obsah stránky')->schema([
                TextInput::make('content.title')->label('Nadpis')->required()->columnSpanFull(),

                CuratorPicker::make('content.image')->label('Obrázek')->required(),

                RichEditor::make('content.text')->label('Text')->columnSpanFull(),

                ...LinkModule::make('content.button', 'Tlačítko'),
            ])
                ->description('Nadpis, text, obrázek a volitelné tlačítko jednoduché stránky.')
                ->icon(Heroicon::OutlinedDocumentText)
                ->iconColor('primary')
                ->columns(2),
        ];
    }
}
