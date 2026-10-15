<?php

namespace App\Models\Concerns;

use App\Models\AnalyticsDailyPage;
use App\Services\Analytics\AnalyticsTracker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Vyžaduje konstantu ANALYTICS_TYPE shodnou s typem v tokenu stránky.
 */
trait HasAnalyticsViews
{
    public function analyticsViews(): HasMany
    {
        return $this->hasMany(AnalyticsDailyPage::class, 'viewable_id')
            ->where('analytics_daily_pages.viewable_type', static::ANALYTICS_TYPE);
    }

    /**
     * Přidá celková zobrazení, zobrazení a unikátní návštěvníky za posledních 30 dní.
     */
    public function scopeWithAnalyticsViews(Builder $query): Builder
    {
        $since = AnalyticsTracker::now()->subDays(29)->toDateString();
        $today = AnalyticsTracker::now()->toDateString();
        $model = $query->getModel();

        return $query
            ->withSum('analyticsViews as views_total', 'views')
            ->addSelect([
                'views_30d' => DB::table('analytics_daily_pages')
                    ->selectRaw('COALESCE(SUM(views), 0)')
                    ->whereBetween('view_date', [$since, $today])
                    ->where(function ($views) use ($model) {
                        $views
                            ->where(fn ($record) => $record
                                ->where('viewable_type', static::ANALYTICS_TYPE)
                                ->whereColumn('viewable_id', $model->qualifyColumn($model->getKeyName())));

                        if ($model instanceof \App\Models\Page) {
                            $views->orWhere(fn ($homepage) => $homepage
                                ->where($model->qualifyColumn('type'), 'homepage')
                                ->where('path', '/'));
                        }
                    }),
            ])
            ->addSelect([
                'visitors_30d' => DB::table('analytics_pageviews')
                    ->selectRaw('COUNT(DISTINCT visitor_hash)')
                    ->whereBetween('view_date', [$since, $today])
                    ->where(function ($visitors) use ($model) {
                        $visitors
                            ->where(fn ($record) => $record
                                ->where('viewable_type', static::ANALYTICS_TYPE)
                                ->whereColumn('viewable_id', $model->qualifyColumn($model->getKeyName())));

                        if ($model instanceof \App\Models\Page) {
                            $visitors->orWhere(fn ($homepage) => $homepage
                                ->where($model->qualifyColumn('type'), 'homepage')
                                ->where('path', '/'));
                        }
                    }),
            ]);
    }
}
