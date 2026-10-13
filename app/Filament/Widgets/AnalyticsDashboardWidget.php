<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\Statistics;
use App\Services\Analytics\AnalyticsPeriod;
use App\Services\Analytics\AnalyticsReport;
use App\Support\AnalyticsAccess;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsDashboardWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Návštěvnost webu – aktuální měsíc';

    public static function canView(): bool
    {
        return AnalyticsAccess::allowed();
    }

    protected function getStats(): array
    {
        $report = app(AnalyticsReport::class);
        $week = AnalyticsPeriod::fromFilters(['period' => AnalyticsPeriod::THIS_MONTH]);
        $summary = $report->summary($week);
        $chart = $report->daily($week)->pluck('views')->all();
        $today = $report->summary(AnalyticsPeriod::fromFilters([
            'period' => AnalyticsPeriod::CUSTOM,
            'start_date' => now(config('analytics.timezone'))->toDateString(),
            'end_date' => now(config('analytics.timezone'))->toDateString(),
        ]));
        $url = Statistics::getUrl();

        return [
            Stat::make('Zobrazení dnes', number_format($today['views'], 0, ',', ' '))
                ->description('Unikátních dnes: '.number_format($today['visitors'], 0, ',', ' '))
                ->url($url),
            Stat::make('Zobrazení za měsíc', number_format($summary['views'], 0, ',', ' '))
                ->chart($chart)
                ->url($url),
            Stat::make('Unikátní návštěvníci (měsíc)', number_format($summary['visitors'], 0, ',', ' '))
                ->description('Vracející se: '.number_format($summary['returning'], 0, ',', ' '))
                ->url($url),
        ];
    }
}
