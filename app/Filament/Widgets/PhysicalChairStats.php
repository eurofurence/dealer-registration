<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PhysicalChairStats extends BaseWidget
{
    protected function getStats(): array
    {
        try {
            $data = Cache::remember('dd-admin-physical-chairs-stats', 1, fn() => Application::query()->toBase()->select(
                // chair_product allows summing the number of chairs requested across groups
                DB::raw('physical_chairs, COUNT(*) as count, (physical_chairs * COUNT(*)) as chair_product'))
                // Taken from ApplicationStats: Do not include cancelled or waiting applications
                ->whereNull('canceled_at')->whereNull('waiting_at')
                // Only count the main dealership entities, not shares etc.
                ->where('type','dealer')
                // Group by chair count so we can derive different metrics
                ->groupBy('physical_chairs')->get()
            );
            return [
                // Any value below 0 means "not set" - let's display this as it would indicate incomplete planning
                Stat::make('Applications missing chair assignment', fn() => $data->where('physical_chairs','<','0')?->sum('count')),
                // List dealerships without chairs separately - we expect this number to be not too high
                Stat::make('Applications with no chairs', fn() => $data->where('physical_chairs','==','0')?->sum('count')),
                // This is the main metric of interest: Assuming a complete planning - how many chairs are needed overall?
                Stat::make('Total assigned Chairs', fn() => $data->where('physical_chairs','>','0')?->sum('chair_product')),
            ];
        } catch (\Throwable $th) {
            return [];
        }
    }
}
