<?php

namespace App\Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord as FilamentEditRecord;

abstract class EditRecordWithSaveAndBack extends FilamentEditRecord
{
    protected bool $shouldReturnToListAfterSave = false;

    protected function getFormActions(): array
    {
        $formActions = parent::getFormActions();

        return [
            ...array_slice($formActions, 0, 1),
            Action::make('saveAndBack')
                ->label('Uložit a jít zpět')
                ->color('gray')
                ->action(fn () => $this->saveAndBack()),
            ...array_slice($formActions, 1),
        ];
    }

    public function saveAndBack(): void
    {
        $this->shouldReturnToListAfterSave = true;

        try {
            $this->save();
        } finally {
            $this->shouldReturnToListAfterSave = false;
        }
    }

    protected function getRedirectUrl(): ?string
    {
        if ($this->shouldReturnToListAfterSave) {
            return static::getResource()::getUrl('index');
        }

        return parent::getRedirectUrl();
    }
}
