<?php

namespace App\Filament\Widgets;

use App\Models\DealStage;
use Filament\Widgets\ChartWidget;

class DealsByStageChart extends ChartWidget
{
    protected static ?string $heading = 'Open deals by stage';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $stages = DealStage::orderBy('sort_order')
            ->withCount(['deals' => fn ($query) => $query->where('status', 'open')])
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Open deals',
                    'data' => $stages->pluck('deals_count'),
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => $stages->pluck('name'),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => ['display' => false],
            ],
        ];
    }
}
