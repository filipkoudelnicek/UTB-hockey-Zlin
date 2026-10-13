<?php

namespace App\Models\Concerns;

use App\Models\AnalyticsDailyPage;
use App\Services\Analytics\AnalyticsTracker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Přidá views_total a views_30d (posledních 30 dní včetně dneška) z denních souhrnů.
     */
    public function scopeWithAnalyticsViews(Builder $query): Builder
    {
        $since = AnalyticsTracker::now()->subDays(29)->toDateString();

        return $query
            ->withSum('analyticsViews as views_total', 'views')
            ->withSum(['analyticsViews as views_30d' => fn ($views) => $views->where('view_date', '>=', $since)], 'views');
    }
}
