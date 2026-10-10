<?php

namespace App\Models;

use App\Services\MediaService;
use App\Services\UrlService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $casts = ['content'=>'array','publish_time'=>'datetime','active'=>'boolean'];
    protected $fillable = ['slug','lang_locale','user_id','title','excerpt','featured_media_id','content','active','publish_time'];

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            $content = $article->content ?? [];
            $content['seo'] = $article->seoWithDefaults();
            $article->content = $content;
        });
    }

    /**
     * Doplní og:title, og:description a og:image z článku, pokud nebyly vyplněny ručně.
     * Hodnoty doplněné automaticky si pamatujeme v og_auto, aby se při změně článku
     * aktualizovaly, zatímco ručně upravené hodnoty zůstanou beze změny.
     */
    public function seoWithDefaults(): array
    {
        $seo = (array) ($this->content['seo'] ?? []);
        $auto = (array) ($seo['og_auto'] ?? []);

        $title = Str::limit($this->plain_title, 60, '');
        $description = Str::limit(trim(strip_tags((string) $this->excerpt)), 157, '...');

        $defaults = [
            'title' => $title,
            'description' => $description,
            'og_title' => $title,
            'og_desc' => $description,
            'og_image' => $this->featured_media_id,
        ];

        foreach ($defaults as $key => $value) {
            $current = $seo[$key] ?? null;
            $isEmpty = $current === null || $current === '' || $current === [];

            if ($isEmpty || (array_key_exists($key, $auto) && $auto[$key] == $current)) {
                $seo[$key] = $value;
                $auto[$key] = $value;
            }
        }

        $seo['og_auto'] = $auto;

        return $seo;
    }

    public function language(): BelongsTo { return $this->belongsTo(Language::class, 'lang_locale', 'locale'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function categories(): BelongsToMany { return $this->belongsToMany(ArticleCategory::class, 'article_category'); }
    public function scopePublished(Builder $query): Builder { return $query->where('active',true)->where(fn(Builder $q)=>$q->whereNull('publish_time')->orWhere('publish_time','<=',now())); }

    public function getUrlAttribute(): string
    {
        $page = Page::active()->where('type','blog')->where('lang_locale',$this->lang_locale)->first();
        $base = trim((string) ($page?->full_slug ?? $page?->slug ?? 'aktuality'), '/');
        $prefix = $this->lang_locale !== UrlService::getDefaultLocale() ? '/'.$this->lang_locale : '';
        return $prefix.'/'.trim($base.'/'.$this->slug, '/');
    }
    public function getPlainTitleAttribute(): string { return trim(strip_tags(html_entity_decode((string) $this->title))); }
    public function getFeaturedImageUrlAttribute(): ?string { return MediaService::getMediaUrl($this->featured_media_id); }
    public function getBannerImageUrlAttribute(): ?string { return $this->featured_image_url; }
}
