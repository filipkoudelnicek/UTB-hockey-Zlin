<?php

namespace App\Models;

use App\Enums\CaptainRole;
use App\Enums\PlayerPosition;
use App\Services\MediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'slug', 'portrait_media_id', 'date_of_birth', 'height', 'weight',
        'stick_side', 'faculty', 'bio', 'profile_heading', 'quote', 'video_media_id', 'seo_title', 'seo_description', 'seo_og_media_id',
        'jersey_number', 'position', 'captain_role', 'is_active', 'source', 'external_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'position' => PlayerPosition::class,
        'captain_role' => CaptainRole::class,
        'is_active' => 'boolean',
    ];

    public function matchStats(): HasMany { return $this->hasMany(MatchPlayerStat::class); }
    public function scopeActive(Builder $query): Builder { return $query->where('is_active', true); }
    public function getFullNameAttribute(): string { return trim($this->first_name.' '.$this->last_name); }
    public function getPortraitUrlAttribute(): ?string { return MediaService::getMediaUrl($this->portrait_media_id); }
    public function getVideoUrlAttribute(): ?string { return MediaService::getMediaUrl($this->video_media_id); }
    public function getSeoOgImageUrlAttribute(): ?string { return MediaService::getMediaFullUrl($this->seo_og_media_id); }
    public function getQuoteAttributionAttribute(): string
    {
        return match ($this->captain_role) {
            CaptainRole::Captain => "{$this->full_name}, kapitán",
            CaptainRole::Assistant => "{$this->full_name}, asistent kapitána",
            default => $this->full_name,
        };
    }
}
