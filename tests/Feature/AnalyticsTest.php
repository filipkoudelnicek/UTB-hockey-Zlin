<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Services\Analytics\AnalyticsPageContext;
use App\Services\Analytics\AnalyticsPeriod;
use App\Services\Analytics\AnalyticsReport;
use App\Services\Analytics\AnalyticsTracker;
use App\Services\Analytics\PageToken;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    private const BROWSER = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';

    protected function setUp(): void
    {
        parent::setUp();

        // Starší migrace projektu nejsou kompatibilní se SQLite, proto spouštíme jen tu analytickou.
        $this->artisan('migrate', [
            '--path' => 'database/migrations/2026_10_13_000001_create_analytics_tables.php',
            '--realpath' => false,
        ])->run();

        Cache::flush();
        $this->travelTo(CarbonImmutable::parse('2026-10-13 12:00:00', 'Europe/Prague'));
    }

    private function collect(string $path = '/aktuality', ?string $visitorId = null, array $headers = [], ?string $token = null, ?string $type = null, ?int $id = null)
    {
        $token ??= PageToken::issue($path, $type, $id);
        $request = $this->withHeaders(['User-Agent' => self::BROWSER] + $headers);

        if ($visitorId) {
            $request = $request->withCookie(config('analytics.cookie.name'), $visitorId);
        }

        return $request->post('/_a/c', ['t' => $token], ['Accept' => 'application/json']);
    }

    public function test_view_is_recorded_and_first_party_cookie_is_set_for_six_months_without_ip(): void
    {
        $response = $this->collect('/aktuality');

        $response->assertNoContent();
        $cookie = $response->getCookie(config('analytics.cookie.name'));
        $this->assertNotNull($cookie);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $cookie->getValue());
        $this->assertEqualsWithDelta(now()->addMonthsNoOverflow(6)->getTimestamp(), $cookie->getExpiresTime(), 120);
        $this->assertSame('lax', $cookie->getSameSite());

        $this->assertSame(1, DB::table('analytics_pageviews')->count());
        $row = DB::table('analytics_pageviews')->first();
        $this->assertSame('/aktuality', $row->path);
        $this->assertSame(AnalyticsTracker::hash($cookie->getValue()), $row->visitor_hash);
        $this->assertNotContains('ip', array_map('strtolower', array_keys((array) $row)));
        $this->assertSame(['visitor_hash', 'first_seen_date', 'last_seen_at'], \Schema::getColumnListing('analytics_visitors'));
        $this->assertSame(1, (int) DB::table('analytics_daily_pages')->value('views'));
    }

    public function test_reload_within_thirty_seconds_is_not_counted_but_later_is(): void
    {
        $id = AnalyticsTracker::newVisitorId();

        $this->collect('/aktuality', $id);
        $this->travel(20)->seconds();
        $this->collect('/aktuality', $id);

        $this->assertSame(1, DB::table('analytics_pageviews')->count());

        $this->collect('/hraci', $id);
        $this->assertSame(2, DB::table('analytics_pageviews')->count());

        $this->travel(15)->seconds();
        $this->collect('/aktuality', $id);

        $this->assertSame(3, DB::table('analytics_pageviews')->count());
        $this->assertSame(2, (int) DB::table('analytics_daily_pages')->where('path', '/aktuality')->value('views'));
    }

    public function test_tokens_are_single_use_and_invalid_tokens_are_ignored(): void
    {
        $token = PageToken::issue('/aktuality', null, null);

        $this->collect(token: $token);
        $this->travel(60)->seconds();
        $this->collect(token: $token);
        $this->collect(token: 'garbage');
        $this->collect(token: PageToken::issue('/admin-utb/articles', null, null));

        $this->assertSame(1, DB::table('analytics_pageviews')->count());
    }

    public function test_bots_previews_and_cross_site_requests_are_not_counted(): void
    {
        $this->withHeaders(['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)'])
            ->postJson('/_a/c', ['t' => PageToken::issue('/a', null, null)])->assertNoContent();
        $this->collect('/b', null, ['Sec-Purpose' => 'prefetch']);
        $this->collect('/c', null, ['Sec-Fetch-Dest' => 'iframe']);
        $this->collect('/d', null, ['Sec-Fetch-Site' => 'cross-site']);
        $this->withHeaders(['User-Agent' => ''])->postJson('/_a/c', ['t' => PageToken::issue('/e', null, null)]);

        $this->assertSame(0, DB::table('analytics_pageviews')->count());
    }

    public function test_page_context_skips_admins_bots_previews_and_non_public_paths(): void
    {
        $make = fn (string $uri, string $method = 'GET', string $ua = self::BROWSER) => Request::create($uri, $method, server: ['HTTP_USER_AGENT' => $ua]);

        $this->assertNotNull(AnalyticsPageContext::build($make('/aktuality/clanek'), []));
        $this->assertNotNull(AnalyticsPageContext::build($make('/'), []));
        $this->assertNull(AnalyticsPageContext::build($make('/aktuality', 'POST'), []));
        $this->assertNull(AnalyticsPageContext::build($make('/aktuality', 'GET', 'Googlebot/2.1'), []));
        $this->assertNull(AnalyticsPageContext::build($make('/admin-utb/articles'), []));
        $this->assertNull(AnalyticsPageContext::build($make('/livewire/update'), []));

        $prefetch = $make('/aktuality');
        $prefetch->headers->set('Sec-Purpose', 'prefetch;prerender');
        $this->assertNull(AnalyticsPageContext::build($prefetch, []));

        $admin = $make('/aktuality');
        $admin->setUserResolver(fn () => new User);
        $this->assertNull(AnalyticsPageContext::build($admin, []));
    }

    public function test_page_context_links_articles_to_their_views(): void
    {
        $article = new Article;
        $article->id = 42;

        $context = AnalyticsPageContext::build(
            Request::create('/aktuality/clanek/', server: ['HTTP_USER_AGENT' => self::BROWSER]),
            ['article' => $article],
        );

        $page = PageToken::consume($context['token']);
        $this->assertSame(['path' => '/aktuality/clanek', 'type' => 'article', 'id' => 42], $page);
    }

    public function test_unique_visitors_are_counted_over_the_whole_period_not_summed_by_day(): void
    {
        $a = AnalyticsTracker::newVisitorId();
        $b = AnalyticsTracker::newVisitorId();

        $this->collect('/aktuality', $a);
        $this->collect('/aktuality', $b);
        $this->travel(1)->days();
        $this->collect('/aktuality', $a);
        $this->collect('/hraci', $a);
        $this->travel(1)->days();
        $this->collect('/hraci', $a);

        $period = AnalyticsPeriod::fromFilters(['period' => 'last_7']);
        $report = new AnalyticsReport;
        $summary = $report->summary($period);

        $this->assertSame(5, $summary['views']);
        $this->assertSame(2, $summary['visitors']);
        $this->assertSame(1, $summary['returning']);
        $this->assertSame(1, $summary['new']);

        $daily = $report->daily($period);
        $this->assertSame(7, $daily->count());
        $this->assertSame(4, array_sum(array_column($daily->all(), 'visitors')));
        $this->assertSame(['views' => 2, 'visitors' => 2, 'returning' => 0], $daily['2026-10-13']);
        $this->assertSame(['views' => 2, 'visitors' => 1, 'returning' => 1], $daily['2026-10-14']);

        $top = $report->topPages($period);
        $this->assertSame('/aktuality', $top[0]->path);
        $this->assertSame(3, (int) $top[0]->views);
        $this->assertSame(2, (int) $top[0]->visitors);
    }

    public function test_withdrawing_consent_erases_visitor_data_and_expires_cookie(): void
    {
        $id = AnalyticsTracker::newVisitorId();
        $other = AnalyticsTracker::newVisitorId();
        $this->collect('/aktuality', $id);
        $this->collect('/aktuality', $other);

        $response = $this->withCookie(config('analytics.cookie.name'), $id)->post('/_a/f', [], ['Accept' => 'application/json']);

        $response->assertNoContent();
        $cookie = $response->getCookie(config('analytics.cookie.name'));
        $this->assertLessThan(time(), $cookie->getExpiresTime());
        $this->assertSame(1, DB::table('analytics_pageviews')->count());
        $this->assertSame(1, DB::table('analytics_visitors')->count());
        $this->assertSame(AnalyticsTracker::hash($other), DB::table('analytics_visitors')->value('visitor_hash'));
        $this->assertSame(2, (int) DB::table('analytics_daily_pages')->value('views'));
    }

    public function test_statistics_are_hidden_everywhere_without_permission(): void
    {
        foreach ([true, false] as $allowed) {
            $user = \Mockery::mock(User::class)->makePartial();
            $user->shouldReceive('hasPermission')->with('analytics.view')->andReturn($allowed);
            $this->actingAs($user);

            $this->assertSame($allowed, \App\Filament\Pages\Statistics::canAccess());
            $this->assertSame($allowed, \App\Filament\Widgets\AnalyticsDashboardWidget::canView());
            $this->assertSame($allowed, \App\Filament\Widgets\AnalyticsOverviewWidget::canView());
            $this->assertSame($allowed, \App\Filament\Widgets\AnalyticsTrafficChartWidget::canView());
            $this->assertSame($allowed, \App\Filament\Widgets\AnalyticsTopPagesWidget::canView());
            $this->assertSame($allowed, \App\Support\AnalyticsAccess::allowed());
        }
    }

    public function test_period_filters_use_prague_dates(): void
    {
        $now = CarbonImmutable::parse('2026-10-13 23:30:00', 'Europe/Prague');

        $p = AnalyticsPeriod::fromFilters(['period' => 'last_7'], $now);
        $this->assertSame(['2026-10-07', '2026-10-13'], [$p->from->toDateString(), $p->to->toDateString()]);

        $p = AnalyticsPeriod::fromFilters(['period' => 'last_30'], $now);
        $this->assertSame('2026-09-14', $p->from->toDateString());

        $p = AnalyticsPeriod::fromFilters(['period' => 'this_month'], $now);
        $this->assertSame(['2026-10-01', '2026-10-13'], [$p->from->toDateString(), $p->to->toDateString()]);

        $p = AnalyticsPeriod::fromFilters(['period' => 'previous_month'], $now);
        $this->assertSame(['2026-09-01', '2026-09-30'], [$p->from->toDateString(), $p->to->toDateString()]);

        $p = AnalyticsPeriod::fromFilters(['period' => 'custom', 'start_date' => '2026-10-10', 'end_date' => '2026-10-05'], $now);
        $this->assertSame(['2026-10-05', '2026-10-10'], [$p->from->toDateString(), $p->to->toDateString()]);

        $p = AnalyticsPeriod::fromFilters(['period' => 'custom'], $now);
        $this->assertSame('2026-09-14', $p->from->toDateString());
    }

    public function test_rate_limit_blocks_abuse_of_the_endpoint(): void
    {
        config(['analytics.rate_limit.per_ip_per_minute' => 3]);

        for ($i = 0; $i < 3; $i++) {
            $this->collect("/p{$i}")->assertNoContent();
        }

        $this->collect('/p4')->assertStatus(429);
    }

    public function test_article_view_counts_are_available_for_sorting(): void
    {
        $this->collect('/a', null, [], null, 'article', 7);
        $this->travel(40)->days();
        $this->collect('/a', null, [], null, 'article', 7);
        $this->collect('/b', null, [], null, 'page', 7);

        $article = new Article;
        $article->id = 7;
        $row = Article::query()->withAnalyticsViews()->getQuery()->toSql();

        $this->assertStringContainsString('views_total', $row);
        $this->assertSame(2, (int) $article->analyticsViews()->sum('views'));
        $this->assertSame(1, (int) $article->analyticsViews()->where('view_date', '>=', now()->subDays(29)->toDateString())->sum('views'));
    }

    public function test_prune_removes_old_raw_data_but_keeps_daily_totals(): void
    {
        $this->collect('/a');
        $this->travel(15)->months();
        $this->collect('/a');

        $result = app(AnalyticsTracker::class)->prune();

        $this->assertSame(1, $result['pageviews']);
        $this->assertSame(1, DB::table('analytics_pageviews')->count());
        $this->assertSame(2, (int) DB::table('analytics_daily_pages')->sum('views'));
    }
}
