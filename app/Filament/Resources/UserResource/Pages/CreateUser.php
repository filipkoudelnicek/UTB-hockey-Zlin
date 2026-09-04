<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Role;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private ?int $selectedRoleId = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->selectedRoleId = $this->ensureAssignableRole($data['role_id'] ?? null);
        unset($data['role_id']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->getRecord()->roles()->sync([$this->selectedRoleId]);
    }

    private function ensureAssignableRole(mixed $roleId): int
    {
        $user = auth()->user();
        $role = Role::query()->find($roleId);

        if (! $user || ! $role || ! $user->canAssignRole($role)) {
            Notification::make()
                ->title('Vyberte právě jednu roli, kterou smíte přiřadit.')
                ->danger()
                ->send();

            throw (new Halt())->rollBackDatabaseTransaction();
        }

        return $role->getKey();
    }
}
