<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionStanding extends Model
{
    protected $fillable = ['competition_season_id', 'team_id', 'wins', 'losses', 'points'];

    public function competitionSeason(): BelongsTo { return $this->belongsTo(CompetitionSeason::class); }
    public function team(): BelongsTo { return $this->belongsTo(Team::class); }

    public function getGamesPlayedAttribute(): int
    {
        return (int) $this->wins + (int) $this->losses;
    }

    public function getRankAttribute(): int
    {
        return static::query()
            ->where('competition_season_id', $this->competition_season_id)
            ->where(function ($query): void {
                $query
                    ->where('points', '>', $this->points)
                    ->orWhere(function ($query): void {
                        $query
                            ->where('points', $this->points)
                            ->where('team_id', '<', $this->team_id);
                    });
            })
            ->count() + 1;
    }
}
