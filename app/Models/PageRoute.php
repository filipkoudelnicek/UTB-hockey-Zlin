<?php

namespace App\Models;

use App\Services\UrlService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionMethod;

class PageRoute extends Model
{
    protected $fillable = [
        'page_type_id',
        'name',
        'path',
        'method',
        'controller',
        'action',
        'template',
        'lang_locale',
        'is_active',
        'auto_generate',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'auto_generate' => 'boolean',
    ];

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function pageType(): BelongsTo
    {
        return $this->belongsTo(PageType::class);
    }

    // ──────────────────────────────────────────────
    // Boot — cache invalidace
    // ──────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }

    public static function clearCache(): void
    {
        Cache::forget('page_routes.all');
    }

    // ──────────────────────────────────────────────
    // Route loading — voláno z web.php
    // ──────────────────────────────────────────────

    /**
     * Načte všechny aktivní routes z DB a zaregistruje je do Laravel routeru.
     */
    public static function loadRoutes(): void
    {
        $routes = Cache::rememberForever('page_routes.all', function () {
            return static::where('is_active', true)
                ->get();
        });

        $defaultLocale = UrlService::getDefaultLocale();

        foreach ($routes as $route) {
            try {
                // auto_generate = true: zaregistruj pouze pokud existují stránky daného typu+jazyka
                if ($route->auto_generate && $route->page_type_id) {
                    $locale = $route->lang_locale ?? $defaultLocale;
                    $hasPages = \Illuminate\Support\Facades\DB::table('pages')
                        ->join('page_types', 'pages.type', '=', 'page_types.handle')
                        ->where('page_types.id', $route->page_type_id)
                        ->where('pages.lang_locale', $locale)
                        ->where('pages.active', true)
                        ->exists();

                    if (!$hasPages) {
                        continue; // žádné stránky tohoto typu = route se do Laravelu nepřidá
                    }
                }

                $locale  = $route->lang_locale;
                $prefix  = ($locale && $locale !== $defaultLocale) ? '/' . $locale : '';
                $path    = $prefix . $route->path;

                // Přeložit {page:handle} placeholdery na aktuální slug dané stránky
                // Např. {page:blog} → slug stránky s typem "blog" v daném jazyce
                if (preg_match_all('/\{page:([a-z0-9_-]+)\}/i', $path, $matches)) {
                    foreach ($matches[1] as $i => $handle) {
                        try {
                            $pageSlug = \Illuminate\Support\Facades\DB::table('pages')
                                ->join('page_types', 'pages.type', '=', 'page_types.handle')
                                ->where('page_types.handle', $handle)
                                ->where('pages.lang_locale', $locale ?? $defaultLocale)
                                ->where('pages.active', true)
                                ->value('pages.slug');
                        } catch (\Throwable $e) {
                            $pageSlug = null;
                        }
                        $path = str_replace($matches[0][$i], $pageSlug ?? $handle, $path);
                    }
                }

                // Normalize double slashes
                $path = preg_replace('#/{2,}#', '/', $path) ?: '/';

                // Handler
                if ($route->controller && $route->action) {
                    $handler = [$route->controller, $route->action];
                } else {
                    continue; // route bez controlleru přeskočíme
                }

                $registered = Route::{strtolower($route->method)}($path, $handler)
                    ->name($route->name);

                $registered->defaults('locale', $locale ?? $defaultLocale);

                // Pokud path obsahuje {slug}, zaregistruj navíc statickou '/' route
                // pro stránky tohoto typu, které mají prázdný full_slug (slug = '/')
                if (str_contains($path, '{slug}') && $route->page_type_id) {
                    $hasRootPage = \Illuminate\Support\Facades\DB::table('pages')
                        ->join('page_types', 'pages.type', '=', 'page_types.handle')
                        ->where('page_types.id', $route->page_type_id)
                        ->where('pages.lang_locale', $locale ?? $defaultLocale)
                        ->where('pages.active', true)
                        ->where(fn ($q) => $q->where('pages.full_slug', '')->orWhere('pages.full_slug', '/'))
                        ->exists();

                    if ($hasRootPage) {
                        $rootPath = ($locale && $locale !== $defaultLocale) ? '/' . $locale : '/';
                        try {
                            Route::{strtolower($route->method)}($rootPath, $handler)
                                ->name($route->name . '.root')
                                ->defaults('locale', $locale ?? $defaultLocale);
                        } catch (\Throwable) {}
                    }
                }

            } catch (\Throwable $e) {
                // Skipneme chybnou route — nebrání spuštění aplikace
                \Illuminate\Support\Facades\Log::warning("PageRoute [{$route->name}] se nepodařilo zaregistrovat: " . $e->getMessage());
            }
        }
    }

