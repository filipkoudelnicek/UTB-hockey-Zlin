<?php

namespace App\Filament\Resources\MatchResource\Pages;

use App\Actions\UpdateMatchResultAction;
use App\Filament\Resources\MatchResource;
use App\Filament\Resources\Pages\EditRecordWithSaveAndBack;
use App\Models\GameMatch;
use Illuminate\Database\Eloquent\Model;

class EditMatch extends EditRecordWithSaveAndBack
{
    protected static string $resource = MatchResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        MatchResource::ensureClubIsInvolved($data);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var GameMatch $record */
        return app(UpdateMatchResultAction::class)->execute($record, $data);
    }
}
