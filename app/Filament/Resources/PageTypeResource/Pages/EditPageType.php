<?php

namespace App\Filament\Resources\PageTypeResource\Pages;

use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use App\Filament\Resources\PageTypeResource;
use Filament\Actions;

class EditPageType extends EditRecordWithSaveAndBack
{
    protected static string $resource = PageTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
