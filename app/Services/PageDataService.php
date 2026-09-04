<?php

namespace App\Services;

use App\Actions\SynchronizeMatchStatusesAction;
use App\Enums\PlayerPositionCategory;
use App\Integrations\Meta\MetaSocialFeedService;
use App\Models\Article;
use App\Models\CompetitionSeason;
use App\Models\CompetitionStanding;
use App\Models\GameMatch;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Setting;
use App\Models\Team;
use Illuminate\Http\Request;

class PageDataService
{
    public function __construct(
        private readonly MatchService $matchService,
        private readonly PlayerStatisticsService $playerStatistics,
        private readonly SynchronizeMatchStatusesAction $synchronizeMatchStatuses,
        private readonly MetaSocialFeedService $metaSocialFeed,
    ) {}

    /**
     * Add domain data required by a page template without moving content
     * or routing responsibilities out of the existing Page/PageType/PageRoute core.
     */
    public function forPage(Page $page, Request $request): array
    {
        $this->synchronizeMatchStatuses->execute();

        return match ($page->type) {
            'homepage' => $this->homepage($page),
            'matches' => $this->matches($request),
            'team' => $this->team(),
            'blog' => [],
            'about' => $this->club(),
            'contact' => $this->contact(),
            default => [],
        };
    }

    /**
     * Data for an article detail, kept here with the other public page data.
     * The controller remains responsible only for routing and the response.
     */
    public function forArticle(string $articleSlug, string $locale): array
    {
        $this->synchronizeMatchStatuses->execute();

        $article = Article::published()
            ->where('slug', $articleSlug)
            ->where('lang_locale', $locale)
            ->firstOrFail();

        $related = Article::published()
            ->where('lang_locale', $locale)
            ->whereKeyNot($article->id)
            ->orderByDesc('publish_time')
            ->limit(2)
            ->get();

        $nextMatch = $this->matchService->nextUpcomingForClub(Team::club());

        return compact('article', 'related', 'nextMatch');
    }

    /**
     * Data for a player detail, kept alongside the data used by the team page.
     */
    public function forPlayer(string $playerSlug): array
    {
        $this->synchronizeMatchStatuses->execute();

        $player = Player::active()->where('slug', $playerSlug)->firstOrFail();
        $competitionSeason = CompetitionSeason::currentForClub();
        $clubTeam = Team::club();
        $stats = $this->playerStatistics->forPlayer($player, $competitionSeason);
        $otherPlayers = Player::query()
            ->active()
            ->whereKeyNot($player->id)
            ->orderBy('position')
            ->orderBy('jersey_number')
            ->limit(4)
            ->get();

        return compact('player', 'competitionSeason', 'clubTeam', 'stats', 'otherPlayers');
    }

    private function homepage(Page $page): array
    {
        $clubTeam = Team::club();
        [$competitionSeason, $standings] = $this->competitionData($clubTeam);
        $nextMatch = $this->matchService->nextForClub($competitionSeason, $clubTeam);
        $lastMatch = $this->matchService->lastForClub($clubTeam);

        $articles = Article::published()
            ->where('lang_locale', $page->lang_locale)
            ->orderByDesc('publish_time')
            ->limit(3)
            ->get();

        $partners = Partner::active()->orderBy('name')->get();
        $socialLinks = [
            'instagram' => Setting::get('social_instagram'),
            'facebook' => Setting::get('social_facebook'),
        ];
        $socialFeed = $this->metaSocialFeed->homepageFeed();

        $promoPlayers = Player::query()
            ->active()
            ->orderBy('position')
            ->orderBy('jersey_number')
            ->limit(8)
            ->get();

        return compact(
            'clubTeam',
            'nextMatch',
            'lastMatch',
            'competitionSeason',
            'standings',
            'articles',
            'partners',
            'socialLinks',
            'socialFeed',
            'promoPlayers',
        );
    }

