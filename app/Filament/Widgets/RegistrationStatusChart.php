<?php

namespace App\Filament\Widgets;

use App\Http\Controllers\Client\RegSysClientController;
use App\Models\Application;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RegistrationStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Registration Status (active; refresh: 10 minutes)';
    protected static ?string $pollingInterval = '3600s';
    protected static ?string $maxHeight = '200px';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        try {
            $data = Cache::remember('dd-admin-application-totals', 10 * 60, function (): array {
                $registrations = RegSysClientController::getAllRegs('id');
                return Application::with('user')->whereNull('canceled_at')->whereNull('waiting_at')->get()
                    ->map(fn (Application $application): string => $registrations[$application->user?->reg_id]['status'] ?? 'unknown')
                    ->reduce(function (array $statusCount, string $status) {
                        $statusCount[$status] += 1;
                        return $statusCount;
                    }, [
                        'new' => 0,
                        'approved' => 0,
                        'partially paid' => 0,
                        'paid' => 0,
                        'checked in' => 0,
                        'unknown' => 0,
                        'cancelled' => 0,
                    ]);
            });
            return [
                'datasets' => [
                    [
                        'label' => 'Total Applications',
                        'data' => array_values($data),
                        'backgroundColor' => [
                            'rgb(255, 200, 80)',
                            'rgb(0, 200, 255)',
                            'rgb(250, 100, 200)',
                            'rgb(100, 255, 100)',
                            'rgb(0, 180, 0)',
                            'rgb(120, 120, 120)',
                            'rgb(250, 80, 80)',
                        ],
                    ],
                ],
                'labels' => array_keys($data),
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
