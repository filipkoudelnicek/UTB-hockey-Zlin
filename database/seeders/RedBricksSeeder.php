<?php

namespace Database\Seeders;

use App\Actions\SynchronizeMatchStatusesAction;
use App\Enums\CaptainRole;
use App\Enums\MatchType;
use App\Enums\PlayerPosition;
use App\Models\Article;
use App\Models\Competition;
use App\Models\CompetitionSeason;
use App\Models\CompetitionStanding;
use App\Models\GameMatch;
use App\Models\MatchPlayerStat;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Setting;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RedBricksSeeder extends Seeder
{
    public function run(): void
    {
        $competition = Competition::updateOrCreate(['slug' => 'univerzitni-hokejova-liga'], [
            'name' => 'Univerzitní hokejová liga', 'short_name' => 'ULLH', 'source' => 'manual',
        ]);
        $competitionSeason = CompetitionSeason::updateOrCreate([
            'competition_id' => $competition->id, 'name' => 'ULLH 2026/2027',
        ], [
            'name' => 'ULLH 2026/2027', 'status' => 'active', 'starts_at' => '2026-09-01', 'ends_at' => '2027-03-31',
            'source' => 'manual',
        ]);

        $teamData = [
            ['UTB RedBricks', 'UTB', 'utb-redbricks', '#6a1b21', '#f47800'],
            ['UK Kings Prague', 'UK', 'uk-kings-prague', '#111827', '#ffffff'],
            ['VUT Cavaliers Brno', 'VUT', 'vut-cavaliers-brno', '#1f2937', '#ef4444'],
            ['Masarykova univerzita', 'MU', 'masarykova-univerzita', '#1d4ed8', '#ffffff'],
            ['Univerzita Hradec Králové', 'UHK', 'univerzita-hradec-kralove', '#2563eb', '#ffffff'],
        ];
        $teams = collect();
        foreach ($teamData as [$name, $short, $slug, $primary, $secondary]) {
            $teams[$short] = Team::updateOrCreate(['slug' => $slug], [
                'name' => $name, 'short_name' => $short, 'primary_color' => $primary, 'secondary_color' => $secondary,
                'is_active' => true, 'source' => 'manual',
            ]);
        }
        $competitionSeason->teams()->sync($teams->values()->mapWithKeys(fn ($team, $i) => [$team->id => ['sort_order' => $i]])->all());
        Setting::set('club_team_id', (string) $teams['UTB']->id, 'sport');

        $psg = Venue::updateOrCreate(['slug' => 'psg-arena'], [
            'name' => 'CCM Aréna', 'address' => 'Březnická 4068', 'city' => 'Zlín', 'latitude' => 49.21677515339962, 'longitude' => 17.66014925582014,
            'map_url' => 'https://www.google.com/maps/search/?api=1&query=CCM+Arena+Zlin', 'source' => 'manual',
        ]);
        $brno = Venue::updateOrCreate(['slug' => 'hala-brno'], ['name' => 'Hokejová hala Brno', 'city' => 'Brno', 'source' => 'manual']);
        $teams['UTB']->update(['home_venue_id' => $psg->id]);
        $teams['VUT']->update(['home_venue_id' => $brno->id]);

        $this->seedMatches($competitionSeason, $teams, $psg, $brno);
        $this->seedStandings($competitionSeason, $teams);
        app(SynchronizeMatchStatusesAction::class)->execute();
        $players = $this->seedPlayers();
        $this->seedPlayerStats($players, $teams['UTB']);
        $this->seedArticles();
        $this->seedClub();
    }

    private function seedMatches(CompetitionSeason $competitionSeason, $teams, Venue $psg, Venue $brno): void
    {
        $matches = [
            ['2026-09-18 19:30:00', 'UTB', 'UHK', null, null, $psg, MatchType::League, $competitionSeason],
            ['2026-09-25 18:00:00', 'VUT', 'UTB', null, null, $brno, MatchType::League, $competitionSeason],
            ['2026-09-11 18:30:00', 'VUT', 'UTB', 2, 5, $brno, MatchType::League, $competitionSeason],
            ['2026-09-04 18:30:00', 'UTB', 'UK', 3, 4, $psg, MatchType::Friendly, null],
            ['2026-08-21 18:00:00', 'UTB', 'MU', 6, 2, $psg, MatchType::League, $competitionSeason],
            ['2026-08-20 18:00:00', 'UTB', 'UK', 4, 3, $psg, MatchType::League, $competitionSeason],
        ];
        foreach ($matches as [$date, $home, $away, $hs, $as, $venue, $type, $comp]) {
            GameMatch::updateOrCreate([
                'played_at'=>$date,'home_team_id'=>$teams[$home]->id,'away_team_id'=>$teams[$away]->id,
            ], [
                'competition_season_id'=>$comp?->id,'match_type'=>$type->value,'venue_id'=>$venue->id,
                'home_score'=>$hs, 'away_score'=>$as, 'source'=>'manual',
            ]);
        }
    }

    private function seedStandings(CompetitionSeason $competitionSeason, $teams): void
    {
        $rows = [
            ['UTB', 2, 0, 6],
            ['UHK', 1, 1, 3],
            ['UK', 1, 1, 3],
            ['VUT', 0, 1, 0],
            ['MU', 0, 3, 0],
        ];

        foreach ($rows as [$team, $wins, $losses, $points]) {
            CompetitionStanding::updateOrCreate([
                'competition_season_id' => $competitionSeason->id,
                'team_id' => $teams[$team]->id,
            ], [
                'wins' => $wins,
                'losses' => $losses,
                'points' => $points,
            ]);
        }
    }

    private function seedPlayers(): array
    {
        $rows = [
            ['Marek','Novotný',30,PlayerPosition::Goalkeeper,24],['Tomáš','Král',31,PlayerPosition::Goalkeeper,22],['Pavel','Horský',35,PlayerPosition::Goalkeeper,21],
            ['David','Veselý',4,PlayerPosition::LeftDefense,23],['Matěj','Černý',7,PlayerPosition::RightDefense,25],['Martin','Jelínek',18,PlayerPosition::LeftDefense,22],['Ondřej','Šimánek',2,PlayerPosition::RightDefense,24],['Adam','Procházka',44,PlayerPosition::LeftDefense,23],['Jan','Sedláček',6,PlayerPosition::RightDefense,21],['Michal','Konečný',27,PlayerPosition::LeftDefense,25],['Vojtěch','Kučera',55,PlayerPosition::RightDefense,22],
            ['Jakub','Dvořák',23,PlayerPosition::Center,24],['Lukáš','Horák',10,PlayerPosition::RightWing,21],['Filip','Blažek',89,PlayerPosition::LeftWing,23],['Petr','Kaděrábek',14,PlayerPosition::Center,22],['Daniel','Marek',11,PlayerPosition::RightWing,24],['Štěpán','Němec',17,PlayerPosition::LeftWing,22],['Radek','Urban',21,PlayerPosition::Center,23],['Dominik','Bartoš',26,PlayerPosition::RightWing,21],['Matyáš','Fiala',71,PlayerPosition::LeftWing,24],['Patrik','Navrátil',81,PlayerPosition::Center,23],['Roman','Holub',91,PlayerPosition::RightWing,25],['Kryštof','Malina',97,PlayerPosition::LeftWing,22],
        ];
        $players=[];
        foreach ($rows as [$first,$last,$number,$position,$age]) {
            $slug=Str::slug($first.' '.$last);
            $player=Player::updateOrCreate(['slug'=>$slug],[
                'first_name'=>$first,'last_name'=>$last,'date_of_birth'=>(2026-$age).'-05-15','height'=>$position===PlayerPosition::Goalkeeper?188:184,
                'weight'=>$position===PlayerPosition::Goalkeeper?86:82,'stick_side'=>'left','faculty'=>'FAME','is_active'=>true,'source'=>'manual',
                'jersey_number'=>$number,'position'=>$position->value,'captain_role'=>$slug==='jakub-dvorak'?CaptainRole::Captain->value:CaptainRole::None->value,
                'bio'=>$slug==='jakub-dvorak'?'Jakub je odchovancem zlínského hokeje a jednou z klíčových postav RedBricks. Na ledě těží z přehledu, důrazu v soubojích a schopnosti strhnout tým v rozhodujících chvílích.':'Hráč UTB RedBricks a člen univerzitního týmu.',
                'profile_heading'=>$slug==='jakub-dvorak'?'Srdcař a lídr.':'Hráč RedBricks.',
                'quote'=>$slug==='jakub-dvorak'?'Nosit céčko je čest. Největší síla RedBricks je v tom, že každý ví, za koho a za co hraje.':null,
            ]);
            $players[$slug]=$player;
        }
        return $players;
    }

    private function seedPlayerStats(array $players, Team $club): void
    {
        $jakub=$players['jakub-dvorak'] ?? null;
        if (!$jakub) return;
        $clubMatches=GameMatch::finished()->where(fn($q)=>$q->where('home_team_id',$club->id)->orWhere('away_team_id',$club->id))->get();
        foreach ($clubMatches as $i=>$match) {
            MatchPlayerStat::updateOrCreate(['match_id'=>$match->id,'player_id'=>$jakub->id,'team_id'=>$club->id],[
                'played'=>true,'goals'=>$i%2===0?1:0,'assists'=>1,'plus_minus'=>$i%2===0?1:0,
            ]);
        }
    }

    private function seedArticles(): void
    {
        $user=User::first();
        $rows=[
            ['vstupujeme-do-nove-sezony','Vstupujeme do nové sezóny. Cíl je jasný','team','2026-09-15','Nová sezóna začíná a RedBricks vstupují do ročníku s jasnými ambicemi.'],
            ['clenska-zakladna-roste','Členská základna roste. Děkujeme!','club','2026-09-12','Komunita kolem univerzitního hokeje ve Zlíně dál roste.'],
            ['vyhra-v-brne','Výhra v Brně a skvělý start','matches','2026-09-09','RedBricks zvládli venkovní utkání v Brně a odvážejí si důležité body.'],
            ['trener-tym-ma-charakter','Trenér: tým má charakter a chuť růst','team','2026-09-02','Před startem soutěže hodnotí hlavní trenér přípravu i cíle týmu.'],
            ['permanentky-jsou-v-prodeji','Permanentky jsou v prodeji','club','2026-08-26','Permanentky na domácí zápasy nové sezóny jsou nyní v prodeji.'],
            ['letni-trenink-zacal','Letní trénink začal. Sezóna se blíží','team','2026-08-14','Tým zahájil společnou přípravu na nový ročník.'],
            ['stadion-se-pripravuje','Stadion se připravuje na nový ročník','club','2026-08-08','CCM Aréna se chystá na další sezonu univerzitního hokeje.'],
        ];
        foreach($rows as [$slug,$title,$category,$date,$excerpt]) Article::updateOrCreate(['slug'=>$slug,'lang_locale'=>'cs'],[
            'user_id'=>$user?->id,'title'=>$title,'excerpt'=>$excerpt,'category'=>$category,'active'=>true,'publish_time'=>$date.' 12:00:00',
            'content'=>['body'=>'<p>'.$excerpt.'</p><p>Sledujte další informace a novinky z klubu UTB RedBricks.</p>'],
        ]);
    }

    private function seedClub(): void
    {
        $partners = [
            'Univerzita Tomáše Bati ve Zlíně',
            'CCM Aréna',
            'Zlín',
        ];

        foreach ($partners as $name) {
            Partner::updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }

}
