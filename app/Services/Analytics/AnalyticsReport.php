<?php

namespace App\Services\Analytics;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsReport
{
    private const RETURNING = 'COUNT(DISTINCT CASE WHEN is_returning = 1 THEN visitor_hash END)';

    /**
     * Unikátní návštěvníci se počítají nad celým obdobím, nikoli jako součet denních hodnot.
     * Vracející se = návštěvník, který v období navštívil web v den po svém prvním dni.
     *
     * @return array{views: int, visitors: int, returning: int, new: int}
     */
    public function summary(AnalyticsPeriod $period): array
    {
        $views = (int) DB::table('analytics_daily_pages')
            ->whereBetween('view_date', $this->range($period))
            ->sum('views');

        $row = DB::table('analytics_pageviews')
            ->whereBetween('view_date', $this->range($period))
            ->selectRaw('COUNT(DISTINCT visitor_hash) AS visitors, '.self::RETURNING.' AS returning_visitors')
            ->first();

        $visitors = (int) ($row->visitors ?? 0);
        $returning = (int) ($row->returning_visitors ?? 0);

        return [
            'views' => $views,
            'visitors' => $visitors,
            'returning' => $returning,
            'new' => $visitors - $returning,
        ];
    }

    /**
     * @return Collection<string, array{views: int, visitors: int, returning: int}>
     */
    public function daily(AnalyticsPeriod $period): Collection
    {
        $views = DB::table('analytics_daily_pages')
            ->whereBetween('view_date', $this->range($period))
            ->groupBy('view_date')
            ->selectRaw('view_date, SUM(views) AS views')
            ->get()
            ->mapWithKeys(fn ($row) => [substr((string) $row->view_date, 0, 10) => (int) $row->views]);

        $visitors = DB::table('analytics_pageviews')
            ->whereBetween('view_date', $this->range($period))
            ->groupBy('view_date')
            ->selectRaw('view_date, COUNT(DISTINCT visitor_hash) AS visitors, '.self::RETURNING.' AS returning_visitors')
            ->get()
            ->keyBy(fn ($row) => substr((string) $row->view_date, 0, 10));

        return collect($period->dates())->mapWithKeys(function (string $date) use ($views, $visitors) {
            $row = $visitors->get($date);

            return [$date => [
                'views' => $views[$date] ?? 0,
                'visitors' => (int) ($row->visitors ?? 0),
                'returning' => (int) ($row->returning_visitors ?? 0),
            ]];
        });
    }

    /**
     * @return Collection<int, object{path: string, views: int, visitors: int}>
     */
    public function topPages(AnalyticsPeriod $period, int $limit = 20): Collection
    {
        return DB::table('analytics_pageviews')
            ->whereBetween('view_date', $this->range($period))
            ->groupBy('path')
            ->selectRaw('path, COUNT(*) AS views, COUNT(DISTINCT visitor_hash) AS visitors')
            ->orderByDesc('views')
            ->orderBy('path')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array{string, string}
     */
    private function range(AnalyticsPeriod $period): array
    {
        return [$period->from->toDateString(), $period->to->toDateString()];
    }
}
