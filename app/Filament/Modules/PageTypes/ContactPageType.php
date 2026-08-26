<?php

namespace App\Filament\Modules\PageTypes;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Fieldset;

class ContactPageType
{
    public static function getSchema()
    {
        return [
            Fieldset::make('Obsah')->schema([
                TextInput::make('content.title')->label('Nadpis')->columnSpanFull(),
                RichEditor::make('content.text')->label('Text')->columnSpanFull(),
            ]),
        ];
    }
}