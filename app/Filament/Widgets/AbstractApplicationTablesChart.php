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
                        'rgb(200, 180, 90)',
                        'rgb(0, 200, 255)',
                        'rgb(255, 200, 80)',
                        'rgb(250, 100, 200)',
                        'rgb(150, 100, 255)',
                        'rgb(0, 180, 0)',
                        'rgb(250, 80, 50)',
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
