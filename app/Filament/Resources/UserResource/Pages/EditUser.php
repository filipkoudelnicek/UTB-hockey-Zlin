<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Role;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    private ?int $selectedRoleId = null;

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
            ->title('Nemáte oprávnění upravit tohoto uživatele.')
            ->danger()
            ->send();

        $this->redirect(static::getResource()::getUrl('index'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['role_id'] = $this->getRecord()->roles()->value('roles.id');

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();
        $role = Role::query()->find($data['role_id'] ?? null);

        if (! $user
            || ! $user->canManageUser($this->getRecord())
            || ! $role
            || ! $user->canAssignRoleTo($this->getRecord(), $role)) {
            Notification::make()
                ->title('Vyberte právě jednu povolenou roli pro tohoto uživatele.')
                ->danger()
                ->send();

            throw (new Halt())->rollBackDatabaseTransaction();
        }

        $this->selectedRoleId = $role->getKey();
        unset($data['role_id']);

        if (filled($data['password'] ?? null) && ! $user->canChangePasswordOf($this->getRecord())) {
            Notification::make()
                ->title('Nemáte oprávnění změnit heslo tohoto uživatele.')
                ->danger()
                ->send();

            throw (new Halt())->rollBackDatabaseTransaction();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->getRecord()->roles()->sync([$this->selectedRoleId]);
    }
}
