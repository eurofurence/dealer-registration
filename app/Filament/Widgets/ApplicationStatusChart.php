<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
        $status = Cache::remember('dd-admin-application-status', 60, fn () => [
            'canceled' => ApplicationStatus::Canceled->orWhere(Application::query())->count(),
            'open' => ApplicationStatus::Open->orWhere(Application::query())->count(),
            'waiting' => ApplicationStatus::Waiting->orWhere(Application::query())->count(),
            'tableAssigned' => ApplicationStatus::TableAssigned->orWhere(Application::query())->count(),
            'tableOffered' => ApplicationStatus::TableOffered->orWhere(Application::query())->count(),
            'tableAccepted' => ApplicationStatus::TableAccepted->orWhere(Application::query())->count(),
            'checkedIn' => ApplicationStatus::CheckedIn->orWhere(Application::query())->count(),
            'checkedOut' => ApplicationStatus::CheckedOut->orWhere(Application::query())->count(),
        ]);

        return [
            'datasets' => [
                [
                    'label' => 'Total Applications',
                    'data' => [
                        $status['canceled'],
                        $status['open'],
                        $status['waiting'],
                        $status['tableAssigned'],
                        $status['tableOffered'],
                        $status['tableAccepted'],
                        $status['checkedIn'],
                        $status['checkedOut'],
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
                Str::of(ApplicationStatus::Canceled->value)->replace('_', ' ')->title(),
                Str::of(ApplicationStatus::Open->value)->replace('_', ' ')->title(),
                Str::of(ApplicationStatus::Waiting->value)->replace('_', ' ')->title(),
                Str::of(ApplicationStatus::TableAssigned->value)->replace('_', ' ')->title(),
                Str::of(ApplicationStatus::TableOffered->value)->replace('_', ' ')->title(),
                Str::of(ApplicationStatus::TableAccepted->value)->replace('_', ' ')->title(),
                Str::of(ApplicationStatus::CheckedIn->value)->replace('_', ' ')->title(),
                Str::of(ApplicationStatus::CheckedOut->value)->replace('_', ' ')->title(),
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
