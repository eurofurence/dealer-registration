<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ApplicationStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Applications by Status';
    protected static ?string $maxHeight = '200px';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total Applications',
                    'data' => [
                        ApplicationStatus::Canceled->orWhere(Application::query())->count(),
                        ApplicationStatus::Open->orWhere(Application::query())->count(),
                        ApplicationStatus::Waiting->orWhere(Application::query())->count(),
                        ApplicationStatus::TableAssigned->orWhere(Application::query())->count(),
                        ApplicationStatus::TableOffered->orWhere(Application::query())->count(),
                        ApplicationStatus::TableAccepted->orWhere(Application::query())->count(),
                        ApplicationStatus::CheckedIn->orWhere(Application::query())->count(),
                        ApplicationStatus::CheckedOut->orWhere(Application::query())->count(),
                    ],
                    'backgroundColor' => [
                        'rgb(250, 80, 80)',
                        'rgb(0, 200, 255)',
                        'rgb(255, 200, 80)',
                        'rgb(250, 100, 200)',
                        'rgb(150, 100, 255)',
                        'rgb(100, 255, 100)',
                        'rgb(0, 180, 0)',
                        'rgb(120, 120, 120)',
                    ],
                ],
            ],
            'labels' => [
                ApplicationStatus::Canceled,
                ApplicationStatus::Open,
                ApplicationStatus::Waiting,
                ApplicationStatus::TableAssigned,
                ApplicationStatus::TableOffered,
                ApplicationStatus::TableAccepted,
                ApplicationStatus::CheckedIn,
                ApplicationStatus::CheckedOut,
            ],
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
