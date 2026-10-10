<?php

namespace App\Filament\Resources\VenueResource\Pages;

use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use App\Filament\Resources\VenueResource;
use Filament\Actions;

class EditVenue extends EditRecordWithSaveAndBack
{
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
