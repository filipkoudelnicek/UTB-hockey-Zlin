<?php

namespace App\Models;

use App\Services\MediaService;
use App\Services\UrlService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $casts = ['content'=>'array','publish_time'=>'datetime','active'=>'boolean'];
    protected $fillable = ['slug','lang_locale','user_id','title','excerpt','category','featured_media_id','content','active','publish_time'];

    public function language(): BelongsTo { return $this->belongsTo(Language::class, 'lang_locale', 'locale'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scopePublished(Builder $query): Builder { return $query->where('active',true)->where(fn(Builder $q)=>$q->whereNull('publish_time')->orWhere('publish_time','<=',now())); }

    public static function categoryOptions(): array
    {
        return [
            'team' => 'A-tým',
            'club' => 'Klub',
            'interviews' => 'Rozhovory',
            'fans' => 'Fanoušci',
        ];
    }

    public static function categoryLabel(?string $category): string
    {
        return static::categoryOptions()[$category] ?? 'Ostatní';
    }

    public static function categoryColor(?string $category): string
    {
        return match ($category) {
            'team' => 'info',
            'club' => 'success',
            'interviews' => 'warning',
            'fans' => 'primary',
            default => 'gray',
        };
    }

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
