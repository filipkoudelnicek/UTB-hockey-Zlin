<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageType extends Model
{
    protected $fillable = [
        'handle',
        'label',
        'template',
        'schema_class',
        'controller',
    ];

    /**
     * Načte PageType podle handle z cache.
     */
    public static function findByHandle(string $handle): ?self
    {
        return Cache::rememberForever("page_type.{$handle}", function () use ($handle) {
            return static::where('handle', $handle)->first();
        });
    }

    /**
     * Invaliduje cache daného handle i globálního seznamu.
     */
    public static function clearCache(string $handle): void
    {
        Cache::forget("page_type.{$handle}");
        Cache::forget('page_types.all');
    }

    /**
     * Vrátí všechny typy jako handle => label (pro selecty ve formulářích).
     */
    public static function allAsOptions(): array
    {
        return Cache::rememberForever('page_types.all', function () {
            return static::orderBy('label')->pluck('label', 'handle')->toArray();
        });
    }

    /**
     * Vrátí všechny typy jako id => label (pro FK selecty jako page_type_id).
     */
    public static function allAsIdOptions(): array
    {
        return Cache::rememberForever('page_types.all_by_id', function () {
            return static::orderBy('label')->pluck('label', 'id')->toArray();
        });
    }

    /**
     * Skenuje složku views/pages a vrátí dostupné šablony jako template => template.
     */
    public static function availableTemplates(): array
    {
        $options = [];

        if (!is_dir(resource_path('views/pages'))) {
            return $options;
        }

        foreach (\Illuminate\Support\Facades\File::files(resource_path('views/pages')) as $file) {
            // service-detail.blade.php → pages.service-detail
            $name = str_replace('.blade.php', '', $file->getFilename());
            $key  = 'pages.' . $name;
            $options[$key] = $key;
        }

        ksort($options);
        return $options;
    }

    /**
     * Skenuje složku app/Filament/Modules/PageTypes a vrátí FQCN tříd.
     */
    public static function availableSchemaClasses(): array
    {
        $options = [];
        $dir = app_path('Filament/Modules/PageTypes');

        if (!is_dir($dir)) {
            return $options;
        }

        foreach (\Illuminate\Support\Facades\File::files($dir) as $file) {
            // HomepagePageType.php → App\Filament\Modules\PageTypes\HomepagePageType
            $class = 'App\\Filament\\Modules\\PageTypes\\' . str_replace('.php', '', $file->getFilename());
            $options[$class] = str_replace('.php', '', $file->getFilename());
        }

        ksort($options);
        return $options;
    }

    /**
     * Skenuje složku app/Http/Controllers a vrátí FQCN controllerů.
     */
    public static function availableControllers(): array
    {
        $options = [];
        $dir = app_path('Http/Controllers');

        if (!is_dir($dir)) {
            return $options;
        }

        foreach (\Illuminate\Support\Facades\File::files($dir) as $file) {
            $filename = str_replace('.php', '', $file->getFilename());
            if ($filename === 'Controller') {
                continue; // přeskoč base třídu
            }
            $class = 'App\\Http\\Controllers\\' . $filename;
            $options[$class] = $filename;
        }

        ksort($options);
        return $options;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function (PageType $type) {
            static::clearCache($type->handle);
        });

        static::deleted(function (PageType $type) {
            static::clearCache($type->handle);
        });
    }
}
