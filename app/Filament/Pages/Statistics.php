<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnalyticsOverviewWidget;
use App\Filament\Widgets\AnalyticsTopPagesWidget;
use App\Filament\Widgets\AnalyticsTrafficChartWidget;
use App\Services\Analytics\AnalyticsPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Statistics extends Dashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'statistiky';

    protected static ?string $slug = 'statistiky';

    protected static ?string $title = 'Návštěvnost webu';

    protected static ?string $navigationLabel = 'Návštěvnost webu';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Přehledy';

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return \App\Support\AnalyticsAccess::allowed();
    }

    public function getColumns(): int|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            AnalyticsOverviewWidget::class,
            AnalyticsTrafficChartWidget::class,
            AnalyticsTopPagesWidget::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()
                ->schema([
                    Select::make('period')
                        ->label('Období')
                        ->options(AnalyticsPeriod::options())
                        ->default(AnalyticsPeriod::LAST_30)
                        ->native(false)
                        ->selectablePlaceholder(false)
                        ->live(),
                    DatePicker::make('start_date')
                        ->label('Od')
                        ->maxDate(now(config('analytics.timezone')))
                        ->visible(fn ($get) => $get('period') === AnalyticsPeriod::CUSTOM),
                    DatePicker::make('end_date')
                        ->label('Do')
                        ->maxDate(now(config('analytics.timezone')))
                        ->visible(fn ($get) => $get('period') === AnalyticsPeriod::CUSTOM),
                ])
                ->columns(['default' => 1, 'md' => 3])
                ->columnSpanFull(),
        ]);
    }
}
