<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

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
        try {
            $status = Cache::remember('dd-admin-application-status', 60, fn () => [
                ApplicationStatus::Canceled->displayName() => ApplicationStatus::Canceled->orWhere(Application::query())->count(),
                ApplicationStatus::Open->displayName() => ApplicationStatus::Open->orWhere(Application::query())->count(),
                ApplicationStatus::Waiting->displayName() => ApplicationStatus::Waiting->orWhere(Application::query())->count(),
                ApplicationStatus::TableAssigned->displayName() => ApplicationStatus::TableAssigned->orWhere(Application::query())->count(),
                ApplicationStatus::TableOffered->displayName() => ApplicationStatus::TableOffered->orWhere(Application::query())->count(),
                ApplicationStatus::TableAccepted->displayName() => ApplicationStatus::TableAccepted->orWhere(Application::query())->count(),
                ApplicationStatus::CheckedIn->displayName() => ApplicationStatus::CheckedIn->orWhere(Application::query())->count(),
                ApplicationStatus::CheckedOut->displayName() => ApplicationStatus::CheckedOut->orWhere(Application::query())->count(),
            ]);

            return [
                'datasets' => [
                    [
                        'label' => 'Total Applications',
                        'data' => array_values($status),
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
                'labels' => array_keys($status),
            ];
        } catch (\Throwable $th) {
            return [];
        }
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
