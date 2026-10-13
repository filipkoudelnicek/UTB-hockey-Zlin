<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\AnalyticsPeriod;
use App\Services\Analytics\AnalyticsReport;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverviewWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    public static function canView(): bool
    {
        return \App\Support\AnalyticsAccess::allowed();
    }

    protected function getStats(): array
    {
        $period = AnalyticsPeriod::fromFilters($this->pageFilters);
        $summary = app(AnalyticsReport::class)->summary($period);

        return [
            Stat::make('Zobrazení', number_format($summary['views'], 0, ',', ' '))
                ->description($period->from->format('j. n. Y').' – '.$period->to->format('j. n. Y')),
            Stat::make('Unikátní návštěvníci', number_format($summary['visitors'], 0, ',', ' '))
                ->description('Za celé období, bez součtu denních hodnot'),
            Stat::make('Vracející se návštěvníci', number_format($summary['returning'], 0, ',', ' '))
                ->description('Noví návštěvníci: '.number_format($summary['new'], 0, ',', ' ')),
        ];
    }
}
