<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApplicationTablesRequestedChart extends AbstractApplicationTablesChart
{
    protected static ?string $heading = 'Total Tables Requested (active)';

    protected function retrieveData(): \Illuminate\Support\Collection
    {
        return Cache::remember('dd-admin-application-tables-requested', 60, fn() => Application::query()->toBase()->join('table_types', 'table_type_requested', '=', 'table_types.id')->select(DB::raw('COUNT(*) as count, name as type'))->where('type', '=', 'dealer')->whereNull('canceled_at')->whereNull('waiting_at')->groupBy('name')->get());
    }
}
