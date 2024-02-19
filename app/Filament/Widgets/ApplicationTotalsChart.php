<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApplicationTotalsChart extends ChartWidget
{
    protected static ?string $heading = 'Total Applications (active)';
    protected static ?string $maxHeight = '200px';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $data = Cache::remember('dd-admin-application-totals', 60, fn() => Application::query()->toBase()->select(DB::raw('COUNT(*) as count, type'))->whereNull('canceled_at')->groupBy('type')->get());
        return [
            'datasets' => [
                [
                    'label' => 'Total Applications',
                    'data' => $data->pluck('count')->all(),
                    'backgroundColor' => [
                      'rgb(50, 180, 80)',
                      'rgb(54, 162, 235)',
                      'rgb(255, 205, 86)'
                    ],
                ],
            ],
            'labels' => $data->pluck('type')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
