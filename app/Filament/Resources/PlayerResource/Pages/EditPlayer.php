<?php

namespace App\Filament\Resources\PlayerResource\Pages;

use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use App\Filament\Resources\PlayerResource;
use Filament\Actions;

class EditPlayer extends EditRecordWithSaveAndBack
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
