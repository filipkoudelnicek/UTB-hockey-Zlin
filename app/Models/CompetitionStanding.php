<?php

namespace App\Models;

use App\Enums\MatchType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionStanding extends Model
{
    protected $fillable = [
        'competition_season_id',
        'team_id',
        'wins',
        'losses',
        'points',
        'goals_for',
        'goals_against',
    ];

    public function competitionSeason(): BelongsTo
    {
        return $this->belongsTo(CompetitionSeason::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function getGamesPlayedAttribute(): int
    {
        return $this->isClubTeam() && $this->automaticStatsLoaded()
            ? (int) ($this->attributes['automatic_games_played'] ?? 0)
            : (int) $this->wins + (int) $this->losses;
    }

    public function getWinsAttribute(): int
    {
        return $this->isClubTeam() && $this->automaticStatsLoaded()
            ? (int) ($this->attributes['automatic_wins'] ?? 0)
            : (int) ($this->attributes['wins'] ?? 0);
    }

    public function getLossesAttribute(): int
    {
        return $this->isClubTeam() && $this->automaticStatsLoaded()
            ? (int) ($this->attributes['automatic_losses'] ?? 0)
            : (int) ($this->attributes['losses'] ?? 0);
    }

    public function getPointsAttribute(): int
    {
        return $this->isClubTeam() && $this->automaticStatsLoaded()
            ? (int) ($this->attributes['automatic_points'] ?? 0)
            : (int) ($this->attributes['points'] ?? 0);
    }

    public function getGoalsForAttribute(): int
    {
        return $this->isClubTeam() && $this->automaticStatsLoaded()
            ? (int) ($this->attributes['automatic_goals_for'] ?? 0)
            : (int) ($this->attributes['goals_for'] ?? 0);
    }

    public function getGoalsAgainstAttribute(): int
    {
        return $this->isClubTeam() && $this->automaticStatsLoaded()
            ? (int) ($this->attributes['automatic_goals_against'] ?? 0)
            : (int) ($this->attributes['goals_against'] ?? 0);
    }

    private function automaticStatsLoaded(): bool
    {
        return array_key_exists('automatic_points', $this->attributes);
    }

    public function isClubTeam(): bool
    {
        $clubTeamId = Team::club()?->getKey();

        return $clubTeamId !== null && (int) $this->team_id === (int) $clubTeamId;
    }

    public function getRankAttribute(): int
    {
        if (! $this->exists) {
            return 1;
        }

        $rank = static::query()
            ->where('competition_season_id', $this->competition_season_id)
            ->orderForTable()
            ->get()
            ->pluck('id')
            ->search($this->getKey());

        return $rank === false ? 1 : $rank + 1;
    }

    public function scopeOrderForTable(Builder $query): Builder
    {
        return $query
            ->withGoalStats()
            ->orderByTableCriteria();
    }

    public function scopeWithGoalStats(Builder $query): Builder
    {
        $clubTeamId = Team::club()?->getKey();
        $goalsFor = static::goalsQuery(true, $clubTeamId);
        $goalsAgainst = static::goalsQuery(false, $clubTeamId);

        return $query
            ->addSelect('competition_standings.*')
            ->selectSub(static::clubMatchStatsQuery($clubTeamId, 'COUNT(*)'), 'automatic_games_played')
            ->selectSub(static::clubMatchStatsQuery($clubTeamId, static::outcomeCountExpression(true)), 'automatic_wins')
            ->selectSub(static::clubMatchStatsQuery($clubTeamId, static::outcomeCountExpression(false)), 'automatic_losses')
            ->selectSub(static::clubMatchStatsQuery($clubTeamId, static::pointsExpression()), 'automatic_points')
            ->selectSub($goalsFor, 'automatic_goals_for')
            ->selectSub($goalsAgainst, 'automatic_goals_against');
    }

    public function scopeOrderByTableCriteria(Builder $query): Builder
    {
        $clubTeamId = Team::club()?->getKey();
        $points = static::orderValueExpression($clubTeamId, 'automatic_points', 'points');
        $goalsFor = static::orderValueExpression($clubTeamId, 'automatic_goals_for', 'goals_for');
        $goalsAgainst = static::orderValueExpression($clubTeamId, 'automatic_goals_against', 'goals_against');

        return $query
            ->orderByRaw("{$points['sql']} DESC", $points['bindings'])
            ->orderByRaw("({$goalsFor['sql']} - {$goalsAgainst['sql']}) DESC", [...$goalsFor['bindings'], ...$goalsAgainst['bindings']])
            ->orderByRaw("{$goalsFor['sql']} DESC", $goalsFor['bindings'])
            ->orderBy(Team::query()
                ->select('name')
                ->whereColumn('teams.id', 'competition_standings.team_id'))
            ->orderBy('competition_standings.team_id');
    }

    private static function clubMatchStatsQuery(int|string|null $clubTeamId, string $expression): Builder
    {
        $query = static::finishedLeagueMatchesQuery();

        if ($clubTeamId === null) {
            $query->whereRaw('1 = 0');
        } else {
            $query->where('competition_standings.team_id', $clubTeamId);
        }

        return $query->selectRaw("COALESCE({$expression}, 0)");
    }

    private static function finishedLeagueMatchesQuery(): Builder
    {
        return GameMatch::query()
            ->finished()
            ->where('matches.match_type', MatchType::League->value)
            ->whereColumn('matches.competition_season_id', 'competition_standings.competition_season_id')
            ->whereNotNull('matches.home_score')
            ->whereNotNull('matches.away_score')
            ->where(function (Builder $query): void {
                $query
                    ->whereColumn('matches.home_team_id', 'competition_standings.team_id')
                    ->orWhereColumn('matches.away_team_id', 'competition_standings.team_id');
            });
    }

    private static function outcomeCountExpression(bool $wins): string
    {
        $comparison = $wins ? '>' : '<';

        return "SUM(CASE WHEN (matches.home_team_id = competition_standings.team_id AND matches.home_score {$comparison} matches.away_score) OR (matches.away_team_id = competition_standings.team_id AND matches.away_score {$comparison} matches.home_score) THEN 1 ELSE 0 END)";
    }

    private static function pointsExpression(): string
    {
        return 'SUM(CASE '
            .'WHEN matches.home_team_id = competition_standings.team_id AND matches.home_score > matches.away_score THEN CASE WHEN matches.went_to_overtime = 1 THEN 2 ELSE 3 END '
            .'WHEN matches.away_team_id = competition_standings.team_id AND matches.away_score > matches.home_score THEN CASE WHEN matches.went_to_overtime = 1 THEN 2 ELSE 3 END '
            .'WHEN matches.home_team_id = competition_standings.team_id AND matches.home_score < matches.away_score AND matches.went_to_overtime = 1 THEN 1 '
            .'WHEN matches.away_team_id = competition_standings.team_id AND matches.away_score < matches.home_score AND matches.went_to_overtime = 1 THEN 1 '
            .'ELSE 0 END)';
    }

    private static function orderValueExpression(int|string|null $clubTeamId, string $automaticColumn, string $manualColumn): array
    {
        if ($clubTeamId === null) {
            return ['sql' => "COALESCE({$manualColumn}, 0)", 'bindings' => []];
        }

        return [
            'sql' => "CASE WHEN competition_standings.team_id = ? THEN COALESCE({$automaticColumn}, 0) ELSE COALESCE({$manualColumn}, 0) END",
            'bindings' => [$clubTeamId],
        ];
    }

    private static function goalsQuery(bool $for, int|string|null $clubTeamId): Builder
    {
        $scoreForTeam = $for ? 'matches.home_score' : 'matches.away_score';
        $scoreAgainstTeam = $for ? 'matches.away_score' : 'matches.home_score';
        $query = static::finishedLeagueMatchesQuery();

        if ($clubTeamId === null) {
            $query->whereRaw('1 = 0');
        } else {
            $query->where('competition_standings.team_id', $clubTeamId);
        }

        return $query->selectRaw(
            "COALESCE(SUM(CASE WHEN matches.home_team_id = competition_standings.team_id THEN {$scoreForTeam} ELSE {$scoreAgainstTeam} END), 0)"
        );
    }
}
