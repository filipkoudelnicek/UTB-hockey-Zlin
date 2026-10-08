<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSeason;
use App\Models\CompetitionStanding;
use App\Models\GameMatch;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionStandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_games_played_are_derived_from_wins_and_losses(): void
    {
        $standing = CompetitionStanding::make([
            'wins' => 3,
            'losses' => 2,
        ]);

        $this->assertSame(5, $standing->games_played);
    }

    public function test_standings_are_ordered_by_points(): void
    {
        $competition = Competition::create(['name' => 'Liga', 'slug' => 'liga']);
        $season = CompetitionSeason::create([
            'competition_id' => $competition->id,
            'name' => 'Liga 2026/2027',
            'status' => 'active',
        ]);
        $first = Team::create(['name' => 'První', 'slug' => 'prvni', 'is_active' => true]);
        $second = Team::create(['name' => 'Druhý', 'slug' => 'druhy', 'is_active' => true]);

        CompetitionStanding::create([
            'competition_season_id' => $season->id,
            'team_id' => $first->id,
            'wins' => 1,
            'losses' => 1,
            'points' => 3,
        ]);
        CompetitionStanding::create([
            'competition_season_id' => $season->id,
            'team_id' => $second->id,
            'wins' => 2,
            'losses' => 0,
            'points' => 6,
        ]);

        $standings = $season->standings()->get();

        $this->assertSame([$second->id, $first->id], $standings->pluck('team_id')->all());
        $this->assertSame(1, $standings->first()->rank);
        $this->assertSame(2, $standings->last()->rank);
    }

    public function test_standings_use_goal_difference_goals_for_and_team_name_as_tiebreakers(): void
    {
        $competition = Competition::create(['name' => 'Liga', 'slug' => 'liga']);
        $season = CompetitionSeason::create([
            'competition_id' => $competition->id,
            'name' => 'Liga 2026/2027',
            'status' => 'active',
        ]);
        $opponent = Team::create(['name' => 'Soupeř', 'slug' => 'soup', 'is_active' => true]);
        $teamData = [
            'Více bodů' => ['points' => 6, 'wins' => 2, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 100],
            'ŠAVŠ Raccoons' => ['points' => 3, 'wins' => 1, 'losses' => 3, 'goals_for' => 8, 'goals_against' => 2, 'match_goals_for' => 2, 'match_goals_against' => 8],
            'UTB Redbricks' => ['points' => 3, 'wins' => 3, 'losses' => 0, 'goals_for' => 100, 'goals_against' => 0, 'match_goals_for' => 7, 'match_goals_against' => 11],
            'Více branek' => ['points' => 3, 'wins' => 0, 'losses' => 0, 'goals_for' => 10, 'goals_against' => 4],
            'Beta' => ['points' => 3, 'wins' => 2, 'losses' => 2, 'goals_for' => 1, 'goals_against' => 0],
            'Alpha' => ['points' => 3, 'wins' => 1, 'losses' => 1, 'goals_for' => 1, 'goals_against' => 0],
        ];
        $teams = [];

        foreach ($teamData as $name => $data) {
            $team = Team::create([
                'name' => $name,
                'slug' => str($name)->slug(),
                'is_active' => true,
            ]);
            $teams[$name] = $team;

            CompetitionStanding::create([
                'competition_season_id' => $season->id,
                'team_id' => $team->id,
                'wins' => $data['wins'],
                'losses' => $data['losses'],
                'points' => $data['points'],
                'goals_for' => $data['goals_for'],
                'goals_against' => $data['goals_against'],
            ]);

            $isHome = $name !== 'ŠAVŠ Raccoons';
            GameMatch::create([
                'competition_season_id' => $season->id,
                'match_type' => 'league',
                'played_at' => now(),
                'home_team_id' => $isHome ? $team->id : $opponent->id,
                'away_team_id' => $isHome ? $opponent->id : $team->id,
                'status' => 'finished',
                'home_score' => $isHome ? ($data['match_goals_for'] ?? $data['goals_for']) : ($data['match_goals_against'] ?? $data['goals_against']),
                'away_score' => $isHome ? ($data['match_goals_against'] ?? $data['goals_against']) : ($data['match_goals_for'] ?? $data['goals_for']),
            ]);
        }

        GameMatch::create([
            'competition_season_id' => $season->id,
            'match_type' => 'league',
            'played_at' => now(),
            'home_team_id' => $teams['UTB Redbricks']->id,
            'away_team_id' => $opponent->id,
            'status' => 'scheduled',
            'home_score' => 100,
            'away_score' => 0,
        ]);

        $standings = $season->standings()->get();

        $this->assertSame([
            'Více bodů',
            'Více branek',
            'ŠAVŠ Raccoons',
            'Alpha',
            'Beta',
            'UTB Redbricks',
        ], $standings->map(fn (CompetitionStanding $standing) => $standing->team->name)->all());
        $this->assertSame(2, $standings->get(1)->rank);
        $utbStanding = $standings->firstWhere('team_id', $teams['UTB Redbricks']->id);
        $this->assertSame(7, $utbStanding->goals_for);
        $this->assertSame(11, $utbStanding->goals_against);
        $this->assertTrue($utbStanding->isClubTeam());
        $raccoonsStanding = $standings->firstWhere('team_id', $teams['ŠAVŠ Raccoons']->id);
        $this->assertSame(8, $raccoonsStanding->goals_for);
        $this->assertSame(2, $raccoonsStanding->goals_against);
        $this->assertFalse($raccoonsStanding->isClubTeam());
    }

    public function test_non_club_goal_stats_remain_manual_even_when_team_has_finished_league_matches(): void
    {
        $competition = Competition::create(['name' => 'Liga', 'slug' => 'liga']);
        $season = CompetitionSeason::create([
            'competition_id' => $competition->id,
            'name' => 'Liga 2026/2027',
            'status' => 'active',
        ]);
        $team = Team::create(['name' => 'ŠAVŠ Raccoons', 'slug' => 'savš-raccoons', 'is_active' => true]);
        $standing = CompetitionStanding::create([
            'competition_season_id' => $season->id,
            'team_id' => $team->id,
            'wins' => 1,
            'losses' => 1,
            'points' => 3,
            'goals_for' => 8,
            'goals_against' => 2,
        ]);
        $opponent = Team::create(['name' => 'Soupeř', 'slug' => 'soup', 'is_active' => true]);
        GameMatch::create([
            'competition_season_id' => $season->id,
            'match_type' => 'league',
            'played_at' => now(),
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'status' => 'finished',
            'home_score' => 100,
            'away_score' => 0,
        ]);

        $listedStanding = $season->standings()->first();

        $this->assertSame($standing->id, $listedStanding->id);
        $this->assertSame(8, $listedStanding->goals_for);
        $this->assertSame(2, $listedStanding->goals_against);
        $this->assertFalse($listedStanding->isClubTeam());
    }

    public function test_club_standings_calculate_overtime_points_and_game_totals_from_finished_league_matches(): void
    {
        $competition = Competition::create(['name' => 'Liga', 'slug' => 'liga']);
        $season = CompetitionSeason::create([
            'competition_id' => $competition->id,
            'name' => 'Liga 2026/2027',
            'status' => 'active',
        ]);
        $club = Team::create(['name' => 'UTB Redbricks', 'slug' => 'utb-redbricks', 'is_active' => true]);
        $opponent = Team::create(['name' => 'Soupeř', 'slug' => 'soup', 'is_active' => true]);
        $standing = CompetitionStanding::create([
            'competition_season_id' => $season->id,
            'team_id' => $club->id,
            'wins' => 99,
            'losses' => 99,
            'points' => 99,
            'goals_for' => 99,
            'goals_against' => 99,
        ]);

        foreach ([
            [4, 2, false],
            [3, 2, true],
            [1, 2, true],
        ] as [$clubScore, $opponentScore, $wentToOvertime]) {
            GameMatch::create([
                'competition_season_id' => $season->id,
                'match_type' => 'league',
                'played_at' => now(),
                'home_team_id' => $club->id,
                'away_team_id' => $opponent->id,
                'status' => 'finished',
                'home_score' => $clubScore,
                'away_score' => $opponentScore,
                'went_to_overtime' => $wentToOvertime,
                'detail_url' => 'https://example.com/match',
            ]);
        }

        $listedStanding = $season->standings()->first();

        $this->assertSame($standing->id, $listedStanding->id);
        $this->assertSame(3, $listedStanding->games_played);
        $this->assertSame(2, $listedStanding->wins);
        $this->assertSame(1, $listedStanding->losses);
        $this->assertSame(6, $listedStanding->points);
        $this->assertSame(8, $listedStanding->goals_for);
        $this->assertSame(6, $listedStanding->goals_against);
        $overtimeMatch = GameMatch::query()->where('went_to_overtime', true)->firstOrFail();
        $this->assertTrue($overtimeMatch->went_to_overtime);
        $this->assertSame('https://example.com/match', $overtimeMatch->detail_url);
    }
}
