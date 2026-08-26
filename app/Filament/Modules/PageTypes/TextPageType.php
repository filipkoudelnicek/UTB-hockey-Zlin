<?php

namespace App\Filament\Modules\PageTypes;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Text;

class TextPageType
{
    public static function getSchema()
    {
        return [
            RichEditor::make('content.text')->label('Obsah')->default(''),
        ];
    }
}