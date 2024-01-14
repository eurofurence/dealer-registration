<?php

namespace App\Filament\Resources\TableTypeResource\Pages;

use App\Filament\Resources\TableTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTableType extends EditRecord
{
    protected static string $resource = TableTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
