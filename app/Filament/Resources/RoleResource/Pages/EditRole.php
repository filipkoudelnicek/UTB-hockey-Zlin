<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\Role;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        if (static::getResource()::canEdit($this->getRecord())) {
            return;
        }

        Notification::make()
            ->title('Nemáte oprávnění upravit tuto roli.')
            ->danger()
            ->send();

        $this->redirect(static::getResource()::getUrl('index'));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();
        $roleIds = $data['manageableRoles'] ?? [];
        $roles = Role::query()->whereIn('id', $roleIds)->get();

        if (! $user || ! $user->canManageRole($this->getRecord())
            || $roles->count() !== count($roleIds)
            || $roles->contains(fn (Role $role): bool => ! $user->canManageRole($role))) {
            Notification::make()
                ->title('Nemáte oprávnění upravit tuto roli nebo nastavit její správu rolí.')
                ->danger()
                ->send();

            throw (new Halt())->rollBackDatabaseTransaction();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->visible(fn (): bool => ! $this->record->is_administrator)];
    }
}
