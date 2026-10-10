<?php

namespace App\Filament\Resources\CompetitionResource\Pages;

use App\Filament\Resources\CompetitionResource;
use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use Filament\Actions;

class EditCompetition extends EditRecordWithSaveAndBack
{
    protected static string $resource = CompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
