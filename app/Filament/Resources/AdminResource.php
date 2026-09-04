<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

abstract class AdminResource extends Resource
{
    protected static ?string $permissionKey = null;

    protected static function canManage(): bool
    {
        $user = auth()->user();

        return $user instanceof User
            && static::$permissionKey !== null
            && $user->hasPermission(static::$permissionKey);
    }

    public static function canAccess(): bool { return static::canManage(); }
    public static function canViewAny(): bool { return static::canManage(); }
    public static function canCreate(): bool { return static::canManage(); }
    public static function canEdit(Model $record): bool { return static::canManage(); }
    public static function canDelete(Model $record): bool { return static::canManage(); }
    public static function canDeleteAny(): bool { return static::canManage(); }
    public static function canView(Model $record): bool { return static::canManage(); }
}
