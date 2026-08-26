<?php

namespace App\Filament\Modules\PageTypes;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Schemas\Components\Fieldset;
use App\Filament\Modules\LinkModule;

class HomepagePageType
{
    public static function getSchema()
    {
        return [
            Fieldset::make('Obsah')->schema([
                TextInput::make('content.title')->label('Nadpis')->required()->columnSpanFull(),

                CuratorPicker::make('content.image')->label('Obrázek'),

                RichEditor::make('content.text')->label('Text')->columnSpanFull(),

                ...LinkModule::make('content.button', 'Tlačítko'),
            ])->columns(2),
        ];
    }
}