<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\CompetitionSeason;
use App\Models\CompetitionStanding;
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
}
