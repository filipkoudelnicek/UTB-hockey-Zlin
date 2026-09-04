<?php

namespace App\Models;

use App\Enums\MatchStatus;
use App\Enums\MatchType;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameMatch extends Model
{
    /** Běžná délka utkání včetně dvou přestávek. */
    public const EXPECTED_DURATION_MINUTES = 150;

    protected $table = 'matches';
    protected $fillable = [
        'competition_season_id', 'match_type', 'played_at', 'venue_id', 'home_team_id', 'away_team_id',
        'status', 'home_score', 'away_score', 'ticket_url', 'report_article_id', 'source', 'external_id',
    ];
    protected $casts = [
        'played_at' => 'datetime',
        'match_type' => MatchType::class,
        'status' => MatchStatus::class,
    ];

    public function competitionSeason(): BelongsTo { return $this->belongsTo(CompetitionSeason::class); }
    public function venue(): BelongsTo { return $this->belongsTo(Venue::class); }
    public function homeTeam(): BelongsTo { return $this->belongsTo(Team::class, 'home_team_id'); }
    public function awayTeam(): BelongsTo { return $this->belongsTo(Team::class, 'away_team_id'); }
    public function reportArticle(): BelongsTo { return $this->belongsTo(Article::class, 'report_article_id'); }
    public function playerStats(): HasMany { return $this->hasMany(MatchPlayerStat::class, 'match_id'); }

    public function scopeFinished(Builder $query): Builder { return $query->where('status', MatchStatus::Finished->value); }
    public function scopeUpcoming(Builder $query): Builder { return $query->where('status', MatchStatus::Scheduled->value)->where('played_at', '>=', now())->orderBy('played_at'); }
    public function scopePast(Builder $query): Builder { return $query->where('status', MatchStatus::Finished->value)->orderByDesc('played_at'); }

    public function automaticStatus(CarbonInterface $now): MatchStatus
    {
        if ($now->lt($this->played_at)) {
            return MatchStatus::Scheduled;
        }

        if ($now->lt($this->played_at->copy()->addMinutes(self::EXPECTED_DURATION_MINUTES))) {
            return MatchStatus::Live;
        }

        return MatchStatus::Finished;
    }

    public function involves(Team|int|null $team): bool
    {
        $id = $team instanceof Team ? $team->getKey() : $team;
        return $id && in_array((int) $id, [(int) $this->home_team_id, (int) $this->away_team_id], true);
    }

    public function opponentFor(Team|int $team): ?Team
    {
        $id = $team instanceof Team ? $team->getKey() : $team;
        return (int) $this->home_team_id === (int) $id ? $this->awayTeam : ((int) $this->away_team_id === (int) $id ? $this->homeTeam : null);
    }
}
