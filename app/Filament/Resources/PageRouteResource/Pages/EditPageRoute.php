<?php

namespace App\Filament\Resources\PageRouteResource\Pages;

use App\Filament\Resources\PageRouteResource;
use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use Filament\Actions;

class EditPageRoute extends EditRecordWithSaveAndBack
{
    protected static string $resource = PageRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Smazat'),
        ];
    }
}
