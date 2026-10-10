<?php

namespace App\Filament\Resources\RedirectResource\Pages;

use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use App\Filament\Resources\RedirectResource;
use Filament\Actions;

class EditRedirect extends EditRecordWithSaveAndBack
{
    protected static string $resource = RedirectResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
