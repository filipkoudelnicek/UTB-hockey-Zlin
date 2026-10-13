<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\AnalyticsPeriod;
use App\Services\Analytics\AnalyticsReport;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class AnalyticsTopPagesWidget extends Widget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    public static function canView(): bool
    {
        return \App\Support\AnalyticsAccess::allowed();
    }

    protected string $view = 'filament.widgets.analytics-top-pages';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'pages' => app(AnalyticsReport::class)->topPages(AnalyticsPeriod::fromFilters($this->pageFilters), 20),
        ];
    }
}
