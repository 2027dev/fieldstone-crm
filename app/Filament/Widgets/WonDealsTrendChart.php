<?php

namespace App\Filament\Widgets;

use App\Models\Deal;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class WonDealsTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Won vs. lost deals (last 6 months)';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => Carbon::now()->subMonths($i)->startOfMonth());

        $deals = Deal::query()
            ->whereIn('status', ['won', 'lost'])
            ->where('updated_at', '>=', $months->first())
            ->get(['status', 'updated_at']);

        $won = $months->map(fn (Carbon $month) => $deals->where('status', 'won')
            ->filter(fn ($deal) => $deal->updated_at->isSameMonth($month))
            ->count());

        $lost = $months->map(fn (Carbon $month) => $deals->where('status', 'lost')
            ->filter(fn ($deal) => $deal->updated_at->isSameMonth($month))
            ->count());

        return [
            'datasets' => [
                [
                    'label' => 'Won',
                    'data' => $won,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Lost',
                    'data' => $lost,
                    'backgroundColor' => '#f43f5e',
                ],
            ],
            'labels' => $months->map(fn (Carbon $month) => $month->format('M')),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true],
            ],
        ];
    }
}
