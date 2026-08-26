<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $casts = [
        'content' => 'array',
        'publish_time' => 'datetime',
    ];

    protected $fillable = [
        'slug',
        'lang_locale',
        'user_id',
        'title',
        'content',
        'active',
        'publish_time',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_locale', 'locale');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: pouze aktivní a publikované články.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('active', true)
            ->where(function (Builder $q) {
                $q->whereNull('publish_time')
                  ->orWhere('publish_time', '<=', now());
            });
    }

    /**
     * Absolute URL for this article.
     * Blog-page slug je cachován per-locale staticky po dobu requestu.
     */
    public function getUrlAttribute(): string
    {
        static $blogSlugCache = [];

        if (!isset($blogSlugCache[$this->lang_locale])) {
            try {
                $blogPage = Page::where('type', 'blog')
                    ->where('lang_locale', $this->lang_locale)
                    ->first();
                $blogSlugCache[$this->lang_locale] = $blogPage?->full_slug ?? 'blog';
            } catch (\Exception $e) {
                $blogSlugCache[$this->lang_locale] = 'blog';
            }
        }

        $defaultLocale = \App\Services\UrlService::getDefaultLocale();
        $prefix = ($this->lang_locale !== $defaultLocale) ? '/' . $this->lang_locale : '';

        return $prefix . '/' . $blogSlugCache[$this->lang_locale] . '/' . $this->slug;
    }
}
