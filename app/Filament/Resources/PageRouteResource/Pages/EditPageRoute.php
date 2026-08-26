<?php

namespace App\Filament\Resources\PageRouteResource\Pages;

use App\Filament\Resources\PageRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPageRoute extends EditRecord
{
    protected static string $resource = PageRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Smazat'),
        ];
    }
}
