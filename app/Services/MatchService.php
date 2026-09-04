<?php

namespace App\Services;

use App\Models\GameMatch;
use App\Models\CompetitionSeason;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MatchService
{
    public function nextForClub(?CompetitionSeason $competitionSeason = null, ?Team $clubTeam = null): ?GameMatch
    {
        $clubTeam ??= Team::club();
        $competitionSeason ??= CompetitionSeason::currentForClub($clubTeam);
        if (!$competitionSeason || !$clubTeam) return null;

        $query = GameMatch::query()->with(['homeTeam','awayTeam','venue','competitionSeason.competition'])
            ->where(fn (Builder $q) => $q->where('home_team_id', $clubTeam->id)->orWhere('away_team_id', $clubTeam->id))
            ->upcoming();

        return $this->withinCompetitionPeriod($query, $competitionSeason)->first();
    }

    /** Nejbližší budoucí zápas klubu bez omezení na konkrétní soutěžní ročník. */
    public function nextUpcomingForClub(?Team $clubTeam = null): ?GameMatch
    {
        $clubTeam ??= Team::club();
        if (! $clubTeam) return null;

        return GameMatch::query()
            ->with(['homeTeam', 'awayTeam', 'venue', 'competitionSeason.competition'])
            ->where(fn (Builder $query) => $query
                ->where('home_team_id', $clubTeam->id)
                ->orWhere('away_team_id', $clubTeam->id))
            ->upcoming()
            ->first();
    }

    public function lastForClub(?Team $clubTeam = null): ?GameMatch
    {
        $clubTeam ??= Team::club();
        if (! $clubTeam) return null;

        $query = GameMatch::query()->with(['homeTeam','awayTeam','venue','competitionSeason.competition','reportArticle'])
            ->where(fn (Builder $q) => $q->where('home_team_id', $clubTeam->id)->orWhere('away_team_id', $clubTeam->id))
            ->past();

        return $query->first();
    }

    public function forCompetitionSeason(CompetitionSeason $competitionSeason): Collection
    {
        $query = GameMatch::query()->with(['homeTeam','awayTeam','venue','competitionSeason.competition','reportArticle'])
            ->orderBy('played_at');

        return $this->withinCompetitionPeriod($query, $competitionSeason)->get();
    }

    private function withinCompetitionPeriod(Builder $query, CompetitionSeason $competitionSeason): Builder
    {
        if ($competitionSeason->starts_at && $competitionSeason->ends_at) {
            return $query->whereBetween('played_at', [
                $competitionSeason->starts_at->copy()->startOfDay(),
                $competitionSeason->ends_at->copy()->endOfDay(),
            ]);
        }

        return $query->where('competition_season_id', $competitionSeason->id);
    }
}
