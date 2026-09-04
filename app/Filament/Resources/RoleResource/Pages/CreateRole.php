<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $roles = Role::query()->whereIn('id', $data['manageableRoles'] ?? [])->get();

        if (! $user || $roles->count() !== count($data['manageableRoles'] ?? [])
            || $roles->contains(fn (Role $role): bool => ! $user->canManageRole($role))) {
            throw new AuthorizationException('Nemáte oprávnění nastavit jednu ze spravovaných rolí.');
        }

        return $data;
    }
}
