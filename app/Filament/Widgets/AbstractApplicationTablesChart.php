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

    abstract protected function retrieveData(): array;

    protected function getData(): array
    {
        $data = $this->retrieveData();
        return [
            'datasets' => [
                [
                    'label' => 'Total Tables',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        'rgb(250, 80, 50)',
                        'rgb(0, 200, 255)',
                        'rgb(255, 200, 80)',
                        'rgb(250, 100, 200)',
                        'rgb(150, 100, 255)',
                        'rgb(0, 180, 0)',
                    ],
                ],
            ],
            'labels' => array_keys($data),
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
