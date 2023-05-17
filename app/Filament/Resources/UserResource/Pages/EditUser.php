<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Http\Controllers\Client\RegSysClientController;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('Book package')
                ->action('bookPackage')
                ->requiresConfirmation(),
            Actions\Action::make('Remove package')
                ->action('removePackage')
                ->requiresConfirmation(),
        ];
    }

    public function bookPackage()
    {
        RegSysClientController::bookPackage($this->getRecord()->reg_id, $this->getRecord()->application()->first()->assignedTable()->first());
        $this->refreshFormData(['packages booked']);
    }

    public function removePackage()
    {
        RegSysClientController::removePackage($this->getRecord()->reg_id, $this->getRecord()->application()->first()->assignedTable()->first());
        $this->refreshFormData(['packages booked']);
    }
}
