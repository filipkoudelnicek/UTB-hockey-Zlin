<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();
        $roleIds = $data['manageableRoles'] ?? [];
        $roles = Role::query()->whereIn('id', $roleIds)->get();

        if (! $user || ! $user->canManageRole($this->getRecord())
            || $roles->count() !== count($roleIds)
            || $roles->contains(fn (Role $role): bool => ! $user->canManageRole($role))) {
            throw new AuthorizationException('Nemáte oprávnění upravit tuto roli nebo nastavit její správu rolí.');
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->visible(fn (): bool => ! $this->record->is_administrator)];
    }
}
