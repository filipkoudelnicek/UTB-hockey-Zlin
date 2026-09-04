<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyPermissions();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function isRoot(): bool
    {
        return $this->roles()->where('name', 'root')->exists();
    }

    public function hierarchyLevel(): int
    {
        return (int) $this->roles()->get()->max(fn (Role $role): int => $role->hierarchyLevel());
    }

    public function canManageUser(self $target): bool
    {
        if ($this->is($target)) {
            return true;
        }

        return $target->roles()->get()->every(fn (Role $role): bool => $this->canManageRole($role));
    }

    public function canAssignRoleTo(self $target, Role $role): bool
    {
        if ($this->is($target)) {
            return $role->hierarchyLevel() <= $this->hierarchyLevel();
        }

        return $this->canAssignRole($role);
    }

    public function canChangePasswordOf(self $target): bool
    {
        if ($this->is($target)) {
            return false;
        }

        if ($target->roles()->where('is_administrator', true)->exists()) {
            return false;
        }

        return $this->canManageUser($target);
    }

    public function canAssignRole(Role $role): bool
    {
        return $this->canManageRole($role);
    }

    public function canManageRole(Role $target): bool
    {
        if ($target->is_administrator) {
            return false;
        }

        if ($this->roles()->where('is_administrator', true)->exists()) {
            return true;
        }

        return $this->roles()->whereHas('manageableRoles', fn ($query) => $query->whereKey($target->getKey()))->exists();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->where(fn ($query) => $query
                ->where('is_administrator', true)
                ->orWhereHas('permissions', fn ($permissions) => $permissions->where('key', $permission)))
            ->exists();
    }

    public function hasAnyPermissions(): bool
    {
        return $this->roles()
            ->where(fn ($query) => $query
                ->where('is_administrator', true)
                ->orWhereHas('permissions'))
            ->exists();
    }
}