    // ──────────────────────────────────────────────
    // Full-slug computation (pro Page model)
    // ──────────────────────────────────────────────

    /**
     * Vrátí aktivní PageRoute pro daný page_type_id a jazyk z cache.
     */
    public static function activeForTypeAndLocale(int $pageTypeId, string $locale): ?self
    {
        return Cache::rememberForever(
            "page_routes.type.{$pageTypeId}.{$locale}",
            fn () => static::where('page_type_id', $pageTypeId)
                ->where('lang_locale', $locale)
                ->where('is_active', true)
                ->first()
        );
    }

    /**
     * Z path vzoru vypočítá full_slug pro danou stránku.
     * Např. path=/sluzby/{slug}, page->slug=web-design → "sluzby/web-design"
     */
    public function resolveFullSlug(Page $page): string
    {
        if (!str_contains($this->path, '{slug}')) {
            // Statická cesta (např. '/') — vrátíme ji bez lomítka na začátku
            return ltrim($this->path, '/');
        }

        // Očisti slug od případných lomítek aby nevzniklo //
        $cleanSlug = trim($page->slug, '/');
        $resolved  = str_replace('{slug}', $cleanSlug, $this->path);

        // Normalizuj případná dvojitá lomítka a odstraň úvodní /
        $resolved = preg_replace('#/{2,}#', '/', $resolved);

        return ltrim($resolved, '/');
    }

    // ──────────────────────────────────────────────
    // Filesystem discovery (pro Filament selecty)
    // ──────────────────────────────────────────────

    /**
     * Vrátí dostupné controllery z app/Http/Controllers/ (FQCN => basename).
     */
    public static function availableControllers(): array
    {
        $controllers = [];

        try {
            $files = File::allFiles(app_path('Http/Controllers'));
            foreach ($files as $file) {
                $class = 'App\\Http\\Controllers\\' . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    $file->getRelativePathname()
                );

                if (class_exists($class) && $class !== \App\Http\Controllers\Controller::class) {
                    $controllers[$class] = class_basename($class);
                }
            }
        } catch (\Throwable $e) {
            //
        }

        asort($controllers);

        return $controllers;
    }

    /**
     * Vrátí veřejné metody daného controlleru via Reflection (pro dynamický select Akcí).
     */
    public static function getAvailableActions(?string $controllerClass): array
    {
        if (!$controllerClass || !class_exists($controllerClass)) {
            return [];
        }

        $actions = [];

        try {
            $reflection = new ReflectionClass($controllerClass);

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                // Pouze metody deklarované přímo v tomto controlleru (ne zděděné)
                if ($method->getDeclaringClass()->getName() !== $controllerClass) {
                    continue;
                }

                // Přeskočit magic metody
                if (str_starts_with($method->getName(), '__')) {
                    continue;
                }

                $actions[$method->getName()] = $method->getName();
            }
        } catch (\Throwable $e) {
            //
        }

        return $actions;
    }

    /**
     * Barva pro badge metody v tabulce.
     */
    public static function methodColor(string $method): string
    {
        return match (strtoupper($method)) {
            'GET'    => 'success',
            'POST'   => 'warning',
            'PUT',
            'PATCH'  => 'info',
            'DELETE' => 'danger',
            default  => 'gray',
        };
    }
}
