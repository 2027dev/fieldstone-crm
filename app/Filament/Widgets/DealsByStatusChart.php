<?php

namespace App\Filament\Widgets;

use App\Models\Deal;
use Filament\Widgets\ChartWidget;

class DealsByStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Deals by status';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = Deal::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'datasets' => [
                [
                    'data' => [
                        $counts->get('open', 0),
                        $counts->get('won', 0),
                        $counts->get('lost', 0),
                    ],
                    'backgroundColor' => ['#78716c', '#10b981', '#f43f5e'],
                ],
            ],
            'labels' => ['Open', 'Won', 'Lost'],
        ];
    }
}
