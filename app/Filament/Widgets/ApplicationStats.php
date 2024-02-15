<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ApplicationStats extends BaseWidget
{
    protected function getStats(): array
    {
        $data = Application::query()->toBase()->select(DB::raw('type, COUNT(*) as count'))->whereNull('canceled_at')->groupBy('type')->get();
        return [
            Stat::make('Total Applications (active)', fn() => $data->sum('count')),
            Stat::make('Total Dealers (active)', fn() => $data->firstWhere('type', 'dealer')?->count ?? 0),
            Stat::make('Total Shares (active)', fn() => $data->firstWhere('type', 'share')?->count ?? 0),
            Stat::make('Total Assistants (active)', fn() => $data->firstWhere('type', 'assistant')?->count ?? 0),
        ];
    }
}
