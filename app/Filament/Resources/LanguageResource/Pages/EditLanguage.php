<?php

namespace App\Filament\Resources\LanguageResource\Pages;

use App\Filament\Resources\LanguageResource;
use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use Filament\Actions;

class EditLanguage extends EditRecordWithSaveAndBack
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
