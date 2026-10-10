<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use App\Filament\Resources\TeamResource;

class EditTeam extends EditRecordWithSaveAndBack
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [TeamResource::makeDeleteAction()];
    }
}
