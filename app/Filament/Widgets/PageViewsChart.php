<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;

class PageViewsChart extends ChartWidget
{
    protected ?string $heading = 'Návštěvnost – posledních 30 dní';

    protected string $color = 'primary';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $stats = PageView::dailyStats(30);

        return [
            'datasets' => [
                [
                    'label'           => 'Zobrazení stránek',
                    'data'            => $stats['pageviews'],
                    'backgroundColor' => 'rgba(116, 0, 255, 0.35)',
                    'borderColor'     => 'rgba(116, 0, 255, 1)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                    'order'           => 2,
                ],
                [
                    'label'           => 'Unikátní návštěvníci',
                    'data'            => $stats['visitors'],
                    'backgroundColor' => 'rgba(0, 180, 120, 0.35)',
                    'borderColor'     => 'rgba(0, 180, 120, 1)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                    'order'           => 1,
                ],
            ],
            'labels' => $stats['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['precision' => 0],
                ],
            ],
        ];
    }
}
