<?php

namespace App\Models;

use App\Enums\CaptainRole;
use App\Enums\MatchStatus;
use App\Enums\PlayerPosition;
use App\Services\MediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Player extends Model
{
    use \App\Models\Concerns\HasAnalyticsViews;

    public const ANALYTICS_TYPE = 'player';

    protected $fillable = [
        'first_name', 'last_name', 'slug', 'portrait_media_id', 'date_of_birth', 'height', 'weight',
        'stick_side', 'faculty', 'bio', 'profile_heading', 'quote', 'video_media_id', 'seo_title', 'seo_description', 'seo_og_title', 'seo_og_description', 'seo_og_media_id',
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

    public function scopeWithAggregatedMatchStats(Builder $query): Builder
    {
        return $query
            ->select([
                'players.id',
                'players.first_name',
                'players.last_name',
            ])
            ->join('match_player_stats', 'match_player_stats.player_id', '=', 'players.id')
            ->join('matches', 'matches.id', '=', 'match_player_stats.match_id')
            ->leftJoin('competition_seasons', 'competition_seasons.id', '=', 'matches.competition_season_id')
            ->leftJoin('competitions', 'competitions.id', '=', 'competition_seasons.competition_id')
            ->where('matches.status', MatchStatus::Finished->value)
            ->selectRaw('SUM(CASE WHEN match_player_stats.played = 1 THEN 1 ELSE 0 END) as games')
            ->selectRaw('COALESCE(SUM(match_player_stats.goals), 0) as goals')
            ->selectRaw('COALESCE(SUM(match_player_stats.assists), 0) as assists')
            ->selectRaw('COALESCE(SUM(match_player_stats.goals + match_player_stats.assists), 0) as points')
            ->selectRaw('COALESCE(SUM(match_player_stats.plus_minus), 0) as plus_minus')
            ->selectRaw("GROUP_CONCAT(DISTINCT competitions.name) as competition_names")
            ->selectRaw("GROUP_CONCAT(DISTINCT competition_seasons.name) as season_names")
            ->groupBy('players.id', 'players.first_name', 'players.last_name');
    }

    public function getFullNameAttribute(): string { return trim($this->first_name.' '.$this->last_name); }
    public function getPortraitUrlAttribute(): ?string { return MediaService::getMediaUrl($this->portrait_media_id); }
    public function getVideoUrlAttribute(): ?string { return MediaService::getMediaUrl($this->video_media_id); }
    public function getSeoOgImageUrlAttribute(): ?string { return MediaService::getMediaFullUrl($this->seo_og_media_id); }

    public function seoWithDefaults(): array
    {
        $description = filled($this->seo_description)
            ? $this->seo_description
            : Str::limit(trim(strip_tags(html_entity_decode((string) $this->bio))), 157, '...');

        return [
            'title' => $this->seo_title,
            'description' => $description,
            'og_title' => filled($this->seo_og_title)
                ? $this->seo_og_title
                : (filled($this->seo_title) ? $this->seo_title : $this->full_name),
            'og_desc' => filled($this->seo_og_description) ? $this->seo_og_description : $description,
            'og_image' => $this->seo_og_media_id ?: $this->portrait_media_id,
        ];
    }

    public function getQuoteAttributionAttribute(): string
    {
        return match ($this->captain_role) {
            CaptainRole::Captain => "{$this->full_name}, kapitán",
            CaptainRole::Assistant => "{$this->full_name}, asistent kapitána",
            default => $this->full_name,
        };
    }
}
