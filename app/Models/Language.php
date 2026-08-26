<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'locale',
        'active',
        'is_default',
        'content',
    ];

    protected $casts = [
        'active'     => 'boolean',
        'is_default' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Při uložení: pokud se nastavuje is_default = true, ostatní jazyky se odznačí
        static::saving(function (Language $language) {
            if ($language->is_default) {
                static::where('id', '!=', $language->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });

        // Invalidace URL cache po každé změně jazyka
        static::saved(function () {
            \App\Services\UrlService::clearLanguageCache();
        });

        static::deleted(function () {
            \App\Services\UrlService::clearLanguageCache();
        });
    }
}
