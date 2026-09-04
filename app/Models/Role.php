<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'description', 'is_administrator'];

    protected $casts = ['is_administrator' => 'boolean'];

    public function hierarchyLevel(): int
    {
        if ($this->is_administrator) {
            return 3;
        }

        return strtolower($this->name) === 'admin' ? 2 : 1;
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function manageableRoles(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'role_management', 'manager_role_id', 'manageable_role_id');
    }

    public function managerRoles(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'role_management', 'manageable_role_id', 'manager_role_id');
    }
}
