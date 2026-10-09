<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Widgets\ChartWidget;

class ActivitiesByTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Activities by type';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $counts = Activity::query()
            ->selectRaw('type, count(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type');

        $labels = Activity::TYPES;

        return [
            'datasets' => [
                [
                    'label' => 'Activities',
                    'data' => collect($labels)->keys()->map(fn ($key) => $counts->get($key, 0)),
                    'backgroundColor' => '#059669',
                ],
            ],
            'labels' => array_values($labels),
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
