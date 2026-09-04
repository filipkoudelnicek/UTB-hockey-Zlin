<?php

namespace App\Services;

use App\Enums\MatchStatus;
use App\Models\CompetitionSeason;
use App\Models\MatchPlayerStat;
use App\Models\Player;
use Illuminate\Support\Facades\DB;

class PlayerStatisticsService
{
    public function forPlayer(Player $player, ?CompetitionSeason $competitionSeason = null): array
    {
        $query = MatchPlayerStat::query()
            ->join('matches', 'matches.id', '=', 'match_player_stats.match_id')
            ->where('match_player_stats.player_id', $player->id)
            ->where('matches.status', MatchStatus::Finished->value);

        if ($competitionSeason) {
            $query->where(function ($matchQuery) use ($competitionSeason): void {
                $matchQuery->where('matches.competition_season_id', $competitionSeason->id);

                if ($competitionSeason->starts_at && $competitionSeason->ends_at) {
                    $matchQuery->orWhereBetween('matches.played_at', [
                        $competitionSeason->starts_at->copy()->startOfDay(),
                        $competitionSeason->ends_at->copy()->endOfDay(),
                    ]);
                }
            });
        }

        $row = $query->selectRaw('COALESCE(SUM(CASE WHEN match_player_stats.played = 1 THEN 1 ELSE 0 END), 0) as games')
            ->selectRaw('COALESCE(SUM(match_player_stats.goals), 0) as goals')
            ->selectRaw('COALESCE(SUM(match_player_stats.assists), 0) as assists')
            ->selectRaw('COALESCE(SUM(match_player_stats.plus_minus), 0) as plus_minus')
            ->first();

        return [
            'games' => (int) ($row->games ?? 0),
            'goals' => (int) ($row->goals ?? 0),
            'assists' => (int) ($row->assists ?? 0),
            'points' => (int) ($row->goals ?? 0) + (int) ($row->assists ?? 0),
            'plus_minus' => (int) ($row->plus_minus ?? 0),
        ];
    }
}
