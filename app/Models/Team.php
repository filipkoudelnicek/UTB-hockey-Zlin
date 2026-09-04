<?php

namespace App\Models;

use App\Services\MediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = ['name', 'short_name', 'slug', 'logo_media_id', 'home_venue_id', 'primary_color', 'secondary_color', 'is_active', 'source', 'external_id'];
    protected $casts = ['is_active' => 'boolean'];

    public function competitionSeasons(): BelongsToMany { return $this->belongsToMany(CompetitionSeason::class)->withPivot('sort_order')->withTimestamps(); }
    public function homeVenue(): BelongsTo { return $this->belongsTo(Venue::class, 'home_venue_id'); }
    public function homeMatches(): HasMany { return $this->hasMany(GameMatch::class, 'home_team_id'); }
    public function awayMatches(): HasMany { return $this->hasMany(GameMatch::class, 'away_team_id'); }
    public function scopeActive(Builder $query): Builder { return $query->where('is_active', true); }
    public function getLogoUrlAttribute(): ?string { return MediaService::getMediaUrl($this->logo_media_id); }

    public static function club(): ?self
    {
        $id = Setting::get('club_team_id');
        return $id ? static::find($id) : static::where('slug', 'utb-redbricks')->first();
    }
}
