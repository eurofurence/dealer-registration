<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\TableType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationTablesRequestedChart extends AbstractApplicationTablesChart
{
    protected static ?string $heading = 'Total Tables Requested (active)';

    protected function retrieveData(): array
    {
        try {
            return Cache::remember('dd-admin-application-tables-requested', 60, function(): array {
                $tableTypeCount = Application::query()->toBase()->join('table_types', 'table_type_requested', '=', 'table_types.id')->select(DB::raw('table_types.id as id, COUNT(*) as count'))->where('type', '=', 'dealer')->whereNull('canceled_at')->whereNull('waiting_at')->groupBy('table_types.id')->get()->pluck('count', 'id');
                return TableType::all()->mapWithKeys(function(TableType $tableType) use ($tableTypeCount) {
                    return [ $tableType->name => $tableTypeCount[$tableType->id] ?? 0];
                })->toArray();
            });
        } catch (\Throwable $th) {
            return [];
        }
    }
}
