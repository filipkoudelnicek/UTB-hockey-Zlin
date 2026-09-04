<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    protected $casts = [
        'content' => 'array',
        'active'  => 'boolean',
    ];

    protected $fillable = [
        'slug',
        'full_slug',
        'lang_locale',
        'title',
        'content',
        'type',
        'active',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang_locale', 'locale');
    }

    public function pageType(): BelongsTo
    {
        return $this->belongsTo(PageType::class, 'type', 'handle');
    }

    /**
     * Scope: pouze aktivní stránky.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Vypočte full_slug pomocí aktivní PageRoute pro daný typ+jazyk.
     * Fallback: vrátí $this->slug.
     */
    public function computeFullSlug(): string
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('page_types') &&
                \Illuminate\Support\Facades\Schema::hasTable('page_routes')) {

                $pageType = PageType::findByHandle($this->type);

                if ($pageType) {
                    $route = PageRoute::activeForTypeAndLocale($pageType->id, $this->lang_locale);

                    if ($route) {
                        return $route->resolveFullSlug($this);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignoruj — fallback níže
        }

        return $this->slug;
    }

    /**
     * Absolutní URL stránky (respektuje jazykový prefix).
     */
    public function getUrlAttribute(): string
    {
        $fullSlug      = trim($this->full_slug ?? $this->computeFullSlug(), '/');
        $defaultLocale = \App\Services\UrlService::getDefaultLocale();

        if ($this->lang_locale === $defaultLocale) {
            return '/' . $fullSlug;
        }

        return '/' . $this->lang_locale . '/' . $fullSlug;
    }
}
