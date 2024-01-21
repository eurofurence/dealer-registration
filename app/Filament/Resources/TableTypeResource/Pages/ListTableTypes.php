<?php

namespace App\Filament\Resources\TableTypeResource\Pages;

use App\Filament\Resources\TableTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTableTypes extends ListRecords
{
    protected static string $resource = TableTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
