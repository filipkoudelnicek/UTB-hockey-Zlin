<?php

namespace App\Services\Analytics;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class AnalyticsTracker
{
    public const VISITOR_ID_PATTERN = '/^[a-f0-9]{32}$/';

    public static function newVisitorId(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function hash(string $visitorId): string
    {
        return hash_hmac('sha256', $visitorId, (string) config('app.key'));
    }

    public static function isValidVisitorId(mixed $value): bool
    {
        return is_string($value) && preg_match(self::VISITOR_ID_PATTERN, $value) === 1;
    }

    public static function isTrackablePath(string $path): bool
    {
        if ($path === '' || $path[0] !== '/' || strlen($path) > (int) config('analytics.max_path_length')) {
            return false;
        }

        return ! preg_match('#^/(admin|admin-utb|livewire|_a|storage|up)(/|$)#', $path);
    }

    public static function now(): Carbon
    {
        return Carbon::now(config('analytics.timezone'));
    }

    /**
     * Zaznamená zobrazení. Vrací false, pokud bylo zahozeno (duplicita, limit).
     */
    public function record(string $visitorId, string $path, ?string $viewableType, ?int $viewableId): bool
    {
        if (! self::isValidVisitorId($visitorId) || ! self::isTrackablePath($path)) {
            return false;
        }

        $hash = self::hash($visitorId);

        $limitKey = 'analytics:visitor:'.$hash;
        if (RateLimiter::tooManyAttempts($limitKey, (int) config('analytics.rate_limit.per_visitor_per_minute'))) {
            return false;
        }
        RateLimiter::hit($limitKey, 60);

        $pathHash = sha1($path);

        if (! Cache::add("analytics:dup:{$hash}:{$pathHash}", 1, (int) config('analytics.dedupe_seconds'))) {
            return false;
        }

        $now = self::now();
        $today = $now->toDateString();
        $nowString = $now->toDateTimeString();

        DB::transaction(function () use ($hash, $path, $pathHash, $viewableType, $viewableId, $today, $nowString) {
            DB::table('analytics_visitors')->insertOrIgnore([
                'visitor_hash' => $hash,
                'first_seen_date' => $today,
                'last_seen_at' => $nowString,
            ]);

            $firstSeen = (string) DB::table('analytics_visitors')->where('visitor_hash', $hash)->value('first_seen_date');
            DB::table('analytics_visitors')->where('visitor_hash', $hash)->update(['last_seen_at' => $nowString]);

            DB::table('analytics_pageviews')->insert([
                'visitor_hash' => $hash,
                'path' => $path,
                'viewable_type' => $viewableType,
                'viewable_id' => $viewableId,
                'view_date' => $today,
                'viewed_at' => $nowString,
                'is_returning' => substr($firstSeen, 0, 10) < $today,
            ]);

            DB::table('analytics_daily_pages')->upsert(
                [[
                    'view_date' => $today,
                    'path_hash' => $pathHash,
                    'path' => $path,
                    'viewable_type' => $viewableType,
                    'viewable_id' => $viewableId,
                    'views' => 1,
                ]],
                ['view_date', 'path_hash'],
                ['views' => DB::raw('views + 1')],
            );
        });

        return true;
    }

    /**
     * Smaže veškerá pseudonymní data návštěvníka (odvolání souhlasu).
     * Anonymní denní souhrny zůstávají.
     */
    public function forget(string $visitorId): void
    {
        if (! self::isValidVisitorId($visitorId)) {
            return;
        }

        $hash = self::hash($visitorId);

        DB::transaction(function () use ($hash) {
            DB::table('analytics_pageviews')->where('visitor_hash', $hash)->delete();
            DB::table('analytics_visitors')->where('visitor_hash', $hash)->delete();
        });

        RateLimiter::clear('analytics:visitor:'.$hash);
    }

    /**
     * @return array{pageviews: int, visitors: int}
     */
    public function prune(): array
    {
        $cutoff = self::now()->subMonthsNoOverflow((int) config('analytics.retention_months'));

        $views = 0;
        do {
            $deleted = DB::table('analytics_pageviews')
                ->where('view_date', '<', $cutoff->toDateString())
                ->limit(5000)
                ->delete();
            $views += $deleted;
        } while ($deleted > 0);

        $visitors = DB::table('analytics_visitors')
            ->where('last_seen_at', '<', $cutoff->toDateTimeString())
            ->delete();

        return ['pageviews' => $views, 'visitors' => $visitors];
    }
}
