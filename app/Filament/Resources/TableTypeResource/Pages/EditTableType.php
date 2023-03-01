<?php

namespace App\Filament\Resources\TableTypeResource\Pages;

use App\Filament\Resources\TableTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTableType extends EditRecord
{
    protected static string $resource = TableTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
