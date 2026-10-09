<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ActivitiesByTypeChart;
use App\Filament\Widgets\CrmStatsOverview;
use App\Filament\Widgets\DealsByStageChart;
use App\Filament\Widgets\DealsByStatusChart;
use App\Filament\Widgets\WonDealsTrendChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Insights extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Insights';

    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Insights';

    public function getWidgets(): array
    {
        return [
            CrmStatsOverview::class,
            DealsByStageChart::class,
            DealsByStatusChart::class,
            ActivitiesByTypeChart::class,
            WonDealsTrendChart::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return 2;
    }
}
