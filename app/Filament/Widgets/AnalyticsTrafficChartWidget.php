<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\AnalyticsPeriod;
use App\Services\Analytics\AnalyticsReport;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AnalyticsTrafficChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    public static function canView(): bool
    {
        return \App\Support\AnalyticsAccess::allowed();
    }

    protected ?string $heading = 'Návštěvnost';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'scales' => [
                'y' => ['beginAtZero' => true, 'suggestedMax' => 5, 'ticks' => ['precision' => 0]],
            ],
            'plugins' => ['legend' => ['display' => true]],
        ];
    }

    protected function getData(): array
    {
        $daily = app(AnalyticsReport::class)->daily(AnalyticsPeriod::fromFilters($this->pageFilters));

        $line = fn (string $label, string $key, string $color, bool $fill = false) => [
            'label' => $label,
            'data' => $daily->pluck($key)->all(),
            'borderColor' => $color,
            'backgroundColor' => $color.($fill ? '22' : ''),
            'fill' => $fill,
            'tension' => 0.25,
            'pointRadius' => $daily->count() > 31 ? 0 : 3,
        ];

        return [
            'datasets' => [
                $line('Zobrazení', 'views', '#7400ff', true),
                $line('Unikátní návštěvníci', 'visitors', '#f57800'),
                $line('Vracející se', 'returning', '#10b981'),
            ],
            'labels' => $daily->keys()->map(fn (string $date) => CarbonImmutable::parse($date)->format('j. n.'))->all(),
        ];
    }
}
