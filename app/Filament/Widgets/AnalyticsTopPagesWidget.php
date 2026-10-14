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

    /** @var array<int, string> */
    public array $types = ['pages', 'articles', 'players'];

    public string $search = '';

    public string $sortColumn = 'views';

    public string $direction = 'desc';

    public int $page = 1;

    public int $perPage = 20;

    public function updated(string $name): void
    {
        if (in_array($name, ['types', 'search', 'perPage'], true) || str_starts_with($name, 'types.')) {
            $this->page = 1;
        }
    }

    public function previousPage(): void
    {
        $this->page = max(1, $this->page - 1);
    }

    public function nextPage(): void
    {
        $this->page++;
    }

    public function gotoPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function sortBy(string $column): void
    {
        $this->page = 1;

        if ($this->sortColumn === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortColumn = $column;
        $this->direction = $column === 'path' ? 'asc' : 'desc';
    }

    protected function getViewData(): array
    {
        $report = app(AnalyticsReport::class);
        $period = AnalyticsPeriod::fromFilters($this->pageFilters);
        $perPage = in_array($this->perPage, [20, 50, 100], true) ? $this->perPage : 20;

        $pages = $report->topPages($period, $perPage, $this->types, $this->search, $this->sortColumn, $this->direction, $this->page);

        if ($pages->currentPage() > $pages->lastPage()) {
            $this->page = max(1, $pages->lastPage());
            $pages = $report->topPages($period, $perPage, $this->types, $this->search, $this->sortColumn, $this->direction, $this->page);
        }

        return ['pages' => $pages];
    }
}

