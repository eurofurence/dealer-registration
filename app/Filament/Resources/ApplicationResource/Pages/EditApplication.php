<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Http\Controllers\Applications\ApplicationController;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('Send status notification')
                ->action('sendStatusNotification')
                ->requiresConfirmation()
                ->icon('heroicon-o-mail'),
        ];
    }

    public function sendStatusNotification()
    {
        ApplicationController::sendStatusNotification($this->getRecord());
        $this->refreshFormData(['is_notified']);
    }
}
