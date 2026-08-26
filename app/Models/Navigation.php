<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    protected $fillable = [
        'name',
        'handle',
        'lang_locale',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    /**
     * Fetch a navigation by its handle (e.g. 'main', 'footer').
     */
    public static function getByHandle(string $handle, ?string $locale = null): ?self
    {
        return static::where('handle', $handle)
            ->when($locale, fn($q) => $q->where('lang_locale', $locale))
            ->first();
    }
}
