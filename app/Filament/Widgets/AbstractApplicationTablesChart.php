<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class AbstractApplicationTablesChart extends ChartWidget
{
    protected static ?string $heading = 'Total Tables (active)';
    protected static ?string $maxHeight = '200px';

    protected function getType(): string
    {
        return 'pie';
    }

    abstract protected function retrieveData(): \Illuminate\Support\Collection;

    protected function getData(): array
    {
        $data = $this->retrieveData();
        return [
            'datasets' => [
                [
                    'label' => 'Total Tables',
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