    private function matches(Request $request): array
    {
        $clubTeam = Team::club();
        $competitionSeasons = CompetitionSeason::with('competition')
            ->when($clubTeam, fn ($query) => $query->whereHas('teams', fn ($teamQuery) => $teamQuery->whereKey($clubTeam->id)))
            ->orderByDesc('starts_at')
            ->get();
        $competitionSeason = $request->filled('competition')
            ? $competitionSeasons->firstWhere('id', $request->integer('competition'))
            : CompetitionSeason::currentForClub($clubTeam);

        $query = GameMatch::with([
            'homeTeam',
            'awayTeam',
            'venue',
            'competitionSeason.competition',
            'reportArticle',
        ]);

        if ($competitionSeason?->starts_at && $competitionSeason?->ends_at) {
            $query->whereBetween('played_at', [
                $competitionSeason->starts_at->copy()->startOfDay(),
                $competitionSeason->ends_at->copy()->endOfDay(),
            ]);
        }

        if ($clubTeam) {
            $query->where(fn ($matchQuery) => $matchQuery
                ->where('home_team_id', $clubTeam->id)
                ->orWhere('away_team_id', $clubTeam->id));
        }

        if ($request->filled('competition')) {
            $query->where('competition_season_id', $request->integer('competition'));
        }

        if ($request->filled('type')) {
            $query->where('match_type', $request->string('type')->toString());
        }

        $matches = $query->orderBy('played_at')->get();
        $selectedCompetition = $request->filled('competition')
            ? $competitionSeasons->firstWhere('id', $request->integer('competition'))
            : $competitionSeason;

        $standings = collect();
        if ($selectedCompetition) {
            $standings = CompetitionStanding::with('team')
                ->where('competition_season_id', $selectedCompetition->id)
                ->orderByDesc('points')
                ->orderBy('team_id')
                ->get();

        }

        return compact(
            'competitionSeason',
            'matches',
            'clubTeam',
            'competitionSeasons',
            'selectedCompetition',
            'standings',
        );
    }

    private function team(): array
    {
        $competitionSeason = CompetitionSeason::currentForClub();
        $clubTeam = Team::club();
        $players = Player::query()
            ->active()
            ->orderBy('position')
            ->orderBy('jersey_number')
            ->get();

        $groups = [
            PlayerPositionCategory::Goalkeeper->value => $players->filter(fn (Player $player) => $player->position?->category() === PlayerPositionCategory::Goalkeeper),
            PlayerPositionCategory::Defender->value => $players->filter(fn (Player $player) => $player->position?->category() === PlayerPositionCategory::Defender),
            PlayerPositionCategory::Forward->value => $players->filter(fn (Player $player) => $player->position?->category() === PlayerPositionCategory::Forward),
        ];

        return compact('competitionSeason', 'clubTeam', 'players', 'groups');
    }

    private function club(): array
    {
        $partners = Partner::active()->orderBy('name')->get();

        return compact('partners');
    }

    private function contact(): array
    {
        $contactDetails = [
            'email' => Setting::get('site_email', 'info@utbhockey.cz'),
            'phone' => Setting::get('site_phone', '+420 777 123 456'),
            'marketing' => Setting::get('marketing_email', 'marketing@utbhockey.cz'),
            'address' => Setting::get('site_address', "CCM Aréna\nBřeznická 4068\n760 01 Zlín"),
            'hours' => Setting::get('office_hours', 'Po – Pa: 9:00 – 17:00'),
        ];

        return compact('contactDetails');
    }

    private function competitionData(?Team $clubTeam): array
    {
        $competitionSeason = CompetitionSeason::currentForClub($clubTeam);

        if (! $competitionSeason) {
            return [null, collect()];
        }

        $standings = CompetitionStanding::with('team')
            ->where('competition_season_id', $competitionSeason->id)
            ->orderByDesc('points')
            ->orderBy('team_id')
            ->get();

        return [$competitionSeason, $standings];
    }
}
