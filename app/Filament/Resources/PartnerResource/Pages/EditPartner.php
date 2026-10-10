<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use App\Filament\Resources\PartnerResource;
use Filament\Actions;

class EditPartner extends EditRecordWithSaveAndBack
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
