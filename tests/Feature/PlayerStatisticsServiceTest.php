<?php

namespace Tests\Feature;

use App\Enums\MatchStatus;
use App\Enums\MatchType;
use App\Models\Competition;
use App\Models\CompetitionSeason;
use App\Models\GameMatch;
use App\Models\MatchPlayerStat;
use App\Models\Player;
use App\Models\Team;
use App\Services\PlayerStatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerStatisticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_competition_round_stats_include_friendlies_in_its_date_range(): void
    {
        $competition=Competition::create(['name'=>'Liga','slug'=>'liga']);
        $cs=CompetitionSeason::create(['competition_id'=>$competition->id,'name'=>'Liga 26/27','status'=>'active','starts_at'=>'2026-08-01','ends_at'=>'2027-05-31']);
        $club=Team::create(['name'=>'Club','slug'=>'club','is_active'=>true]);
        $opponent=Team::create(['name'=>'Opponent','slug'=>'opponent','is_active'=>true]);
        $player=Player::create(['first_name'=>'Jakub','last_name'=>'Dvořák','slug'=>'jakub-dvorak','is_active'=>true]);
        $cs->teams()->sync([$club->id,$opponent->id]);

        $league=GameMatch::create(['competition_season_id'=>$cs->id,'match_type'=>MatchType::League,'played_at'=>'2026-09-01 18:00:00','home_team_id'=>$club->id,'away_team_id'=>$opponent->id,'status'=>MatchStatus::Finished,'home_score'=>4,'away_score'=>2]);
        $friendly=GameMatch::create(['competition_season_id'=>null,'match_type'=>MatchType::Friendly,'played_at'=>'2026-09-03 18:00:00','home_team_id'=>$club->id,'away_team_id'=>$opponent->id,'status'=>MatchStatus::Finished,'home_score'=>3,'away_score'=>1]);

        MatchPlayerStat::create(['match_id'=>$league->id,'player_id'=>$player->id,'team_id'=>$club->id,'played'=>true,'goals'=>1,'assists'=>2,'plus_minus'=>2]);
        MatchPlayerStat::create(['match_id'=>$friendly->id,'player_id'=>$player->id,'team_id'=>$club->id,'played'=>true,'goals'=>2,'assists'=>0,'plus_minus'=>1]);

        $service=app(PlayerStatisticsService::class);
        $roundStats=$service->forPlayer($player,$cs);

        $this->assertSame(2,$roundStats['games']);
        $this->assertSame(3,$roundStats['goals']);
        $this->assertSame(5,$roundStats['points']);

        $playerRows = Player::query()->withAggregatedMatchStats()->get();

        $this->assertCount(1, $playerRows);
        $this->assertSame(1, Player::query()->withAggregatedMatchStats()->count());
        $this->assertSame(2, (int) $playerRows->first()->games);
        $this->assertSame(3, (int) $playerRows->first()->goals);
        $this->assertSame(2, (int) $playerRows->first()->assists);
        $this->assertSame(3, (int) $playerRows->first()->plus_minus);

        $nextSeason = CompetitionSeason::create([
            'competition_id' => $competition->id,
            'name' => 'Liga 27/28',
            'status' => 'upcoming',
            'starts_at' => '2027-08-01',
            'ends_at' => '2028-05-31',
        ]);
        $nextSeasonMatch = GameMatch::create([
            'competition_season_id' => $nextSeason->id,
            'match_type' => MatchType::League,
            'played_at' => '2027-09-01 18:00:00',
            'home_team_id' => $club->id,
            'away_team_id' => $opponent->id,
            'status' => MatchStatus::Finished,
            'home_score' => 3,
            'away_score' => 2,
        ]);
        MatchPlayerStat::create([
            'match_id' => $nextSeasonMatch->id,
            'player_id' => $player->id,
            'team_id' => $club->id,
            'played' => true,
            'goals' => 3,
            'assists' => 1,
            'plus_minus' => 1,
        ]);

        $allSeasonRows = Player::query()->withAggregatedMatchStats()->get();

        $this->assertCount(1, $allSeasonRows);
        $this->assertSame(3, (int) $allSeasonRows->first()->games);
        $this->assertSame(6, (int) $allSeasonRows->first()->goals);
        $this->assertSame(3, (int) $allSeasonRows->first()->assists);
        $this->assertSame(4, (int) $allSeasonRows->first()->plus_minus);

        $seasonRows = Player::query()
            ->withAggregatedMatchStats()
            ->where('competition_seasons.id', $nextSeason->id)
            ->get();

        $this->assertCount(1, $seasonRows);
        $this->assertSame(1, (int) $seasonRows->first()->games);
        $this->assertSame(3, (int) $seasonRows->first()->goals);
        $this->assertSame(1, (int) $seasonRows->first()->assists);
        $this->assertSame(4, (int) $seasonRows->first()->points);
    }
}
